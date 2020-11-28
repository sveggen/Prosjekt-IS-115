<?php

namespace App\controllers;

use App\models\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


class Index extends BaseController
{

    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index()
    {
        $model = new User();
        $users = $model->getAllUsers();

        $session = new Session();
        $session->start();
        $session->set('hei', 'heiaaaa');

            return new Response(
                $this->twig->render('pages/index.html.twig',
                    ['users' => $users, 'session' => $session]));

    }
}