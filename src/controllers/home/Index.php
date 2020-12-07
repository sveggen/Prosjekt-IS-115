<?php

namespace App\controllers\home;

use App\controllers\BaseController;
use App\models\activity\Activity;
use App\models\member\Member;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;



class Index extends BaseController {

    public function renderStartPage(): Response {
        $totalMembers = $this->getCurrentMemberTotal();
        $totalLeaders = $this->getCurrentLeaderTotal();
        $totalActivities = $this->getUpcomingActivitiesTotal();

        $interests = array(2, 3);
        $memberModel = new Member();
        $memberModel->addMemberInterests(4, $interests);

        return new Response(
            $this->twig->render('pages/home/index.html.twig',
                ['session' => (new Session),
                    'totalMembers' => $totalMembers,
                    'totalLeaders' => $totalLeaders,
                    'totalActivities' => $totalActivities
                ])
        );
    }

    private function getCurrentMemberTotal(): int {
        $memberModel = new Member();
        return $memberModel->getTotalMembers();
    }

    private function getCurrentLeaderTotal(): int {
        $memberModel = new Member();
        return $memberModel->getTotalLeaders();
    }

    private function getUpcomingActivitiesTotal(): int {
        $activityModel = new Activity();
        return $activityModel->getTotalFutureActivities();

    }

}