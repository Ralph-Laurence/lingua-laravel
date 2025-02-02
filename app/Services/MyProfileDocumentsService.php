<?php

namespace App\Services;

use App\Http\Utils\Constants;
use App\Http\Utils\HashSalts;
use App\Models\FieldNames\DocProofFields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\FieldNames\ProfileFields;
use App\Models\Profile;
use Exception;
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery\Undefined;

class MyProfileDocumentsService
{
    public function updateEducation(Request $request)
    {
        $validation = $this->getEducationValidationRules($request, 'update');

        $validator = Validator::make(
            $request->only($validation['fields']),
            $validation['rules'],
            $validation['messages']
        );

        if ($validator->fails())
        {
            $errors = $validator->errors();

            if ($request->has('file-upload') && !$errors->has('file-upload'))
            {
                // Make sure to add the error message for 'file-upload' only once
                // $errors->forget('file-upload');
                $errors->add('file-upload', 'Due to security reasons, you may need to reupload the PDF document.');
            }

            foreach ($errors->all() as $e)
            {
                error_log($e);
            }

            session()->flash('action_error_type', 'edit');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $validator->validated();

        try
        {
            DB::beginTransaction();

            $profile     = Profile::where(ProfileFields::UserId, Auth::id())->firstOrFail();
            $education   = $profile->{ProfileFields::Education};
            $targetEntry = [];
            $targetEntryKey = null;

            foreach ($education as $k => $obj)
            {
                if ($obj[DocProofFields::DocId] == $inputs['doc_id'])
                {
                    $targetEntry = $education[$k];

                    $targetEntry[DocProofFields::YearFrom]            = $inputs['year-from'];
                    $targetEntry[DocProofFields::YearTo]              = $inputs['year-to'];
                    $targetEntry[DocProofFields::EducInstitution]     = $inputs['institution'];
                    $targetEntry[DocProofFields::EducDegree]          = $inputs['degree'];

                    $targetEntryKey = $k;
                    break;
                }
            }

            if (empty($targetEntry) || $targetEntryKey == null)
            {
                session()->flash('action_error_type', 'edit');
                session()->flash('profile_update_message', "Sorry, we're unable to update the entry. Please try again later.");
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if (array_key_exists('file-upload', $inputs))
            {
                $hashids        = new Hashids(HashSalts::Files, 10);
                $hashedUserId   = $hashids->encode(Auth::id());
                $fileToUpload   = $inputs['file-upload'];
                $fileUploadPath = Constants::DocPathEducation . $hashedUserId;

                // Cache the filename of the currently uploaded file
                $lastStoredFile = $targetEntry[DocProofFields::FullPath];

                // Ensure $obj['file'] is an instance of UploadedFile and not treated as an array
                if ($fileToUpload instanceof \Illuminate\Http\UploadedFile)
                {
                    // Generate a unique file name
                    $fileName = Str::uuid() . '.pdf';
                    $uploadedFile = $fileToUpload->storeAs($fileUploadPath, $fileName);
                    $targetEntry[DocProofFields::FullPath] = $uploadedFile;

                    // Get and store the original file name
                    $targetEntry[DocProofFields::OriginalFileName] = $fileToUpload->getClientOriginalName();

                    // After upload, remove the old uploaded file
                    Storage::delete($lastStoredFile);
                }
                else
                {
                    session()->flash('profile_update_message', "Sorry, we encountered an error while trying to upload the file. Please try again later.");
                    return redirect()->back();
                }
            }


            // $education[DocProofFields::FullPath]            = $uploadedFile;
            // $education[DocProofFields::OriginalFileName]    = $originalFileNam;

            $education[$targetEntryKey] = $targetEntry;
            $profile->{ProfileFields::Education} = array_values($education);
            $created = $profile->save();

            DB::commit();

            if (!$created)
                throw new Exception();

            session()->flash('profile_update_message', "An educational attainment entry has been successfully updated.");
            return redirect()->back();
        }
        catch (Exception $ex)
        {
            DB::rollBack();

            // Storage::delete($uploadedFile);
            error_log($ex->getMessage());
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
            $education = $profile->{ProfileFields::Education};

            if (empty($education))
                throw new ModelNotFoundException();

            /**
             * [{"doc_id":"Vbt4R3QQXOOeSNAN","from":"2012", "to":"2016","degree":"Bachelors Degree in PolSci","institution":"Pangasinan State University","file_upload":"public\/documentary_proofs\/education\/5e9Q15Q4Ev","full_path":"public\/documentary_proofs\/education\/5e9Q15Q4Ev\/6946f41d-c7dc-44f4-bb02-d85821628b2e.pdf"},{"doc_id":"B37W0pA4dbITvLYt","from":"2017", "to":"2021","degree":"Masters Degree in Mass Comm","institution":"University of Oxford","file_upload":"public\/documentary_proofs\/education\/5e9Q15Q4Ev","full_path":"public\/documentary_proofs\/education\/5e9Q15Q4Ev\/6946f41d-c7dc-44f4-bb02-d85821628b2e.pdf"}]
             */
            for ($i = 0; $i < count($education); $i++)
            {
                $row = $education[$i];

                if ($row[DocProofFields::DocId] == $docId)
                {
                    Storage::delete($row[DocProofFields::FullPath]);
                    unset($education[$i]);
                    break;
                }
            }

            $profile->{ProfileFields::Education} = array_values($education);
            $updated = $profile->save();

            DB::commit();

            if (!$updated)
                throw new Exception();

            session()->flash('profile_update_message', "An entry for educational attainment has been successfully removed.");
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

    public function addEducation(Request $request)
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

            if (!$errors->has('file-upload'))
            {
                // Make sure to add the error message for 'file-upload' only once
                // $errors->forget('file-upload');
                $errors->add('file-upload', 'Due to security reasons, you may need to reupload the PDF document.');
            }

            session()->flash('action_error_type', 'add');
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
            $fileUploadPath = Constants::DocPathEducation . $hashedUserId;

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

            $education   = $profile->{ProfileFields::Education};
            $education[] = [
                DocProofFields::DocId               => Str::random(16),
                DocProofFields::YearFrom            => $inputs['year-from'],
                DocProofFields::YearTo              => $inputs['year-to'],
                DocProofFields::EducInstitution     => $inputs['institution'],
                DocProofFields::EducDegree          => $inputs['degree'],
                DocProofFields::FullPath            => $uploadedFile,
                DocProofFields::OriginalFileName    => $originalFileName
            ];

            $profile->{ProfileFields::Education} = array_values($education);
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

            Storage::delete($uploadedFile);

            session()->flash('action_error_type', 'add');
            session()->flash('profile_update_message', "Sorry, we encountered an error while trying to create the record. Please try again later.");
            return redirect()->back();
        }
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
    //
    //==========================================
    //     V A L I D A T I O N  R U L E S
    //==========================================
    //
    private function getYearRangeValidationRules(Request $request)
    {
        $currentYear = date('Y');
        $rules = [
            "year-from" => "required|numeric|min:1980|max:$currentYear",
            "year-to" => [
                'required',
                'numeric',
                'min:1980',
                "max:$currentYear",
                    function ($attribute, $value, $fail) use ($request)
                    {
                        $fromYear = $request->input("year-from");

                        if ($fromYear && $value < $fromYear)
                            $fail('The end year must be greater than or equal to the start year.');
                    },
                ]
        ];
        $messages = [
            "year-from.required"    => "Please select a start year.",
            "year-from.numeric"     => "The start year must be a number.",
            "year-from.min"         => "The start year cannot be before 1980.",
            "year-from.max"         => "The start year cannot be after the current year.",
            "year-to.required"      => "Please select an end year.",
            "year-to.numeric"       => "The end year must be a number.",
            "year-to.min"           => "The end year cannot be before 1980.",
            "year-to.max"           => "The end year cannot be after the current year.",
            "year-to.custom"        => "The end year must be greater than or equal to the start year.",
        ];

        return [
            'rules' => $rules,
            'messages' => $messages
        ];
    }

    private function getPdfValidationRules() : array
    {
        $messages = [
            "file-upload.required" => "Please upload a supporting document you claim to hold.",
            "file-upload.file"     => "The file must be a valid PDF document.",
            "file-upload.mimes"    => "The file must be a PDF document.",
            "file-upload.max"      => "The file size cannot exceed 5MB.",
            "file-upload.custom"   => "The file must be a PDF document."
        ];

        $rules = [
            'file-upload' => [
                'required',
                'file',
                'mimes:pdf',
                'max:5120', // 5MB in kilobytes
                function ($attribute, $value, $fail) use ($messages)
                {
                    if (is_null($value) || !$value->isValid()) {
                        $fail($messages['file-upload.required']);
                        return;
                    }

                    if ($value && $value->getMimeType() !== 'application/pdf') {
                        $fail('The file must be a PDF document.');
                    }
                }
            ]
        ];

        return [
            'rules' => $rules,
            'messages' => $messages
        ];
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
            $educationDetails = $model->{ProfileFields::Education};

            if (empty($educationDetails))
                return $err404;

            // Find the entry with matching document id
            $entry = null;

            foreach ($educationDetails as $k => $obj)
            {
                if ($obj[DocProofFields::DocId] == $docId)
                {
                    $entry = $educationDetails[$k];
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
                'docProofName'  => basename($entry[DocProofFields::FullPath]),
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
