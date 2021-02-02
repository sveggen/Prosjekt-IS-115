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
        // clears all session variables related to the site
        $session->clear();

        return new RedirectResponse('http://localhost:8081/login');
    }

}