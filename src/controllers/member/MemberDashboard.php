<?php


namespace App\controllers\member;


use App\controllers\BaseController;
use App\helpers\EmailSender;
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

        return new Response(
            $this->twig->render('pages/member/member_dashboard.html.twig',
                ['members' => $this->listMembers()]));
    }

    private function listMembers() {
        return (new Member)->getAllMembersAndAddress();
    }

    public function distributeSearchQuery() {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        //gets the name of the first get request
        switch ($this->request->query->keys()[0]) {
            case "payment-status":
                return $this->filterPaymentStatus();
            case "interests":
                return $this->filterOnInterests();
            case 2:
                echo "i equals 2";
                break;
        } return $this->renderMemberDashboard();

    }

    private function filterPaymentStatus(): Response {
        $memberModel = new Member();
        $query = $this->request->query->get('payment-status');
        $sorted = $memberModel->getMembersSortPaymentStatus($query);
        $searchQuery = $query;


        return new Response(
            $this->twig->render('pages/member/member_dashboard.html.twig',
                ['searchQuery' => $searchQuery,
                'members' => $sorted]));
    }

    private function filterOnInterests(): Response {
        $memberModel = new Member();
        $query = $this->request->query->get('payment-status');
        $sorted = $memberModel->getMembersSortPaymentStatus($query);

        return new Response(
            $this->twig->render('pages/member/member_dashboard.html.twig',
                ['members' => $sorted]));
    }

}