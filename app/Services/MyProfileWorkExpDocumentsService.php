<?php

namespace App\Services;

use App\Http\Utils\Constants;
use App\Http\Utils\HashSalts;
use App\Models\FieldNames\DocProofFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\Profile;
use Exception;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MyProfileWorkExpDocumentsService extends MyProfileDocumentsService
{
    public function addWorkExperience(Request $request)
    {
        $validation = $this->getWorkExpValidationRules($request, 'create');

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
            $docOps  = $this->addDocument($inputs['file-upload'], DocumentTypes::WorkExperience);

            if (empty($docOps['status']))
            {
                session()->flash('profile_update_message', "Sorry, we encountered an error while trying to upload the file. Please try again later.");
                return redirect()->back();
            }

            $uploadedFile = $docOps['uploadedFile'];

            $work   = $profile->{ProfileFields::Experience};
            $work[] = [
                DocProofFields::DocId               => Str::random(16),
                DocProofFields::YearFrom            => $inputs['work-year-from'],
                DocProofFields::YearTo              => $inputs['work-year-to'],
                DocProofFields::WorkCompany         => $inputs['company'],
                DocProofFields::WorkRole            => $inputs['role'],
                DocProofFields::FullPath            => $uploadedFile,
                DocProofFields::OriginalFileName    => $docOps['originalName']
            ];

            $profile->{ProfileFields::Experience} = array_values($work);
            $created = $profile->save();

            DB::commit();

            if (!$created)
                throw new Exception();

            session()->flash('profile_update_message', "A new work experience entry has been successfully added.");
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

    public function updateWorkExperience(Request $request)
    {
        $validation = $this->getWorkExpValidationRules($request, 'update');

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
            $workExp     = $profile->{ProfileFields::Experience};
            $targetEntry = [];
            $targetEntryKey = null;

            foreach ($workExp as $k => $obj)
            {
                if ($obj[DocProofFields::DocId] == $inputs['doc_id'])
                {
                    $targetEntry = $workExp[$k];

                    $targetEntry[DocProofFields::YearFrom]      = $inputs['work-year-from'];
                    $targetEntry[DocProofFields::YearTo]        = $inputs['work-year-to'];
                    $targetEntry[DocProofFields::WorkCompany]   = $inputs['company'];
                    $targetEntry[DocProofFields::WorkRole]      = $inputs['role'];

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
                    DocumentTypes::WorkExperience
                );

                if (!$docOps)
                {
                    session()->flash('profile_update_message', "Sorry, we encountered an error while trying to upload the file. Please try again later.");
                    return redirect()->back();
                }
            }

            $workExp[$targetEntryKey] = $targetEntry;
            $profile->{ProfileFields::Experience} = array_values($workExp);
            $updated = $profile->save();

            DB::commit();

            if (!$updated)
                throw new Exception();

            session()->flash('profile_update_message', "A work experience entry has been successfully updated.");
            return redirect()->route('myprofile.edit');
        }
        catch (Exception $ex)
        {
            DB::rollBack();

            if ($uploadedFile !== null)
                Storage::delete($uploadedFile);

            return response()->view('errors.500', [
                'message'   => 'Sorry, we encountered an error while trying to update the record. Please try again later.',
                'redirect'  => route('myprofile.edit')
            ], 500);
        }
    }

    public function removeWorkExperience(Request $request)
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
            $workExp = $profile->{ProfileFields::Experience};

            if (empty($workExp))
                throw new ModelNotFoundException();

            for ($i = 0; $i < count($workExp); $i++)
            {
                $row = $workExp[$i];

                if ($row[DocProofFields::DocId] == $docId)
                {
                    $this->removeDocProof($row[DocProofFields::FullPath]);
                    unset($workExp[$i]);
                    break;
                }
            }

            $profile->{ProfileFields::Experience} = array_values($workExp);
            $updated = $profile->save();

            DB::commit();

            if (!$updated)
                throw new Exception();

            session()->flash('profile_update_message', "An entry for work experience has been successfully removed.");
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

    public function formatWorkProofList($workProof)
    {
        foreach ($workProof as $k => $obj)
        {
            $pdfPath = $obj[DocProofFields::FullPath];

            // Ensure the PDF path is sanitized and validated
            if (!Storage::exists($pdfPath))
                $workProof[$k]['docUrl'] = '-1'; // 'corrupted'

            // Generate a secure URL for the PDF file
            $workProof[$k]['docUrl'] = asset(Storage::url($pdfPath)) . '#toolbar=0';
            $workProof[$k]['docId'] = $obj[DocProofFields::DocId];

            unset(
                $workProof[$k][DocProofFields::FullPath],
                $workProof[$k][DocProofFields::FileUpload]
            );
        }

        return $workProof;
    }

    private function getWorkExpValidationRules(Request $request, $mode = 'create')
    {
        $yearValidation = $this->getYearRangeValidationRules($request, 'work-');
        $pdfValidation  = $this->getPdfValidationRules();

        $workExpValidation = [
            "company" => 'required|string|max:255',
            "role"    => 'required|string|max:255',
        ];

        $workExpErrMessages = [
            "company.required"      => "Please enter the name of the company you work on.",
            "company.string"        => "The company name must be a valid string.",
            "company.max"           => "The company name cannot exceed 255 characters.",
            "role.required"         => "Please enter the role title.",
            "role.string"           => "The role title must be a valid string.",
            "role.max"              => "The role title cannot exceed 255 characters."
        ];

        $messages = array_merge($yearValidation['messages'], $workExpErrMessages);
        $rules    = array_merge($yearValidation['rules'], $workExpValidation);

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
    public function fetchWorkExp(Request $request)
    {
        $docId = $request->input('docId');
        $err404 = response()->json(["message" => "The record doesn't exist or can't be found"], 404);

        if (empty($docId))
            return $err404;

        try
        {
            $model = Profile::where(ProfileFields::UserId, Auth::id())->firstOrFail();
            $workExpDetails = $model->{ProfileFields::Experience};

            if (empty($workExpDetails))
                return $err404;

            // Find the entry with matching document id
            $entry = null;

            foreach ($workExpDetails as $k => $obj)
            {
                if ($obj[DocProofFields::DocId] == $docId)
                {
                    $entry = $workExpDetails[$k];
                    break;
                }
            }

            if ($entry == null)
                return $err404;

            return response()->json([
                'docId'         => $entry[DocProofFields::DocId],
                'company'       => $entry[DocProofFields::WorkCompany],
                'role'          => $entry[DocProofFields::WorkRole],
                'yearFrom'      => $entry[DocProofFields::YearFrom],
                'yearTo'        => $entry[DocProofFields::YearTo],
                // 'docProofName'  => basename($entry[DocProofFields::FullPath]),
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

