<?php

namespace App\Http\Controllers;

use App\Models\FieldNames\UserFields;
use App\Services\LearnerService;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearnerController extends Controller
{
    protected $learnerService;
    protected $registrationService;

    public function __construct(LearnerService $service, RegistrationService $regSvc)
    {
        $this->learnerService = $service;
        $this->registrationService = $regSvc;
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
        $fluencyFilter = $this->learnerService->getFluencyFilters();
        return view('learner.registration', compact('fluencyFilter'));
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

        // Log the user in after registration
        // auth()->login($register['createdUser']);

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
        error_log('must have flashed!');
        // Show the homepage
        return redirect()->to('/');
    }

    //===================================================================//
    //        F O R   A U T H E N T I C A T E D   L E A R N E R S
    //....................................................................
    //              Each learner is free to become a tutor;
    //            eg Controllers prefixed with "becomeTutor_*"
    //===================================================================//

    public function index()
    {
        return view('learner.index');
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
}


// $inputs = $model;
// return view('test.test', compact('inputs'));

