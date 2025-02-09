<?php

namespace App\Services;

use App\Services\MyProfileDocumentsService;
use App\Models\FieldNames\DocProofFields;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\FieldNames\ProfileFields;
use App\Models\Profile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MyProfileEducationDocumentsService extends MyProfileDocumentsService
{
    public function addEducation(Request $request)
    {
        $validation = $this->getEducationValidationRules($request, 'create');

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
            $docOps  = $this->addDocument($inputs['file-upload'], DocumentTypes::Education);

            if (empty($docOps['status']))
            {
                session()->flash('profile_update_message', "Sorry, we encountered an error while trying to upload the file. Please try again later.");
                return redirect()->back();
            }

            $uploadedFile = $docOps['uploadedFile'];

            $educ   = $profile->{ProfileFields::Education};
            $educ[] = [
                DocProofFields::DocId               => Str::random(16),
                DocProofFields::YearFrom            => $inputs['educ-year-from'],
                DocProofFields::YearTo              => $inputs['educ-year-to'],
                DocProofFields::EducInstitution     => $inputs['institution'],
                DocProofFields::EducDegree          => $inputs['degree'],
                DocProofFields::FullPath            => $uploadedFile,
                DocProofFields::OriginalFileName    => $docOps['originalName']
            ];

            $profile->{ProfileFields::Education} = array_values($educ);
            $created = $profile->save();

            DB::commit();

            if (!$created)
                throw new Exception();

            session()->flash('profile_update_message', "A new educational attainment entry has been successfully added.");
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

    public function updateEducation(Request $request)
    {
        $validation = $this->getEducationValidationRules($request, 'update');

        // If we got any errors, we should go back
        if ($validation instanceof \Illuminate\Http\RedirectResponse)
            return $validation;

        // Otherwise, we'll retrieve the validated inputs
        $inputs = $validation;
        $uploadedFile = null;

        try
        {
            DB::beginTransaction();

            $profile        = Profile::where(ProfileFields::UserId, Auth::id())->firstOrFail();
            $educ           = $profile->{ProfileFields::Education};
            $targetEntry    = [];
            $targetEntryKey = null;

            foreach ($educ as $k => $obj)
            {
                if ($obj[DocProofFields::DocId] == $inputs['doc_id'])
                {
                    $targetEntry = $educ[$k];

                    $targetEntry[DocProofFields::YearFrom]          = $inputs['educ-year-from'];
                    $targetEntry[DocProofFields::YearTo]            = $inputs['educ-year-to'];
                    $targetEntry[DocProofFields::EducInstitution]   = $inputs['institution'];
                    $targetEntry[DocProofFields::EducDegree]        = $inputs['degree'];

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
                    DocumentTypes::Education
                );

                if (!$docOps)
                {
                    session()->flash('profile_update_message', "Sorry, we encountered an error while trying to upload the file. Please try again later.");
                    return redirect()->back();
                }
            }

            $educ[$targetEntryKey] = $targetEntry;
            $profile->{ProfileFields::Education} = array_values($educ);
            $updated = $profile->save();

            DB::commit();

            if (!$updated)
                throw new Exception();

            session()->flash('profile_update_message', "An educational attainment entry has been successfully updated.");
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

    public function removeEducation(Request $request)
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
            $educ = $profile->{ProfileFields::Education};

            if (empty($educ))
                throw new ModelNotFoundException();

            for ($i = 0; $i < count($educ); $i++)
            {
                $row = $educ[$i];

                if ($row[DocProofFields::DocId] == $docId)
                {
                    $this->removeDocProof($row[DocProofFields::FullPath]);
                    unset($educ[$i]);
                    break;
                }
            }

            $profile->{ProfileFields::Education} = array_values($educ);
            $updated = $profile->save();

            DB::commit();

            if (!$updated)
                throw new Exception();

            session()->flash('profile_update_message', "An educational attainment entry has been successfully removed.");
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

    private function getEducationValidationRules(Request $request, $mode = 'create')
    {
        $yearValidation = $this->getYearRangeValidationRules($request, 'educ-');
        $pdfValidation  = $this->getPdfValidationRules();

        $educValidation = [
            "institution" => 'required|string|max:255',
            "degree"      => 'required|string|max:255',
        ];

        $educErrMessages = [
            "institution.required"  => "Please enter the name of the educational institution you studied at.",
            "institution.string"    => "The institution name must be a valid string.",
            "institution.max"       => "The institution name cannot exceed 255 characters.",
            "degree.required"       => "Please enter the degree title.",
            "degree.string"         => "The degree title must be a valid string.",
            "degree.max"            => "The degree title cannot exceed 255 characters."
        ];

        $messages = array_merge($yearValidation['messages'], $educErrMessages);
        $rules    = array_merge($yearValidation['rules'], $educValidation);

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

    public function formatEducationProofList($educationProof)
    {
        foreach ($educationProof as $k => $obj)
        {
            $pdfPath = $obj[DocProofFields::FullPath];

            // Ensure the PDF path is sanitized and validated
            if (!Storage::exists($pdfPath))
                $educationProof[$k]['docUrl'] = '-1'; // 'corrupted'

            // Generate a secure URL for the PDF file
            $educationProof[$k]['docUrl'] = asset(Storage::url($pdfPath)) . '#toolbar=0';
            $educationProof[$k]['docId'] = $obj[DocProofFields::DocId];

            unset(
                $educationProof[$k][DocProofFields::FullPath],
                $educationProof[$k][DocProofFields::FileUpload]
            );
        }

        return $educationProof;
    }
    //
    //==========================================
    //           A P I   C A L L S
    //==========================================
    //
    public function fetchEducationDetails(Request $request)
    {
        $docId = $request->input('docId');
        $err404 = response()->json(["message" => "The record doesn't exist or can't be found"], 404);

        if (empty($docId))
            return $err404;

        try
        {
            $model = Profile::where(ProfileFields::UserId, Auth::id())->firstOrFail();
            $educDetails = $model->{ProfileFields::Education};

            if (empty($educDetails))
                return $err404;

            // Find the entry with matching document id
            $entry = null;

            foreach ($educDetails as $k => $obj)
            {
                if ($obj[DocProofFields::DocId] == $docId)
                {
                    $entry = $educDetails[$k];
                    break;
                }
            }

            if ($entry == null)
                return $err404;

            return response()->json([
                'docId'         => $entry[DocProofFields::DocId],
                'institution'   => $entry[DocProofFields::EducInstitution],
                'degree'        => $entry[DocProofFields::EducDegree],
                'yearFrom'      => $entry[DocProofFields::YearFrom],
                'yearTo'        => $entry[DocProofFields::YearTo],
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
