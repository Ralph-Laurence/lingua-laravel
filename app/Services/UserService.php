<?php

namespace App\Services;

use App\Http\Utils\HashSalts;
use App\Models\FieldNames\ProfileFields;
use App\Models\PendingRegistration;
use App\Models\Profile;
use App\Models\User;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use HTMLPurifier_Config;
use HTMLPurifier;

class UserService
{
    public function buildRegistrationData($userId, $registrationData)
    {
        // Update the profile data
        $profileData = [
            ProfileFields::UserId       => $userId,
            ProfileFields::About        => $registrationData[ProfileFields::About],
            ProfileFields::Bio          => $registrationData[ProfileFields::Bio],
            ProfileFields::Fluency      => $registrationData[ProfileFields::Fluency],
            ProfileFields::Education    => $registrationData[ProfileFields::Education],
        ];

        // Work Experience
        if (array_key_exists(ProfileFields::Experience, $registrationData))
            $profileData[ProfileFields::Experience] = $registrationData[ProfileFields::Experience];

        // Skills
        if (array_key_exists(ProfileFields::Skills, $registrationData))
            $profileData[ProfileFields::Skills] = $registrationData[ProfileFields::Skills];

        // Certifications
        if (array_key_exists(ProfileFields::Certifications, $registrationData))
            $profileData[ProfileFields::Certifications] = $registrationData[ProfileFields::Certifications];

        return $profileData;
    }

    public function updateProfile($userId, $upgradeData)
    {
        try
        {
            // Find the profile account first to make sure it exists
            //$profile = Profile::findOrFail($userId);
            $profile = Profile::where(ProfileFields::UserId, $userId)->firstOrFail();

            $profileData = $this->buildRegistrationData($userId, $upgradeData);

            $profile->update($profileData);
            return $profileData;

            // // Update the profile data
            // $data = [
            //     ProfileFields::UserId       => $userId,
            //     ProfileFields::About        => $upgradeData[ProfileFields::About],
            //     ProfileFields::Bio          => $upgradeData[ProfileFields::Bio],
            //     ProfileFields::Fluency      => $upgradeData[ProfileFields::Fluency],
            //     ProfileFields::Education    => $upgradeData[ProfileFields::Education],
            // ];

            // // Work Experience
            // if (array_key_exists(ProfileFields::Experience, $upgradeData))
            //     $data[ProfileFields::Experience] = $upgradeData[ProfileFields::Experience];

            // // Skills
            // if (array_key_exists(ProfileFields::Skills, $upgradeData))
            //     $data[ProfileFields::Skills] = $upgradeData[ProfileFields::Skills];

            // // Certifications
            // if (array_key_exists(ProfileFields::Certifications, $upgradeData))
            //     $data[ProfileFields::Certifications] = $upgradeData[ProfileFields::Certifications];

            // $profile->update($data);
            // return $profile;
        }
        catch (ModelNotFoundException $e)
        {
            return null;
        }
    }

