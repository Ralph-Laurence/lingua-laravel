<?php

namespace App\Services;

use App\Http\Utils\FluencyLevels;
use App\Http\Utils\HashSalts;
use App\Mail\RegistrationApprovedMail;
use App\Mail\RegistrationDeclinedMail;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use App\Models\PendingRegistration;
use App\Models\Profile;
use App\Models\User;
use Exception;
use Hashids\Hashids;
use HTMLPurifier_Config;
use HTMLPurifier;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class RegistrationService
{
    const STATUS_CODE_SUCCESS = 1;
    const STATUS_CODE_FAILED = -1;

    /**
     * Check if there is still a pending registration for the tutor
     */
    public function isPendingTutorRegistration($userId)
    {
        $exists = false;

        if (Auth::user())
            $exists = PendingRegistration::where(ProfileFields::UserId, $userId)->exists();

        return $exists;
    }

    public function buildTutorRegistrationFormView()
    {
        $viewData = [
            'softSkills'        => User::SOFT_SKILLS,
            'currentYear'       =>  date('Y'),
            'fluencyOptions'    => FluencyLevels::Tutor,
            'guestRegistration' => false,

            'showConvertAccWarning' => false
        ];

        // If the user is currently a learner, we warn them
        // about their account being converted to tutor account
        if (Auth::user() && Auth::user()->{UserFields::Role} == User::ROLE_LEARNER)
            $viewData['showConvertAccWarning'] = true;

        return $viewData;
    }

    /**
     * The validation rules for User model
     */
    public function getUserValidationRules(array $exclude = []) : array
    {
        $rules = [
            'firstname' => 'required|string|max:32',
            'lastname'  => 'required|string|max:32',
            'contact'   => 'required|string|max:20',
            'address'   => 'required|string|max:255',
            'fluency'   => 'required|integer|max:3|in:' . implode(',', array_keys(FluencyLevels::Learner)),
            'email'     => 'required|string|email|max:255|unique:users',
            'username'  => 'required|string|max:32|unique:users',
            'password'  => 'required|string|min:4|confirmed',
        ];

        // Remove rules for fields in the exclude array
        foreach ($exclude as $field) {
            unset($rules[$field]);
        }

        return $rules;
    }

    /**
     * The rules for Profile model
     */
    private function getProfileValidationRules(Request $request, $mergeRules = [])
    {
        $currentYear = date('Y');

        //------------------------------------------------
        // GET THE TOTAL ENTRIES PER CATEGORY
        //------------------------------------------------

        $totalEntries = $this->getTotalEntriesPerCategory($request);

        $maxEducEntries = $totalEntries['educ'];
        $maxWorkEntries = $totalEntries['work'];
        $maxCertEntries = $totalEntries['cert'];

        //------------------------------------------------
        // GENERATE RULES FOR EACH DYNAMIC ENTRY
        //------------------------------------------------
        $rules = [
            'bio'               => 'required|string|max:180',
            'about'             => 'required|string|max:2000',
            'fluency'           => 'required|integer|max:3|in:' . implode(',', array_keys(FluencyLevels::Tutor)),
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

        // More rules...
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
                    "certification-description{$suffix}" => 'required|string',
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

    /**
     * These data will come from the Registration Resume Dynamic Entries.
     * We will collect and construct the relevant entries needed for Profiles table.
     */
    public function getResumeProfileEntries(Request $request, $userId)
    {
        $rules     = $this->getProfileValidationRules($request);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $validator->validated();

        // Group together dynamic entries data
        $educ         = [];
        $work         = [];
        $cert         = [];
        $skills       = [];
        $uploadQueue  = [];

        $hashids      = new Hashids(HashSalts::Files, 10);
        //$userId       = Auth::user()->id;
        $hashedUserId = $hashids->encode($userId);

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

            // Match keys that start with 'certification-year-from-'
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

        // Collect the optional dynamic skills
        if (!empty($inputs['skills-arr']))
        {
            $skills = json_decode($inputs['skills-arr'], true);
            $skills = array_column($skills, 'value');
        }

        // Sanitize the HTML content sent by QuillJS
        $config     = HTMLPurifier_Config::createDefault();
        $purifier   = new HTMLPurifier($config);
        $about      = $purifier->purify($inputs['about']);

        $returnData = [
            "profileModel" => [
                ProfileFields::UserId           => $userId,
                ProfileFields::Bio              => $inputs['bio'],
                ProfileFields::About            => $about,
                ProfileFields::Fluency          => $inputs['fluency'],
                ProfileFields::Education        => $educ,
                ProfileFields::Certifications   => $cert,
                ProfileFields::Experience       => $work,
                ProfileFields::Skills           => $skills
            ],
            "upload" => $uploadQueue
        ];

        // if ($request->has('is_guest'))
        // {
        //     $returnData["usersModel"] = [
        //         UserFields::Firstname   => $inputs['firstname'],
        //         UserFields::Lastname    => $inputs['lastname'],
        //         UserFields::Contact     => $inputs['contact'],
        //         UserFields::Address     => $inputs['address'],
        //         UserFields::Username    => $inputs['username'],
        //         'email'                 => $inputs['email'],
        //         'password'              => $inputs['password']
        //     ];
        // }

        return $returnData;
    }

     /**
     * These data will come from the Registration User Identity Entries.
     * This can also be used to collect the data from Learner Registration.
     * We will collect and construct the relevant entries needed for Profiles table.
     */
    public function getUserIdentityEntries(Request $request, $exclude = [])
    {
        $rules     = $this->getUserValidationRules($exclude);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $validator->validated();

        return [
            UserFields::Firstname   => $inputs['firstname'],
            UserFields::Lastname    => $inputs['lastname'],
            UserFields::Contact     => $inputs['contact'],
            UserFields::Address     => $inputs['address'],
            UserFields::Username    => $inputs['username'],
            ProfileFields::Fluency  => $inputs['fluency'],
            'email'                 => $inputs['email'],
            'password'              => Hash::make($inputs['password'])
        ];
    }

    public function registerLearner(Request $request)
    {
        // Extract the data of every dynamic entry from the resume registration
        $data = $this->getUserIdentityEntries($request);

        // If the validation fails... we go back
        if ($data instanceof \Illuminate\Http\RedirectResponse)
            return $data;

        DB::beginTransaction();

        try
        {
            $user = User::create([
                UserFields::Firstname   => $data['firstname'],
                UserFields::Lastname    => $data['lastname'],
                UserFields::Contact     => $data['contact'],
                UserFields::Address     => $data['address'],
                'email'                 => $data['email'],
                UserFields::Username    => $data['username'],
                'password'              => $data['password']
            ]);

            $profile = Profile::create([
                ProfileFields::UserId   => $user->id,
                ProfileFields::Fluency  => $data['fluency']
            ]);

            DB::commit();

            return [
                'status'          => 200,
                'createdUser'     => $user,
                'createdProfile'  => $profile
            ];
        }
        catch (Exception $ex)
        {
            DB::rollBack();

            return [
                'status'          => 500,
                'createdUser'     => null,
                'createdProfile'  => null
            ];
        }
    }

    public function registerTutor(Request $request)
    {
        // Extract the user details from the resume registration
        $userEntries = $this->getUserIdentityEntries($request);

        // If the validation fails... we go back
        if ($userEntries instanceof \Illuminate\Http\RedirectResponse)
            return $userEntries;

        // Store here the paths of the uploaded files. We will use
        // these later when we will delete them
        $uploadedFiles = [];

        DB::beginTransaction();

        try
        {
            //===================================
            // 1. Create the user first
            //===================================
            $userModel = User::create($userEntries);

            //===================================
            // 2. Then create the user's profile
            //===================================

            // Extract the data of every dynamic entry from the resume registration
            $profileEntries = $this->getResumeProfileEntries($request, $userModel->id);

            // If the validation fails... we go back
            if ($profileEntries instanceof \Illuminate\Http\RedirectResponse)
                return $profileEntries;

            // The profile is saved into the pending registrations for the admin to approve...
            PendingRegistration::create($profileEntries['profileModel']);

            $profileModel = Profile::create([
                ProfileFields::UserId  => $userModel->id,
                ProfileFields::Fluency => $profileEntries['profileModel']['fluency']
            ]);

            //===================================
            // 3. Upload the documentary proofs
            //===================================
            $uploadedFiles = $this->uploadDocumentaryProofs($profileEntries['upload']);

            DB::commit();

            //===================================
            // 4. Assume successful operation
            //===================================

            return [
                'status'          => 200,
                'createdUser'     => $userModel,
                'createdProfile'  => $profileModel
            ];
        }
        catch (Exception $ex)
        {
            DB::rollBack();

            // Delete uploaded files if any
            $this->deleteUploadedDocumentaryProofs($uploadedFiles);

            return [
                'status'          => 500,
                'createdUser'     => null,
                'createdProfile'  => null
            ];
        }

        // Execute the action then get the status code
        // $register = $this->createUser($data); //($profileModel, $upload);

        // if ($register == RegistrationService::STATUS_CODE_SUCCESS)
        // {
        //     // We will use this to guard the registration success screen
        //     // so that it can only be visited once if ONLY there is a
        //     // successful registration.
        //     $request->session()->put('registration_success', true);

        //     // Redirect to the registration success screen
        //     return redirect()->route('become-tutor.success');
        // }
        // else
        // {
        //     // If there is an error in the database or fileupload during
        //     // the registration, we abort the execution
        //     return response()->view('errors.500', [], 500);
        // }
    }

    public function upgradeLearnerToTutor(Request $request)
    {
        // Store here the paths of the uploaded files. We will use
        // these later when we will delete them
        $uploadedFiles = [];

        DB::beginTransaction();

        try
        {
            $userId = Auth::user()->id;

            //===================================
            // 1. Create the user's profile
            //===================================

            // Extract the data of every dynamic entry from the resume registration
            $profileEntries = $this->getResumeProfileEntries($request, $userId);

            // If the validation fails... we go back
            if ($profileEntries instanceof \Illuminate\Http\RedirectResponse)
                return $profileEntries;

            // The profile is saved into the pending registrations for the admin to approve...
            PendingRegistration::create($profileEntries['profileModel']);

            $profileModel = Profile::create([
                ProfileFields::UserId  => $userId,
                ProfileFields::Fluency => $profileEntries['profileModel']['fluency']
            ]);

            //===================================
            // 2. Upload the documentary proofs
            //===================================
            $uploadedFiles = $this->uploadDocumentaryProofs($profileEntries['upload']);

            DB::commit();

            //===================================
            // 3. Assume successful operation
            //===================================

            return [
                'status' => 200,
                'createdProfile'  => $profileModel
            ];
        }
        catch (Exception $ex)
        {
            DB::rollBack();

            // Delete uploaded files if any
            $this->deleteUploadedDocumentaryProofs($uploadedFiles);

            return [
                'status' => 500,
                'createdProfile'  => null
            ];
        }
    }

    private function uploadDocumentaryProofs($uploadQueue)
    {
        $uploadedFiles = [];

        // $category    -> the entry category eg Education | Work | Certs
        // $uploadData  -> the documentary proofs array per category
        foreach ($uploadQueue as $category => $uploadData)
        {
            foreach ($uploadData as $obj)
            {
                // Ensure $obj['file'] is an instance of UploadedFile and not treated as an array
                if ($obj['file'] instanceof \Illuminate\Http\UploadedFile)
                {
                    // Generate a unique file name
                    $fileName = $obj['filename'];
                    $path     = $obj['file']->storeAs($obj['filepath'], $fileName);

                    $uploadedFiles[$category][] = $path;
                }
            }
        }

        return $uploadedFiles;
    }

    private function deleteUploadedDocumentaryProofs(array $uploadedFiles) : void
    {
        foreach ($uploadedFiles as $files)
        {
            foreach ($files as $file)
            {
                Storage::delete($file);
            }
        }
    }
}
