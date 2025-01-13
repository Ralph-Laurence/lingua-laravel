<?php

namespace App\Services;

use App\Http\Utils\ChatifyUtils;
use App\Http\Utils\Constants;
use App\Http\Utils\FluencyLevels;
use App\Http\Utils\HashSalts;
use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\RatingsAndReviewFields;
use App\Models\FieldNames\UserFields;
use App\Models\RatingsAndReview;
use App\Models\User;
use Exception;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class TutorSvc extends CommonModelService
{
    private static $hashids = null;

    function __construct()
    {

    }

    // public function listTutorsForLearner()
    // {
    //     $query = $this
    //     $data           = $this->getTutors($learnerId);
    //     $tutors         = $data['tutors'];
    //     $totalTutors    = $data['totalTutors'];
    //     $fluencyFilters = $data['fluencyFilters'];
    //     $hashids        = $data['hashids'];

    //     return view('learner.find-tutors', compact('tutors', 'totalTutors', 'fluencyFilters', 'hashids'));
    // }

    /**
     * Retrieve all students for viewing by tutor
     */
    public function getTutorsListForLearner($options)
    {
        // If min entries is not defined, give the fallback value
        if (!isset($options['minEntries']))
            $options['minEntries'] = Constants::MinPageEntries;

        // Retrieve all tutors (fallback default)
        $query = $this->query_GetTutors($options);

        // Exclude tutors that are already connected to the learner
        if (isset($options['exceptConnected']))
        {
            $query->whereDoesntHave('bookingsAsTutor', function($subquery) use($options)
            {
                $subquery->where(BookingFields::LearnerId, $options['exceptConnected']);
            });
        }

        // Get only tutors connected to the learner
        if (isset($options['mode']) && $options['mode'] === 'myTutors')
        {
            $query->whereHas('bookingsAsTutor', function($subquery) use($options)
            {
                $subquery->where(BookingFields::LearnerId, $options['learnerId']);
            });
        }

        return $this->mapTutorsQueryResult($query, $options['minEntries']);
    }

    /**
     * Base query for getting the list of tutors, including basic filtrations
     */
    private function query_GetTutors($options)
    {
        // Get all existing tutors
        $fields = [
            'users.id',
            UserFields::Firstname,
            UserFields::Lastname,
            UserFields::Photo,
            UserFields::Role,
            ProfileFields::Fluency
        ];

        if (isset($options['extraFields']))
            $fields = array_merge($fields, $options['extraFields']);

        // Filter transformations
        $hasFluencyFilter = array_key_exists('fluency', $options) && $options['fluency'] != -1;

        // Build the query
        $builder = User::select($fields)
            ->join('profiles', 'users.id', '=', 'profiles.'.ProfileFields::UserId)
            ->where(UserFields::Role, User::ROLE_TUTOR)
            ->orderBy(UserFields::Firstname, 'ASC')
            ->withCount(['bookingsAsTutor as totalLearners' => function($query)
            {
                $query->whereHas('learner', function($query)
                {
                    $query->where(UserFields::Role, User::ROLE_LEARNER);
                });
            }])
            ->when(!empty($options['includeRatings']), function($query)
            {
                $query->withCount(['receivedRatings as totalReviews' => function($subquery)
                {
                    // Count only rows where review (comment) is not null
                    $subquery->whereNotNull(RatingsAndReviewFields::Review);
                }]);

                $query->selectRaw('(select FORMAT(avg(' . RatingsAndReviewFields::Rating . '), 1) from `ratings_and_reviews` where `users`.`id` = `ratings_and_reviews`.`' . RatingsAndReviewFields::TutorId. '`) as averageRating');
            })
            ->when(!empty($options['includeDateJoined']), function($query)
            {
                // Format date as 'date_joined'
                $query->addSelect(DB::raw("DATE_FORMAT(users.created_at, '%Y-%m-%d') as date_joined"));
            })
            ->when($hasFluencyFilter, function($query) use($options)
            {
                $query->where(ProfileFields::Fluency, $options['fluency']);
            })
            ->when(!empty($options['search']), function($query) use($options)
            {
                // {empty() can be used to check for key existence or value existence}
                $searchWord = $options['search'];

                $query->where(function ($subquery) use ($searchWord)
                {
                    $subquery->where(UserFields::Firstname, 'LIKE', "%$searchWord%")
                             ->orWhere(UserFields::Lastname, 'LIKE', "%$searchWord%");
                });
            });

        error_log($builder->toSql());
        return $builder;
    }

    /**
     * Beautify the returned dataset into human readable form
     */
    private function mapTutorsQueryResult($query, $minEntries = 10)
    {
        return $query->paginate($minEntries)->through(function($result)
        {
            $fluency = FluencyLevels::Tutor[$result->{ProfileFields::Fluency}];

            $returnData = [
                'tutorId'       => self::toHashedId($result->id),
                'chatUserId'    => ChatifyUtils::toHashedChatId($result->id),
                'name'          => implode(' ', [$result->{UserFields::Firstname}, $result->{UserFields::Lastname}]),
                'photo'         => User::getPhotoUrl($result->{UserFields::Photo}),
                //'email'         => $result->email,
                'fluencyStr'    => $fluency['Level'],
                'fluencyBadge'  => $fluency['Badge Color'],
                'fluencyDesc'   => $fluency['Description'],
                'totalLearners' => $result->totalLearners
            ];

            if (isset($result->{ProfileFields::Bio}))
                $returnData['bioNotes'] = $result->{ProfileFields::Bio};

            if (isset($result->date_joined))
                $returnData['dateJoined'] = $result->date_joined;

            if (isset($result->totalReviews))
            {
                $returnData['reviews'] = $result->totalReviews;
                $returnData['ratings'] = $result->averageRating ?? 0;
            }

            if (isset($result->contact))
                $returnData['contact'] = $result->{UserFields::Contact};

            return $returnData;
        });
    }

    public static function getHashidInstance()
    {
        if (self::$hashids == null)
            self::$hashids = new Hashids(HashSalts::Tutors, 10);

        return self::$hashids;
    }

    public static function toHashedId($rawId)
    {
        $hashid = self::getHashidInstance();
        return $hashid->encode($rawId);
    }

    public static function toRawId($hashedId)
    {
        $hashid = self::getHashidInstance();
        return $hashid->decode($hashedId)[0];
    }
}
