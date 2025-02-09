<?php

namespace App\Services;

use App\Http\Utils\Constants;
use App\Http\Utils\HashSalts;
use App\Http\Utils\Helper;
use App\Models\FieldNames\DocProofFields;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentTypes
{
    const Education = 1;
    const WorkExperience = 2;
    const Certifications = 3;
}

class MyProfileDocumentsService
{
    private const DocumentPaths = [
        DocumentTypes::Education => Constants::DocPathEducation,
        DocumentTypes::WorkExperience => Constants::DocPathWorkExp,
        DocumentTypes::Certifications => Constants::DocPathCertification
    ];

    /**
     * Delete the pdf file along with its parent folder
     */
    protected function removeDocProof($pdfPath)
    {
        // Extract the parent folder
        $parentFolder = dirname($pdfPath);

        // Delete the PDF file
        Storage::delete($pdfPath);

        // Delete the parent folder
        // Storage::deleteDirectory($parentFolder);
    }

    /**
     * This function will replace the target documentary proof
     */
    protected function replaceDocument(&$targetEntry, $fileToUpload, $documentType)
    {
        $hashids        = new Hashids(HashSalts::Files, 10);
        $hashedUserId   = $hashids->encode(Auth::id());
        $fileUploadPath = self::DocumentPaths[$documentType] . $hashedUserId;

        // Cache the filename of the currently uploaded file
        $lastStoredFile = $targetEntry[DocProofFields::FullPath];
        $successResult  = false;

        // Ensure $obj['file'] is an instance of UploadedFile and not treated as an array
        try
        {
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

            $successResult = true;
        }
        catch (\Throwable $th)
        {
            $successResult = false;
        }

        return $successResult;
    }
    /**
     * This function will add a documentary proof
     */
    protected function addDocument($fileToUpload, $documentType)
    {
        $hashids        = new Hashids(HashSalts::Files, 10);
        $hashedUserId   = $hashids->encode(Auth::id());
        $fileUploadPath = self::DocumentPaths[$documentType] . $hashedUserId;

        $operation = [
            'status' => 0,
            'uploadedFile' => '',
            'originalName' => '',
        ];

        try
        {
            if ($fileToUpload instanceof \Illuminate\Http\UploadedFile)
            {
                // Generate a unique file name
                $fileName = Str::uuid() . '.pdf';
                $operation['uploadedFile'] = $fileToUpload->storeAs($fileUploadPath, $fileName);

                // Get and store the original file name
                $operation['originalName'] = $fileToUpload->getClientOriginalName();
            }

            $operation['status'] = 1;
        }
        catch (\Throwable $th)
        {
            $operation['status'] = 0;
        }

        return $operation;
    }
    //
    //==========================================
    //     V A L I D A T I O N  R U L E S
    //==========================================
    //
    protected function getYearRangeValidationRules(Request $request, $prefix = '')
    {
        $currentYear = date('Y');
        $rules = [
            $prefix."year-from" => "required|numeric|min:1980|max:$currentYear",
            $prefix."year-to" => [
                'required',
                'numeric',
                'min:1980',
                "max:$currentYear",
                    function ($attribute, $value, $fail) use ($request, $prefix)
                    {
                        $fromYear = $request->input($prefix."year-from");

                        if ($fromYear && $value < $fromYear)
                            $fail('The end year must be greater than or equal to the start year.');
                    },
                ]
        ];
        $messages = Helper::prefixArrayKeys($prefix, [
            "year-from.required"    => "Please select a start year.",
            "year-from.numeric"     => "The start year must be a number.",
            "year-from.min"         => "The start year cannot be before 1980.",
            "year-from.max"         => "The start year cannot be after the current year.",
            "year-to.required"      => "Please select an end year.",
            "year-to.numeric"       => "The end year must be a number.",
            "year-to.min"           => "The end year cannot be before 1980.",
            "year-to.max"           => "The end year cannot be after the current year.",
            "year-to.custom"        => "The end year must be greater than or equal to the start year.",
        ]);

        return [
            'rules' => $rules,
            'messages' => $messages
        ];
    }

    protected function getPdfValidationRules($prefix = '') : array
    {
        $messages = Helper::prefixArrayKeys($prefix, [
            "file-upload.required" => "Please upload a supporting document you claim to hold.",
            "file-upload.file"     => "The file must be a valid PDF document.",
            "file-upload.mimes"    => "The file must be a PDF document.",
            "file-upload.max"      => "The file size cannot exceed 5MB.",
            "file-upload.custom"   => "The file must be a PDF document."
        ]);

        $rules = [
            $prefix.'file-upload' => [
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
}
