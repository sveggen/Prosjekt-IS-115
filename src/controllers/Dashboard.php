<?php


namespace App\controllers;


use App\helpers\EmailSender;
use Symfony\Component\HttpFoundation\Response;
use App\models\Member;
use Symfony\Component\HttpFoundation\Session\Session;

class Dashboard extends BaseController {


    public function dashboard(): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        return new Response(
            $this->twig->render('pages/member/dashboard.html.twig',
                ['members' => $this->listMembers()]));
    }

    private function listMembers() {
        return (new Member)->getAllMembersAndMemberData();
    }

    public function renderMembersInterests(): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }
    }

    public function sendEmailToMarked(): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $sendEmail = new EmailSender();
        $recipients = $this->request->get('markedMembers');
        $subject = $this->request->get('subject');
        $content = $this->request->get('content');

        if ($sendEmail->sendEmail($recipients, $subject, $content)){
            (new Session)->getFlashBag()->add('dashboardSuccess', 'Email was sent successfully');
            return $this->dashboard();
        } else {
            (new Session)->getFlashBag()->add(
                'dashboardError',
                'There occurred a problem with sending your email, please try again.');
            return $this->dashboard();
        }
    }

    public function renderMemberOnPaymentStatus(): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $memberModel = new Member();
        $query = $this->request->query->get('payment-status');
        $sorted = $memberModel->getMembersSortPaymentStatus($query);

        return new Response(
            $this->twig->render('pages/member/dashboard.html.twig',
                ['members' => $sorted]));
    }

    public function deleteMember(){

    }

}