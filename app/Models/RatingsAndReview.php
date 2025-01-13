<?php

namespace App\Models;

use App\Models\FieldNames\RatingsAndReviewFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingsAndReview extends Model
{
    use HasFactory;

    protected $fillable = [
        RatingsAndReviewFields::TutorId,
        RatingsAndReviewFields::LearnerId,
        RatingsAndReviewFields::Rating,
        RatingsAndReviewFields::Review,
    ];

    public function tutor() {
        return $this->belongsTo(User::class, RatingsAndReviewFields::TutorId);
    }

    public function learner() {
        return $this->belongsTo(User::class, RatingsAndReviewFields::LearnerId);
    }
}
