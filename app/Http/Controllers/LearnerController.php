<?php

namespace App\Http\Controllers;

use App\Http\Utils\Constants;
use App\Http\Utils\HashSalts;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\RatingsAndReviewFields;
use App\Models\FieldNames\UserFields;
use App\Models\RatingsAndReview;
use App\Models\User;
use App\Services\LearnerBookingRequestService;
use App\Services\LearnerService;
use App\Services\LearnerSvc;
use App\Services\RegistrationService;
use App\Services\TutorSvc;
use Exception;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LearnerController extends Controller
{
    public function __construct(
        private LearnerService $learnerService,
        private RegistrationService $registrationService,
        private LearnerBookingRequestService $lrnBookingReqSvc,
        private LearnerSvc $learnerSvc,
        private TutorSvc $tutorSvc
    )
    {
        // Constructor property promotion
    }

    public function fetchLearnerDetails(Request $request)
    {
        $id = $this->learnerSvc->toRawId(
            $request->input('learner_id', '')
        );

        return $this->learnerSvc->fetchLearnerDetails($id);
    }

    //===================================================================//
    //                   F O R   G U E S T   U S E R S
    //....................................................................
    // These route controllers are accessible to the unauthenticated users
    //===================================================================//

    /**
     * Show the learner registration form for guest users.
     */
    public function registerLearner_create()
    {
        $disabilityFilter = User::getDisabilityFilters();

        return view('learner.registration', compact('disabilityFilter'));
    }

    /**
     * Save the learner's registration inputs into the database.
     */
    public function registerLearner_store(Request $request)
    {
        $register = $this->registrationService->registerLearner($request);

        // If the validation fails... we go back
        if ($register instanceof \Illuminate\Http\RedirectResponse)
            return $register;

        if ($register['status'] == 500)
        {
            // If there is an error in the database or fileupload during
            // the registration, we abort the execution
            return response()->view('errors.500', [], 500);
        }

        // Log the user in after registration
        auth()->login($register['createdUser']);

        // We will use this to guard the registration success screen
        // so that it can only be visited once if ONLY there is a
        // successful registration.
        // $request->session()->put('registration_success', true);

        // Add a welcome greetings
        $firstname = $register['createdUser']->{UserFields::Firstname};

        session()->flash('registration_message', "Welcome to the community, $firstname!");
        // Show the homepage
        return redirect()->to('/');
    }

    //===================================================================//
    //        F O R   A U T H E N T I C A T E D   L E A R N E R S
    //....................................................................
    //              Each learner is free to become a tutor;
    //            eg Controllers prefixed with "becomeTutor_*"
    //===================================================================//

    //
    //..............................................
    //           FOR VIEWING THE TUTORS
    //..............................................
    //
    public function find_tutors(Request $request)
    {
        $availableFilters = [
            'search'  => '',
            'disability' => -1,
            'exceptConnected' => Auth::user()->id
            // Add other filters here with default values
        ];

        $options = $this->tutorSvc->createRequestFilterRules($request, $availableFilters);

        $minEntries = $request->input('min-entries');

        if (in_array($minEntries, Constants::PageEntries))
        {
            $options['minEntries'] = $minEntries;
            session()->flash('minEntries', $minEntries);
        }

        $options['extraFields'] = [ProfileFields::Bio];
        $options['includeRatings'] = true;
        $options['includeDateJoined'] = true;

        $tutors = $this->tutorSvc->getTutorsListForLearner($options);
        $disabilityFilter = User::getDisabilityFilters();

        // Determine if any filters are applied
        $filtersApplied = $this->tutorSvc->areFiltersApplied($options, $availableFilters);

        session()->flash('search', $options['search']);
        session()->flash('disability', $options['disability']);
        // ...Flash other filters as needed

        $entriesOptions = Constants::PageEntries;

        $totalTutors = $tutors->count();

        return view('learner.find-tutors', compact(
            'tutors',
            'disabilityFilter',
            'filtersApplied',
            'entriesOptions',
            'totalTutors'
        ));
    }

    public function clearFilterTutors()
    {
        return redirect()->route('learner.find-tutors');
    }

    /**
     * Show the list of all tutors connected to the learner
     */
    public function myTutors()
    {
        $myTutors = $this->learnerService->getConnectedTutors(Auth::user()->id);

        return view('learner.my-tutors')->with('myTutors', $myTutors);
    }

    /**
     * The landing page for becoming a tutor, coming from learner auth
     */
    public function becomeTutor_index()
    {
        // Check if we have a pending registration for the current learner
        // then we shouldn't allow them to visit the tutor registration page
        $userId = Auth::user()->id;

        if ($this->registrationService->isPendingTutorRegistration($userId))
            return response()->view('shared.pending-registration', [], 301);

        // Show the landing page otherwise
        return view('shared.become-tutor');
    }

    /**
     * Launch the tutor registration form
     */
    public function becomeTutor_create()
    {
        // Check if we have a pending registration for the current learner
        // then we shouldn't allow them to visit the tutor registration page
        $userId = Auth::user()->id;

        if ($this->registrationService->isPendingTutorRegistration($userId))
            return response()->view('shared.pending-registration', [], 301);

        // Show the forms page otherwise ...
        $returnData = $this->registrationService->buildTutorRegistrationFormView();

        return view('shared.contents.become-tutor-forms', $returnData);
    }

    /**
     * This action is executed when the EXISTING learner wants to become a tutor.
     * This will MIGRATE or CONVERT the existing learner's profile into a tutor
     * profile. Unlike the Guest Member Registration, this function only updates
     * the learner's existing PROFILE record without having to create a new user
     * account.
     */
    public function becomeTutor_store(Request $request)
    {
        $register = $this->registrationService->upgradeLearnerToTutor($request);

        // If the validation fails... we go back
        if ($register instanceof \Illuminate\Http\RedirectResponse)
            return $register;

        if ($register['status'] == 200)
        {
            // We will use this to guard the registration success screen
            // so that it can only be visited once if ONLY there is a
            // successful registration.
            $request->session()->put('registration_success', true);

            // Redirect to the registration success screen
            return redirect()->route('become-tutor.success');
        }
        else
        {
            // If there is an error in the database or fileupload during
            // the registration, we abort the execution
            return response()->view('errors.500', [], 500);
        }
    }

    /**
     * Show the registration success screen after a successful registration.
     */
    public function becomeTutor_success(Request $request)
    {
        if ($request->session()->has('registration_success'))
        {
            // Remove the session variable to prevent access after the first visit
            $request->session()->forget('registration_success');

            return view('shared.registration-success');
        }

        // Redirect to home if the session variable is not set
        return redirect('/');
    }

    public function hireTutor(Request $request)
    {
        $hashedId = $request->input('tutor_id');
        $error500 = response()->view('errors.500', [], 500);
        $error404 = response()->view('errors.404', [], 404);

        if (empty($hashedId))
            return $error404;

        $hashids = new Hashids(HashSalts::Tutors, 10);
        $decodeTutorId = $hashids->decode($hashedId);

        if (empty($decodeTutorId))
            return $error500;

        $tutorId     = $decodeTutorId[0];
        $learnerId   = Auth::user()->id;
        $bookRequest = $this->lrnBookingReqSvc->hireTutor($learnerId, $tutorId);

        if ($bookRequest == 404)
            return $error404;

        if ($bookRequest == 500)
            return $error500;

        session()->flash('booking_request_success', true);

        return redirect(route('tutor.show', $hashedId));
    }

    public function cancelHireTutor(Request $request)
    {
        $hashedId = $request->input('tutor_id');
        $error500 = response()->view('errors.500', [], 500);
        $error404 = response()->view('errors.404', [], 404);

        if (empty($hashedId))
            return $error404;

        $hashids = new Hashids(HashSalts::Tutors, 10);
        $decodeTutorId = $hashids->decode($hashedId);

        if (empty($decodeTutorId))
            return $error500;

        $tutorId     = $decodeTutorId[0];
        $learnerId   = Auth::user()->id;
        $cancelRequest = $this->lrnBookingReqSvc->cancelHireTutor($learnerId, $tutorId);

        if ($cancelRequest == 404)
            return $error404;

        if ($cancelRequest == 500)
            return $error500;

        session()->flash('booking_request_canceled', true);

        return redirect(route('tutor.show', $hashedId));
    }

    /**
     * When learner makes a rating and review to a tutor
     */
    public function storeTutorReview(Request $request)
    {
        $rules = [
            'tutorId'   => 'required|string',
            'rating'    => 'required|integer|max:5|min:1',
            'review'    => 'nullable|string|max:250'
        ];

        $messages = [
            'tutorId'         => 'Tutor does not exist.',
            'rating.required' => 'Please select a rating from the stars.',
            'rating.integer'  => 'The rating must be at least 1 star and may not be greater than 5 stars.',
            'rating.min'      => 'The rating must be at least 1 star.',
            'rating.max'      => 'The rating may not be greater than 5 stars.',
            'review.string'   => 'The review is invalid.',
            'review.max'      => 'The review may not be greater than 250 characters.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs     = $validator->validated();
        $tutorId    = TutorSvc::toRawId($inputs['tutorId']);
        $learnerId  = Auth::user()->id;

        $recordMatch = RatingsAndReview::where(RatingsAndReviewFields::LearnerId, $learnerId)
                ->where(RatingsAndReviewFields::TutorId, $tutorId);

        try
        {
            $upsert = [
                RatingsAndReviewFields::LearnerId => $learnerId,
                RatingsAndReviewFields::TutorId   => $tutorId,
                RatingsAndReviewFields::Rating    => $inputs['rating'],
                RatingsAndReviewFields::Review    => $inputs['review'] ?? null,
            ];

            $successMessage = 'Your review has been published!';

            // Update the existing review
            if ($recordMatch->exists())
            {
                $recordMatch->update($upsert);
                $successMessage = 'Your review has been updated!';
            }

            // Add new review
            else
                RatingsAndReview::create($upsert);

            session()->flash('review_msg', $successMessage);

            return redirect()->route('tutor.show', $inputs['tutorId']);
        }
        catch (Exception $ex)
        {
            session()->flash('review_msg', "We're unable to publish your review because of a technical error. Please try again later.");

            return redirect()->back();
        }

        return view('test.test', compact('inputs'));
    }

    public function deleteTutorReview(Request $request)
    {
        return $this->learnerSvc->deleteTutorReview($request->input('tutor_id'));
    }
}
