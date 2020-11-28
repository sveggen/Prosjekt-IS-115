<?php


namespace App\controllers;


use App\models\Activity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Activities extends BaseController{

    public function activities() {

        $activityModel = new Activity();
        $allActivities = $activityModel->getAllFutureActivities();

        $session = new Session();
        $session->start();

        return new Response(
            $this->twig->render('pages/activity/activities.html.twig',
            ['activities' => $allActivities,
            'session' => $session])
        );

    }

    public function newActivity(){
        $session = new Session();
        $session->start();

        $title = $this->request->get('title');
        $startTime = $this->request->get('start-time');
        $endTime = $this->request->get('end-time');
        $leader = $session->get('memberID');

        $activityModel = new Activity();

        if ( $activityModel->addActivity($title, $startTime, $endTime, $leader)) {
            return new RedirectResponse('http://localhost:8081');
        } else {
            $this->activities();
        }
    }

}