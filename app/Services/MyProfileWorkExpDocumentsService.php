<?php

namespace App\Services;

use App\Http\Utils\Constants;
use App\Http\Utils\HashSalts;
use App\Models\FieldNames\DocProofFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\Profile;
use Exception;
use Hashids\Hashids;
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
        $validation = $this->getEducationValidationRules($request);

        $validator = Validator::make(
            $request->only($validation['fields']),
            $validation['rules'],
            $validation['messages']
        );

        if ($validator->fails())
        {
            $errors = $validator->errors();

            // if (!$errors->has('file-upload'))
            // {
            //     // Make sure to add the error message for 'file-upload' only once
            //     // $errors->forget('file-upload');
            //     $errors->add('file-upload', 'Due to security reasons, you may need to reupload the PDF document.');
            // }

            session()->flash('workexp_action_error_type', 'add');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs             = $validator->validated();
        $hashids            = new Hashids(HashSalts::Files, 10);
        $hashedUserId       = $hashids->encode(Auth::id());
        $uploadedFile       = null;
        $originalFileName   = null;

        try
        {
            DB::beginTransaction();

            $profile        = Profile::where(ProfileFields::UserId, Auth::id())->firstOrFail();
            $fileToUpload   = $inputs['file-upload'];
            $fileUploadPath = Constants::DocPathWorkExp . $hashedUserId;

            // Ensure $obj['file'] is an instance of UploadedFile and not treated as an array
            if ($fileToUpload instanceof \Illuminate\Http\UploadedFile)
            {
                // Generate a unique file name
                $fileName = Str::uuid() . '.pdf';
                $uploadedFile = $fileToUpload->storeAs($fileUploadPath, $fileName);

                // Get the original file name
                $originalFileName = $fileToUpload->getClientOriginalName();
            }
            else
            {
                session()->flash('profile_update_message', "Sorry, we encountered an error while trying to upload the file. Please try again later.");
                return redirect()->back();
            }

            $work   = $profile->{ProfileFields::Experience};
            $work[] = [
                DocProofFields::DocId               => Str::random(16),
                DocProofFields::YearFrom            => $inputs['year-from'],
                DocProofFields::YearTo              => $inputs['year-to'],
                DocProofFields::WorkCompany         => $inputs['company'],
                DocProofFields::WorkRole            => $inputs['role'],
                DocProofFields::FullPath            => $uploadedFile,
                DocProofFields::OriginalFileName    => $originalFileName
            ];

            $profile->{ProfileFields::Education} = array_values($work);
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

            Storage::delete($uploadedFile);

            session()->flash('workexp_action_error_type', 'add');
            session()->flash('profile_update_message', "Sorry, we encountered an error while trying to create the record. Please try again later.");
            return redirect()->back();
        }
    }

    public function updateWorkExperience(Request $request)
    {

    }

    public function removeWorkExperience(Request $request)
    {

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

    private function getEducationValidationRules(Request $request, $mode = 'create')
    {
        $yearValidation = $this->getYearRangeValidationRules($request);
        $pdfValidation  = $this->getPdfValidationRules();

        $educationValidation = [
            "institution"           => 'required|string|max:255',
            "degree"                => 'required|string|max:255',
        ];

        $educationErrMessages = [
            "institution.required"  => "Please enter the name of the educational institution.",
            "institution.string"    => "The institution name must be a valid string.",
            "institution.max"       => "The institution name cannot exceed 255 characters.",
            "degree.required"       => "Please enter the degree title.",
            "degree.string"         => "The degree title must be a valid string.",
            "degree.max"            => "The degree title cannot exceed 255 characters."
        ];

        $messages = array_merge($yearValidation['messages'], $educationErrMessages);
        $rules    = array_merge($yearValidation['rules'], $educationValidation);

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

        return [
            'rules'     => $rules,
            'messages'  => $messages,
            'fields'    => array_keys($rules)
        ];
    }
}

