<?php

namespace App\Services;

use App\Http\Utils\FluencyLevels;
use App\Http\Utils\HashSalts;
use App\Models\Booking;
use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TutorServiceForLearner
{
    private $tutorHashids;

    public function __construct()
    {
        $this->tutorHashids = new Hashids(HashSalts::Tutors, 10);
    }

    public function listAllTutors($learnerId)
    {
        $data           = $this->getTutors($learnerId);
        $tutors         = $data['tutors'];
        $totalTutors    = $data['totalTutors'];
        $fluencyFilters = $data['fluencyFilters'];
        $hashids        = $data['hashids'];

        return view('learner.find-tutors', compact('tutors', 'totalTutors', 'fluencyFilters', 'hashids'));
    }

    public function listAllTutorsWithFilter(Request $request, $learnerId)
    {
        // Validation rules
        $rules = [
            'select-fluency' => 'required|integer|in:-1,'. FluencyLevels::AsValidationRule(FluencyLevels::SELECT_OPTIONS_TUTOR),
            'search-term'    => 'nullable|string'
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->view('errors.404', [], 404);
        }

        // Validated input filter options
        $inputs  = $validator->validated();
        $inputs['withFilter'] = true;

        $data           = $this->getTutors($learnerId, $inputs);
        $tutors         = $data['tutors'];
        $totalTutors    = $data['totalTutors'];
        $fluencyFilters = $data['fluencyFilters'];
        $hashids        = $data['hashids'];

        return view('learner.find-tutors', compact('tutors', 'totalTutors', 'fluencyFilters', 'hashids', 'inputs'));
    }

    private function getTutors($learnerId, $options = [])
    {
        // Store here the ids of tutors that were already hired
        // Fetch the hired tutors' IDs as a single dimension indexed array
        $hiredTutors = Booking::where(BookingFields::LearnerId, $learnerId)
                     ->pluck(BookingFields::TutorId)
                     ->toArray();

        $totalTutors = User::where(UserFields::Role, User::ROLE_TUTOR)
                    ->where(UserFields::IsVerified, 1)
                    ->count();

        // Get all list of tutors
        // Perform the query with eager loading
        $tutors = User::where(UserFields::Role, User::ROLE_TUTOR)
            ->where(UserFields::IsVerified, 1)
            ->with('profile');

        // Apply fluency filter if provided
        if (array_key_exists('select-fluency', $options) && $options['select-fluency'] != -1) {
            $tutors = $tutors->whereHas('profile', function($query) use ($options) {
                $query->where(ProfileFields::Fluency, $options['select-fluency']);
            });
        }

        // Apply search-term filter if provided
        if (array_key_exists('search-term', $options) && !empty($options['search-term']))
        {
            $searchTerm = $options['search-term'];
            $tutors = $tutors->where(function($query) use ($searchTerm) {
                $query->where(UserFields::Firstname, 'like', '%' . $searchTerm . '%')
                      ->orWhere(UserFields::Lastname, 'like', '%' . $searchTerm . '%');
            });
        }

        $tutors = $tutors->paginate(5)
            ->through(function($request) use($hiredTutors)
            {
                $tutorId = $request->id;
                $isHired = !empty($hiredTutors) && in_array($tutorId, $hiredTutors);

                $fluencyLevel = FluencyLevels::Tutor[$request->profile->{ProfileFields::Fluency}];

                // Transform the data to the desired structure..
                // Meaning, we only get those we need

                return [
                    'hashedId'      => $this->tutorHashids->encode($tutorId),
                    'profilePic'    => User::getPhotoUrl($request->{UserFields::Photo}),
                    'fullname'      => implode(' ', [$request->{UserFields::Firstname}, $request->{UserFields::Lastname}]),
                    'verified'      => $request->{UserFields::IsVerified} == 1,
                    'bioNotes'      => $request->profile->{ProfileFields::Bio} ?? "No Bio Available",
                    'isHired'       => $isHired,

                    'hiredIndicator'     => !$isHired ? 'd-none' : '',
                    'fluencyBadgeIcon'   => $fluencyLevel['Badge Icon'],
                    'fluencyBadgeColor'  => $fluencyLevel['Badge Color'],
                    'fluencyLevelText'   => $fluencyLevel['Level'],
                ];
            });

        $fluencyFilters = FluencyLevels::ToSelectOptions(FluencyLevels::SELECT_OPTIONS_TUTOR);

        return [
            'tutors'         => $tutors,
            'totalTutors'    => $totalTutors,
            'fluencyFilters' => $fluencyFilters,
            'hashids'        => $this->tutorHashids
        ];
    }
}
