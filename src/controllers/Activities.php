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


        return new Response(
            $this->twig->render('pages/activity/activities.html.twig',
            ['activities' => $allActivities,
            'session' => (new Session)])
        );
    }

    public function newActivity() {
        $session = new Session();
        $session->start();

        $title = $this->request->get('title');
        $startTime = (string)$this->request->get('start-time');
        $endTime = (string)$this->request->get('end-time');
        $description = (string)$this->request->get('description');
        $leader= (int)$session->get('memberID');

        $activityModel = new Activity();

        if ($activityModel->addActivity($title, $startTime, $endTime, $description, $leader)) {
            return $this->activities();
        } else {
            return $this->activities();
        }
    }

    public function joinActivity($id, $activity){
        $activityModel = new Activity();
        $activityModel->addActivityMember($id['id'], $activity['activity']);
        return $this->handleUsers($id);
    }

    public function handleUsers($id){
        $activityModel = new Activity();
        $activity = $activityModel->getActivity($id['id']);
        $attendees = $activityModel->getActivityAttendees($id['id']);
        $attendeesCount = $attendees->num_rows;

        return new Response(
            $this->twig->render('pages/activity/activity.html.twig',
                ['session' => (new Session),
                    'activity' => $activity,
                    'attendees' => $attendees,
                    'attendeesCount' => $attendeesCount,
                    'id' => $id['id']])
        );
    }

}