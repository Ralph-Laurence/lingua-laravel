<?php

use App\Models\FieldNames\BookingFields;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up() {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(BookingFields::TutorId);
            $table->unsignedBigInteger(BookingFields::LearnerId);
            //$table->timestamps();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign(BookingFields::TutorId)->references('id')->on('users')->onDelete('cascade');
            $table->foreign(BookingFields::LearnerId)->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('bookings');
    }
};
