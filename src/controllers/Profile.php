<?php


namespace App\controllers;


use App\helpers\UploadFile;
use App\models\Activity;
use App\models\Member;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Profile extends BaseController{

    public function renderMyProfile(): Response {
        $session = new Session();
        $memberID = $session->get('memberID');

        $memberModel = new Member();
        $member = $memberModel->getSingleMemberAndMemberData($memberID);

        $interests = $memberModel->getAllMembersInterests($memberID);

        $activityModel = new Activity();
        $activities = $activityModel->getAllMembersActivities($memberID);

        $roles = $memberModel->getSingleUserRoles($memberID);

        $profileImage = (new UploadFile)->getProfileImage($memberID);

        return new Response(
            $this->twig->render('pages/user/edit_profile.html.twig',
                ['member' => $member,
                    'profileImage' => $profileImage,
                    'interests' => $interests,
                'activities' => $activities,
                    'roles' => $roles])
        );
    }

    public function renderMemberProfile($memberID): Response {
        $memberModel = new Member();
        $member = $memberModel->getSingleMemberAndMemberData($memberID['id']);

        $interests = $memberModel->getAllMembersInterests($memberID['id']);

        $profileImage = (new UploadFile)->getProfileImage($memberID['id']);

        return new Response(
            $this->twig->render('pages/user/public_profile.html.twig',
                ['member' => $member,
                    'profileImage' => $profileImage,
                    'interests' => $interests])
        );
    }

    public function updateProfile(): Response {
        $this->updateProfileImage();
        return $this->renderMyProfile();
    }


    private function updateProfileImage(){
        $session = new Session();
        $memberID = (new Session)->get('memberID');
        $image = $this->request->files->get('image');
        $uploadFile = new UploadFile;
        $uploadProfileImage = $uploadFile->uploadProfileImage($image, $memberID);

        if ($uploadProfileImage){
            $session->getFlashBag()->add('UpdateSuccess', 'Uploaded image successfully');
        } else {
            $errorMessages = $uploadFile->getErrorMessages();
            foreach ($errorMessages as $message){
                $session->getFlashBag()->add('profileUpdateError', $message);
            }
        }
    }

}