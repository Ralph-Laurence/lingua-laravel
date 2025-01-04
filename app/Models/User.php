<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\BookingRequestFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    const ROLE_STR_ADMIN     = 'Admin';
    const ROLE_STR_TUTOR     = 'Tutor';
    const ROLE_STR_LEARNER   = 'Learner';

    const ROLE_MAPPING = [
        self::ROLE_ADMIN     => self::ROLE_STR_ADMIN,
        self::ROLE_TUTOR     => self::ROLE_STR_TUTOR,
        self::ROLE_LEARNER   => self::ROLE_STR_LEARNER,
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

    // Other model methods and properties

    /* Get the short abbreviated name */
    public static function toShortName($firstName, $lastName)
    {
        // Take the first character of the last name
        $lastNameInitial = strtoupper(mb_substr($lastName, 0, 1)) . '.';

        return "{$firstName} {$lastNameInitial}";
    }

    /**
     * Get the url of user's photo. Returns the default if photo doesn't exist.
     */
    public static function getPhotoUrl($photo)
    {
        if (!empty($photo))
        {
            return asset(Storage::url("public/uploads/profiles/$photo"));
        }

        return asset('assets/img/default_avatar.png');
    }
}
