<?php

namespace App\Services;

use Illuminate\Http\Request;

//===================================================================//
//                        F O R   T U T O R S
//....................................................................
//               These functions are specific to the tutors
//....................................................................
//===================================================================//
class LearnerServiceForTutor extends LearnerService
{
    public function filterMyLearners(Request $request)
    {
        $filterLearners = $this->filterLearners($request);

        $filter = $filterLearners['filterOptions'];
        $inputs = $filterLearners['filterInputs'];

        $request->session()->put('learner-filter-inputs-for-tutor', $inputs);
        $request->session()->put('mylearner-filter-for-tutor', $filter);

        return redirect()->route('mylearners');
    }

    public function clearMyLearnerFilters(Request $request)
    {
        // Forget multiple session variables in one line
        $request->session()->forget(['mylearner-filter-for-tutor', 'learner-filter-inputs-for-tutor']);

        return redirect()->route('mylearners');
    }

    public function filterLearnersList(Request $request)
    {
        $filterLearners = $this->filterLearners($request);

        $filter = $filterLearners['filterOptions'];
        $inputs = $filterLearners['filterInputs'];

        $request->session()->put('learner-filter-inputs-for-tutor', $inputs);
        $request->session()->put('mylearner-filter-for-tutor', $filter);

        return redirect()->route('mylearners');
    }

    public function listAllLearners(Request $request, $tutorId)
    {
        $result = null;
        $filter = ['exceptConnected' => $tutorId];

        if ($request->session()->has('learner-filter-except'))
        {
            $dataSetFilters = $request->session()->get('learner-filter-except');
            $filter = array_merge($filter, $dataSetFilters);
        }

        $result = $result = $this->getLearners($filter);

        $learners = $result['learnersSet'];
        $fluencyFilter = $result['fluencyFilter'];

        if ($request->session()->has('learner-filter-inputs-for-tutor'))
        {
            $learnerFilterInputs = $request->session()->get('learner-filter-inputs-for-tutor');
            $hasFilter = true;

            return view('tutor.find-learners', compact('learners', 'fluencyFilter', 'learnerFilterInputs', 'hasFilter'));
        }

        return view('tutor.find-learners', compact('learners', 'fluencyFilter'));
    }

    public function listMyLearners(Request $request, $tutorId)
    {
        $result = null;
        $filter = ['forTutor' => $tutorId];

        if ($request->session()->has('mylearner-filter-for-tutor'))
        {
            $dataSetFilters = $request->session()->get('mylearner-filter-for-tutor');
            $filter = array_merge($filter, $dataSetFilters);
        }

        $result = $result = $this->getLearners($filter);

        $learners = $result['learnersSet'];
        $fluencyFilter = $result['fluencyFilter'];

        if ($request->session()->has('learner-filter-inputs-for-tutor'))
        {
            $learnerFilterInputs = $request->session()->get('learner-filter-inputs-for-tutor');
            $hasFilter = true;

            return view('tutor.mylearners', compact('learners', 'fluencyFilter', 'learnerFilterInputs', 'hasFilter'));
        }

        return view('tutor.mylearners', compact('learners', 'fluencyFilter'));
    }

    //
    // This must be accessed via Asynchronous POST
    //
    public function showLearnerDetails(Request $request)
    {
        $id = $request->input('learner_id');
        $learnerDetails = $this->getLearnerDetails($id);
        $status  = 200;
        $message = 'Found';

        if ($learnerDetails == 400)
        {
            $status  = 400;
            $message = "The learner does not exist or has been deleted.";
        }

        if ($learnerDetails == 500)
        {
            $status  = 500;
            $message = "There was a problem while trying to read the learner's data.";
        }

        // Return the data as JSON
        return response()->json([
            'status'  => $status,
            'message' => $message,
            'data'    => $learnerDetails
        ], $status);
    }
}
