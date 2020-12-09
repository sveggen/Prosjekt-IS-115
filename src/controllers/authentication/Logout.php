<?php


namespace App\controllers\authentication;


use App\controllers\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Logout extends BaseController {

    /**
     * Logs the member out of the system.
     *
     * @return Response
     */
    public function logout(): Response {

        $session = new Session();
        $session->clear();
        $session->getFlashBag()->add('authentication', 'Successfully logged out');

        return new RedirectResponse('http://localhost:8081/login');
    }

}