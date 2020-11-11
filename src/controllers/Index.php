<?php

namespace App\controllers;

use App\models\User;
use Symfony\Component\HttpFoundation\Response;


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

            return new Response(
                $this->twig->render('pages/index.html.twig',
                    ['name' => $users]));

    }
}