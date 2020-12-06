<?php


namespace App\controllers;

use App\models\Member;
use App\models\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Login extends BaseController {


    public function renderLoginPage(): Response {
        if ($this->hasMemberPrivileges() == true
            or $this->hasLeaderPrivileges() == true){
            return $this->methodNotAllowed();
        }

        return new Response(
            $this->twig->render('pages/user/login.html.twig')
        );
    }

    public function login() {
        if ($this->hasMemberPrivileges() == true
            or $this->hasLeaderPrivileges() == true){
            return $this->methodNotAllowed();
        }

        $session = new Session();

        $email = $this->request->get('email');
        $password = $this->request->get('password');

        if ($email && $password){
            $userModel = new User();
            $credentials = $userModel->login($email, $password);

            if ($credentials) {

                $this->setUserSession($credentials);
                return new RedirectResponse('http://localhost:8081');

            } else {
                $session->getFlashBag()->add('loginError', 'Wrong password or username');

                return new Response(
                    $this->twig->render('/pages/user/login.html.twig')
                );
            }
        } else{
            $session->getFlashBag()->add('loginError', 'Password or username is missing.');

            return new Response(
                $this->twig->render('/pages/user/login.html.twig')
            );
        }
    }

    private function setUserSession($credentials){
        $session = new Session();

        $memberModel = new Member();
        $userRoles = $memberModel->getSingleUserRoles($credentials['fk_member_id'])->fetch_assoc();
        // highest role/privilege the user has eg. 4 is leader, 3 is member
        $highestRole = max($userRoles['fk_role_id']);

        $session->set('memberID', $credentials['fk_member_id']);
        $session->set('username', $credentials['username']);
        $session->set('role', 4);

    }


}

