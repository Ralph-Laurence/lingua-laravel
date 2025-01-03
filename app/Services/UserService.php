<?php

namespace App\Services;

use App\Http\Utils\HashSalts;
use App\Models\FieldNames\ProfileFields;
use App\Models\PendingRegistration;
use App\Models\Profile;
use App\Models\User;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use HTMLPurifier_Config;
use HTMLPurifier;

class UserService
{
    // public function buildRegistrationData($userId, $registrationData)
    // {
    //     // Update the profile data
    //     $profileData = [
    //         ProfileFields::UserId       => $userId,
    //         ProfileFields::About        => $registrationData[ProfileFields::About],
    //         ProfileFields::Bio          => $registrationData[ProfileFields::Bio],
    //         ProfileFields::Fluency      => $registrationData[ProfileFields::Fluency],
    //         ProfileFields::Education    => $registrationData[ProfileFields::Education],
    //     ];

    //     // Work Experience
    //     if (array_key_exists(ProfileFields::Experience, $registrationData))
    //         $profileData[ProfileFields::Experience] = $registrationData[ProfileFields::Experience];

    //     // Skills
    //     if (array_key_exists(ProfileFields::Skills, $registrationData))
    //         $profileData[ProfileFields::Skills] = $registrationData[ProfileFields::Skills];

    //     // Certifications
    //     if (array_key_exists(ProfileFields::Certifications, $registrationData))
    //         $profileData[ProfileFields::Certifications] = $registrationData[ProfileFields::Certifications];

    //     return $profileData;
    // }

    // public function updateProfile($userId, $upgradeData)
    // {
    //     try
    //     {
    //         // Find the profile account first to make sure it exists
    //         //$profile = Profile::findOrFail($userId);
    //         $profile = Profile::where(ProfileFields::UserId, $userId)->firstOrFail();

    //         $profileData = $this->buildRegistrationData($userId, $upgradeData);

    //         $profile->update($profileData);
    //         return $profileData;

    //         // // Update the profile data
    //         // $data = [
    //         //     ProfileFields::UserId       => $userId,
    //         //     ProfileFields::About        => $upgradeData[ProfileFields::About],
    //         //     ProfileFields::Bio          => $upgradeData[ProfileFields::Bio],
    //         //     ProfileFields::Fluency      => $upgradeData[ProfileFields::Fluency],
    //         //     ProfileFields::Education    => $upgradeData[ProfileFields::Education],
    //         // ];

    //         // // Work Experience
    //         // if (array_key_exists(ProfileFields::Experience, $upgradeData))
    //         //     $data[ProfileFields::Experience] = $upgradeData[ProfileFields::Experience];

    //         // // Skills
    //         // if (array_key_exists(ProfileFields::Skills, $upgradeData))
    //         //     $data[ProfileFields::Skills] = $upgradeData[ProfileFields::Skills];

    //         // // Certifications
    //         // if (array_key_exists(ProfileFields::Certifications, $upgradeData))
    //         //     $data[ProfileFields::Certifications] = $upgradeData[ProfileFields::Certifications];

    //         // $profile->update($data);
    //         // return $profile;
    //     }
    //     catch (ModelNotFoundException $e)
    //     {
    //         return null;
    //     }
    // }
}
