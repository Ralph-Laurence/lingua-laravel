<?php

namespace App\Http\Controllers;

use App\Services\AdminDashboardService;
use App\Services\LearnerServiceForAdmin;
use App\Services\TutorService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $tutorService;
    protected $learnerServiceForAdmin;
    protected $adminService;

    function __construct(TutorService $tutService, LearnerServiceForAdmin $lrnService, AdminDashboardService $adminSvc)
    {
        $this->tutorService = $tutService;
        $this->adminService = $adminSvc;
        $this->learnerServiceForAdmin  = $lrnService;
    }

    function index()
    {
        $totals = $this->adminService->getTotals();

        return view('admin.dashboard', compact('totals'));
    }
    //
    //..............................................
    //                  FOR TUTORS
    //..............................................
    //
    public function tutors_index(Request $request)
    {
        return $this->tutorService->listAllTutors($request);
    }

    public function tutors_filter(Request $request)
    {
        return $this->tutorService->filterTutors($request);
    }

    public function tutors_clear_filter(Request $request)
    {
        return $this->tutorService->clearFilters($request);
    }

    public function tutors_review_registration($id)
    {
        return $this->tutorService->showReviewRegistration($id);
    }

    public function tutors_approve_registration($id)
    {
        return $this->tutorService->approveRegistration($id);
    }

    public function tutors_decline_registration($id)
    {
        return $this->tutorService->declineRegistration($id);
    }

    public function tutors_show($id)
    {
        return $this->tutorService->showTutorDetails($id);
    }
    //
    //..............................................
    //                FOR LEARNERS
    //..............................................
    //
    public function learners_index(Request $request)
    {
        return $this->learnerServiceForAdmin->listAllLearnersForAdmin($request);
    }

    public function learners_filter(Request $request)
    {
        return $this->learnerServiceForAdmin->filterLearnersForAdmin($request);
    }

    public function learners_clear_filter(Request $request)
    {
        return $this->learnerServiceForAdmin->clearFiltersForAdmin($request);
    }

    public function learners_show($id)
    {
        return $this->learnerServiceForAdmin->showLearnerDetailsForAdmin($id);
    }
}
