<?php


namespace App\controllers;

use App\models\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Login extends BaseController{

    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();

    }

    public function index() {
        return new Response(
            $this->twig->render('pages/user/login.html.twig')
        );

    }

    public function login (){
        $email = $this->request->get('email');
        $password = $this->request->get('password');

        if ($this->userModel->login($email, $password)){
            //set cookie
            return new RedirectResponse('http://localhost:8081');
        } else {
            return new Response(
                $this->twig->render('/pages/user/login.html.twig')
            );
        }
    }


}

