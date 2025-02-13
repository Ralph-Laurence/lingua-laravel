<?php

namespace App\Services;

use App\Models\FieldNames\DocProofFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\Profile;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MyProfileCertificationsDocumentsService extends MyProfileDocumentsService
{
    public function addCertification(Request $request)
    {
        $validation = $this->getCertificationValidationRules($request, 'create');

        // If we got any errors, we should go back
        if ($validation instanceof \Illuminate\Http\RedirectResponse)
            return $validation;

        // Otherwise, we'll retrieve the validated inputs
        $inputs = $validation;
        $uploadedFile = null;

        try
        {
            DB::beginTransaction();

            $profile = Profile::where(ProfileFields::UserId, Auth::id())->firstOrFail();
            $docOps  = $this->addDocument($inputs['file-upload'], DocumentTypes::Certifications);

            if (empty($docOps['status']))
            {
                session()->flash('profile_update_message', "Sorry, we encountered an error while trying to upload the file. Please try again later.");
                return redirect()->back();
            }

            $uploadedFile = $docOps['uploadedFile'];

            $cert   = $profile->{ProfileFields::Certifications};
            $cert[] = [
                DocProofFields::DocId               => Str::random(16),
                DocProofFields::YearFrom            => $inputs['cert-year-from'],
                DocProofFields::CertTitle           => $inputs['certification'],
                DocProofFields::CertDescr           => $inputs['description'],
                DocProofFields::FullPath            => $uploadedFile,
                DocProofFields::OriginalFileName    => $docOps['originalName']
            ];

            $profile->{ProfileFields::Certifications} = array_values($cert);
            $created = $profile->save();

            DB::commit();

            if (!$created)
                throw new Exception();

            session()->flash('profile_update_message', "A new certification entry has been successfully added.");
            return redirect()->back();
        }
        catch (Exception $ex)
        {
            DB::rollBack();

            if (!empty($uploadedFile))
                Storage::delete($uploadedFile);

            session()->flash('profile_update_message', "Sorry, we encountered an error while trying to create the record. Please try again later.");
            return redirect()->back();
        }
    }

    public function updateCertification(Request $request)
    {
        $validation = $this->getCertificationValidationRules($request, 'update');

        // If we got any errors, we should go back
        if ($validation instanceof \Illuminate\Http\RedirectResponse)
            return $validation;

        // Otherwise, we'll retrieve the validated inputs
        $inputs = $validation;
        $uploadedFile = null;

        try
        {
            DB::beginTransaction();

            $profile     = Profile::where(ProfileFields::UserId, Auth::id())->firstOrFail();
            $cert     = $profile->{ProfileFields::Certifications};
            $targetEntry = [];
            $targetEntryKey = null;

            foreach ($cert as $k => $obj)
            {
                if ($obj[DocProofFields::DocId] == $inputs['doc_id'])
                {
                    $targetEntry = $cert[$k];

                    $targetEntry[DocProofFields::YearFrom]  = $inputs['cert-year-from'];
                    $targetEntry[DocProofFields::CertTitle] = $inputs['certification'];
                    $targetEntry[DocProofFields::CertDescr] = $inputs['description'];

                    $targetEntryKey = $k;
                    break;
                }
            }

            if (empty($targetEntry) || $targetEntryKey === null)
            {
                session()->flash('profile_update_message', "Sorry, we're unable to update the entry. Please try again later.");
                return redirect()->back();
            }

            if (array_key_exists('file-upload', $inputs))
            {
                $docOps = $this->replaceDocument(
                    $targetEntry,
                    $inputs['file-upload'],
                    DocumentTypes::Certifications
                );

                if (!$docOps)
                {
                    session()->flash('profile_update_message', "Sorry, we encountered an error while trying to upload the file. Please try again later.");
                    return redirect()->back();
                }
            }

            $cert[$targetEntryKey] = $targetEntry;
            $profile->{ProfileFields::Certifications} = array_values($cert);
            $updated = $profile->save();

            DB::commit();

            if (!$updated)
                throw new Exception();

            session()->flash('profile_update_message', "A certification entry has been successfully updated.");
            return redirect()->route('myprofile.edit');
        }
        catch (Exception $ex)
        {
            error_log($ex->getMessage());
            DB::rollBack();

            if ($uploadedFile !== null)
                Storage::delete($uploadedFile);

            return response()->view('errors.500', [
                'message'   => 'Sorry, we encountered an error while trying to update the record. Please try again later.',
                'redirect'  => route('myprofile.edit')
            ], 500);
        }
    }

    public function removeCertification(Request $request)
    {
        $validator = Validator::make($request->only('docId'), [
            'docId' => 'required|string|max:16'
        ]);

        if ($validator->fails())
        {
            session()->flash('profile_update_message', "We're unable to process the requested action because of a technical error.");
            return redirect()->back();
        }

        $docId = $request->docId;

        try
        {
            DB::beginTransaction();

            $profile = Profile::where(ProfileFields::UserId, Auth::id())->firstOrFail();
            $cert = $profile->{ProfileFields::Certifications};

            if (empty($cert))
                throw new ModelNotFoundException();

            for ($i = 0; $i < count($cert); $i++)
            {
                $row = $cert[$i];

                if ($row[DocProofFields::DocId] == $docId)
                {
                    $this->removeDocProof($row[DocProofFields::FullPath]);
                    unset($cert[$i]);
                    break;
                }
            }

            $profile->{ProfileFields::Certifications} = array_values($cert);
            $updated = $profile->save();

            DB::commit();

            if (!$updated)
                throw new Exception();

            session()->flash('profile_update_message', "An entry for certifications has been successfully removed.");
            return redirect()->back();
        }
        catch (ModelNotFoundException $ex)
        {
            DB::rollBack();

            session()->flash('profile_update_message', "Sorry, we're unable to find the record.");
            return redirect()->back();
        }
        catch (Exception $ex)
        {
            DB::rollBack();

            session()->flash('profile_update_message', "Sorry, we encountered an error while trying to remove the record. Please try again later.");
            return redirect()->back();
        }
    }

    public function formatCertificationProofList($certProof)
    {
        foreach ($certProof as $k => $obj)
        {
            $pdfPath = $obj[DocProofFields::FullPath];

            // Ensure the PDF path is sanitized and validated
            if (!Storage::exists($pdfPath))
                $certProof[$k]['docUrl'] = '-1'; // 'corrupted'

            // Generate a secure URL for the PDF file
            $certProof[$k]['docUrl'] = asset(Storage::url($pdfPath)) . '#toolbar=0';
            $certProof[$k]['docId'] = $obj[DocProofFields::DocId];

            unset(
                $certProof[$k][DocProofFields::FullPath],
                $certProof[$k][DocProofFields::FileUpload]
            );
        }

        return $certProof;
    }

    private function getCertificationValidationRules(Request $request, $mode = 'create')
    {
        $except = ['year-to'];
        $yearValidation = $this->getYearRangeValidationRules($request, 'cert-', $except);
        $pdfValidation  = $this->getPdfValidationRules();

        $certValidation = [
            "certification" => 'required|string|max:255',
            "description"   => 'required|string|max:255',
        ];

        $certErrMessages = [
            "certification.required"    => "Please enter the certification title you claim to hold.",
            "certification.string"      => "The certification title must be a valid string.",
            "certification.max"         => "The certification title cannot exceed 255 characters.",
            "description.required"      => "What was the certification about?",
            "description.string"        => "The certification description must be a valid string.",
            "description.max"           => "The certification description cannot exceed 255 characters."
        ];

        $messages = array_merge($yearValidation['messages'], $certErrMessages);
        $rules    = array_merge($yearValidation['rules'], $certValidation);

        switch ($mode)
        {
            // Always require pdf validation during create mode
            case 'create':
                $rules = array_merge($rules, $pdfValidation['rules']);
                $messages = array_merge($messages, $pdfValidation['messages']);
                break;

            // Pdf validation can be made optional if user decides not to change the pdf docs
            case 'update':

                // The request may not always include a file-upload input as it is dynamically
                // added by frontend code
                if ($request->has('file-upload')) // && $request->file('file-upload')->isValid())
                {
                    $rules = array_merge($rules, $pdfValidation['rules']);
                    $messages = array_merge($messages, $pdfValidation['messages']);
                }

                // We require the doc_id as this will help identify the target entry from json
                $rules = array_merge($rules, ['doc_id' => 'required|string|max:16']);
                $messages = array_merge($messages, ['doc_id.required' => 'Process Failed. Some of the required fields are missing.']);

                break;
        }

        $validator = Validator::make(
            $request->only(array_keys($rules)),
            $rules,
            $messages
        );

        if ($validator->fails())
        {
            $errors = $validator->errors();
            $errBag = [
                'last_action' => $mode
            ];

            foreach ($request->except('_token') as $k => $v)
            {
                $errBag['errors'][$k] = [
                    'oldValue' => $v,
                    'message' => $errors->first($k)
                ];
            }

            if ($request->has('file-upload'))
            {
                $errBag['errors']['file-upload'] = [
                    'message' => 'Due to security reasons, you may need to reupload the PDF document.'
                ];
            }

            $errBagJson = json_encode($errBag, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            session()->flash('validationErrors', $errBagJson);

            return redirect()
                    ->back()
                    ->withErrors($validator);
        }

        return $validator->validated();
    }
    //
    //==========================================
    //           A P I   C A L L S
    //==========================================
    //
    public function fetchCertification(Request $request)
    {
        $docId = $request->input('docId');
        $err404 = response()->json(["message" => "The record doesn't exist or can't be found"], 404);

        if (empty($docId))
            return $err404;

        try
        {
            $model = Profile::where(ProfileFields::UserId, Auth::id())->firstOrFail();
            $certDetails = $model->{ProfileFields::Certifications};

            if (empty($certDetails))
                return $err404;

            // Find the entry with matching document id
            $entry = null;

            foreach ($certDetails as $k => $obj)
            {
                if ($obj[DocProofFields::DocId] == $docId)
                {
                    $entry = $certDetails[$k];
                    break;
                }
            }

            if ($entry == null)
                return $err404;

            return response()->json([
                'docId'         => $entry[DocProofFields::DocId],
                'certification' => $entry[DocProofFields::CertTitle],
                'description'   => $entry[DocProofFields::CertDescr],
                'yearFrom'      => $entry[DocProofFields::YearFrom],
                'docProofUrl'   => asset(Storage::url($entry[DocProofFields::FullPath])),
                'docProofOrig'  => $entry[DocProofFields::OriginalFileName]
            ], 200);
        }
        catch (ModelNotFoundException $ex)
        {
            return response()->json(["message" => "We're unable to read the record. Please try again later."], 500);
        }
    }
}

