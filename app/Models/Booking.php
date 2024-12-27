<?php

namespace App\Models;

use App\Models\FieldNames\BookingFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [BookingFields::TutorId, BookingFields::LearnerId];

    public function tutor() {
        return $this->belongsTo(User::class, BookingFields::TutorId);
    }

    public function learner() {
        return $this->belongsTo(User::class, BookingFields::LearnerId);
    }
}
