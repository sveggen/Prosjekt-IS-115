<?php


namespace App\controllers;


use App\helpers\UploadFile;
use App\models\Member;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Profile extends BaseController{

    public function renderMyProfile(): Response {
        $session = new Session();
        $memberID = $session->get('memberID');

        $memberModel = new Member();
        $member = $memberModel->getSingleMemberAndMemberData($memberID);

        $profileImage = (new UploadFile)->getProfileImage($memberID);

        return new Response(
            $this->twig->render('pages/user/my_profile.html.twig',
                ['member' => $member,
                    'profileImage' => $profileImage])
        );
    }

    public function renderMemberProfile($memberID): Response {
        $memberModel = new Member();
        $member = $memberModel->getSingleMemberAndMemberData($memberID['id']);

        $profileImage = (new UploadFile)->getProfileImage($memberID['id']);

        return new Response(
            $this->twig->render('pages/user/profile.html.twig',
                ['member' => $member,
                    'profileImage' => $profileImage])
        );
    }

    public function updateProfile(): Response {
        $this->updateProfileImage();
        return $this->renderMyProfile();
    }


    private function updateProfileImage(){
        $memberID = (new Session)->get('memberID');
        $image = $this->request->files->get('image');
        (new UploadFile)->uploadProfileImage($image, $memberID);
    }

}