<?php


namespace App\controllers;


use App\models\Member;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Profile extends BaseController{

    public function renderMyProfile() {
        $session = new Session();
        $session->start();
        $memberID = $session->get('memberID');
        $session->getFlashBag()->add('error', 'Error !!!');

        $memberModel = new Member();
        $member = $memberModel->getSingleMemberAndMemberData($memberID);

        return new Response(
            $this->twig->render('pages/user/my_profile.html.twig',
                ['member' => $member])
        );
    }

    public function renderMemberProfile($memberID) {
        $memberModel = new Member();
        $member = $memberModel->getSingleMemberAndMemberData($memberID['id']);

        return new Response(
            $this->twig->render('pages/user/profile.html.twig',
                ['member' => $member])
        );
    }

}