<?php


namespace App\controllers\member;

use App\controllers\BaseController;
use App\models\interest\Interest;
use App\models\member\Member;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\helpers\FileHandler;
use Symfony\Component\HttpFoundation\Session\Session;

class Register extends BaseController {


    public function renderRegisterPage(): Response {
        if ($this->hasMemberPrivileges() == true
            and $this->hasLeaderPrivileges() == true){
            return $this->methodNotAllowed();
        }

        $interestModel = new Interest();
        $interests = $interestModel->getAllInterests();

        return new Response(
            $this->twig->render('pages/member/register.html.twig',
            ['interests' => $interests])
        );
    }

    public function newUser() {
        if ($this->hasMemberPrivileges() == true
            and $this->hasLeaderPrivileges() == true){
            return $this->methodNotAllowed();
        }

        $image = $this->request->files->get('image');

        $memberData = [
            'firstName' => $this->request->get('first-name'),
            'lastName' => $this->request->get('last-name'),
            'username' => $this->request->get('username'),
            'email' => $this->request->get('email'),
            'password' => $this->request->get('password'),
            'phoneNumber' => $this->request->get('phone-number'),
            'birthDate' => $this->request->get('birth-date'),
            'gender' => $this->request->get('gender'),
            'streetAddress' => $this->request->get('street-address'),
            'zipCode' => $this->request->get('zip-code'),
            'interests' => (array)$this->request->get('interests'),
            'paymentStatus' => 0,
                'role' => 3, // ordinary member,
        ];

        //define authentication role
        $memberModel = new Member();
        $registerMember = $memberModel->registerMember($memberData);

        if ($registerMember) {
            $fileUpload = new FileHandler();
            $fileUpload->uploadProfileImage($image, $registerMember);
            return new RedirectResponse('http://localhost:8081/login');
        } else {
            (new Session)->getFlashBag()->add('registrationError', 'Account could not be created');
            return new Response(
                $this->twig->render('pages/member/register.html.twig')
            );
        }
    }
}