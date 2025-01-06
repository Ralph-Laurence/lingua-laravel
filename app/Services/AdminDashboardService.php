<?php

namespace App\Services;

use App\Http\Utils\HashSalts;
use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\UserFields;
use App\Models\PendingRegistration;
use App\Models\User;
use Hashids\Hashids;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    const Role = User::ROLE_TUTOR;

    private $tutorHashIds;
    private $learnerHashIds;

    function __construct()
    {
        $this->tutorHashIds = new Hashids(HashSalts::Tutors, 10);
        $this->learnerHashIds = new Hashids(HashSalts::Learners, 10);
    }

    public function getTotals()
    {
        $strRoleTutor    = User::ROLE_TUTOR;
        $strRoleLearner  = User::ROLE_LEARNER;
        $strFieldRole    = UserFields::Role;
        $strFieldFname   = UserFields::Firstname;
        $strFieldLname   = UserFields::Lastname;
        $strFieldPhoto   = UserFields::Photo;
        $strFieldLrnId   = BookingFields::LearnerId;
        $strFieldTutId   = BookingFields::TutorId;

        $viewData = [];

        $totals = DB::table('users')
            ->select(
                DB::raw("SUM($strFieldRole = '$strRoleTutor')   AS total_tutors"),
                DB::raw("SUM($strFieldRole = '$strRoleLearner') AS total_learners")
            )
            ->first();

        $topTutors = DB::table('bookings')
            ->select([
                'tutors.id',
                $strFieldPhoto,
                "$strFieldFname as tutor_fname",
                DB::raw("CONCAT($strFieldFname,' ',$strFieldLname) as tutor_name") ,
                DB::raw("COUNT(bookings.$strFieldLrnId) AS total_students")
            ])
            ->join('users AS tutors', "bookings.$strFieldTutId", '=', 'tutors.id')
            ->groupBy('tutors.id', $strFieldFname, $strFieldLname, $strFieldPhoto)
            ->orderBy('total_students', 'desc')
            ->limit(5)
            ->get();

        $learnerWithMostTutors = DB::table('bookings')
            ->select([
                'learner.id',
                $strFieldPhoto,
                DB::raw("CONCAT($strFieldFname,' ',$strFieldLname) as learner_name") ,
                DB::raw("COUNT(bookings.$strFieldTutId) AS total_tutors")
            ])
            ->join('users AS learner', "bookings.$strFieldLrnId", '=', 'learner.id')
            ->groupBy('learner.id', $strFieldFname, $strFieldLname, $strFieldPhoto)
            ->orderBy('total_tutors', 'desc')
            ->first();

        $tutorWithMostLearners = DB::table('bookings')
            ->select([
                'tutor.id',
                $strFieldPhoto,
                DB::raw("CONCAT($strFieldFname,' ',$strFieldLname) as tutor_name") ,
                DB::raw("COUNT(bookings.$strFieldLrnId) AS total_learners")
            ])
            ->join('users AS tutor', "bookings.$strFieldTutId", '=', 'tutor.id')
            ->groupBy('tutor.id', $strFieldFname, $strFieldLname, $strFieldPhoto)
            ->orderBy('total_learners', 'desc')
            ->first();

        if ($tutorWithMostLearners)
        {
            $topTutorId = $this->tutorHashIds->encode($tutorWithMostLearners->id);
            $topTutor = [
                'tutorDetails'   => route('admin.tutors-show', $topTutorId),
                'tutorName'      => $tutorWithMostLearners->tutor_name,
                'tutorPhoto'     => User::getPhotoUrl($tutorWithMostLearners->photo),
                'totalLearners'  => $tutorWithMostLearners->total_learners,
            ];
            $viewData['topTutor'] = $topTutor;
        }

        if ($topTutors->isNotEmpty())
        {
            $topTutorsArr = [];

            foreach ($topTutors as $k => $obj)
            {
                $topTutorsArr[] = [
                    'tutorDetails'  => route('admin.tutors-show', $this->tutorHashIds->encode($obj->id)),
                    'tutorFname'    => $obj->tutor_fname,
                    'tutorName'     => $obj->tutor_name,
                    'tutorPhoto'    => User::getPhotoUrl($obj->photo),
                    'totalLearners' => $obj->total_students,
                ];
            }

            $viewData['topTutors'] = json_encode($topTutorsArr);
        }

        if ($learnerWithMostTutors)
        {
            $topLearnerId = $this->learnerHashIds->encode($learnerWithMostTutors->id);
            $topLearner = [
                'learnerDetails'   => route('admin.learners-show', $topLearnerId),
                'learnerName'      => $learnerWithMostTutors->learner_name,
                'learnerPhoto'     => User::getPhotoUrl($learnerWithMostTutors->photo),
                'totalTutors'      => $learnerWithMostTutors->total_tutors,
            ];
            $viewData['topLearner']    = $topLearner;
        }

        $totalPending = PendingRegistration::count();

        $viewData['totalTutors']   = $totals->total_tutors;
        $viewData['totalLearners'] = $totals->total_learners;
        $viewData['totalMembers']  = $totals->total_learners + $totals->total_tutors;
        $viewData['totalPending']  = $totalPending;

        return $viewData;
    }
}
