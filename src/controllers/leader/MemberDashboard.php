<?php


namespace App\controllers\leader;


use App\controllers\BaseController;
use App\helpers\EmailSender;
use App\models\activity\Activity;
use App\models\interest\Interest;
use App\models\member\Member;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class MemberDashboard extends BaseController {

    public function sendEmailToMarked(): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $sendEmail = new EmailSender();
        $recipients = $this->request->get('markedMembers');
        $subject = $this->request->get('subject');
        $content = $this->request->get('content');

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

        $activities = (new Activity)->getAllFutureActivities();

        $listMembers = (new Member)->getAllMembersAndAddress();

        $interests = (new Interest)->getAllInterests();

        return new Response(
            $this->twig->render('pages/leader/member_dashboard.html.twig',
                ['members' => $listMembers,
                    'interests' => $interests,
                    'activities' => $activities]));
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
        $membersSortedPayment = $memberModel->getMembersSortPaymentStatus($query);

        if ($query == 1) {
            $searchQuery = "Payment status - Paid";
        } elseif ($query == 0) {
            $searchQuery = "Payment status - Not paid";
        }

        return $this->renderSearchResults($membersSortedPayment, $searchQuery);
    }

    /**
     * Render the Member Dashboard with a specific member listing,
     * and a string representing the search query.
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
        $activity = $activityModel->getActivity($activityID)->fetch_assoc();
        $activityTitle = $activity['title'];
        $activityStartDate = date("d. m. y. ", strtotime($activity['start_time']));

        $searchQuery = "Activity - " . $activityTitle . " " . $activityStartDate;

        return $this->renderSearchResults($membersWithActivity, $searchQuery);
    }

}