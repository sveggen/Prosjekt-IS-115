<?php


namespace App\controllers;

use App\models\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Register extends Base
{

    private $userModel;

    /**
     * Register constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();

    }

    public function register()
    {

        return new Response(
            $this->twig->render('register.html.twig')
        );

    }

    public function newUser(){
        $request = Request::createFromGlobals();
        $email = $request->get('email');
        $password = $request->get('password');
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        if ($this->userModel->registerUser($email, $hashedPassword)){
            return new Response(
                $this->twig->render('index.html.twig')
            );
        } else {
            return new Response(
                $this->twig->render('register.html.twig')
            );
        }
    }
}