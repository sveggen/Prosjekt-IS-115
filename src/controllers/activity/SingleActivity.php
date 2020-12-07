<?php


namespace App\controllers\activity;


use App\controllers\BaseController;
use App\models\activity\Activity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class SingleActivity extends BaseController {

    public function leaveActivity($activityID): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $session = new Session();
        $memberID = $session->get('memberID');

        $activityModel = new Activity();
        $activityModel->leaveActivity($memberID, $activityID['id']);

        return $this->renderSingleActivity($activityID);
    }

    public function renderSingleActivity($id): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $activityModel = new Activity();
        $activity = $activityModel->getActivity($id['id']);
        $attendees = $activityModel->getActivityAttendees($id['id']);
        $attendeesCount = $attendees->num_rows;

        $memberID = (new Session)->get('memberID');
        $attendanceStatus = $activityModel->getMemberActivityAttendanceStatus($memberID, $id['id']);

        return new Response(
            $this->twig->render('pages/activity/single_activity.html.twig',
                ['activity' => $activity,
                    'attendees' => $attendees,
                    'attendeesCount' => $attendeesCount,
                    'attendanceStatus' => $attendanceStatus])
        );
    }

    public function joinActivity($activityID): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $session = new Session();
        $memberID = $session->get('memberID');
        $joinTime = date('Y-m-d H:i:s');
        $activityModel = new Activity();


        //$emptyActivitySlots = $activityModel->getEmptySlotsInActivity($activityID);
        $joinActivity = $activityModel->addActivityMember($memberID, $activityID['id'], $joinTime);

        if ($joinActivity) {
            return $this->renderSingleActivity($activityID);
        } else {
            return $this->renderSingleActivity($activityID);
        }

    }

    public function removeActivity($activityID): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $activityModel = new Activity();
        $removeActivity = $activityModel->removeActivity($activityID['id']);

        $session = new Session();
        if ($removeActivity) {
            return new RedirectResponse('http://www.localhost:8081/activities');

        } else {
            $session->getFlashBag()->add('activityFailure', 'Activity could not be removed');
            return $this->renderSingleActivity($activityID);

        }
    }

}