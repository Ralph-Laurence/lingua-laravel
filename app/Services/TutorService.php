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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TutorService
{
    const Role = User::ROLE_TUTOR;

    private $tutorHashIds;

    function __construct()
    {
        $this->tutorHashIds = new Hashids(HashSalts::Tutors, 10);
    }

    public static function FindById($id)
    {
        try
        {
            $tutor = User::where('id', $id)->where('role', self::Role)->firstOrFail();
            return $tutor;
        }
        catch (ModelNotFoundException $e)
        {
            return null;
        }
    }

    public function showReviewRegistration($id)
    {
        try
        {
            // Decode the hashed ID
            $decodedId = $this->tutorHashIds->decode($id);

            // Check if the ID is empty
            if (empty($decodedId)) {
                return view('errors.404');
            }

            // Fetch the tutor along with their pending registration data
            $tutorId      = $decodedId[0];
            $tutor        = User::findOrFail($tutorId);
            $pending      = PendingRegistration::where(ProfileFields::UserId, $tutorId)->firstOrFail();
            $fluencyLevel = FluencyLevels::Tutor[$pending->{ProfileFields::Fluency}];
            $skills       = [];

            if ($pending->{ProfileFields::Skills})
            {
                foreach($pending->{ProfileFields::Skills} as $skill)
                {
                    $skills[] = User::SOFT_SKILLS[$skill];
                }
            }

            $educationProof = $pending->{ProfileFields::Education};
            $workProof      = $pending->{ProfileFields::Experience};
            $certProof      = $pending->{ProfileFields::Certifications};

            if (!empty($educationProof))
            {
                foreach ($educationProof as $k => $obj)
                {
                    $pdfPath = $obj['full_path'];

                    // Ensure the PDF path is sanitized and validated
                    if (!Storage::exists($pdfPath))
                        $educationProof[$k]['docProof'] = '-1'; // 'corrupted'

                    // Generate a secure URL for the PDF file
                    $educationProof[$k]['docProof'] = Storage::url($pdfPath);
                }
            }

            if (!empty($workProof))
            {
                foreach ($workProof as $k => $obj)
                {
                    $pdfPath = $obj['full_path'];

                    // Ensure the PDF path is sanitized and validated
                    if (!Storage::exists($pdfPath))
                        $workProof[$k]['docProof'] = '-1'; // 'corrupted'

                    // Generate a secure URL for the PDF file
                    $workProof[$k]['docProof'] = Storage::url($pdfPath);
                }
            }

            if (!empty($certProof))
            {
                foreach ($certProof as $k => $obj)
                {
                    $pdfPath = $obj['full_path'];

                    // Ensure the PDF path is sanitized and validated
                    if (!Storage::exists($pdfPath))
                        $certProof[$k]['docProof'] = '-1'; // 'corrupted'

                    // Generate a secure URL for the PDF file
                    $certProof[$k]['docProof'] = Storage::url($pdfPath);
                }
            }

            $applicantDetails = [
                'hashedId'           => $id,
                'fullname'           => implode(' ', [$tutor->{UserFields::Firstname}, $tutor->{UserFields::Lastname}]),
                'email'              => $tutor->email,
                'contact'            => $tutor->{UserFields::Contact},
                'address'            => $tutor->{UserFields::Address},
                'verified'           => $tutor->{UserFields::IsVerified} == 1,
                'bio'                => $pending->{ProfileFields::Bio},
                'about'              => $pending->{ProfileFields::About},
                'work'               => $workProof,
                'education'          => $educationProof,
                'certs'              => $certProof,
                'skills'             => $skills,
                'fluencyBadgeIcon'   => $fluencyLevel['Badge Icon'],
                'fluencyBadgeColor'  => $fluencyLevel['Badge Color'],
                'fluencyLevelText'   => $fluencyLevel['Level'],
            ];

            $fluencyFilter = $this->getFluencyFilters();

            // Return the view with the tutor data
            return view('admin.tutors-review', compact('applicantDetails', 'fluencyFilter'));
        }
        catch (ModelNotFoundException $e)
        {
            error_log($e->getMessage());
            // Return custom 404 page
            return view('errors.404');
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            // Return custom 404 page
            return view('errors.500');
        }
    }

    public function listAllTutors(Request $request)
    {
        $result = null;

        // if ($request->session()->has('result'))
        //     $result = $request->session()->get('result');
        if ($request->session()->has('filter'))
        {
            $filter = $request->session()->get('filter');
            $result = $result = $this->getTutors($filter);
        }
        else
        {
            $result = $this->getTutors();
        }

        $tutors = $result['tutorsSet'];
        $fluencyFilter = $result['fluencyFilter'];

        if ($request->session()->has('inputs'))
        {
            // Remove the session variable to prevent access after the first visit.
            // Which means GET and CLEAR the session data
            // $inputs = $request->session()->pull('inputs');

            // However, This will retain the old session data...
            $inputs = $request->session()->get('inputs');
            $hasFilter = true;

            return view('admin.tutors', compact('tutors', 'fluencyFilter', 'inputs', 'hasFilter'));
        }

        return view('admin.tutors', compact('tutors', 'fluencyFilter'));
    }

    public function showTutorDetails($id)
    {
        try
        {
            // Decode the hashed ID
            $decodedId = $this->tutorHashIds->decode($id);

            // Check if the ID is empty
            if (empty($decodedId)) {
                return view('errors.404');
            }

            // Fetch the tutor along with their profile
            $tutorId      = $decodedId[0];
            $tutor        = User::with('profile')->findOrFail($tutorId);
            $fluencyLevel = FluencyLevels::Tutor[$tutor->profile->{ProfileFields::Fluency}];

            $photo = $tutor->{UserFields::Photo};
            $profilePic = asset('assets/img/default_avatar.png');

            if (!empty($photo))
                $profilePic = Storage::url("public/uploads/profiles/$photo");

            $skills = [];

            if ($tutor->profile->{ProfileFields::Skills})
            {
                foreach($tutor->profile->{ProfileFields::Skills} as $skill)
                {
                    $skills[] = User::SOFT_SKILLS[$skill];
                }
            }

            $tutorDetails = [
                'firstname'          => $tutor->{UserFields::Firstname},
                'fullname'           => implode(' ', [$tutor->{UserFields::Firstname}, $tutor->{UserFields::Lastname}]),
                'email'              => $tutor->email,
                'contact'            => $tutor->{UserFields::Contact},
                'address'            => $tutor->{UserFields::Address},
                'verified'           => $tutor->{UserFields::IsVerified} == 1,
                'work'               => $tutor->profile->{ProfileFields::Experience},
                'bio'                => $tutor->profile->{ProfileFields::Bio},
                'about'              => $tutor->profile->{ProfileFields::About},
                'education'          => $tutor->profile->{ProfileFields::Education},
                'certs'              => $tutor->profile->{ProfileFields::Certifications},
                'skills'             => $skills,
                'photo'              => $profilePic,
                'fluencyBadgeIcon'   => $fluencyLevel['Badge Icon'],
                'fluencyBadgeColor'  => $fluencyLevel['Badge Color'],
                'fluencyLevelText'   => $fluencyLevel['Level'],
            ];

            // Return the view with the tutor data
            return view('admin.show-tutor', compact('tutorDetails'));
        }
        catch (ModelNotFoundException $e)
        {
            // Return custom 404 page
            return view('errors.404');
        }
        catch (Exception $e)
        {
            // Return custom 404 page
            return view('errors.500');
        }
    }

    public function getTutors($options = [])
    {
        $options = array_merge(['min_entries' => 10], $options);

        // Get the ids of all users with pending registration
        $pending = PendingRegistration::pluck(ProfileFields::UserId)->toArray();

        // Get all existing tutors
        $fields = [
            'users.id',
            UserFields::Firstname,
            UserFields::Lastname,
            UserFields::Photo,
            UserFields::Role,
            UserFields::IsVerified,
            ProfileFields::Fluency
        ];

        // Build the query
        $tutors = User::select($fields)
                    ->join('profiles', 'users.id', '=', 'profiles.'.ProfileFields::UserId)
                    // Filter based on status
                    ->when(isset($options['status']) && $options['status'] != 0, function($query) use ($pending, $options)
                    {
                        if ($options['status'] == 1) // Pending
                            return $query->whereIn('users.id', $pending);

                        else if ($options['status'] == 2) // Verified
                            return $query->where(UserFields::IsVerified, true);
                    },
                    function($query) use ($pending)
                    {
                        // If no status filter or status filter is 0 (ALL), select both
                        return $query->where(function($query) use ($pending)
                        {
                            $query->where(UserFields::Role, User::ROLE_TUTOR)
                                  ->orWhereIn('users.id', $pending);
                        });
                    })
                    ->withCount(['bookingsAsTutor as totalStudents' => function($query)
                    {
                        $query->whereHas('learner', function($query)
                        {
                            $query->where(UserFields::Role, User::ROLE_LEARNER);
                        });
                    }])
                    ->orderBy(UserFields::Firstname, 'ASC');

        if (array_key_exists('fluency', $options) && $options['fluency'] != -1)
        {
            $tutors = $tutors->where(ProfileFields::Fluency, $options['fluency']);
        }

        // if (array_key_exists('search', $options))
        // {
        //     $searchWord = $options['search'];
        //     $tutors = $tutors->where(UserFields::Firstname, 'LIKE', "%$searchWord%")
        //             ->orWhere(UserFields::Lastname, 'LIKE', "%$searchWord%");
        // }
        if (array_key_exists('search', $options))
        {
            $searchWord = $options['search'];
            $learners = $tutors->where(function ($query) use ($searchWord)
            {
                $query->where(UserFields::Firstname, 'LIKE', "%$searchWord%")
                      ->orWhere(UserFields::Lastname, 'LIKE', "%$searchWord%");
            });
        }

        // Get the results
        $tutors = $tutors->paginate($options['min_entries']);

        $defaultPic     = asset('assets/img/default_avatar.png');
        $fluencyFilter  = $this->getFluencyFilters();

        foreach ($tutors as $key => $obj)
        {
            $obj['totalStudents'] = $obj->totalStudents;

            if ($obj->{UserFields::IsVerified} == 1)
            {
                $obj['statusStr']   = 'Verified';
                $obj['statusBadge'] = 'bg-primary';
                $obj['needsReview'] = false;
                $obj['verified']    = true;
            }

            else if (!$obj->{UserFields::IsVerified} || in_array($obj->id, $pending) )
            {
                $obj['statusStr']   = 'Pending';
                $obj['statusBadge'] = 'bg-warning text-dark';
                $obj['needsReview'] = true;
                $obj['verified']    = false;
            }

            $obj->name = implode(' ', [$obj->{UserFields::Firstname}, $obj->{UserFields::Lastname}]);
            $obj['hashedId'] = $this->tutorHashIds->encode($obj->id);

            $fluency = $obj->{ProfileFields::Fluency};
            $obj['fluencyStr']   = $fluencyFilter[$fluency];
            $obj['fluencyBadge'] = FluencyLevels::Tutor[$fluency]['Badge Color'];

            $photo = $obj->{UserFields::Photo};
            $obj['photo'] = $defaultPic;

            if (!empty($photo))
            {
                $obj['photo'] = Storage::url("public/uploads/profiles/$photo");
            }
        }

        return [
            'tutorsSet'     => $tutors,
            'fluencyFilter' => $fluencyFilter,
            'options'       => $options
        ];
    }

    public function filterTutors(Request $request)
    {
        $rules = [
            'search-keyword' => 'nullable|string|max:64',
            'select-status'  => 'required|integer|in:0,1,2',
            'select-entries' => 'required|integer|in:10,25,50,100',
            'select-fluency' => 'required|integer|in:-1,' . implode(',', array_keys($this->getFluencyFilters()))
        ];

        $validator = Validator::make($request->all(), $rules);
        $error500  = response()->view('errors.500', [], 500);

        if ($validator->fails())
            return $error500;

        // Select Options validation
        $inputs = $validator->validated();
        $filter = [
            'status'        => $inputs['select-status'],
            'min_entries'   => $inputs['select-entries'],
            'fluency'       => $inputs['select-fluency']
        ];

        if (!empty($inputs['search-keyword']))
        {
            $filter['search'] = $inputs['search-keyword'];
        }

        $request->session()->put('inputs', $inputs);
        $request->session()->put('filter', $filter);
        // $request->session()->put('result', $this->tutorService->getTutors($filter));

        return redirect()->route('admin.tutors-index');
    }

    public function clearFilters(Request $request)
    {
        // Forget multiple session variables in one line
        $request->session()->forget(['result', 'filter', 'inputs']);

        return redirect()->route('admin.tutors-index');
    }

    public function approveRegistration($id)
    {
        try
        {
            // Decode the hashed ID
            $decodedId = $this->tutorHashIds->decode($id);

            // Check if the ID is empty
            if (empty($decodedId)) {
                return view('errors.404');
            }

            // Fetch the tutor along with their pending registration data
            $userId = $decodedId[0];

            // Start a transaction
            DB::beginTransaction();

            // Retrieve the pending registration record
            $pending = PendingRegistration::where('user_id', $userId)->firstOrFail();

            // Create or update the profile record
            $existingProfile = Profile::where(ProfileFields::UserId, $userId)->first();
            $upsertData = [
                ProfileFields::UserId           => $userId,
                ProfileFields::About            => $pending->{ProfileFields::About},
                ProfileFields::Bio              => $pending->{ProfileFields::Bio},
                ProfileFields::Fluency          => $pending->{ProfileFields::Fluency},
                ProfileFields::Education        => $pending->{ProfileFields::Education},
                ProfileFields::Experience       => $pending->{ProfileFields::Experience},
                ProfileFields::Certifications   => $pending->{ProfileFields::Certifications},
                ProfileFields::Skills           => $pending->{ProfileFields::Skills}
            ];

            if ($existingProfile)
            {
                // Update existing profile
                $existingProfile->update($upsertData);
            }
            else
            {
                // Update the profile
                $existingProfile->create($upsertData);
            }

            // Delete the pending registration record
            $pending->delete();

            // Find the applicant
            $applicant = User::findOrFail($userId);
                        // where('id', $userId)
                        //->select(UserFields::Firstname, UserFields::Lastname, 'email')
                        //->firstOrFail();

            // Update his details to be a tutor
            $applicant->update([
                UserFields::IsVerified => 1,
                UserFields::Role => User::ROLE_TUTOR
            ]);

            $applicantName = implode(' ', [$applicant->{UserFields::Firstname}, $applicant->{UserFields::Lastname}]);

            // Disable AVAST Mail Shield "Outbound SMTP" before sending emails
            $emailData = [
                'firstname' => $applicant->{UserFields::Firstname},
                'login'     => route('login'),
                'logo'      => public_path('assets/img/logo-brand-sm.png')
            ];

            Mail::to($applicant->email)->send(new RegistrationApprovedMail($emailData));

            // Commit the transaction
            DB::commit();

            // Return a success response
            return redirect()
                ->route('admin.tutors-index')
                ->with('registrationResultMsg', "Registration approved successfully for $applicantName");
        }
        catch (ModelNotFoundException $e)
        {
            error_log($e->getMessage());
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Return an error response
            return view('errors.404');
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Return an error response
            return view('errors.500');
        }
    }

    public function declineRegistration($id)
    {
        try
        {
            // Decode the hashed ID
            $decodedId = $this->tutorHashIds->decode($id);

            // Check if the ID is empty
            if (empty($decodedId)) {
                return view('errors.404');
            }

            // Fetch the tutor along with their pending registration data
            $userId = $decodedId[0];

            // Start a transaction
            DB::beginTransaction();

            // Retrieve the pending registration record
            $pending = PendingRegistration::where('user_id', $userId)->firstOrFail();

            // We can only continue if there was an existing record
            if ($pending)
            {
                // Delete the pending registration record
                $pending->delete();
            }
            else
            {
                return view('errors.404');
            }

            // Find the details of the applicant
            $applicant = User::findOrFail($userId);

            $applicantName = implode(' ', [$applicant->{UserFields::Firstname}, $applicant->{UserFields::Lastname}]);

            // Note: Disable AVAST Mail Shield "Outbound SMTP" before sending emails
            $emailData = [
                'firstname' => $applicant->{UserFields::Firstname},
                'logo'      => public_path('assets/img/logo-brand-sm.png')
            ];

            Mail::to($applicant->email)->send(new RegistrationDeclinedMail($emailData));

            // Commit the transaction
            DB::commit();

            // Return a success response
            return redirect()
                ->route('admin.tutors-index')
                ->with('registrationResultMsg', "Registration has been declined for $applicantName");
        }
        catch (ModelNotFoundException $e)
        {
            error_log($e->getMessage());
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Return an error response
            return view('errors.404');
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Return an error response
            return view('errors.500');
        }
    }

    private function getFluencyFilters()
    {
        $fluencyFilter = [];

        foreach (FluencyLevels::Tutor as $key => $obj)
        {
            $fluencyFilter[$key] = $obj['Level'];
        }

        return $fluencyFilter;
    }
}
