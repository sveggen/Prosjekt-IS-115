<?php


namespace App\controllers;

use Symfony\Component\HttpFoundation\Response;

class Register extends Base
{
    public function register()
    {

        return new Response(
            $this->twig->render('register.html.twig')
        );

    }
}