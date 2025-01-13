<?php

use App\Models\FieldNames\RatingsAndReviewFields;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ratings_and_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(RatingsAndReviewFields::TutorId);
            $table->unsignedBigInteger(RatingsAndReviewFields::LearnerId);
            $table->integer(RatingsAndReviewFields::Rating);
            $table->string(RatingsAndReviewFields::Review, 255)->nullable();
            $table->timestamps();

            $table->foreign(RatingsAndReviewFields::TutorId)->references('id')->on('users')->onDelete('cascade');
            $table->foreign(RatingsAndReviewFields::LearnerId)->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings_and_reviews');
    }
};
