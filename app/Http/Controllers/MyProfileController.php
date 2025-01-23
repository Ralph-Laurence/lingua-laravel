<?php

namespace App\Http\Controllers;

use App\Services\MyProfileService;
use Illuminate\Http\Request;

class MyProfileController extends Controller
{
    public function __construct(
        private MyProfileService $myProfileService
    )
    {

    }

    public function index()
    {
        return $this->myProfileService->index();
    }

    public function updatePassword(Request $request)
    {
        return $this->myProfileService->updatePassword($request);
    }

    public function updateAccount(Request $request)
    {
        return $this->myProfileService->updateAccount($request);
    }

    public function updateIdentity(Request $request)
    {
        return $this->myProfileService->updateIdentity($request);
    }

    public function revertEmailUpdate()
    {
        return $this->myProfileService->revertEmailUpdate();
    }

    public function showEmailConfirmation($id)
    {
        return $this->myProfileService->showEmailConfirmation($id);
    }

    public function confirmEmailUpdate(Request $request)
    {
        return $this->myProfileService->confirmEmailUpdate($request);
    }
}
