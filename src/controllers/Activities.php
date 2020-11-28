<?php


namespace App\controllers;


use App\models\Activity;
use Symfony\Component\HttpFoundation\Response;

class Activities extends BaseController{

    public function activity() {

        $activityModel = new Activity();
        $allActivities = $activityModel->getAllFutureActivities();

        return new Response(
            $this->twig->render('pages/activity/activity.html.twig',
            ['activities', $allActivities])
        );

    }

}