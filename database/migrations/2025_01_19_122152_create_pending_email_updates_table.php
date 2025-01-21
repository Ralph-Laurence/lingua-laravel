<?php

use App\Models\FieldNames\PendingEmailUpdateFields;
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
        Schema::create('pending_email_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(PendingEmailUpdateFields::UserId);
            $table->string(PendingEmailUpdateFields::OldEmail);
            $table->string(PendingEmailUpdateFields::NewEmail);
            $table->timestamps();

            $table->foreign(PendingEmailUpdateFields::UserId)
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_email_updates');
    }
};
