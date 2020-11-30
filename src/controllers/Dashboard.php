<?php


namespace App\controllers;


use Symfony\Component\HttpFoundation\Response;
use App\models\Member;

class Dashboard extends BaseController{


    public function dashboard() {
        return new Response(
            $this->twig->render('pages/member/dashboard.html.twig',
                ['members' =>$this->listMembers()]));

    }

    public function listMembers(){
        return (new Member)->getAllMembersAndMemberData();
    }

}