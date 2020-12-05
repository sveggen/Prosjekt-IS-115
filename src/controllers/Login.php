<?php


namespace App\controllers;

use App\models\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Login extends BaseController {

    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();

    }

    public function renderLoginPage() {
        return new Response(
            $this->twig->render('pages/user/login.html.twig')
        );
    }

    public function login() {
        $session = new Session();
        $email = $this->request->get('email');
        $password = $this->request->get('password');

        if ($email && $password){
            $credentials = $this->userModel->login($email, $password);

            if ($credentials) {
                $session->set('userID', $credentials['user_id']);
                $session->set('memberID', $credentials['fk_member_id']);
                $session->set('userID', $credentials['user_id']);
                $session->set('username', $credentials['username']);

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


}

