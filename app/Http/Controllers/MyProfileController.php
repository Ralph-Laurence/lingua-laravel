<?php

namespace App\Http\Controllers;

use App\Services\MyProfileCertificationsDocumentsService;
use App\Services\MyProfileEducationDocumentsService;
use App\Services\MyProfileService;
use App\Services\MyProfileWorkExpDocumentsService;
use Illuminate\Http\Request;

class MyProfileController extends Controller
{
    public function __construct(
        private MyProfileService $myProfileService,
        private MyProfileCertificationsDocumentsService $certDocsService,
        private MyProfileEducationDocumentsService $educDocsService,
        private MyProfileWorkExpDocumentsService $workDocsService
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
    public function updateAbout(Request $request)
    {
        return $this->myProfileService->updateAbout($request);
    }

    public function updateDisability(Request $request)
    {
        return $this->myProfileService->updateDisability($request);
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
    //
    //=======================================================
    //      E D U C A T I O N A L  A T T A I N M E N T
    //=======================================================
    //
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
    //
    //=======================================================
    //              W O R K  E X P E R I E N C E
    //=======================================================
    //
    public function fetchWorkExp(Request $request)
    {
        return $this->workDocsService->fetchWorkExp($request);
    }

    public function addWorkExp(Request $request)
    {
        return $this->workDocsService->addWorkExperience($request);
    }

    public function removeWorkExp(Request $request)
    {
        return $this->workDocsService->removeWorkExperience($request);
    }

    public function updateWorkExp(Request $request)
    {
        return $this->workDocsService->updateWorkExperience($request);
    }
    //
    //=======================================================
    //              C E R T I F I C A T I O N S
    //=======================================================
    //
    public function fetchCertification(Request $request)
    {
        return $this->certDocsService->fetchCertification($request);
    }

    public function addCertification(Request $request)
    {
        return $this->certDocsService->addCertification($request);
    }

    public function removeCertification(Request $request)
    {
        return $this->certDocsService->removeCertification($request);
    }

    public function updateCertification(Request $request)
    {
        return $this->certDocsService->updateCertification($request);
    }
     //
    //=======================================================
    //                      S K I L L S
    //=======================================================
    //
    public function removeSkills(Request $request)
    {
        return $this->myProfileService->removeSkills($request);
    }

    public function addSkills(Request $request)
    {
        return $this->myProfileService->addSkills($request);
    }

    public function updateSkills(Request $request)
    {
        return $this->myProfileService->updateSkills($request);
    }
}