    /*
    Construct the data that will be saved into the Profiles table,
    with data coming from the validated inputs
    */
    public function buildProfilePayloadData(Request $request)
    {
        $rules     = $this->buildValidationRules($request);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $validator->validated();

        // Group together education data
            // Initialize the education array
        $educ = [];
        $work = [];
        $cert = [];

        $hashids      = new Hashids(HashSalts::Files, 10);
        $userId       = Auth::user()->id;
        $hashedUserId = $hashids->encode($userId);

        $uploadQueue = [];

        // Iterate over the validated data and extract education-related fields
        foreach ($inputs as $key => $value)
        {
            // Match keys that start with 'education-year-from-'
            if (preg_match('/^education-year-from-(\d+)$/', $key, $matches))
            {
                $index = $matches[1];

                // Store the uploaded file ...
                $file     = $inputs["education-file-upload-$index"];
                $fileName = Str::uuid() . '.pdf';
                // $filePath = $file->storeAs("public/documentary_proofs/education/$hashedUserId", $fileName);
                $filePath = "public/documentary_proofs/education/$hashedUserId";

                $uploadQueue['education'][] = [
                    'file'      => $file,
                    'filepath'  => $filePath,
                    'filename'  => $fileName
                ];

                $educ[] = [
                    'from'          => $value,
                    'to'            => $inputs["education-year-to-$index"],
                    'institution'   => $inputs["education-institution-$index"],
                    'degree'        => $inputs["education-degree-$index"],
                    'file_upload'   => $filePath,
                    'full_path'     => "$filePath/$fileName",
                ];
            }

            // Match keys that start with 'work-year-from-'
            if (preg_match('/^work-year-from-(\d+)$/', $key, $matches))
            {
                $index = $matches[1];

                // Store the uploaded file ...
                $file     = $inputs["work-file-upload-$index"];
                $fileName = Str::uuid() . '.pdf';
                // $filePath = $file->storeAs("public/documentary_proofs/work_experience/$hashedUserId", $fileName);
                $filePath = "public/documentary_proofs/work_experience/$hashedUserId";

                $uploadQueue['work_experience'][] = [
                    'file'      => $file,
                    'filepath'  => $filePath,
                    'filename'  => $fileName
                ];

                $work[] = [
                    'from'          => $value,
                    'to'            => $inputs["work-year-to-$index"],
                    'company'       => $inputs["work-company-$index"],
                    'role'          => $inputs["work-role-$index"],
                    'file_upload'   => $filePath,
                    'full_path'     => "$filePath/$fileName",
                ];
            }

            // Match keys that start with 'work-year-from-'
            if (preg_match('/^certification-year-from-(\d+)$/', $key, $matches))
            {
                $index = $matches[1];

                // Store the uploaded file ...
                $file     = $inputs["certification-file-upload-$index"];
                $fileName = Str::uuid() . '.pdf';
                //$filePath = $file->storeAs("public/documentary_proofs/certification/$hashedUserId", $fileName);
                $filePath = "public/documentary_proofs/certification/$hashedUserId";

                $uploadQueue['certification'][] = [
                    'file'      => $file,
                    'filepath'  => $filePath,
                    'filename'  => $fileName
                ];

                $cert[] = [
                    'from'          => $value,
                    'title'         => $inputs["certification-title-$index"],
                    'description'   => $inputs["certification-description-$index"],
                    'file_upload'   => $filePath,
                    'full_path'     => "$filePath/$fileName",
                ];
            }
        }

        $skills = [];

        if (!empty($inputs['skills-arr']))
        {
            $skills = json_decode($inputs['skills-arr'], true);
            $skills = array_column($skills, 'value');
        }

        // Sanitize the HTML content sent by QuillJS
        $config     = HTMLPurifier_Config::createDefault();
        $purifier   = new HTMLPurifier($config);
        $about      = $purifier->purify($inputs['about']);

        $payloadData = [
            "model" => [
                ProfileFields::UserId           => $userId,
                ProfileFields::Bio              => $inputs['bio'],
                ProfileFields::About            => $about,
                ProfileFields::Fluency          => $inputs['fluency-level'],
                ProfileFields::Education        => $educ,
                ProfileFields::Certifications   => $cert,
                ProfileFields::Experience       => $work,
                ProfileFields::Skills           => $skills
            ],
            "modelx" => $inputs,
            "upload" => $uploadQueue
        ];

        return $payloadData;
    }

    private function getTotalEntriesPerCategory(Request $request)
    {
        $educIndices = [0]; // Start with 0 instead of -1
        $workIndices = [0]; // Same for work entries
        $certIndices = [0]; // Same for cert entries

        foreach ($request->all() as $key => $value)
        {
            // Check for education fields with numeric suffix
            if (preg_match('/education-.*-(\d+)$/', $key, $matches))
                $educIndices[] = (int)$matches[1];

            // Check for work fields with numeric suffix
            else if ($request->has('work-year-from-0') && preg_match('/work-.*-(\d+)$/', $key, $matches))
                $workIndices[] = (int)$matches[1];

            // Check for certification fields with numeric suffix
            else if ($request->has('certification-year-from-0') && preg_match('/certification-.*-(\d+)$/', $key, $matches))
                $certIndices[] = (int)$matches[1];
        }

        return [
            'educ' => max($educIndices),
            'work' => max($workIndices),
            'cert' => max($certIndices)
        ];
    }

