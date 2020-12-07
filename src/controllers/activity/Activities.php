<?php


namespace App\controllers\activity;

use App\controllers\BaseController;
use App\models\activity\Activity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Activities extends BaseController {

    public function newActivity(): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        //retrieve POST-parameters from form.
        $title = $this->request->get('title');
        $startTime = $this->request->get('start-time');
        $endTime = $this->request->get('end-time');
        $description = $this->request->get('description');
        $maxAttendees = $this->request->get('max-attendees');

        $session = new Session();
        $leader = $session->get('memberID');

        $activityModel = new Activity();
        $newActivity = $activityModel->addActivity($title, $startTime, $endTime, $description, $leader, $maxAttendees);

        if ($newActivity) {
            return $this->renderAllActivities();
        } else {
            $session->getFlashBag()->add('error', 'Activity could not be created.');
            return $this->renderAllActivities();
        }
    }

    public function renderAllActivities(): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }


        $activityModel = new Activity();
        $allActivities = $activityModel->getAllFutureActivities();

        return new Response(
            $this->twig->render('pages/activity/activities.html.twig',
                ['activities' => $allActivities])
        );
    }





}