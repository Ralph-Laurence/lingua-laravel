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
        View::composer(['shared.header-admin', 'shared.header-members'], function ($view)
        {
            $user = Auth::user();
            $role = $user->{UserFields::Role};

            $profilePic = asset('assets/img/default_avatar.png');

            if (!empty($user->photo))
            {
                $profilePic = Storage::url("public/uploads/profiles/$user->photo");
            }

            $roleStr = User::ROLE_MAPPING[$role];

            $headerData = [
                'fullname'          => implode(' ', [$user->{UserFields::Firstname}, $user->{UserFields::Lastname}]),
                'username'          => $user->{UserFields::Username},
                'profilePic'        => $profilePic,
                'showBecomeTutor'   => $role == User::ROLE_LEARNER,
                'roleBadge'         => Str::lower('role-' . $roleStr),
                'roleStr'           => $roleStr
            ];

            $view->with('headerData', $headerData);
        });
    }
}

