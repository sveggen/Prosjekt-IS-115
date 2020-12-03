<?php


namespace App\controllers;

use App\models\Interest;
use App\models\Member;
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
        $interestModel = new Interest();
        $interests = $interestModel->getAllInterests();

        return new Response(
            $this->twig->render('pages/user/register.html.twig',
            ['interests' => $interests])
        );
    }

    public function newUser() {
        $image = $this->request->files->get('image');

        $memberdata['firstName'] = $this->request->get('first-name');
        $memberdata['lastName'] = $this->request->get('last-name');
        $memberdata['email'] = $this->request->get('email');
        $memberdata['password'] = $this->request->get('password');
        $memberdata['phoneNumber'] = $this->request->get('phone-number');
        $memberdata['birthDate'] = $this->request->get('birth-date');
        $memberdata['gender'] = $this->request->get('gender');
        $memberdata['streetAddress'] = $this->request->get('street-address');
        $memberdata['zipCode'] = $this->request->get('zip-code');
        $memberdata['interests'] = $this->request->get('interests');

        //define user role
        $role = 3;
        $memberModel = new Member();

        if ($memberModel->registerMember($memberdata, $role)) {
            (new UploadFile)->uploadFile($image);
            return new RedirectResponse('http://localhost:8081/login');
        } else {
            (new Session)->getFlashBag()->add('registrationError', 'Account could not be created');
            return new Response(
                $this->twig->render('pages/user/register.html.twig')
            );
        }
    }
}