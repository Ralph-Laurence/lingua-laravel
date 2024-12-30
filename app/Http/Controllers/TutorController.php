<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TutorController extends Controller
{
    private $hashids;

    public function __construct()
    {
        $this->hashids = new Hashids(HashSalts::Tutors, 10);
    }

    public function listTutors()
    {
        $user = Auth::user();

        // Get all list of tutors
        // Perform the query with eager loading
        $listOfTutors = User::where(UserFields::Role, User::ROLE_TUTOR)
                    ->where(UserFields::IsVerified, 1)
                    ->with('profile')
                    ->get();

        // Store here the ids of tutors that were already hired
        // Fetch the hired tutors' IDs as a single dimension indexed array
        $hiredTutors = Booking::where(BookingFields::LearnerId, $user->id)
                     ->pluck(BookingFields::TutorId)
                     ->toArray();

        $tutors = [];

        foreach ($listOfTutors as $key => $obj)
        {
            $photo   = $obj->{UserFields::Photo};
            $tutorId = $obj['id'];
            $isHired = !empty($hiredTutors) && in_array($tutorId, $hiredTutors);

            $fluencyLevel = FluencyLevels::Tutor[$obj->profile->{ProfileFields::Fluency}];

            if (empty($photo))
                $photo = asset('assets/img/default_avatar.png');

            else
                $photo = Storage::url("public/uploads/profiles/$photo");

            $tutors[] = [
                'hashedId'      => $this->hashids->encode($tutorId),
                'profilePic'    => $photo,
                'fullname'      => implode(' ', [$obj->{UserFields::Firstname}, $obj->{UserFields::Lastname}]),
                'verified'      => $obj->{UserFields::IsVerified} == 1,
                'bioNotes'      => $obj->profile->{ProfileFields::Bio} ?? "No Bio Available",
                'isHired'       => $isHired,

                'hiredIndicator'     => !$isHired ? 'd-none' : '',
                'fluencyBadgeIcon'   => $fluencyLevel['Badge Icon'],
                'fluencyBadgeColor'  => $fluencyLevel['Badge Color'],
                'fluencyLevelText'   => $fluencyLevel['Level'],
            ];
        }

        return view('learner.list-tutors')
               ->with('tutors', $tutors)
               ->with('totalTutors', count($tutors))
               ->with('hashids', $this->hashids);
    }

    public function hireTutor(Request $request)
    {
        $hashedId = $request->input('tutor_id');
        $error = response()->view('errors.500', [], 500);

        if (empty($hashedId))
            return $error;

        $decodeTutorId = $this->hashids->decode($hashedId);

        if (empty($decodeTutorId))
            return $error;

        $tutorId = $decodeTutorId[0];

        try
        {
            $tutorExists = User::where('id', $tutorId)->exists();

            if (!$tutorExists) {
                return $error;
            }

            Booking::create([
                BookingFields::LearnerId => Auth::user()->id,
                BookingFields::TutorId   => $tutorId
            ]);

            return redirect(route('tutor.show', $hashedId));
        }
        catch (Exception $ex)
        {
            return $error;
        }
    }

    public function endContract(Request $request)
    {
        $hashedId = $request->input('tutor_id');
        $error = response()->view('errors.500', [], 500);

        if (empty($hashedId))
            return $error;

        $decodeTutorId = $this->hashids->decode($hashedId);

        if (empty($decodeTutorId))
            return $error;

        $tutorId = $decodeTutorId[0];

        try
        {
            $tutorExists = User::where('id', $tutorId)->exists();

            if (!$tutorExists) {
                return $error;
            }

            Booking::where(BookingFields::LearnerId, Auth::user()->id)
                   ->where(BookingFields::TutorId, $tutorId)
                   ->delete();

            return redirect(route('tutor.show', $hashedId));
        }
        catch (Exception $ex)
        {
            return $error;
        }
    }

    public function show($id)
    {
        try
        {
            // Decode the hashed ID
            $decodedId = $this->hashids->decode($id);

            // Check if the ID is empty
            if (empty($decodedId)) {
                return view('errors.404');
            }

            // Fetch the tutor along with their profile
            $tutorId      = $decodedId[0];
            $tutor        = User::with('profile')->findOrFail($tutorId);
            $fluencyLevel = FluencyLevels::Tutor[$tutor->profile->{ProfileFields::Fluency}];

            error_log($tutor);
            // Check if the tutor was already hired
            $isHired = Booking::where(BookingFields::TutorId, $tutorId)
                   ->where(BookingFields::LearnerId, Auth::user()->id)
                   ->exists();

            $photo = $tutor->{UserFields::Photo};
            $profilePic = asset('assets/img/default_avatar.png');

            if (!empty($photo))
                $profilePic = Storage::url("public/uploads/profiles/$photo");


            $skills = [];

            if ($tutor->profile->{ProfileFields::Skills})
            {
                foreach($tutor->profile->{ProfileFields::Skills} as $skill)
                {
                    $skills[] = User::SOFT_SKILLS[$skill];
                }
            }

            $tutorDetails = [
                'hashedId'           => $id,
                'firstname'          => $tutor->{UserFields::Firstname},
                'fullname'           => implode(' ', [$tutor->{UserFields::Firstname}, $tutor->{UserFields::Lastname}]),
                'email'              => $tutor->email,
                'contact'            => $tutor->{UserFields::Contact},
                'address'            => $tutor->{UserFields::Address},
                'verified'           => $tutor->{UserFields::IsVerified} == 1,
                'work'               => $tutor->profile->{ProfileFields::Experience},
                'bio'                => $tutor->profile->{ProfileFields::Bio},
                'about'              => $tutor->profile->{ProfileFields::About},
                'education'          => $tutor->profile->{ProfileFields::Education},
                'certs'              => $tutor->profile->{ProfileFields::Certifications},
                'skills'             => $skills,
                'photo'              => $profilePic,
                'isHired'            => $isHired,
                'fluencyBadgeIcon'   => $fluencyLevel['Badge Icon'],
                'fluencyBadgeColor'  => $fluencyLevel['Badge Color'],
                'fluencyLevelText'   => $fluencyLevel['Level'],
            ];

            // Return the view with the tutor data
            return view('tutors.show', compact('tutorDetails'));
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
}
