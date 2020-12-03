<?php


namespace App\controllers;

use App\helpers\EmailSender;
use App\helpers\PasswordGenerator;
use App\models\Member;
use App\models\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class RetrieveUser extends BaseController {


    public function renderRetrieveUserPage() {
        return new Response(
            $this->twig->render('pages/user/retrieve_user.html.twig')
        );
    }

    /**
     * Creates a user-account for a given member and sends a temporary password
     * to the members email address.
     *
     * @return Response renders Flash message and retrieve user page.
     */
    public function retrieveUser() {
        $session = new Session();
        $email = $this->request->get('email');

        $memberModel = new Member();
        $memberID = $memberModel->getSingleMemberID($email);

        $userModel = $userModel = new User();
        $userExists = $userModel->checkUserExistence($memberID);

        //checks if member exists and if a user does not exist.
        if ($memberID && !$userExists){
            $passwordGenerator = new PasswordGenerator();
            //generates temporary password
            $temporaryPassword = $passwordGenerator->generatePassword();
            $createUser = $userModel->registerUser($email, $temporaryPassword, $memberID);
            if ($createUser){
                $emailSender = new EmailSender();
                $emailSender->sendEmail("from", $email, "subject", $temporaryPassword);
            }
        }
        $session->getFlashBag()->add('retrieveUserSuccess',
            'If there is a member associated with this email, a temporary password
             will be sent to the email submitted.');
        return new Response(
            $this->twig->render('pages/user/retrieve_user.html.twig')
        );
    }


}