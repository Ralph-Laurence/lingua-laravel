<?php

use App\Models\FieldNames\ProfileFields;
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

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(ProfileFields::UserId);
            $table->integer(ProfileFields::Disability);
            $table->text(ProfileFields::Bio)->nullable();
            $table->text(ProfileFields::About)->nullable();
            $table->json(ProfileFields::Education)->nullable();
            $table->json(ProfileFields::Experience)->nullable();
            $table->json(ProfileFields::Certifications)->nullable();
            $table->json(ProfileFields::Skills)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign(ProfileFields::UserId)
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
        Schema::dropIfExists('profiles');
    }
};
