<?php


namespace App\controllers\activity;


use App\controllers\BaseController;
use App\models\activity\Activity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class SingleActivity extends BaseController {

    public function leaveActivity($requestParameters): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $session = new Session();
        $memberID = $session->get('memberID');

        $activityModel = new Activity();
        $activityID = $requestParameters['activityID'];
        $activityModel->leaveActivity($memberID, $activityID);

        return $this->renderSingleActivity($requestParameters);
    }

    public function renderSingleActivity($requestParameters): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }
        $activityID = $requestParameters['activityID'];

        $activityModel = new Activity();
        $activity = $activityModel->getActivity($activityID);
        $attendees = $activityModel->getActivityAttendees($activityID);
        $attendeesCount = $attendees->num_rows;

        $memberID = (new Session)->get('memberID');
        $attendanceStatus = $activityModel->getMemberActivityAttendanceStatus($memberID, $activityID);

        return new Response(
            $this->twig->render('pages/activity/single_activity.html.twig',
                ['activity' => $activity,
                    'attendees' => $attendees,
                    'attendeesCount' => $attendeesCount,
                    'attendanceStatus' => $attendanceStatus])
        );
    }

    public function joinActivity($requestParameters): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $session = new Session();
        $memberID = $session->get('memberID');
        $joinTime = date('Y-m-d H:i:s');
        $activityModel = new Activity();

        $activityID = $requestParameters['activityID'];

        //$emptyActivitySlots = $activityModel->getEmptySlotsInActivity($activityID);
        $joinActivity = $activityModel->addActivityMember($memberID, $activityID, $joinTime);

        if ($joinActivity) {
            return $this->renderSingleActivity($requestParameters);
        } else {
            return $this->renderSingleActivity($requestParameters);
        }

    }

    public function removeActivity($requestParameters): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $activityID = $requestParameters['activityID'];

        $activityModel = new Activity();
        $removeActivity = $activityModel->removeActivity($activityID);

        $session = new Session();
        if ($removeActivity) {
            return new RedirectResponse('/activities');

        } else {
            $session->getFlashBag()->add('activityFailure', 'Activity could not be removed');
            return $this->renderSingleActivity($requestParameters);

        }
    }

    public function removeMemberFromActivity($requestParameters): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }
        $activityModel = new Activity();
        $activityModel->leaveActivity($requestParameters['memberID'], $requestParameters['activityID']);

        return $this->renderSingleActivity($requestParameters);
    }

}