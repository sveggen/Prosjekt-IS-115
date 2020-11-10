<?php


namespace App\controllers;


use Symfony\Component\HttpFoundation\Response;

class Profile extends BaseController{

    public function profile() {
        return new Response(
            $this->twig->render('profile.html.twig')
        );
    }

}