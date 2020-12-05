<?php


namespace App\controllers;

use App\helpers\EmailSender;
use App\helpers\PasswordGenerator;
use App\helpers\Validate;
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

        if ($memberID && !$userExists) {
            //checks if member exists and if an associated user does not exist.
            $temporaryPassword = $this->createUserAndPassword($memberID, $email);

            //checks if temporary password was created
            if ($temporaryPassword) {
                $this->sendTemporaryPasswordEmail($email, $temporaryPassword);
            }
        }


        $session->getFlashBag()->add('retrieveUserSuccess',
            'If there is a member associated with this email, a temporary password
             will be sent to the email submitted.');
        $session->set('pass', $temporaryPassword);

        return new Response(
            $this->twig->render('pages/user/retrieve_user.html.twig')
        );
    }

    /**
     * Creates a User and generates a temporary password.
     *
     * @param $memberID
     * @param $email
     * @return false|string
     */
    private function createUserAndPassword($memberID, $email) {
        $passwordGenerator = new PasswordGenerator();
        //generates temporary password
        $temporaryPassword = $passwordGenerator->generatePassword();
        $userModel = new User();
        $createUser = $userModel->registerUser($email, $temporaryPassword, $memberID);
        if ($createUser) {
            return $temporaryPassword;
        } else {
            return false;
        }
    }

    private function sendTemporaryPasswordEmail($email, $temporaryPassword) {
        if ((new Validate)->validateEmail($email)) {

            $subject = "Retrieve account";
            $content = "<p> Your temporary password is: $temporaryPassword.
                        You can log in <a href='http://localhost:8081/login'>here</a> and change
                        your password.</p><br> 
                        Best Regards <br>
                        Neo Youtclub";
            $emailSender = new EmailSender();
            $emailSender->sendEmail($email, $subject, $content);
        }
    }

}