<?php

namespace App\Http\Controllers;

use App\Http\Utils\Constants;
use App\Http\Utils\FluencyLevels;
use App\Http\Utils\HashSalts;
use App\Http\Utils\Helper;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\BookingRequestFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\RatingsAndReviewFields;
use App\Models\FieldNames\UserFields;
use App\Models\RatingsAndReview;
use App\Models\User;
use App\Services\LearnerServiceForTutor;
use App\Services\LearnerSvc;
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
        private TutorBookingRequestService  $tutorBookingRequestService,
        private LearnerSvc $learnerSvc
    )
    {
        $this->hashids = new Hashids(HashSalts::Tutors, 10);
    }

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
            $tutorId = $decodedId[0];
            $learnerId = Auth::user()->id;
            // $tutor   = User::with(['profile', 'receivedRatings' => function($query) {
            //     $query->select(DB::raw('FORMAT(avg(' . RatingsAndReviewFields::Rating . '), 1) as averageRating'));
            // }])->findOrFail($tutorId);
            $userFields = Helper::prependFields('users.', [
                'id',
                'email',
                UserFields::Firstname,
                UserFields::Lastname,
                UserFields::Address,
                UserFields::Photo,
                UserFields::IsVerified,
                UserFields::Contact
            ]);

            $selectFields = $userFields + [
                DB::raw('IFNULL(FORMAT(AVG(ratings_and_reviews.rating), 1), 0.0) as averageRating'),
            ];

            $tutor = User::select(array_merge($userFields, [
                DB::raw('(SELECT IFNULL(FORMAT(AVG(' . RatingsAndReviewFields::Rating . '), 1), 0.0) FROM ratings_and_reviews WHERE ' . RatingsAndReviewFields::TutorId . ' = users.id) as averageRating'),
                DB::raw('(SELECT COUNT(DISTINCT ' . BookingFields::LearnerId . ') FROM bookings WHERE ' . BookingFields::TutorId . ' = users.id) as totalLearners')
            ]))
            ->with([
                'profile',
                'receivedRatings' => function ($query) {
                    $query->select([
                        RatingsAndReviewFields::TutorId,
                        RatingsAndReviewFields::LearnerId,
                        RatingsAndReviewFields::Rating,
                        RatingsAndReviewFields::Review
                    ]);
                },
                'receivedRatings.learner' => function ($query) {
                    $query->select(['id']); // Only retrieve learner ID
                }
            ])
            ->where('users.id', $tutorId)
            ->where(UserFields::Role, User::ROLE_TUTOR)
            ->firstOrFail();

            $fluencyLevel  = FluencyLevels::Tutor[$tutor->profile->{ProfileFields::Fluency}];

            // Calculate total learners
            $totalLearners = $tutor->totalLearners; //->bookingsAsTutor->count();

            $learnerReview = RatingsAndReview::select([
                RatingsAndReviewFields::Rating,
                RatingsAndReviewFields::Review
            ])
            ->where(RatingsAndReviewFields::TutorId, $tutorId)
            ->where(RatingsAndReviewFields::LearnerId, $learnerId)
            ->first();

            // Determine hire status
            // $hireStatus = -1;

            // if ($tutor->bookingRequestsReceived->isNotEmpty()) {
            //     // Tutor hasn't accepted the hire request yet
            //     $hireStatus = 2;
            // }

            // if ($hireStatus == -1 && Booking::where(BookingFields::TutorId, $tutorId)
            //     ->where(BookingFields::LearnerId, Auth::user()->id)
            //     ->exists())
            // {
            //     // Tutor is hired by learner ...
            //     $hireStatus = 1;
            // }

            // if ($tutor->bookingsAsTutor->isNotEmpty()) {
            //     // Tutor is hired by learner
            //     $hireStatus = 1;
            // } elseif ($tutor->bookingRequestsReceived->isNotEmpty()) {
            //     // Tutor hasn't accepted the hire request yet
            //     $hireStatus = 2;
            // }

            $hireStatus = -1;

            if (Booking::where(BookingFields::TutorId, $tutorId)
                ->where(BookingFields::LearnerId, $learnerId)
                ->exists())
            {
                // Tutor is hired by learner ...
                $hireStatus = 1;
            }
            else if (BookingRequest::where(BookingRequestFields::ReceiverId, $tutorId)
                ->where(BookingRequestFields::SenderId, $learnerId)
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

            $firstname = $tutor->{UserFields::Firstname};
            $tutorDetails = [
                'hashedId'           => $id,
                'firstname'          => $firstname,
                'possessiveName'     => User::toPossessiveName($firstname),
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
                'averageRating'      => $tutor->averageRating,
                'ratingsAndReviews'  => $tutor->receivedRatings
            ];

            $starRatings = Constants::StarRatings;

            // Return the view with the tutor data
            return view('tutor.show', compact('tutorDetails', 'totalLearners', 'starRatings', 'learnerReview'));
        }
        catch (ModelNotFoundException $e)
        {
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
    //
    //..............................................
    //           FOR VIEWING THE LEARNERS
    //..............................................
    //
    public function find_learners(Request $request)
    {
        $availableFilters = [
            'search'  => '',
            'fluency' => -1,
            'exceptConnected' => Auth::user()->id
            // Add other filters here with default values
        ];

        $options = $this->learnerSvc->createRequestFilterRules($request, $availableFilters);

        $minEntries = $request->input('min-entries');

        if (in_array($minEntries, Constants::PageEntries))
        {
            $options['minEntries'] = $minEntries;
            session()->flash('minEntries', $minEntries);
        }

        $learners = $this->learnerSvc->getLearnersListForTutor($options);
        $fluencyFilter = FluencyLevels::ToSelectOptions(FluencyLevels::SELECT_OPTIONS_LEARNER);

        // Determine if any filters are applied
        $filtersApplied = $this->learnerSvc->areFiltersApplied($options, $availableFilters);

        session()->flash('search', $options['search']);
        session()->flash('fluency', $options['fluency']);
        // ...Flash other filters as needed

        $entriesOptions = Constants::PageEntries;

        return view('tutor.find-learners', compact('learners', 'fluencyFilter', 'filtersApplied', 'entriesOptions'));
    }

    public function find_learners_clear_filter()
    {
        session()->forget('fluency', 'search');
        return redirect()->route('tutor.find-learners');
    }

    public function my_learners_clear_filter()
    {
        session()->forget('fluency', 'search');
        return redirect()->route('tutor.my-learners');
    }

    public function my_learners(Request $request)
    {
        $availableFilters = [
            'search'  => '',
            'fluency' => -1,
            'mode'    => 'myLearners',
            'tutorId' => Auth::user()->id
            // Add other filters here with default values
        ];

        $options = $this->learnerSvc->createRequestFilterRules($request, $availableFilters);

        $minEntries = $request->input('min-entries');

        if (in_array($minEntries, Constants::PageEntries))
        {
            $options['minEntries'] = $minEntries;
            session()->flash('minEntries', $minEntries);
        }

        $learners = $this->learnerSvc->getLearnersListForTutor($options);
        $fluencyFilter = FluencyLevels::ToSelectOptions(FluencyLevels::SELECT_OPTIONS_LEARNER);

        // Determine if any filters are applied
        $filtersApplied = $this->learnerSvc->areFiltersApplied($options, $availableFilters);

        session()->flash('search', $options['search']);
        session()->flash('fluency', $options['fluency']);
        // ...Flash other filters as needed

        $entriesOptions = Constants::PageEntries;

        return view('tutor.mylearners', compact('learners', 'fluencyFilter', 'filtersApplied', 'entriesOptions'));






        // $options = [
        //     'search'  => $request->input('search', ''),
        //     'mode'    => 'myLearners',
        //     'tutorId' => Auth::user()->id
        // ];

        // $minEntries = $request->input('min-entries');

        // if (in_array($minEntries, Constants::PageEntries))
        //     $options['minEntries'] = $minEntries;

        // $learners       = $this->learnerSvc->getLearnersListForTutor($options);
        // $fluencyFilter  = FluencyLevels::ToSelectOptions(FluencyLevels::SELECT_OPTIONS_LEARNER);

        // return view('tutor.mylearners', compact('learners', 'fluencyFilter'));
        // $tutorId = Auth::user()->id;
        // return $this->learnerServiceForTutor->listMyLearners($request, $tutorId);
    }
    //
    //..............................................
    //      FOR MANAGING LEARNER HIRE REQUESTS
    //..............................................
    //
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
