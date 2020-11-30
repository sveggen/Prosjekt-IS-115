<?php


namespace App\controllers;

use App\models\Activity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Activities extends BaseController {

    public function newActivity() {
        $session = new Session();
        $session->start();

        $title = $this->request->get('title');
        $startTime = (string)$this->request->get('start-time');
        $endTime = (string)$this->request->get('end-time');
        $description = (string)$this->request->get('description');
        $maxAttendees = (int)$this->request->get('max-attendees');
        $leader = (int)$session->get('memberID');

        $activityModel = new Activity();
        $newActivity = $activityModel->addActivity($title, $startTime, $endTime, $description, $leader, $maxAttendees);

        if ($newActivity) {
            return $this->renderAllActivities();
        } else {
            return $this->renderAllActivities();
        }
    }

    public function renderAllActivities() {
        $activityModel = new Activity();
        $allActivities = $activityModel->getAllFutureActivities();

        return new Response(
            $this->twig->render('pages/activity/activities.html.twig',
                ['activities' => $allActivities,
                    'session' => (new Session)])
        );
    }

    public function joinActivity($activityID) {
        $session = new Session();
        $session->start();
        $memberID = $session->get('memberID');
        $activityModel = new Activity();

        //$emptyActivitySlots = $activityModel->getEmptySlotsInActivity($activityID);
        $joinActivity = $activityModel->addActivityMember($memberID, $activityID['id']);

        if ($joinActivity) {
            return $this->renderSingleActivity($activityID);
        } else {
            return $this->renderSingleActivity($activityID);
        }

    }

    public function renderSingleActivity($id) {
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