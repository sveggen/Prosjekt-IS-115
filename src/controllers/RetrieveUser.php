<?php


namespace App\controllers;

use App\helpers\EmailSender;
use App\helpers\Validate;
use App\models\Member;
use App\models\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class RetrieveUser extends BaseController {


    public function renderRetrieveUserPage(): Response {
        if ($this->hasMemberPrivileges() == true
            or $this->hasLeaderPrivileges() == true){
            return $this->methodNotAllowed();
        }

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
    public function retrieveUser(): Response {
        if ($this->hasMemberPrivileges() == true
            or $this->hasLeaderPrivileges() == true){
            return $this->methodNotAllowed();
        }

        $memberModel = new Member();
        $email = $this->request->get('email');
        $memberID = $memberModel->getSingleMemberID($email);

        $userModel = $userModel = new User();
        $userExists = $userModel->checkUserExistence($memberID);

        //checks if member exists and if an associated user does not exist.
        if ($memberID && !$userExists) {
            // create user with temporary password
            $temporaryPassword = $userModel->createUserAndPassword($memberID, $email);

            //checks if temporary password was created
            if ($temporaryPassword) {
                $this->sendTemporaryPasswordEmail($email, $temporaryPassword);
            }
        }

        $session = new Session();
        $session->getFlashBag()->add('retrieveUserSuccess',
            'If there is a member associated with this email, a temporary password
             will be sent to the email submitted.');

        return new Response(
            $this->twig->render('pages/user/retrieve_user.html.twig')
        );
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