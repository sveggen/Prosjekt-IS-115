<?php


namespace App\controllers;


use Symfony\Component\HttpFoundation\Response;

class Dashboard extends BaseController{


    public function dashboard() {
        return new Response(
            $this->twig->render('dashboard.html.twig')
        );

    }

}