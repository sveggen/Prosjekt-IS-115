<?php


namespace App\controllers;

use App\models\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\helpers\UploadFile;
use Symfony\Component\HttpFoundation\Session\Session;

class Register extends BaseController {

    private $userModel;

    /**
     * Register constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();

    }

    public function renderRegisterPage() {
        return new Response(
            $this->twig->render('pages/user/register.html.twig')
        );
    }

    public function newUser() {
        $image = $this->request->files->get('image');
        $email = $this->request->get('email');
        $password = $this->request->get('password');

        if ($this->userModel->registerUser($email, $password, 4)) {
            (new UploadFile)->uploadFile($image);
            return new RedirectResponse('http://localhost:8081');
        } else {
            (new Session)->getFlashBag()->add('registrationError', 'Account could not be created');
            return new Response(
                $this->twig->render('pages/user/register.html.twig')
            );
        }
    }
}