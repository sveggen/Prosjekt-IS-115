<?php


namespace App\controllers;


use Symfony\Component\HttpFoundation\Response;

class Activities extends BaseController{

    public function activities() {
        return new Response(
            $this->twig->render('activities.html.twig')
        );

    }

}