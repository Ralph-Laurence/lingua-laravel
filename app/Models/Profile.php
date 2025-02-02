<?php

namespace App\Models;

use App\Models\FieldNames\ProfileFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        ProfileFields::UserId,
        ProfileFields::Fluency,
        ProfileFields::Bio,
        ProfileFields::About,
        ProfileFields::Education,
        ProfileFields::Experience,
        ProfileFields::Certifications,
        ProfileFields::Skills
    ];

    protected $casts = [
         // Cast the JSON field as an array
        ProfileFields::Education        => 'array',
        ProfileFields::Experience       => 'array',
        ProfileFields::Certifications   => 'array',
        ProfileFields::Skills           => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class, ProfileFields::UserId);
    }

    // public function getBioAttribute()
    // {
    //     return $this->{ProfileFields::Bio};
    // }
}
