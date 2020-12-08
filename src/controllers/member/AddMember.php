<?php


namespace App\controllers\member;


use App\controllers\BaseController;
use App\models\interest\Interest;
use App\models\member\Member;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class AddMember extends BaseController {

    public function renderAddMemberPage(): Response {
        if ($this->hasLeaderPrivileges() == false){
            return $this->methodNotAllowed();
        }

        $interestModel = new Interest();
        $interests = $interestModel->getAllInterests();

        return new Response(
            $this->twig->render('pages/member/add_member.html.twig',
                ['interests' => $interests])
        );
    }

    /**
     * Adds a new member to the youth club.
     */
    public function addMember(): Response {

        $memberData = [
            'firstName' => $this->request->get('first-name'),
            'lastName' => $this->request->get('last-name'),
            'email' => $this->request->get('email'),
            'phoneNumber' => $this->request->get('phone-number'),
            'birthDate' => $this->request->get('birth-date'),
            'gender' => $this->request->get('gender'),
            'streetAddress' => $this->request->get('street-address'),
            'zipCode' => $this->request->get('zip-code'),
            'interests' => $this->request->get('interests'),
            'paymentStatus' => $this->request->get('payment-status'),
            'role' => $this->request->get('roles'),
        ];

        $memberModel = new Member();
        $addMember = $memberModel->adminRegisterMember($memberData);

        $session = new Session();

        if($addMember) {
            $session->getFlashBag()->add('addMemberSuccess', 'Member was successfully added');
        } else {
            $session->getFlashBag()->add('addMemberError', 'There occurred an error and the member was not added.');
        }
        return $this->renderAddMemberPage();
    }

}