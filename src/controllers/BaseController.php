<?php


namespace App\controllers;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Loader\FilesystemLoader;

abstract class BaseController {

    protected $request;
    protected $twig;

    /**
     * BaseController constructor.
     */
    public function __construct() {
        $this->httpFoundationSetup();
        $this->twigSetup();
    }

    /**
     * Setup for HttpFoundation
     */
    private function httpFoundationSetup() {
        $this->request = Request::createFromGlobals();
    }

    /**
     * Setup for Twig.
     */
    private function twigSetup() {
        // defines directory of Twig-templates.
        $loader = new FilesystemLoader(__DIR__ . '/../../src/views/');
        // load the directory as the Twig Environment
        $this->twig = new Environment($loader);

        // set default timezone
        $this->twig->getExtension(CoreExtension::class)->setTimezone('Europe/Paris');
        // add the session variable as Global Variable in Twig
        $this->twig->addGlobal('session', new Session);
    }


    protected function hasLeaderPrivileges(): bool {
        $session = new Session();
        $role = $session->get('role');
        // if user is "leader" or an "admin"
        if ($this->getSession() && $role == 4) {
            return true;
        } else {
            return false;
        }
    }


    protected function hasMemberPrivileges(): bool {
        $session = new Session();
        $role = $session->get('role');
        // if user is a "member"
        if ($this->getSession() && $role == 3) {
            return true;
        } else {
            return false;
        }
    }

    private function getSession() {
        $session = new Session();
        $memberID = $session->get('memberID');
        if ($memberID) {
            return true;
        } else {
            return false;
        }

    }
        protected function methodNotAllowed(): Response {
            return new Response(
                $this->twig->render('pages/errors/405.html.twig'));
        }



}