<?php


namespace App\controllers\member;

use App\controllers\BaseController;
use App\helpers\EmailSender;
use App\helpers\Validate;
use App\models\authentication\User;
use App\models\member\Member;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class RetrieveAccount extends BaseController {


    public function renderRetrieveAccountPage(): Response {
        if ($this->hasMemberPrivileges() == true
            and $this->hasLeaderPrivileges() == true){
            return $this->methodNotAllowed();
        }

        return new Response(
            $this->twig->render('pages/member/retrieve_account.html.twig')
        );
    }

    /**
     * Creates a authentication-account for a given member and sends a temporary password
     * to the members email address.
     *
     * @return Response renders Flash message and retrieve authentication page.
     */
    public function retrieveAccount(): Response {
        if ($this->hasMemberPrivileges() == true
            and $this->hasLeaderPrivileges() == true){
            return $this->methodNotAllowed();
        }

        $memberModel = new Member();
        $email = $this->request->get('email');
        $memberID = $memberModel->getSingleMemberID($email);

        $userModel = $userModel = new User();
        $userExists = $userModel->checkUserExistence($memberID);

        //checks if member exists and if an associated authentication does not exist.
        if ($memberID && !$userExists) {
            // create authentication with temporary password
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
            $this->twig->render('pages/member/retrieve_account.html.twig')
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