    private function buildValidationRules(Request $request, $mergeRules = [])
    {
        $currentYear = date('Y');

        //------------------------------------------------
        // STEP 1 : GET THE TOTAL ENTRIES PER CATEGORY
        //------------------------------------------------

        $totalEntries = $this->getTotalEntriesPerCategory($request);

        $maxEducEntries = $totalEntries['educ'];
        $maxWorkEntries = $totalEntries['work'];
        $maxCertEntries = $totalEntries['cert'];

        //------------------------------------------------
        // STEP 2 : GENERATE RULES FOR EACH DYNAMIC ENTRY
        //------------------------------------------------
        $rules = [
            'bio'               => 'required|string|max:180',
            'about'             => 'required|string|max:2000',
            'fluency-level'     => 'required|integer',
            'skills-arr' => [
                'nullable',
                'json',
                function ($attribute, $value, $fail)
                {
                    $skills = json_decode($value, true);

                    if (is_array($skills) && !empty($skills))
                    {
                        foreach ($skills as $skill)
                        {
                            // Skill value must be numeric
                            if (!isset($skill['value']) || !is_numeric($skill['value']))
                                $fail('One of the selected skills is invalid.');
                        }
                    }
                },
            ],
        ];

        if (!empty($mergeRules))
        {
            $rules = array_merge($rules, $mergeRules);
        }

        // Generate rules only for suffixed fields (starting from -0)
        for ($i = 0; $i <= $maxEducEntries; $i++)
        {
            $suffix = "-{$i}";

            $rules = array_merge($rules, [
                "education-year-from{$suffix}" => "required|numeric|min:1980|max:$currentYear",
                "education-year-to{$suffix}" => [
                    'required',
                    'numeric',
                    'min:1980',
                    "max:$currentYear",
                    function ($attribute, $value, $fail) use ($suffix, $request)
                    {
                        $fromYear = $request->input("education-year-from{$suffix}");

                        if ($fromYear && $value < $fromYear)
                            $fail('The end year must be greater than or equal to the start year.');
                    },
                ],
                "education-institution{$suffix}" => 'required|string|max:255',
                "education-degree{$suffix}" => 'required|string|max:255',

                "education-file-upload{$suffix}" => [
                    'required',
                    'file',
                    'mimes:pdf',
                    'max:5120', // 5MB in kilobytes
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            $mimeType = $value->getMimeType();
                            if ($mimeType !== 'application/pdf') {
                                $fail('The file must be a PDF document.');
                            }
                        }
                    }
                ]
            ]);
        }

        // Generate rules only for suffixed fields (starting from -0)
        if ($request->has('work-year-from-0'))
        {
            for ($i = 0; $i <= $maxWorkEntries; $i++)
            {
                $suffix = "-{$i}";

                $rules = array_merge($rules, [
                    "work-year-from{$suffix}" => "required|numeric|min:1980|max:$currentYear",
                    "work-year-to{$suffix}" => [
                        'required',
                        'numeric',
                        'min:1980',
                        "max:$currentYear",
                        function ($attribute, $value, $fail) use ($suffix, $request)
                        {
                            $fromYear = $request->input("work-year-from{$suffix}");

                            if ($fromYear && $value < $fromYear)
                                $fail('The end year must be greater than or equal to the start year.');
                        },
                    ],
                    "work-company{$suffix}" => 'required|string|max:255',
                    "work-role{$suffix}" => 'required|string|max:255',

                    "work-file-upload{$suffix}" => [
                        'required',
                        'file',
                        'mimes:pdf',
                        'max:5120', // 5MB in kilobytes
                        function ($attribute, $value, $fail)
                        {
                            if ($value)
                            {
                                $mimeType = $value->getMimeType();

                                if ($mimeType !== 'application/pdf')
                                    $fail('The file must be a PDF document.');
                            }
                        }
                    ]
                ]);
            }
        }

        // Generate rules only for suffixed fields (starting from -0)
        if ($request->has('certification-year-from-0'))
        {
            for ($i = 0; $i <= $maxCertEntries; $i++)
            {
                $suffix = "-{$i}";

                $rules = array_merge($rules, [
                    "certification-year-from{$suffix}"   => "required|numeric|min:1980|max:$currentYear",
                    "certification-title{$suffix}"       => 'required|string|max:255',
                    "certification-description{$suffix}" => 'required|string|max:255',
                    "certification-file-upload{$suffix}" => [
                        'required',
                        'file',
                        'mimes:pdf',
                        'max:5120', // 5MB in kilobytes
                        function ($attribute, $value, $fail) {
                            if ($value) {
                                $mimeType = $value->getMimeType();
                                if ($mimeType !== 'application/pdf') {
                                    $fail('The file must be a PDF document.');
                                }
                            }
                        }
                    ]
                ]);
            }
        }

        return $rules;
    }
}
