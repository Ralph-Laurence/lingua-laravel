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
use App\Services\TutorSvc;
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
        private LearnerSvc $learnerSvc,
        private TutorSvc $tutorSvc
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
        return $this->tutorSvc->showTutorDetails($id);
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
