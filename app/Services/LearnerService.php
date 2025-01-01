<?php

namespace App\Services;

use App\Http\Utils\HashSalts;
use App\Models\Booking;
use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LearnerService extends UserService
{
    const Role = User::ROLE_LEARNER;

    // public function upgradeAsTutor($learnerId, $upgradeData)
    // {
    //     return $this->updateProfile($learnerId, $upgradeData);
    // }

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
}
