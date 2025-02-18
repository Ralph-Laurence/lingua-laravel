<?php

namespace App\Http\Controllers;

use App\Services\AdminDashboardService;
use App\Services\LearnerServiceForAdmin;
use App\Services\TutorService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    function __construct(
        private TutorService $tutorService,
        private LearnerServiceForAdmin $learnerService,
        private AdminDashboardService $adminDashboardSvc)
    {
    }

    function index()
    {
        $totals = $this->adminDashboardSvc->getTotals();

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

    public function dashboard_view_pending_registration(Request $request)
    {
        return $this->adminDashboardSvc->viewDashboardPendingRegistration($request);
    }
    //
    //..............................................
    //                FOR LEARNERS
    //..............................................
    //
    public function learners_index(Request $request)
    {
        return $this->learnerService->listAllLearners($request);
    }

    public function learners_filter(Request $request)
    {
        return $this->learnerService->filterResults($request);
    }

    public function learners_clear_filter(Request $request)
    {
        return $this->learnerService->clearFilters($request);
    }

    public function learners_show($id)
    {
        return $this->learnerService->showLearnerDetails($id);
    }
}
