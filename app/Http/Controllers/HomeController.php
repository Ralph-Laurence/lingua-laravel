<?php

namespace App\Http\Controllers;

use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use App\Models\PendingRegistration;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $viewData = [
            'currentRole' => 'guest',
            'headerData'  => [],
            'showJoinCommunity' => true,
            'isPendingAccount'  => false
        ];

        if (Auth::user())
        {
            $user = Auth::user();

            $viewData['headerData'] = [
                'roleStr'       => User::ROLE_MAPPING[$user->{UserFields::Role}],
                'profilePhoto'  => User::getPhotoUrl($user->{UserFields::Photo}),
                'username'      => $user->{UserFields::Username}
            ];

            switch ($user->{UserFields::Role})
            {
                case User::ROLE_LEARNER:
                    $viewData['currentRole'] = 'learner';
                    break;

                case User::ROLE_TUTOR:
                    $viewData['currentRole'] = 'tutor';
                    $viewData['showJoinCommunity'] = false;
                    break;

                case User::ROLE_PENDING:
                    $viewData['currentRole'] = 'pending';
                    $viewData['showJoinCommunity'] = false;
                    break;

                case User::ROLE_ADMIN:
                    return redirect()->route('admin.dashboard');
                    break;
            }

            // Check if the authenticated user has pending account
            $hasPendingRegistration = PendingRegistration::where(ProfileFields::UserId, Auth::user()->id)->exists();
            $viewData['isPendingAccount'] = $hasPendingRegistration;
        }

        return view('shared.contents.home-page', $viewData);
    }
}
