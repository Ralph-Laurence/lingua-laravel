<?php

namespace App\Services;

use App\Http\Utils\Constants;
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
use Illuminate\Support\Facades\Auth;

class LearnerSvc extends CommonModelService
{
    private static $hashids = null;

    //
    //==========================================
    //      Q U E R Y  B U I L D E R S
    //==========================================
    //
    /**
     * Base query for getting the list of learners,
     * including basic filtrations
     */
    private function query_GetLearners($options)
    {
        // Get all existing learners
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
        $hasDisabilityFilter = array_key_exists('disability', $options) && $options['disability'] != -1;

        // Build the query
        $builder = User::select($fields)
            ->join('profiles', 'users.id', '=', 'profiles.'.ProfileFields::UserId)
            ->where(UserFields::Role, User::ROLE_LEARNER)
            ->orderBy(UserFields::Firstname, 'ASC')
            ->withCount(['bookingsAsLearner as totalTutors' => function($query)
            {
                $query->whereHas('tutor', function($query)
                {
                    $query->where(UserFields::Role, User::ROLE_TUTOR);
                });
            }])
            ->when($hasDisabilityFilter, function($query) use($options)
            {
                $query->where(ProfileFields::Disability, $options['disability']);
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
    //
    //==========================================
    //      F O R M A T T E D   D A T A
    //==========================================
    //
    /**
     * Beautify the returned dataset into human readable form
     */
    private function mapLearnersQueryResult($query, $minEntries = 10)
    {
        return $query->paginate($minEntries)->through(function($result)
        {
            $badges = Constants::DisabilitiesBadge;
            $disability = $result->{ProfileFields::Disability};
            $impairments = User::getDisabilityFilters();

            $returnData = [
                'learnerId'     => self::toHashedId($result->id),
                'name'          => implode(' ', [$result->{UserFields::Firstname}, $result->{UserFields::Lastname}]),
                'photo'         => User::getPhotoUrl($result->{UserFields::Photo}),
                'email'         => $result->email,
                'totalTutors'   => $result->totalTutors,
                'disabilityBadge' => '',
                'disability'      => $impairments[$disability],
                'disabilityId'    => $disability
            ];

            if (array_key_exists($disability, $badges))
                $returnData['disabilityBadge'] = $badges[$disability];

            if (isset($result->contact))
                $returnData['contact'] = $result->{UserFields::Contact};

            return $returnData;
        });
    }
    /**
     * Retrieve all students for viewing by tutor
     */
    public function getLearnersListForTutor($options)
    {
        // If min entries is not defined, give the fallback value
        if (!isset($options['minEntries']))
            $options['minEntries'] = Constants::MinPageEntries;

        // Retrieve all learners (fallback default)
        $query = $this->query_GetLearners($options);

        // Exclude learners that are already connected to the tutor
        if (isset($options['exceptConnected']))
        {
            $query->whereDoesntHave('bookingsAsLearner', function($subquery) use($options) {
                $subquery->where(BookingFields::TutorId, $options['exceptConnected']);
            });
        }

        // Get only learners connected to the tutor
        if (isset($options['mode']) && $options['mode'] === 'myLearners')
        {
            $query->whereHas('bookingsAsLearner', function($subquery) use($options) {
                $subquery->where(BookingFields::TutorId, $options['tutorId']);
            });
        }

        return $this->mapLearnersQueryResult($query, $options['minEntries']);
    }
    //
    //==========================================
    //    C O N T R O L L E R   A C T I O N S
    //==========================================
    //
    /**
     * Delete the review made by learner on target tutor
     */
    public function deleteTutorReview($hashedId)
    {
        $tutorId = TutorSvc::toRawId($hashedId);

        if (empty($tutorId))
            return response()->view('errors.404', [], 404);

        try
        {
            $learnerId = Auth::user()->id;
            $delete    = RatingsAndReview::where(RatingsAndReviewFields::LearnerId, $learnerId)
                        ->where(RatingsAndReviewFields::TutorId, $tutorId)
                        ->delete();

            if ($delete)
                session()->flash('review_msg', 'Your review has been deleted.');

            else
                session()->flash('review_msg', "We're unable to remove your review because of a technical error. Please try again later.");

            return redirect(route('tutor.show', $hashedId));
        }
        catch (Exception $ex)
        {
            return response()->view('errors.500', [], 500);
        }
    }
    /**
     * This must be accessed via Asynchronous GET
     */
    public function fetchLearnerDetails($learnerId)
    {
        // Get all existing learners
        $fields = [
            'users.id',
            'email',
            UserFields::Address,
            UserFields::Firstname,
            UserFields::Lastname,
            UserFields::Contact,
            UserFields::Photo,
            ProfileFields::Disability
        ];

        try
        {
            // Build the query
            $learner = User::select($fields)
                ->join('profiles', 'users.id', '=', 'profiles.'.ProfileFields::UserId)
                ->where('users.id', $learnerId)
                ->withCount(['bookingsAsLearner as totalTutors' => function($query)
                {
                    $query->whereHas('tutor', function($query)
                    {
                        $query->where(UserFields::Role, User::ROLE_TUTOR);
                    });
                }])
                ->firstOrFail();

            return [
                'learnerId'     => self::toHashedId($learner->id),
                'name'          => implode(' ', [$learner->{UserFields::Firstname}, $learner->{UserFields::Lastname}]),
                'photo'         => User::getPhotoUrl($learner->{UserFields::Photo}),
                'email'         => $learner->email,
                'contact'       => $learner->{UserFields::Contact},
                'address'       => $learner->{UserFields::Address}
            ];
        }
        catch (ModelNotFoundException $ex)
        {
            return response()->json([
                'message' => 'The learner does not exist or has been deleted.'
            ], 404);
        }
        catch (Exception $ex)
        {
            return response()->json([
                'message' => "There was a problem while trying to read the learner's data."
            ], 500);
        }
    }
    //
    //==========================================
    //    S E R V I C E   M E T H O D S
    //==========================================
    //
    /**
     * Retrieve the rating and review created by learner for target tutor.
     */
    public function getReviewOnTutor($tutorId, $learnerId)
    {
        $res = RatingsAndReview::select([
                RatingsAndReviewFields::Rating,
                RatingsAndReviewFields::Review
            ])
            ->where(RatingsAndReviewFields::TutorId, $tutorId)
            ->where(RatingsAndReviewFields::LearnerId, $learnerId)
            ->first();

        return $res;
    }
    //
    //==========================================
    //              H A S H I N G
    //==========================================
    //
    public static function getHashidInstance()
    {
        if (self::$hashids == null)
            self::$hashids = new Hashids(HashSalts::Learners, 10);

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
