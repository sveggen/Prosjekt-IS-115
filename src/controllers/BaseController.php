<?php


namespace App\controllers;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Parent class for all controllers.
 *
 * Class BaseController
 * @package App\controllers
 */
abstract class BaseController {

    protected $request;
    protected $twig;

    /**
     * BaseController constructor.
     */
    public function __construct() {
        $this->symfonyHttpFoundationSetup();
        $this->twigSetup();
    }

    /**
     * Setup for Symfony HttpFoundation
     */
    private function symfonyHttpFoundationSetup() {
        // loads the "request"-variable with all the PHP-superglobals.
        $this->request = Request::createFromGlobals();
    }

    /**
     * Setup for Twig Template Engine.
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

    /**
     * Checks if a given member has "leader"-privileges,
     * based on the "role" session variable.
     *
     * @return bool
     */
    protected function hasLeaderPrivileges(): bool {
        $session = new Session();
        $role = $session->get('role');
        // return true if the member's role is "leader"
        if ($this->getMemberSession() && $role == 'leader') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if a given member is logged in, based
     * on the "memberID"-session object.
     *
     * @return bool
     */
    private function getMemberSession(): bool {
        $session = new Session();
        $memberID = $session->get('memberID');
        if ($memberID) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Checks if a given member has "member"-privileges,
     * based on the "role" session variable.
     *
     * @return bool
     */
    protected function hasMemberPrivileges(): bool {
        $session = new Session();
        $role = $session->get('role');
        // return true if the member's role is "member"
        if ($this->getMemberSession() && $role == 'member') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Redirects the user to the Method Not Allowed Error-page.
     */
    protected function methodNotAllowed(): Response {
        return new Response(
            $this->twig->render('pages/errors/405.html.twig'));
    }



}