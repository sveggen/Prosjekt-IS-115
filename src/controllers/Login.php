<?php


namespace App\controllers;

use App\models\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class Login extends Base{

    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();

    }

    public function login() {
        return new Response(
            $this->twig->render('login.html.twig')
        );

    }

    public function authenticate (){
        $request = Request::createFromGlobals();
        $email = $request->get('email');
        $password = $request->get('password');
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        if ($this->userModel->login($email)){
            //set cookie
            return new RedirectResponse('http://localhost:8081');
        } else {
            return new Response(
                $this->twig->render('login.html.twig')
            );
        }
    }


}

