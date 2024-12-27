<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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


    // Other model methods and properties

    public function bookingsAsTutor() {
        return $this->hasMany(Booking::class, BookingFields::TutorId);
    }

    public function bookingsAsLearner() {
        return $this->hasMany(Booking::class, BookingFields::LearnerId);
    }

    public function profile() {
        return $this->hasOne(Profile::class, ProfileFields::UserId);
    }
}
