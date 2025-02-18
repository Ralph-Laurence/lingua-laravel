<?php

namespace App\Services;

use App\Http\Utils\Constants;
use App\Http\Utils\HashSalts;
use App\Mail\RegistrationApprovedMail;
use App\Mail\RegistrationDeclinedMail;
use App\Models\BookingRequest;
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
    private $tutorHashIds;
    private $learnerHashIds;

    function __construct()
    {
        $this->tutorHashIds   = new Hashids(HashSalts::Tutors, 10);
        $this->learnerHashIds = new Hashids(HashSalts::Learners, 10);
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
            $disability   = $tutor->profile->{ProfileFields::Disability};
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
                'fullname'           => $tutor->name,
                'email'              => $tutor->email,
                'contact'            => $tutor->{UserFields::Contact},
                'address'            => $tutor->{UserFields::Address},
                'verified'           => $tutor->{UserFields::IsVerified} == 1,
                'bio'                => $pending->{ProfileFields::Bio},
                'about'              => $pending->{ProfileFields::About},
                'work'               => $workProof,
                'education'          => $educationProof,
                'certs'              => $certProof,
                'skills'             => $skills
            ];

            if (!empty($disability))
            {
                $applicantDetails['disability'] = Constants::Disabilities[$disability];
            }

            // Return the view with the tutor data
            return view('admin.tutors-review', compact('applicantDetails'));
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
        if (session()->has('remove_admin_temporary_filter_tutors'))
        {
            return $this->clearFilters($request);
        }

        $result = null;

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
        $disabilityFilter = $result['disabilityFilter'];
        $disabilityDesc   = $result['disabilityDesc'];

        if ($request->session()->has('inputs'))
        {
            // Remove the session variable to prevent access after the first visit.
            // Which means GET and CLEAR the session data
            // $inputs = $request->session()->pull('inputs');

            // However, This will retain the old session data...
            $inputs = $request->session()->get('inputs');
            $hasFilter = true;

            return view('admin.tutors', compact('tutors', 'disabilityFilter', 'disabilityDesc', 'inputs', 'hasFilter'));
        }

        return view('admin.tutors', compact('tutors', 'disabilityFilter', 'disabilityDesc'));
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
            $disability   = $tutor->profile->{ProfileFields::Disability};
            $skills       = [];

            if ($tutor->profile->{ProfileFields::Skills})
            {
                foreach($tutor->profile->{ProfileFields::Skills} as $skill)
                {
                    $skills[] = User::SOFT_SKILLS[$skill];
                }
            }

            $tutorDetails = [
                'firstname'          => $tutor->{UserFields::Firstname},
                'fullname'           => $tutor->name,
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
                'photo'              => $tutor->photoUrl
            ];

            if (!empty($disability))
            {
                $tutorDetails['disability'     ] = Constants::Disabilities[$disability];
                $tutorDetails['disabilityDesc' ] = Constants::DisabilitiesDescription[$disability];
                $tutorDetails['disabilityBadge'] = Constants::DisabilitiesBadge[$disability];
            }

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
            ProfileFields::Disability
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

        if (array_key_exists('disability', $options) && $options['disability'] != -1)
        {
            $tutors = $tutors->where(ProfileFields::Disability, $options['disability']);
        }

        if (array_key_exists('search', $options))
        {
            $searchWord = $options['search'];
            $tutors = $tutors->where(function ($query) use ($searchWord)
            {
                $query->where(UserFields::Firstname, 'LIKE', "%$searchWord%")
                      ->orWhere(UserFields::Lastname, 'LIKE', "%$searchWord%");
            });
        }

        // Get the results
        $tutors = $tutors->paginate($options['min_entries']);
        $disabilityFilter = User::getDisabilityFilters();
        $badges           = Constants::DisabilitiesBadge;

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

            $obj['hashedId']     = $this->tutorHashIds->encode($obj->id);
            $disability          = $obj->{ProfileFields::Disability};
            $obj['disabilityId'] = $disability;
            $obj->disability     = $disabilityFilter[$disability];

            if (array_key_exists($disability, $badges))
                $obj['disabilityBadge'] = $badges[$disability];
        }

        return [
            'tutorsSet'     => $tutors,
            'options'       => $options,
            'disabilityFilter'  => $disabilityFilter,
            'disabilityDesc'    => Constants::DisabilitiesDescription
        ];
    }

    public function filterTutors(Request $request)
    {
        // $inputs = $request->all();
        // return view('test.test', compact('inputs'));
        $rules = [
            'search-keyword' => 'nullable|string|max:64',
            'select-status'  => 'required|integer|in:0,1,2',
            'select-entries' => 'required|integer|in:10,25,50,100',
            'select-disability' => 'required|integer|in:-1,' . implode(',', User::getDisabilityFilters('keys'))
        ];

        $validator = Validator::make($request->all(), $rules);
        $error500  = response()->view('errors.500', [], 500);

        if ($validator->fails())
        {
            // http://127.0.0.1:8000/admin/tutors/pending
            error_log('stops here');

            foreach ($validator->errors()->toArray() as $field => $errors) {
                foreach ($errors as $error) {
                    error_log($error);
                }
            }
            return $error500;
        }

        // Select Options validation
        $inputs = $validator->validated();
        $filter = [
            'status'        => $inputs['select-status'],
            'min_entries'   => $inputs['select-entries'],
            'disability'    => $inputs['select-disability']
        ];

        if (!empty($inputs['search-keyword']))
        {
            $filter['search'] = $inputs['search-keyword'];
        }

        $request->session()->put('inputs', $inputs);
        $request->session()->put('filter', $filter);

        if ($request->has('temporary_filter') &&
            $request->temporary_filter === true)
        {
            // Useful when accessed from dashboard
            $request->session()->put('admin_temporary_filter_tutors', true);
        }

        return redirect()->route('admin.tutors-index');
    }

    public function clearFilters(Request $request)
    {
        // Forget multiple session variables in one line
        $request->session()->forget(['result', 'filter', 'inputs', 'remove_admin_temporary_filter_tutors']);

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
                ProfileFields::Disability          => $pending->{ProfileFields::Disability},
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
                'login'     => url(route('login')),
                'logo'      => public_path('assets/img/logo-brand-sm.png')
            ];

            if (config('app.feature_send_mails'))
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

    //=======================================
    // HIRE REQUESTS
    //=======================================
    public function getHireRequests($tutorId)
    {
        $hashids = $this->learnerHashIds;
        $hireRequests = BookingRequest::with(['sender' => function($query)
        {
            // (aliasing not necessary anyway)
            $selectUserFields = [
                UserFields::Firstname . ' as fname',
                UserFields::Lastname  . ' as lname',
                UserFields::Photo     . ' as photo',
                UserFields::Contact   . ' as contact',
                'id',
                'email',
            ];

            $selectProfileFields = implode(',', [
                'profile:id',
                ProfileFields::Disability.' as impairment',
                ProfileFields::UserId .' as user_id'
            ]);

            $query->select($selectUserFields)->with($selectProfileFields);
        }])
        ->where('receiver_id', $tutorId)
        ->paginate(10)
        ->through(function($request) use($hashids)
        {
            // Transform the data to the desired structure..
            // Meaning, we only get those we need

            error_log($request);
            $name = implode(' ', [
                $request->sender->fname,
                $request->sender->lname
            ]);

            $data = [
                'name'       => $name,
                'photo'      => User::getPhotoUrl($request->sender->photo),
                'contact'    => $request->sender->contact,
                'email'      => $request->sender->email,
                'user_id'    => $hashids->encode($request->sender->profile->user_id),
            ];

            return $data;
        });

        return view('tutor.hire-requests', compact('hireRequests'));
    }
}
