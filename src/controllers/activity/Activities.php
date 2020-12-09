<?php


namespace App\controllers\activity;

use App\controllers\BaseController;
use App\helpers\Validate;
use App\models\activity\Activity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Activities extends BaseController {


    /**
     * Creates a new activity after the connected
     * form has been submitted.
     *
     * @return Response
     */
    public function newActivity(): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        // Retrieve all parameters from the "add activity"-form
        $newActivityForm = [
            'title' => $this->request->get('title'),
            'startTime' => $this->request->get('start-time'),
            'endTime' => $this->request->get('end-time'),
            'description' => $this->request->get('description'),
            'maxAttendees' => $this->request->get('max-attendees')
        ];

        $session = new Session();

        // validates the form
        $errorMessages = $this->validateNewActivityForm($newActivityForm);
        // checks if the list of error messages is not empty
        if (!empty($errorMessages)) {
            // adds each error message as a flash message to the session object
            foreach ($errorMessages as $message) {
                $session->getFlashBag()->add('addActivityError', $message);
            }
            return $this->renderAllActivities();
        }

        $leader = $session->get('memberID');

        $activityModel = new Activity();
        $newActivity = $activityModel->addActivity(
            $newActivityForm['title'], $newActivityForm['startTime'], $newActivityForm['endTime'],
            $newActivityForm['description'], $leader, $newActivityForm['maxAttendees']);

        if ($newActivity) {
            $session->getFlashBag()->add('addActivitySuccess', 'Activity created.');
            return $this->renderAllActivities();
        } else {
            $session->getFlashBag()->add('addActivityError', 'Activity could not be created.');
            return $this->renderAllActivities();
        }
    }

    /**
     * Validate the "new activity" form.
     *
     * @param $newActivityForm
     * @return array|false
     */
    private function validateNewActivityForm($newActivityForm){
        $validator = new Validate();

        $validator->validateTitle($newActivityForm['title']);
        $validator->validateFutureDate($newActivityForm['startTime']);
        $validator->validateActivityDescription($newActivityForm['description']);
        $validator->validatePositiveNumber($newActivityForm['maxAttendees']);

        // get the list of error messages
        $errorMessages = $validator->getErrorMessages();

        // return the list of error messages
        // if it is not empty
        if (!empty($errorMessages)){
            return $errorMessages;
        } else{
            return false;}

    }

    /**
     * Render all activities that have a start date
     * in the future counted from the current time.
     *
     * @return Response
     */
    public function renderAllActivities(): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $activityModel = new Activity();
        // retrieves all activities with an upcoming start date
        $allActivities = $activityModel->getAllFutureActivities();

        return new Response(
            $this->twig->render('pages/activity/activities.html.twig',
                ['activities' => $allActivities])
        );
    }





}