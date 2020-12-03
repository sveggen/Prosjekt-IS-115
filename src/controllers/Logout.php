<?php


namespace App\controllers;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Logout extends BaseController {

    public function logout() {
        $session = new Session();

        $currentSession = $session->has('memberID');
        if ($currentSession){
            $session->clear();
        }
        return new RedirectResponse('http://localhost:8081');
    }


}