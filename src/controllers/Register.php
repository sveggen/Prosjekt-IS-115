<?php


namespace App\controllers;

use App\models\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\helpers\UploadFile;

class Register extends BaseController
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
            $this->twig->render('pages/user/register.html.twig')
        );

    }

    public function newUser(){
        $image = $this->request->files->get('image');
        $email = $this->request->get('email');
        $password = $this->request->get('password');

        if ($this->userModel->registerUser($email, $password, 4)){
            (new UploadFile)->uploadFile($image);
            return new RedirectResponse('http://localhost:8081');
        } else {
            return new Response(
                $this->twig->render('pages/user/register.html.twig')
            );
        }
    }
}