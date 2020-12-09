<?php


namespace App\controllers\leader;


use App\controllers\BaseController;
use App\helpers\EmailSender;
use App\models\activity\Activity;
use App\models\interest\Interest;
use App\models\member\Member;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * This class is the controller for the "Member Dashboard".
 *
 * Only leaders have access to this class
 *
 * Class MemberDashboard
 * @package App\controllers\leader
 */
class MemberDashboard extends BaseController {

    /**
     * Sends an email to all the members
     * who have been checked in the checkbox.
     *
     * @return Response
     */
    public function sendEmailToSelected(): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $recipients = $this->request->get('selectedMembers');
        $subject = $this->request->get('subject');
        $content = $this->request->get('content');

        $sendEmail = new EmailSender();
        if ($sendEmail->sendEmail($recipients, $subject, $content)) {
            (new Session)->getFlashBag()->add('dashboardSuccess', 'Email was sent successfully');
            return $this->renderMemberDashboard();
        } else {
            (new Session)->getFlashBag()->add(
                'dashboardError',
                'There occurred a problem with sending your email, please try again.');
            return $this->renderMemberDashboard();
        }
    }

    /**
     * Renders the Member Dashboard.
     *
     * @return Response
     */
    public function renderMemberDashboard(): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        // this variable represents the "currently"
        // activated filter/search query
        $searchQuery = "";
        $listMembers = (new Member)->getAllMembersAndAddress();

        return $this->renderSearchResults($listMembers, $searchQuery);
    }

    /**
     * Redirects a search query to the respective
     * function based on the request name
     *
     * @return Response
     */
    public function distributeSearchQuery(): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        //gets the name of the first get request
        switch ($this->request->query->keys()[0]) {
            case "payment-status":
                return $this->filterPaymentStatus();
            case "interests":
                return $this->filterOnInterest();
            case "activities":
                return $this->filterOnActivity();
            case "gender":
                return $this->filterOnGender();
        }
        return $this->renderMemberDashboard();

    }

    /**
     * Filters the member list on members who have paid/not paid
     * their member fee.
     *
     * @return Response
     */
    private function filterPaymentStatus(): Response {
        $query = $this->request->query->get('payment-status');

        $memberModel = new Member();
        // get all members sorted on a given payment status
        $membersSortedByPayment = $memberModel->getMembersSortPaymentStatus($query);

        $paid = 1;
        $notPaid = 0;

        if ($query == $paid) {
            $searchQuery = "Payment status - Paid";
        } elseif ($query == $notPaid) {
            $searchQuery = "Payment status - Not paid";
        }

        return $this->renderSearchResults($membersSortedByPayment, $searchQuery);
    }

    /**
     * Render the Member Dashboard with a specific member listing,
     * and a string representing the active search query.
     *
     * @param $memberList
     * @param $searchQueryName
     * @return Response
     */
    private function renderSearchResults($memberList, $searchQueryName): Response {
        $activities = (new Activity)->getAllFutureActivities();

        $interests = (new Interest)->getAllInterests();

        return new Response(
            $this->twig->render('pages/leader/member_dashboard.html.twig',
                ['searchQuery' => $searchQueryName,
                    'members' => $memberList,
                    'interests' => $interests,
                    'activities' => $activities]));
    }

    /**
     * Filters the member list on all members who
     * likes a specific interest.
     *
     * @return Response
     */
    private function filterOnInterest(): Response {
        $interestID = $this->request->query->get('interests')[0];

        $memberModel = new Member();
        $membersWithInterest = $memberModel->getAllMembersWithSpecificInterest($interestID);

        $interestModel = new Interest();
        $interestName = $interestModel->getInterestName($interestID)['type'];

        $searchQuery = "Interest - " . $interestName;

        return $this->renderSearchResults($membersWithInterest, $searchQuery);
    }

    /**
     * Filters the member list on all members
     * which are attending a specific activity.
     *
     * @return Response
     */
    private function filterOnActivity(): Response {
        $activityID = $this->request->query->get('activities')[0];

        $activityModel = new Activity();
        $membersWithActivity = $activityModel->getActivityAttendees($activityID);
        $activity = $activityModel->getActivityAndLeader($activityID)->fetch_assoc();
        $activityTitle = $activity['title'];

        // adds the activity start date to easier differ between different activities
        $activityStartDate = date("d. m. y. ", strtotime($activity['start_time']));

        $searchQuery = "Activity - " . $activityTitle . " " . $activityStartDate;

        return $this->renderSearchResults($membersWithActivity, $searchQuery);
    }

    /**
     * Filters the member list on a given gender.
     *
     * @return Response
     */
    private function filterOnGender(): Response {
        $gender = $this->request->query->get('gender');

        $memberModel = new Member();
        $membersWithSpecificGender = $memberModel->getMembersWithSpecificGender($gender);

        $searchQuery = "Gender - " . ucfirst($gender);

        return $this->renderSearchResults($membersWithSpecificGender, $searchQuery);
    }

}