<?php

namespace App\controllers;

use App\models\Member;
use App\models\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


class Index extends BaseController
{

    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function renderStartPage()
    {
        $totalMembers = $this->getCurrentMemberTotal();
        $totalLeaders = $this->getCurrentLeaderTotal();
        $totalActivities = $this->getUpcomingActivitiesTotal();

            return new Response(
                $this->twig->render('pages/index.html.twig',
                    ['session' => (new Session),
                        'totalMembers' => $totalMembers,
                        'totalLeaders' => $totalLeaders,
                        'totalActivities' => $totalActivities]));

    }

    private function getCurrentMemberTotal(){
        $memberModel = new Member();
        return $memberModel->getTotalMembers();
    }

    private function getUpcomingActivitiesTotal(){

    }

    private function getCurrentLeaderTotal(){

        //return int
    }



}