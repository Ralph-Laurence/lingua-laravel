<?php

use App\Models\FieldNames\UserFields;
use App\Models\User;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string(UserFields::Firstname, 50);
            $table->string(UserFields::Lastname, 50);

            // Login Information
            $table->string(UserFields::Username, 50)->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedTinyInteger(UserFields::Role)
                  ->default(User::ROLE_LEARNER);

            // Common Details
            $table->string(UserFields::Contact, 20);
            $table->string(UserFields::Address, 128);
            $table->string(UserFields::Photo)->nullable();
            $table->integer(UserFields::IsVerified)->default(0);

            $table->rememberToken();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
