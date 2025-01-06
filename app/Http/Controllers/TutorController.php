<?php

namespace App\Http\Controllers;

use App\Http\Utils\FluencyLevels;
use App\Http\Utils\HashSalts;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\BookingRequestFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use App\Services\LearnerServiceForTutor;
use App\Services\RegistrationService;
use App\Services\TutorBookingRequestService;
use App\Services\TutorService;
use Exception;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TutorController extends Controller
{
    private $hashids;

    // Property Promotion
    public function __construct(
        private RegistrationService         $registrationService,
        private LearnerServiceForTutor      $learnerServiceForTutor,
        private TutorService                $tutorService,
        private TutorBookingRequestService  $tutorBookingRequestService
    )
    {
        $this->hashids = new Hashids(HashSalts::Tutors, 10);
    }
    // Traditional:
    // public function __construct(RegistrationService $regSvc, LearnerServiceForTutor $lrnSvc, TutorService $tutSvc, TutorBookingRequestService $tutBookReqSvc)
    // {
    //     $this->hashids = new Hashids(HashSalts::Tutors, 10);
    //     $this->registrationService = $regSvc;
    //     $this->learnerServiceForTutor = $lrnSvc;
    //     $this->tutorService = $tutSvc;
    //     $this->tutorBookingRequestService = $tutBookReqSvc;
    // }

    // public function listTutors()
    // {
    //     $user = Auth::user();

    //     // Get all list of tutors
    //     // Perform the query with eager loading
    //     $listOfTutors = User::where(UserFields::Role, User::ROLE_TUTOR)
    //                 ->where(UserFields::IsVerified, 1)
    //                 ->with('profile')
    //                 ->get();

    //     // Store here the ids of tutors that were already hired
    //     // Fetch the hired tutors' IDs as a single dimension indexed array
    //     $hiredTutors = Booking::where(BookingFields::LearnerId, $user->id)
    //                  ->pluck(BookingFields::TutorId)
    //                  ->toArray();

    //     $tutors = [];

    //     foreach ($listOfTutors as $key => $obj)
    //     {
    //         $photo   = $obj->{UserFields::Photo};
    //         $tutorId = $obj['id'];
    //         $isHired = !empty($hiredTutors) && in_array($tutorId, $hiredTutors);

    //         $fluencyLevel = FluencyLevels::Tutor[$obj->profile->{ProfileFields::Fluency}];

    //         if (empty($photo))
    //             $photo = asset('assets/img/default_avatar.png');

    //         else
    //             $photo = Storage::url("public/uploads/profiles/$photo");

    //         $tutors[] = [
    //             'hashedId'      => $this->hashids->encode($tutorId),
    //             'profilePic'    => $photo,
    //             'fullname'      => implode(' ', [$obj->{UserFields::Firstname}, $obj->{UserFields::Lastname}]),
    //             'verified'      => $obj->{UserFields::IsVerified} == 1,
    //             'bioNotes'      => $obj->profile->{ProfileFields::Bio} ?? "No Bio Available",
    //             'isHired'       => $isHired,

    //             'hiredIndicator'     => !$isHired ? 'd-none' : '',
    //             'fluencyBadgeIcon'   => $fluencyLevel['Badge Icon'],
    //             'fluencyBadgeColor'  => $fluencyLevel['Badge Color'],
    //             'fluencyLevelText'   => $fluencyLevel['Level'],
    //         ];
    //     }

    //     return view('learner.list-tutors')
    //            ->with('tutors', $tutors)
    //            ->with('totalTutors', count($tutors))
    //            ->with('hashids', $this->hashids);
    // }

    //
    // A learner sends a hire request to the tutor ...
    //
    // public function hireTutor(Request $request)
    // {
    //     $hashedId = $request->input('tutor_id');
    //     $error = response()->view('errors.500', [], 500);

    //     if (empty($hashedId))
    //         return $error;

    //     $decodeTutorId = $this->hashids->decode($hashedId);

    //     if (empty($decodeTutorId))
    //         return $error;

    //     $tutorId = $decodeTutorId[0];

    //     try
    //     {
    //         $tutorExists = User::where('id', $tutorId)->exists();

    //         if (!$tutorExists) {
    //             return $error;
    //         }

    //         Booking::create([
    //             BookingFields::LearnerId => Auth::user()->id,
    //             BookingFields::TutorId   => $tutorId
    //         ]);

    //         return redirect(route('tutor.show', $hashedId));
    //     }
    //     catch (Exception $ex)
    //     {
    //         return $error;
    //     }
    // }

    public function endContract(Request $request)
    {
        $hashedId = $request->input('tutor_id');
        $error = response()->view('errors.500', [], 500);

        if (empty($hashedId))
            return $error;

        $decodeTutorId = $this->hashids->decode($hashedId);

        if (empty($decodeTutorId))
            return $error;

        $tutorId = $decodeTutorId[0];

        try
        {
            DB::beginTransaction();

            $tutorExists = User::where('id', $tutorId)->exists();

            if (!$tutorExists) {
                return $error;
            }

            $deleted = Booking::where(BookingFields::LearnerId, Auth::user()->id)
                    ->where(BookingFields::TutorId, $tutorId)
                    ->delete();

            if ($deleted)
            {
                DB::commit();
                return redirect(route('tutor.show', $hashedId));
            }
            else
            {
                DB::rollBack();
                return $error;
            }
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            return $error;
        }
    }

    public function show($id)
    {
        try
        {
            // Decode the hashed ID
            $decodedId = $this->hashids->decode($id);

            // Check if the ID is empty
            if (empty($decodedId)) {
                return view('errors.404');
            }

            // Fetch the tutor along with their profile
            $tutorId      = $decodedId[0];
            $tutor        = User::with('profile')->findOrFail($tutorId);
            $fluencyLevel = FluencyLevels::Tutor[$tutor->profile->{ProfileFields::Fluency}];

            $hireStatus = -1;

            if (Booking::where(BookingFields::TutorId, $tutorId)
                ->where(BookingFields::LearnerId, Auth::user()->id)
                ->exists())
            {
                // Tutor is hired by learner ...
                $hireStatus = 1;
            }
            else if (BookingRequest::where(BookingRequestFields::ReceiverId, $tutorId)
                ->where(BookingRequestFields::SenderId, Auth::user()->id)
                ->exists())
            {
                // Tutor havent accepted the hire request yet
                $hireStatus = 2;
            }

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
                'hashedId'           => $id,
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
                'hireStatus'         => $hireStatus,
                'fluencyBadgeIcon'   => $fluencyLevel['Badge Icon'],
                'fluencyBadgeColor'  => $fluencyLevel['Badge Color'],
                'fluencyLevelText'   => $fluencyLevel['Level'],
            ];

            // Return the view with the tutor data
            return view('tutor.show', compact('tutorDetails'));
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
    //
    //..............................................
    //                FOR LEARNERS
    //..............................................
    //
    public function find_learners(Request $request)
    {
        $tutorId = Auth::user()->id;
        return $this->learnerServiceForTutor->listAllLearners($request, $tutorId);
    }

    public function myLearners(Request $request)
    {
        $tutorId = Auth::user()->id;
        return $this->learnerServiceForTutor->listMyLearners($request, $tutorId);
    }

    public function myLearners_show(Request $request)
    {
        return $this->learnerServiceForTutor->showLearnerDetails($request);
    }

    public function myLearners_filter(Request $request)
    {
        $filter = ['forTutor' => Auth::user()->id];
        return $this->learnerServiceForTutor->filterMyLearners($request, $filter);
    }

    public function myLearners_clear_filter(Request $request)
    {
        return $this->learnerServiceForTutor->clearMyLearnerFilters($request);
    }

    public function hire_requests()
    {
        return $this->tutorService->getHireRequests(Auth::user()->id);
    }

    public function hire_request_accept(Request $request)
    {
        return $this->tutorBookingRequestService
                    ->acceptHireRequest($request, Auth::user()->id);
    }

    public function hire_request_decline(Request $request)
    {
        return $this->tutorBookingRequestService
                    ->declineHireRequest($request, Auth::user()->id);
    }

    //===================================================================//
    //                   F O R   G U E S T   U S E R S
    //....................................................................
    // These route controllers are accessible to the unauthenticated users
    //===================================================================//

    /**
     * Launch the tutor registration form
     */
    public function registerTutor_create()
    {
        // Show the forms page otherwise ...
        $returnData = $this->registrationService->buildTutorRegistrationFormView();

        // This will signal the blade view to include the firstnames, emails etc
        $returnData['guestRegistration'] = true;

        return view('shared.contents.become-tutor-forms', $returnData);
    }

    public function registerTutor_store(Request $request)
    {
        $register = $this->registrationService->registerTutor($request);

        // If the validation fails... we go back
        if ($register instanceof \Illuminate\Http\RedirectResponse)
            return $register;

        if ($register['status'] == 200)
        {
            // Log the user in after registration
            auth()->login($register['createdUser']);
        }
        else
        {
            // If there is an error in the database or fileupload during
            // the registration, we abort the execution
            return response()->view('errors.500', [], 500);
        }

        // Add a welcome greetings
        $firstname = $register['createdUser']->{UserFields::Firstname};

        session()->flash('registration_message', "Welcome to the community, $firstname!");

        // Show the homepage
        return redirect()->to('/');
    }
}
