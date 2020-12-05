<?php

namespace App\controllers;

use App\helpers\EmailSender;
use App\models\Activity;
use App\models\Member;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


class Index extends BaseController {

    public function renderStartPage() {
        $totalMembers = $this->getCurrentMemberTotal();
        $totalLeaders = $this->getCurrentLeaderTotal();
        $totalActivities = $this->getUpcomingActivitiesTotal();

        $emailSender = new EmailSender();


        return new Response(
            $this->twig->render('pages/index.html.twig',
                ['session' => (new Session),
                    'totalMembers' => $totalMembers,
                    'totalLeaders' => $totalLeaders,
                    'totalActivities' => $totalActivities,
                        'sent' => $emailSender->sendEmail('m.sveggen@gmail.com', 'Markus', 'Heisann', 'Innhold')
                ])
        );
    }

    private function getCurrentMemberTotal() {
        $memberModel = new Member();
        return $memberModel->getTotalMembers();
    }

    private function getCurrentLeaderTotal() {
        $memberModel = new Member();
        return $memberModel->getTotalLeaders();
    }

    private function getUpcomingActivitiesTotal() {
        $activityModel = new Activity();
        return $activityModel->getTotalFutureActivities();

    }

}