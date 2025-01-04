<?php

namespace App\Services;

use Illuminate\Http\Request;

class LearnerServiceForAdmin extends LearnerService
{
    //===================================================================//
    //                 F O R   A D M I N I S T R A T O R S
    //....................................................................
    //    These route controllers are specific to the administrators
    //===================================================================//

    public function filterLearnersForAdmin(Request $request)
    {
        $filterLearners = $this->filterLearners($request);

        $filter = $filterLearners['filterOptions'];
        $inputs = $filterLearners['filterInputs'];
            
        $request->session()->put('learner-filter-inputs-for-admin', $inputs);
        $request->session()->put('learner-filter-for-admin', $filter);

        return redirect()->route('admin.learners-index');
    }

    public function clearFiltersForAdmin(Request $request)
    {
        // Forget multiple session variables in one line
        $request->session()->forget(['learner-filter-for-admin', 'learner-filter-inputs-for-admin']);

        return redirect()->route('admin.learners-index');
    }

    public function listAllLearnersForAdmin(Request $request)
    {
        $result = null;

        if ($request->session()->has('learner-filter-for-admin'))
        {
            $filter = $request->session()->get('learner-filter-for-admin');
            $result = $result = $this->getLearners($filter);
        }
        else
        {
            $result = $this->getLearners();
        }

        $learners = $result['learnersSet'];
        $fluencyFilter = $result['fluencyFilter'];

        if ($request->session()->has('learner-filter-inputs-for-admin'))
        {
            $learnerFilterInputs = $request->session()->get('learner-filter-inputs-for-admin');
            $hasFilter = true;

            return view('admin.learners', compact('learners', 'fluencyFilter', 'learnerFilterInputs', 'hasFilter'));
        }

        return view('admin.learners', compact('learners', 'fluencyFilter'));
    }

    //
    // This must be accessed via Standard HTTP GET
    //
    public function showLearnerDetailsForAdmin($id)
    {
        $learnerDetails = $this->getLearnerDetails($id);

        if ($learnerDetails == 400)
        {
            // Return custom 404 page
            return view('errors.404');
        }

        if ($learnerDetails == 500)
        {
            // Return custom 404 page
            return view('errors.500');
        }

        // Return the view with the tutor data
        return view('admin.show-learner', compact('learnerDetails'));
    }
}