<?php

namespace App\Services;

use App\Http\Utils\ChatifyUtils;
use App\Http\Utils\Constants;
use App\Http\Utils\FluencyLevels;
use App\Http\Utils\HashSalts;
use App\Http\Utils\Helper;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\BookingRequestFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\RatingsAndReviewFields;
use App\Models\FieldNames\UserFields;
use App\Models\RatingsAndReview;
use App\Models\User;
use Exception;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TutorSvc extends CommonModelService
{
    private static $hashids = null;

    public function __construct(
        private LearnerSvc $learnerSvc
    )
    {
        // Property promotion
    }
    //
    //==========================================
    //      Q U E R Y  B U I L D E R S
    //==========================================
    //
    /**
     * Base query for getting the list of tutors, including basic filtrations
     */
    private function query_GetTutors(array $options) : Builder
    {
        // Get all existing tutors
        $fields = [
            'users.id',
            UserFields::Firstname,
            UserFields::Lastname,
            UserFields::Photo,
            UserFields::Role,
            ProfileFields::Disability
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
                $query->where(ProfileFields::Disability, $options['fluency']);
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

        return $builder;
    }
    /**
     * Base query for getting the details of tutors
     */
    private function query_ShowTutor(int $tutorId) : Builder
    {
        $userFields = Helper::prependFields('users.', [
            'id',
            'email',
            UserFields::Firstname,
            UserFields::Lastname,
            UserFields::Address,
            UserFields::Photo,
            UserFields::IsVerified,
            UserFields::Contact
        ]);

        $builder = User::select(array_merge($userFields, [
            DB::raw('(SELECT IFNULL(FORMAT(AVG(' . RatingsAndReviewFields::Rating . '), 1), 0.0) FROM ratings_and_reviews WHERE ' . RatingsAndReviewFields::TutorId . ' = users.id) as averageRating'),
            DB::raw('(SELECT COUNT(DISTINCT ' . BookingFields::LearnerId . ') FROM bookings WHERE ' . BookingFields::TutorId . ' = users.id) as totalLearners')
        ]))
        ->with([
            'profile',
            'receivedRatings' => function ($query) {
                $query->select([
                    RatingsAndReviewFields::TutorId,
                    RatingsAndReviewFields::LearnerId,
                    RatingsAndReviewFields::Rating,
                    RatingsAndReviewFields::Review,
                    DB::raw("DATE_FORMAT(ratings_and_reviews.created_at, '%b %d, %Y') as reviewDate")
                ])
                ->with(['learner' => function($subQuery)
                {
                    $subQuery->select([
                        'id',
                        UserFields::Firstname,
                        UserFields::Lastname,
                        UserFields::Photo
                    ]);
                }])
                ->paginate(10);
            },
            'receivedRatings.learner' => function ($query) {
                $query->select(['id']); // Only retrieve learner ID
            }
        ])
        ->where('users.id', $tutorId)
        ->where(UserFields::Role, User::ROLE_TUTOR);

        return $builder;
    }
    //
    //==========================================
    //      F O R M A T T E D   D A T A
    //==========================================
    //
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
     * Beautify the returned dataset into human readable form
     */
    private function mapTutorsQueryResult($query, $minEntries = 10)
    {
        return $query->paginate($minEntries)->through(function($result)
        {
            //$fluency = FluencyLevels::Tutor[$result->{ProfileFields::Disability}];

            $returnData = [
                'tutorId'       => self::toHashedId($result->id),
                'chatUserId'    => ChatifyUtils::toHashedChatId($result->id),
                'name'          => implode(' ', [$result->{UserFields::Firstname}, $result->{UserFields::Lastname}]),
                'photo'         => User::getPhotoUrl($result->{UserFields::Photo}),
                //'email'         => $result->email,
                // 'fluencyStr'    => $fluency['Level'],
                // 'fluencyBadge'  => $fluency['Badge Color'],
                // 'fluencyDesc'   => $fluency['Description'],
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
    //
    //==========================================
    //    C O N T R O L L E R   A C T I O N S
    //==========================================
    //
    public function showTutorDetails($hashedId)
    {
        $error500 = response()->view('errors.500', [], 500);
        $error404 = response()->view('errors.404', [], 404);

        try
        {
            // Fetch the tutor along with their profile
            $tutorId    = self::toRawId($hashedId);
            $learnerId  = Auth::user()->id;
            $tutor      = $this->query_ShowTutor($tutorId)->firstOrFail();

            // Statistics (Both numeric and non-numeric)
            $fluencyLevel   = FluencyLevels::Tutor[$tutor->profile->{ProfileFields::Disability}];
            $totalLearners  = $tutor->totalLearners;
            $starRatings    = Constants::StarRatings;
            $learnerReview  = $this->learnerSvc->getReviewOnTutor($tutorId, $learnerId);
            $hireStatus     = $this->getHireStatus($learnerId, $tutorId);
            $totalReviews   = 0; // The total textual review comments

            $totalIndividualRatings = [
                '5' => 0,
                '4' => 0,
                '3' => 0,
                '2' => 0,
                '1' => 0
            ];
            $skills = [];

            if ($tutor->profile->{ProfileFields::Skills})
            {
                foreach($tutor->profile->{ProfileFields::Skills} as $skill)
                {
                    $skills[] = User::SOFT_SKILLS[$skill];
                }
            }

            // Flatten (merge) the eager-loaded learner's info into the parent collection.
            // We do this to retrieve only the properties we need.
            $receivedRatings = $tutor->receivedRatings->map(function($item) use(&$totalReviews, &$totalIndividualRatings)
            {
                $transformed = [
                    'learnerName'       => $item->learner->name,
                    'learnerPhoto'      => $item->learner->photoUrl,
                    'learnerReviewName' => $item->learner->possessiveFirstName
                ];

                // Count total individual star-ratings
                $totalIndividualRatings[$item->rating] ++;

                // Count textual reviews
                if (!empty($item->review))
                    $totalReviews++;

                // Remove tutor_id and learner_id from the parent collection
                unset($item->tutor_id, $item->learner_id, $item->learner);

                // Merge the transformed collection with the parent collection
                return array_merge($item->toArray(), $transformed);
            });

            // Find the highest individual rating
            $highestIndivRating = max( array_values($totalIndividualRatings) );

            // Final transformed data
            $tutorDetails = [
                'hashedId'           => $hashedId,
                'firstname'          => $tutor->{UserFields::Firstname},
                'possessiveName'     => $tutor->possessiveFirstName, //User::toPossessiveName($tutor->{UserFields::Firstname}),
                'fullname'           => $tutor->name,//implode(' ', [$tutor->{UserFields::Firstname}, $tutor->{UserFields::Lastname}]),
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
                'photo'              => $tutor->photoUrl,//User::getPhotoUrl($tutor->{UserFields::Photo}),
                'hireStatus'         => $hireStatus,
                'fluencyBadgeIcon'   => $fluencyLevel['Badge Icon'],
                'fluencyBadgeColor'  => $fluencyLevel['Badge Color'],
                'fluencyLevelText'   => $fluencyLevel['Level'],
                'averageRating'      => $tutor->averageRating,
                'totalReviews'       => $totalReviews,
                'ratingsAndReviews'  => $receivedRatings,
                'totalIndividualRatings'  => $totalIndividualRatings,
                'highestIndividualRating' => $highestIndivRating
            ];

            // Return the view with the tutor data
            return view('tutor.show', compact('tutorDetails', 'totalLearners', 'starRatings', 'learnerReview'));
        }
        catch (ModelNotFoundException $e)
        {
            // Return custom 404 page
            return $error404;
        }
        catch (Exception $ex)
        {
            error_log($ex->getMessage());
            // Return custom 404 page
            return $error500;
        }
    }
    //
    //==========================================
    //    S E R V I C E   M E T H O D S
    //==========================================
    //
    /**
     * Get the hire relation between learner and tutor
     */
    public function getHireStatus($learnerId, $tutorId)
    {
        $hireStatus = -1;

        if (Booking::where(BookingFields::TutorId, $tutorId)
            ->where(BookingFields::LearnerId, $learnerId)
            ->exists())
        {
            // Tutor is hired by learner ...
            $hireStatus = 1;
        }
        else if (BookingRequest::where(BookingRequestFields::ReceiverId, $tutorId)
            ->where(BookingRequestFields::SenderId, $learnerId)
            ->exists())
        {
            // Tutor havent accepted the hire request yet
            $hireStatus = 2;
        }

        return $hireStatus;
    }
    //
    //==========================================
    //              H A S H I N G
    //==========================================
    //
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
        return $hashid->decode($hashedId)[0] ?? 0;
    }
}
