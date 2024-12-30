<?php

namespace App\Http\Controllers;

use App\Http\Utils\FluencyLevels;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use App\Models\PendingRegistration;
use App\Models\User;
use App\Services\LearnerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LearnerController extends Controller
{
    protected $learnerService;

    public function __construct(LearnerService $service)
    {
        $this->learnerService = $service;
    }

    public function index()
    {
        return view('learner.index');
    }

    public function myTutors()
    {
        $myTutors = $this->learnerService->getConnectedTutors(Auth::user()->id);

        return view('learner.my-tutors')->with('myTutors', $myTutors);
    }

    public function becomeTutor()
    {
        // Check if we have a pending registration
        if ($this->isPendingRegistration())
            return response()->view('shared.pending-registration', [], 301);

        // Show the landing page otherwise
        return view('learner.become-tutor');
    }

    public function becomeTutorFormsPage()
    {
        // Check if we have a pending registration
        if ($this->isPendingRegistration())
            return response()->view('shared.pending-registration', [], 301);

        // Show the forms page otherwise ...

        $softSkills     = User::SOFT_SKILLS;
        $currentYear    = date('Y');
        $fluencyOptions = FluencyLevels::Tutor;

        // If the user is currently a learner, we warn them
        // about their account being converted to tutor account
        $showConvertAccWarning = Auth::user()->{UserFields::Role} == User::ROLE_LEARNER;
        $returnData = compact('currentYear', 'softSkills', 'fluencyOptions', 'showConvertAccWarning');

        return view('learner.become-tutor-forms', $returnData);
    }

    public function becomeTutorSuccess(Request $request)
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

            // $inputs = $model;
        // return view('test.test', compact('inputs'));

    public function becomeTutorOnSubmit(Request $request)
    {
        $data = $this->learnerService->buildProfilePayloadData($request);

        // If the validation fails ... we go back
        if ($data instanceof \Illuminate\Http\RedirectResponse)
            return $data;

        // Otherwise, we get the processed data.
        $model  = $data['model'];
        $upload = $data['upload'];

        $uploadedFiles = [];

        DB::beginTransaction();

        try
        {
            // Save to the database
            $registration = $this->learnerService->buildRegistrationData(Auth::user()->id, $model);
            PendingRegistration::create($registration);

            // Process uploads...
            foreach ($upload as $category => $uploadData)
            {
                foreach ($uploadData as $data)
                {
                    // Ensure $data['file'] is an instance of UploadedFile and not treated as an array
                    if ($data['file'] instanceof \Illuminate\Http\UploadedFile)
                    {
                        // Generate a unique file name
                        $fileName = $data['filename'];
                        $path     = $data['file']->storeAs($data['filepath'], $fileName);

                        $uploadedFiles[$category][] = $path;
                    }
                }
            }

            DB::commit();

            //Set session variable to indicate successful registration
            $request->session()->put('registration_success', true);

            // Redirect to the registration success screen
            return redirect()->route('become-tutor.success');
        }
        catch(Exception $e)
        {
            // Rollback the transaction
            DB::rollBack();

            // Delete uploaded files if any
            foreach ($uploadedFiles as $files)
            {
                foreach ($files as $file)
                {
                    Storage::delete($file);
                }
            }

            return response()->view('errors.500', [], 500);
        }
    }

    private function isPendingRegistration()
    {
        $exists = PendingRegistration::where(ProfileFields::UserId, Auth::user()->id)->exists();
        return $exists;
    }
}

