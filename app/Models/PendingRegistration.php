<?php

namespace App\Models;

use App\Models\FieldNames\ProfileFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        ProfileFields::UserId,
        ProfileFields::Disability,
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
}
