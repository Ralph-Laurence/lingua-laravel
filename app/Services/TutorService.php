<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TutorService
{
    const Role = User::ROLE_TUTOR;

    // Create a profile data
    public function CreateProfile($data)
    {

    }

    public static function FindById($id)
    {
        try
        {
            $tutor = User::where('id', $id)->where('role', self::Role)->firstOrFail();
            return $tutor;
        }
        catch (ModelNotFoundException $e)
        {
            return null;
        }
    }
}
