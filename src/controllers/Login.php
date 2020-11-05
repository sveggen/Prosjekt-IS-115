<?php


namespace App\controllers;

use Symfony\Component\HttpFoundation\Response;

class Login extends Base{

    public function login() {

        return new Response(
            $this->twig->render('login.html.twig')
        );

    }

    public function authenticate ($username, $password){
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        return new Response(
            $this->twig->render('index.html.twig')
        );
    }


}

