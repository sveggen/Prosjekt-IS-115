<?php


namespace App\controllers\member;


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

    public function renderMemberDashboard(): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }


        $activityModel = new Activity();
        $activities = $activityModel->getAllFutureActivities();

       $listMembers =  (new Member)->getAllMembersAndAddress();
        $interests = (new Interest)->getAllInterests();

        return new Response(
            $this->twig->render('pages/member/member_dashboard.html.twig',
                ['members' => $listMembers,
                    'interests' => $interests,
                    'activities' => $activities]));
    }


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

        } return $this->renderMemberDashboard();

    }

    private function filterPaymentStatus(): Response {
        $memberModel = new Member();
        $query = $this->request->query->get('payment-status');
        $membersSortedPayment = $memberModel->getMembersSortPaymentStatus($query);

        if ($query == 1){
            $searchQuery = "Payment status - Paid";
        } elseif ($query == 0) {
            $searchQuery = "Payment status - Not paid";
        }

        return $this->renderSearchResults($membersSortedPayment, $searchQuery);
    }

    private function filterOnInterest(): Response {
        $memberModel = new Member();
        $interestID = $this->request->query->get('interests')[0];
        $membersWithInterest = $memberModel->getAllMembersWithSpecificInterest($interestID);

        $interestModel = new Interest();
        $interestName = $interestModel->getInterestName($interestID)['type'];

        $searchQuery = "Interest - " . $interestName;


        return $this->renderSearchResults($membersWithInterest, $searchQuery);
    }

    private function filterOnActivity(): Response {
        $activityModel = new Activity();

        $activityID = $this->request->query->get('activities')[0];
        $membersWithActivity = $activityModel->getActivityAttendees($activityID);

        $searchQuery = "Interest - " . $membersWithActivity->fetch_assoc()['title'];


        return $this->renderSearchResults($membersWithActivity, $searchQuery);
    }

    private function renderSearchResults($memberList, $searchQueryName): Response {
        $activityModel = new Activity();
        $activities = $activityModel->getAllFutureActivities();

        $interestModel = new Interest();
        $interests = $interestModel->getAllInterests();

        return new Response(
            $this->twig->render('pages/member/member_dashboard.html.twig',
                ['searchQuery' => $searchQueryName,
                    'members' => $memberList,
                    'interests' => $interests,
                    'activities' => $activities]));
    }

}