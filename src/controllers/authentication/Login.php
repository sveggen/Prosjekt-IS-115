<?php


namespace App\controllers\authentication;

use App\controllers\BaseController;

use App\models\authentication\User;
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

    public function login() {
        if ($this->hasMemberPrivileges() == true
            and $this->hasLeaderPrivileges() == true) {
            return $this->methodNotAllowed();
        }

        $session = new Session();

        $email = $this->request->get('email');
        $password = $this->request->get('password');

        if ($email && $password) {
            $userModel = new User();
            $credentials = $userModel->login($email, $password);

            if ($credentials) {

                $this->setUserSession($credentials);
                return new RedirectResponse('http://localhost:8081');

            } else {
                $session->getFlashBag()->add('loginError', 'Wrong password or username');

                return new Response(
                    $this->twig->render('/pages/authentication/login.html.twig')
                );
            }
        } else {
            $session->getFlashBag()->add('loginError', 'Password or username is missing.');

            return new Response(
                $this->twig->render('/pages/authentication/login.html.twig')
            );
        }
    }

    private function setUserSession($credentials) {
        $session = new Session();

        $session->set('memberID', $credentials['fk_member_id']);
        $session->set('username', $credentials['username']);

        $role = $this->getUserRole($credentials['fk_member_id']);
        $session->set('role', $role);
    }

    /**
     * @param $memberID
     * @return string The user's role with the highest privilege and
     * assigns this role to the session object
     */
    private function getUserRole($memberID): string {
        $roleModel = new Role();
        $userRoles = $roleModel->getSingleMemberRoles($memberID);

        // evaluates the users roles - assigns the users role with
        // the highest privilege to the session object.
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

