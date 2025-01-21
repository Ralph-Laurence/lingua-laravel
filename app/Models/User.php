<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\BookingRequestFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\RatingsAndReviewFields;
use App\Models\FieldNames\UserFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN     = 0;
    const ROLE_TUTOR     = 1;
    const ROLE_LEARNER   = 2;
    const ROLE_PENDING   = 3;

    const ROLE_STR_ADMIN     = 'Admin';
    const ROLE_STR_TUTOR     = 'Tutor';
    const ROLE_STR_LEARNER   = 'Learner';
    const ROLE_STR_PENDING   = 'Pending';

    const ROLE_MAPPING = [
        self::ROLE_ADMIN     => self::ROLE_STR_ADMIN,
        self::ROLE_TUTOR     => self::ROLE_STR_TUTOR,
        self::ROLE_LEARNER   => self::ROLE_STR_LEARNER,
        self::ROLE_PENDING   => self::ROLE_STR_PENDING,
    ];

    const SOFT_SKILLS = [
        '0'  => 'Accepting Criticism',
        '1'  => 'Adaptability',
        '2'  => 'Analytical Thinking',
        '3'  => 'Assertivenes',
        '4'  => 'Attitude',
        '5'  => 'Communication',
        '6'  => 'Confidence',
        '7'  => 'Creative Thinking',
        '8'  => 'Critical Thinking',
        '9'  => 'Decision Making',
        '10' => 'Discipline',
        '11' => 'Empathy',
        '12' => 'Flexibility',
        '13' => 'Innovation',
        '14' => 'Listening',
        '15' => 'Negotation',
        '16' => 'Organization',
        '17' => 'Persuasion',
        '18' => 'Problem Solving',
        '19' => 'Responsibility',
        '20' => 'Self Assessment',
        '21' => 'Self Management',
        '22' => 'Stress Management',
        '23' => 'Team Building',
        '24' => 'Tolerance',
        '25' => 'Time Management',
        '26' => 'Willing to Learn',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        UserFields::Firstname,
        UserFields::Lastname,
        UserFields::Username,
        UserFields::Contact,
        UserFields::Address,
        UserFields::Role,
        UserFields::Photo,
        UserFields::IsVerified,
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    //
    //---------------------------------------------------
    //                  Relationships
    //---------------------------------------------------
    //

    // The bookings are the established connections
    // between a tutor and learner
    public function bookingsAsTutor() {
        return $this->hasMany(Booking::class, BookingFields::TutorId);
    }

    public function bookingsAsLearner() {
        return $this->hasMany(Booking::class, BookingFields::LearnerId);
    }

    // The booking requests on the other hand are temporary.
    // These must be accepted or rejected by user.
    // Each booking request must be accepted by a user
    // to establish a connection (eg bookings).
    // ONE-TO-MANY ---- Each user can send Many friend request
    public function bookingRequestsSent() {
        return $this->hasMany(BookingRequest::class, BookingRequestFields::SenderId);
    }

    // ONE-TO-MANY ---- Each user can receive Many friend request
    public function bookingRequestsReceived() {
        return $this->hasMany(BookingRequest::class, BookingRequestFields::ReceiverId);
    }

    public function profile() {
        return $this->hasOne(Profile::class, ProfileFields::UserId);
    }

    // Ratings & Reviews Relationships

    // A learner (user) gives a tutor (also user) a rating score
    public function givenRatings() {
        return $this->hasMany(RatingsAndReview::class, RatingsAndReviewFields::LearnerId);
    }

    // The tutor (user) recieves the ratings given by learner (also a user)
    public function receivedRatings() {
        return $this->hasMany(RatingsAndReview::class, RatingsAndReviewFields::TutorId);
    }

    // Pending Email Updates Relationships
    public function pendingEmailUpdate() : HasOne
    {
        return $this->hasOne(PendingEmailUpdate::class);
    }
    //
    //---------------------------------------------------
    //                  Helper Functions
    //---------------------------------------------------
    //
    // Other model methods and properties

    /* Get the short abbreviated name */
    public static function toShortName($firstName, $lastName)
    {
        // Take the first character of the last name
        $lastNameInitial = strtoupper(mb_substr($lastName, 0, 1)) . '.';

        return "{$firstName} {$lastNameInitial}";
    }

    // Add an 's or not. This is depending on the name's last letter.
    public static function toPossessiveName($name)
    {
        return $name . (substr($name, -1) === 's' ? "'" : "'s");
    }

    /**
     * Get the url of user's photo. Returns the default if photo doesn't exist.
     */
    public static function getPhotoUrl($photo)
    {
        if (!empty($photo) && Storage::exists("public/uploads/profiles/$photo"))
        {
            return asset(Storage::url("public/uploads/profiles/$photo"));
        }

        return asset('assets/img/default_avatar.png');
    }

    /**
     * Non-static accessor.
     * Transforms separate Firstname and Lastname into a single Name.
     * Use it like $user->name
     */
    public function getNameAttribute()
    {
        return implode(' ', [$this->{UserFields::Firstname}, $this->{UserFields::Lastname}]);
    }

    /**
     * Non-static accessor.
     * Transforms raw photo filename into full URL.
     * Use it like $user->photoUrl
     */
    public function getPhotoUrlAttribute()
    {
        $photo    = $this->{UserFields::Photo};
        $photoUrl = User::getPhotoUrl($photo);

        return $photoUrl;
    }

    /**
     * Non-static accessor.
     * Transforms fullname into possessive (*'s) name.
     * Use it like $user->possessiveName
     */
    public function getPossessiveNameAttribute()
    {
        $name = $this->getNameAttribute();
        return $name . (substr($name, -1) === 's' ? "'" : "'s");
    }

    /**
     * Non-static accessor.
     * Transforms firstname into possessive (*'s) name.
     * Use it like $user->possessiveFirstName
     */
    public function getPossessiveFirstNameAttribute()
    {
        $name = $this->{UserFields::Firstname};
        return $name . (substr($name, -1) === 's' ? "'" : "'s");
    }
}
