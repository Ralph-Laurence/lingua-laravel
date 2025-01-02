<?php

namespace App\Services;

use App\Http\Utils\FluencyLevels;
use App\Http\Utils\HashSalts;
use App\Models\Booking;
use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use Exception;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LearnerService extends UserService
{
    const Role = User::ROLE_LEARNER;

    private $learnerHashIds;

    function __construct()
    {
        $this->learnerHashIds = new Hashids(HashSalts::Learners, 10);
    }

    public function getConnectedTutors($learnerId) : array
    {
        // Retrieve all userids of tutors tied to the learner
        $tutorIds = Booking::where(BookingFields::LearnerId, $learnerId)
                  ->pluck(BookingFields::TutorId)
                  ->toArray();

        // Store each tutor's data here
        $connectedTutors = [];

        if (!empty($tutorIds))
        {
            $hashids = new Hashids(HashSalts::Tutors, 10);
            $tutors  = User::whereIn('id', $tutorIds)->get();

            foreach ($tutors as $key => $obj)
            {
                $photo = $obj->{UserFields::Photo};

                if (empty($photo))
                    $photo = asset('assets/img/default_avatar.png');

                else
                    $photo = Storage::url("public/uploads/profiles/$photo");

                $connectedTutors[] = [
                    'tutorId'   => $hashids->encode($obj['id']),
                    'shortName' => User::toShortName($obj->{UserFields::Firstname}, $obj->{UserFields::Lastname}),
                    'photo'     => $photo
                ];
            }
        }

        return $connectedTutors;
    }

    public function listAllLearners(Request $request)
    {
        $result = null;

        if ($request->session()->has('learner-filter'))
        {
            $filter = $request->session()->get('learner-filter');
            $result = $result = $this->getLearners($filter);
        }
        else
        {
            $result = $this->getLearners();
        }

        $learners = $result['learnersSet'];
        $fluencyFilter = $result['fluencyFilter'];

        if ($request->session()->has('learner-filter-inputs'))
        {
            $learnerFilterInputs = $request->session()->get('learner-filter-inputs');
            $hasFilter = true;

            return view('admin.learners', compact('learners', 'fluencyFilter', 'learnerFilterInputs', 'hasFilter'));
        }

        return view('admin.learners', compact('learners', 'fluencyFilter'));
    }

    public function showLearnerDetails($id)
    {
        try
        {
            // Decode the hashed ID
            $decodedId = $this->learnerHashIds->decode($id);

            // Check if the ID is empty
            if (empty($decodedId)) {
                return view('errors.404');
            }

            // Fetch the learner's details
            $learnerId    = $decodedId[0];
            $learner      = User::findOrFail($learnerId);
            $fluencyLevel = FluencyLevels::Learner[$learner->profile->{ProfileFields::Fluency}];

            $photo = $learner->{UserFields::Photo};
            $profilePic = asset('assets/img/default_avatar.png');

            if (!empty($photo))
                $profilePic = Storage::url("public/uploads/profiles/$photo");

            $learnerDetails = [
                'fullname'           => implode(' ', [$learner->{UserFields::Firstname}, $learner->{UserFields::Lastname}]),
                'email'              => $learner->email,
                'contact'            => $learner->{UserFields::Contact},
                'address'            => $learner->{UserFields::Address},
                'photo'              => $profilePic,
                'fluencyBadgeIcon'   => $fluencyLevel['Badge Icon'],
                'fluencyBadgeColor'  => $fluencyLevel['Badge Color'],
                'fluencyLevelText'   => $fluencyLevel['Level'],
            ];

            // Return the view with the tutor data
            return view('admin.show-learner', compact('learnerDetails'));
        }
        catch (ModelNotFoundException $e)
        {
            // Return custom 404 page
            return view('errors.404');
        }
        catch (Exception $e)
        {
            // Return custom 404 page
            return view('errors.500');
        }
    }

    private function getLearners($options = [])
    {
        $options = array_merge(['min_entries' => 10], $options);

        // Get all existing learners
        $fields = [
            'users.id',
            UserFields::Firstname,
            UserFields::Lastname,
            UserFields::Photo,
            UserFields::Role,
            ProfileFields::Fluency
        ];

        // Build the query
        $learners = User::select($fields)
            ->join('profiles', 'users.id', '=', 'profiles.'.ProfileFields::UserId)
            ->where(UserFields::Role, User::ROLE_LEARNER)
            ->withCount(['bookingsAsLearner as totalTutors' => function($query) {
                $query->whereHas('tutor', function($query) {
                    $query->where(UserFields::Role, User::ROLE_TUTOR);
                });
            }])
            ->orderBy(UserFields::Firstname, 'ASC');

        if (array_key_exists('fluency', $options) && $options['fluency'] != -1) {
            $learners = $learners->where(ProfileFields::Fluency, $options['fluency']);
        }

        if (array_key_exists('search', $options))
        {
            $searchWord = $options['search'];
            $learners = $learners->where(function ($query) use ($searchWord) {
                $query->where(UserFields::Firstname, 'LIKE', "%$searchWord%")
                      ->orWhere(UserFields::Lastname, 'LIKE', "%$searchWord%");
            });
        }

        // Get the results
        $learners = $learners->paginate($options['min_entries']);

        $defaultPic     = asset('assets/img/default_avatar.png');
        $fluencyFilter  = $this->getFluencyFilters();

        foreach ($learners as $key => $obj)
        {
            $obj->name = implode(' ', [$obj->{UserFields::Firstname}, $obj->{UserFields::Lastname}]);
            $obj['hashedId'] = $this->learnerHashIds->encode($obj->id);

            $fluency = $obj->{ProfileFields::Fluency};
            $obj['fluencyStr']   = $fluencyFilter[$fluency];
            $obj['fluencyBadge'] = FluencyLevels::Learner[$fluency]['Badge Color'];

            $photo = $obj->{UserFields::Photo};
            $obj['photo'] = $defaultPic;

            if (!empty($photo))
            {
                $obj['photo'] = Storage::url("public/uploads/profiles/$photo");
            }
        }

        return [
            'learnersSet'   => $learners,
            'fluencyFilter' => $fluencyFilter
        ];
    }

    public function filterLearners(Request $request)
    {
        $rules = [
            'search-keyword' => 'nullable|string|max:64',
            'select-entries' => 'required|integer|in:10,25,50,100',
            'select-fluency' => 'required|integer|in:-1,' . implode(',', array_keys($this->getFluencyFilters()))
        ];

        $validator = Validator::make($request->all(), $rules);
        $error500  = response()->view('errors.500', [], 500);

        if ($validator->fails())
            return $error500;

        // Select Options validation
        $inputs = $validator->validated();
        $filter = [
            'min_entries'   => $inputs['select-entries'],
            'fluency'       => $inputs['select-fluency']
        ];

        if (!empty($inputs['search-keyword']))
        {
            $filter['search'] = $inputs['search-keyword'];
        }

        $request->session()->put('learner-filter-inputs', $inputs);
        $request->session()->put('learner-filter', $filter);

        return redirect()->route('admin.learners-index');
    }

    public function clearFilters(Request $request)
    {
        // Forget multiple session variables in one line
        $request->session()->forget(['learner-filter', 'learner-filter-inputs']);

        return redirect()->route('admin.learners-index');
    }

    private function getFluencyFilters()
    {
        $fluencyFilter = [];

        foreach (FluencyLevels::Learner as $key => $obj)
        {
            $fluencyFilter[$key] = $obj['Level'];
        }

        return $fluencyFilter;
    }
}
