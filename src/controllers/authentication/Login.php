<?php


namespace App\controllers\authentication;

use App\controllers\BaseController;

use App\helpers\Validate;
use App\models\user\User;
use App\models\member\Member;
use App\models\role\Role;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Login extends BaseController {


    public function renderLoginPage(): Response {
        if ($this->hasMemberPrivileges() == true
            and $this->hasLeaderPrivileges() == true) {
            return $this->methodNotAllowed();
        }

        return new Response(
            $this->twig->render('pages/authentication/login.html.twig')
        );
    }

    /**
     * Logs the member into the system, and assigns them a session based on the
     * members role + their Member ID.
     *
     * @return RedirectResponse|Response
     */
    public function login() {
        if ($this->hasMemberPrivileges() == true
            and $this->hasLeaderPrivileges() == true) {
            return $this->methodNotAllowed();
        }

        $session = new Session();

        $email = $this->request->get('email');
        $password = $this->request->get('password');

        // validates if email and password is set. NB: this will only happen
        // if the user bypasses the JavaScript form validation.
        $errorMessages = $this->validateLoginForm($email, $password);

        // redirects user to home page if user bypasses JavaScript form validation
        if (!empty($errorMessages)){
            return new RedirectResponse('/');
        }

        $userModel = new User();
        // checks if the credentials provided are correct
        $correctCredentials = $userModel->login($email, $password);

        // sets the user session if the credentials where correct
        if ($correctCredentials) {
            $this->setUserSession($correctCredentials);
            return new RedirectResponse('/');
        } else {
            $session->getFlashBag()->add('loginError', 'Wrong password or username');
            return new Response(
                $this->twig->render('/pages/authentication/login.html.twig'));
        }
    }

    /**
     * Returns true if any of the tests fail
     *
     * @param $email
     * @param $password
     * @return bool
     */
    private function validateLoginForm($email, $password): bool {
        $validator = new Validate();

        //validate all the fields from the form
        $validator->validatePasswordNotEmpty($password);
        $validator->validateEmail($email);

        // get the list of error messages
        $errorMessages = $validator->getErrorMessages();

        if (!empty($errorMessages)){
            return true;
        } else{
            return false;}
    }

    /**
     * Sets variables for the user.
     *
     * @param $credentials
     */
    private function setUserSession($credentials) {
        $session = new Session();

        $session->set('memberID', $credentials['fk_member_id']);
        $session->set('username', $credentials['username']);

        $role = $this->getUserRole($credentials['fk_member_id']);
        $session->set('role', $role);
    }

    /**
     * @param $memberID
     * @return string Gets the user's role with the highest privilege.
     */
    private function getUserRole($memberID): string {
        $roleModel = new Role();
        $userRoles = $roleModel->getSingleMemberRoles($memberID);

        // evaluates the users roles - assigns the user's role with
        // the highest privilege to the Session object.
        $role = "";
        while ($row = $userRoles->fetch_assoc()){
            if ($row['privilege'] == 'leader') {
                $role = $row['privilege'];
                break;
            } elseif ($row['privilege'] == 'member') {
                $role = $row['privilege'];
            } else {
                exit();
            }
        }
        return $role;
    }
}

