<?php

namespace App\Http\Controllers;

use App\Http\Utils\FluencyLevels;
use App\Http\Utils\HashSalts;
use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use App\Models\PendingRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Hashids\Hashids;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    private $hashids;

    public function __construct()
    {
        $this->hashids = new Hashids(HashSalts::Tutors, 10);
    }

    function index()
    {
        return view('admin.dashboard');
    }

    public function tutors_index(Request $request)
    {  
        $result = null;
        
        if ($request->session()->has('result'))
            $result = $request->session()->get('result');
        else
            $result = $this->getTutors();

        $tutors = $result['tutorsSet'];
        $fluencyFilter = $result['fluencyFilter'];

        if ($request->session()->has('inputs'))
        {
            // Remove the session variable to prevent access after the first visit.
            // Which means GET and CLEAR the session data
            // $inputs = $request->session()->pull('inputs');

            // However, This will retain the old session data...
            $inputs = $request->session()->get('inputs');
            $hasFilter = true;

            return view('admin.tutors', compact('tutors', 'fluencyFilter', 'inputs', 'hasFilter'));
        }

        return view('admin.tutors', compact('tutors', 'fluencyFilter'));
    }

    public function tutors_filter(Request $request)
    {
        $rules = [
            'search-keyword' => 'nullable|string|max:64',
            'select-status'  => 'required|integer|in:0,1,2',
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
            'status'        => $inputs['select-status'],
            'min_entries'   => $inputs['select-entries'],
            'fluency'       => $inputs['select-fluency']
        ];

        if (!empty($inputs['search-keyword']))
        {
            $filter['search'] = $inputs['search-keyword'];
        }

        $request->session()->put('inputs', $inputs);
        $request->session()->put('result', $this->getTutors($filter));

        return redirect()->route('admin.tutors-index');
    }

    public function tutors_clear_filter(Request $request)
    {
        if ($request->session()->has('result'))
            $request->session()->forget('result');

        if ($request->session()->has('inputs'))
            $request->session()->forget('inputs');

        return redirect()->route('admin.tutors-index');
    }

    private function getTutors($options = [])
    {
        $options = array_merge(['min_entries' => 10], $options);
    
        // Get the ids of all users with pending registration
        $pending = PendingRegistration::pluck(ProfileFields::UserId)->toArray();
    
        // Get all existing tutors
        $fields = [
            'users.id',
            UserFields::Firstname,
            UserFields::Lastname,
            UserFields::Photo,
            UserFields::Role,
            UserFields::IsVerified,
            ProfileFields::Fluency
        ];
    
        // Build the query
        $tutors = User::select($fields)
                    ->join('profiles', 'users.id', '=', 'profiles.'.ProfileFields::UserId)
                    // Filter based on status
                    ->when(isset($options['status']) && $options['status'] != 0, function($query) use ($pending, $options)
                    {
                        if ($options['status'] == 1) // Pending
                            return $query->whereIn('users.id', $pending);
                        
                        else if ($options['status'] == 2) // Verified
                            return $query->where(UserFields::IsVerified, true);    
                    },
                    function($query) use ($pending)
                    {
                        // If no status filter or status filter is 0 (ALL), select both
                        return $query->where(function($query) use ($pending)
                        {
                            $query->where(UserFields::Role, User::ROLE_TUTOR)
                                  ->orWhereIn('users.id', $pending);
                        });
                    })
                    ->withCount(['bookingsAsTutor as totalStudents' => function($query)
                    {
                        $query->whereHas('learner', function($query)
                        {
                            $query->where(UserFields::Role, User::ROLE_LEARNER);
                        });
                    }])
                    ->orderBy(UserFields::Firstname, 'ASC');
    
        if (array_key_exists('fluency', $options) && $options['fluency'] != -1)
        {
            $tutors = $tutors->where(ProfileFields::Fluency, $options['fluency']);
        }
        
        if (array_key_exists('search', $options))
        {
            $searchWord = $options['search'];
            $tutors = $tutors->where(UserFields::Firstname, 'LIKE', "%$searchWord%")
                    ->orWhere(UserFields::Lastname, 'LIKE', "%$searchWord%");
        }
    
        // Get the results
        $tutors = $tutors->paginate($options['min_entries']);
    
        $defaultPic = asset('assets/img/default_avatar.png');
        $fluencyFilter = $this->getFluencyFilters();
    
        foreach ($tutors as $key => $obj)
        {
            $obj['totalStudents'] = $obj->totalStudents;
    
            if ($obj->{UserFields::IsVerified} == 1)
            {
                $obj['statusStr']   = 'Verified';
                $obj['statusBadge'] = 'bg-primary';
            }

            else if (!$obj->{UserFields::IsVerified} || in_array($obj->id, $pending) )
            {
                $obj['statusStr']   = 'Pending';
                $obj['statusBadge'] = 'bg-warning text-dark';
            }

            $obj->name  = implode(' ', [$obj->{UserFields::Firstname}, $obj->{UserFields::Lastname}]);
            $obj->id    = $this->hashids->encode($obj->id);
            
            $fluency = $obj->{ProfileFields::Fluency};
            $obj['fluencyStr']   = $fluencyFilter[$fluency];
            $obj['fluencyBadge'] = FluencyLevels::Tutor[$fluency]['Badge Color'];

            $photo = $obj->{UserFields::Photo};
            $obj['photo'] = $defaultPic;
    
            if (!empty($photo))
            {
                $obj['photo'] = Storage::url("public/uploads/profiles/$photo");
            }
        }
    
        return [
            'tutorsSet'     => $tutors,
            'fluencyFilter' => $fluencyFilter
        ];
    }

    private function getFluencyFilters()
    {
        $fluencyFilter = [];

        foreach (FluencyLevels::Tutor as $key => $obj)
        {
            $fluencyFilter[$key] = $obj['Level'];
        }

        return $fluencyFilter;
    }
}
