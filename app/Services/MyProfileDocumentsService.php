<?php

namespace App\Services;

use Illuminate\Http\Request;


class MyProfileDocumentsService
{
    //
    //==========================================
    //     V A L I D A T I O N  R U L E S
    //==========================================
    //
    protected function getYearRangeValidationRules(Request $request)
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

    protected function getPdfValidationRules() : array
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
}
