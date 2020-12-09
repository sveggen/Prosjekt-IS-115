<?php


namespace App\controllers\activity;


use App\controllers\BaseController;
use App\models\activity\Activity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class SingleActivity extends BaseController {

    /**
     * Removes a member from a given activity, aka. unsubscribes the
     * member from the attendance list of the activity.
     *
     * NOTE: This function can only be triggered by the user self.
     *
     * @param $requestParameters
     * @return Response
     */
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

    /**
     * Renders a single activity based on the activity ID retrieved from the
     * request.
     *
     * @param $requestParameters
     * @return Response
     */
    public function renderSingleActivity($requestParameters): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }
        $activityID = $requestParameters['activityID'];

        $activityModel = new Activity();
        $activity = $activityModel->getActivityAndLeader($activityID);
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


    /**
     *
     * Adds a member to a given activity, aka. subscribes the
     * to the attendance list of the activity.
     *
     * NOTE: This function can only be triggered by the user self.
     *
     * @param $requestParameters
     * @return Response
     */
    public function joinActivity($requestParameters): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $session = new Session();
        $memberID = $session->get('memberID');
        // current time
        $joinTime = date('Y-m-d H:i:s');
        $activityModel = new Activity();

        $activityID = $requestParameters['activityID'];

        $joinActivity = $activityModel->addActivityMember($memberID, $activityID, $joinTime);

        if ($joinActivity) {
            return $this->renderSingleActivity($requestParameters);
        } else {
            return $this->renderSingleActivity($requestParameters);
        }

    }

    /**
     * Removes an activity completely from
     * the activity list and database.
     *
     * @param $requestParameters
     * @return Response
     */
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

    /**
     * Removes a member which have joined an activity
     * - from said activity.
     *
     * @param $requestParameters
     * @return Response
     */
    public function removeMemberFromActivity($requestParameters): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }
        $activityModel = new Activity();
        $activityModel->leaveActivity($requestParameters['memberID'], $requestParameters['activityID']);

        return $this->renderSingleActivity($requestParameters);
    }

}