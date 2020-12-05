<?php


namespace App\controllers;


use Symfony\Component\HttpFoundation\Response;
use App\models\Member;

class Dashboard extends BaseController{


    public function dashboard(): Response {
        return new Response(
            $this->twig->render('pages/member/dashboard.html.twig',
                ['members' =>$this->listMembers()]));

    }

    public function listMembers(){
        return (new Member)->getAllMembersAndMemberData();
    }

    public function renderMembersInterests(){

    }

    public function renderMemberOnPaymentStatus(){
        $memberModel = new Member();
        $query = $this->request->query->get('payment-status');
        $sorted = $memberModel->getMembersSortPaymentStatus($query);

        return new Response(
        $this->twig->render('pages/member/dashboard.html.twig',
            ['members' => $sorted]));
    }

}