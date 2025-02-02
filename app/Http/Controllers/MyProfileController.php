<?php

namespace App\Http\Controllers;

use App\Services\MyProfileEducationDocumentsService;
use App\Services\MyProfileService;
use Illuminate\Http\Request;

class MyProfileController extends Controller
{
    public function __construct(
        private MyProfileService $myProfileService,
        //private MyProfileDocumentsService $documentService,
        private MyProfileEducationDocumentsService $educDocsService
    )
    {

    }

    public function index()
    {
        return $this->myProfileService->index();
    }

    public function removePhoto()
    {
        return $this->myProfileService->removePhoto();
    }

    public function updatePhoto(Request $request)
    {
        return $this->myProfileService->updatePhoto($request);
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

    public function updateBio(Request $request)
    {
        return $this->myProfileService->updateBio($request);
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

    public function updateEducation(Request $request)
    {
        return $this->educDocsService->updateEducation($request);
    }

    public function removeEducation(Request $request)
    {
        return $this->educDocsService->removeEducation($request);
    }

    public function addEducation(Request $request)
    {
        return $this->educDocsService->addEducation($request);
    }

    public function fetchEducation(Request $request)
    {
        return $this->educDocsService->fetchEducationDetails($request);
    }
}
