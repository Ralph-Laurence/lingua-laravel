<?php

// app/Providers/ViewComposerServiceProvider.php
namespace App\Providers;

use App\Models\FieldNames\UserFields;
use App\Models\User;
use Hashids\Hashids;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer(['partials.header-admin', 'partials.header-learner-tutor'], function ($view)
        {
            $user    = Auth::user();
            $hashids = new Hashids('a8F3kL9zQ2', 10);
            $userId  = $hashids->encode($user->id);

            $profilePic = asset('assets/img/default_avatar.png');

            if (!empty($user->photo)) {
                $profilePic = Storage::url("public/uploads/profiles/$user->photo");
            }

            $role    = Auth::user()->{UserFields::Role};
            $roleStr = User::ROLE_MAPPING[$role];

            $headerData = [
                'fullname'          => implode(' ', [$user->firstname, $user->lastname]),
                'profilePic'        => $profilePic,
                'hashedUserId'      => $userId,
                'showBecomeTutor'   => $role == User::ROLE_LEARNER,
                'roleBadge'         => Str::lower('role-' . $roleStr),
                'roleStr'           => $roleStr,
            ];

            $view->with('headerData', $headerData);
        });
    }
}

