<?php


namespace App\controllers\member;


use App\controllers\BaseController;
use App\helpers\UploadFile;
use App\models\activity\Activity;
use App\models\member\Member;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Profile extends BaseController {

    public function renderMemberProfile($memberID): Response {
        if ($this->hasLeaderPrivileges() == true){

            return $this->renderProfile($memberID['id']);
        }

        if ($this->hasMemberPrivileges() == true) {

            $session = new Session();
            $sessionMemberID = $session->get('memberID');

            // if the ID in the request belong to the
            // logged in member - render profile.
            if ($memberID['id'] == $sessionMemberID) {
                return $this->renderProfile($sessionMemberID);

            }
        }
        return $this->methodNotAllowed();

    }

    private function renderProfile($memberID): Response {

        $memberModel = new Member();
        $member = $memberModel->getSingleMemberAndMemberData($memberID);

        $interests = $memberModel->getAllMembersInterests($memberID);

        $activityModel = new Activity();
        $activities = $activityModel->getAllMembersActivities($memberID);

        $roles = $memberModel->getSingleUserRoles($memberID);

        $uploadFile = new UploadFile;
        $profileImage = $uploadFile->getProfileImage($memberID);

        return new Response(
            $this->twig->render('pages/member/edit_member_profile.html.twig',
                ['member' => $member,
                    'profileImage' => $profileImage,
                    'interests' => $interests,
                    'activity' => $activities,
                    'roles' => $roles])
        );
    }

    public function updateProfile(): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $this->updateProfileImage();
        return new RedirectResponse('http://localhost:8081/profile');
    }

    private function updateProfileImage() {
        $session = new Session();
        $memberID = (new Session)->get('memberID');
        $image = $this->request->files->get('image');
        $uploadFile = new UploadFile;
        $uploadProfileImage = $uploadFile->uploadProfileImage($image, $memberID);

        if ($uploadProfileImage) {
            $session->getFlashBag()->add('UpdateSuccess', 'Uploaded image successfully');
        } else {
            $errorMessages = $uploadFile->getErrorMessages();
            foreach ($errorMessages as $message) {
                $session->getFlashBag()->add('profileUpdateError', $message);
            }
        }
    }
}