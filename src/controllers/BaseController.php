<?php


namespace App\controllers;

use App\helpers\validation\ContainsAlphanumeric;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Validation;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Loader\FilesystemLoader;

abstract class BaseController {

    protected $request;
    protected $twig;
    protected $validator;

    /**
     * BaseController constructor.
     */
    public function __construct() {
        $this->symfonyHttpFoundationSetup();
        $this->symfonyValidatorSetup();
        $this->twigSetup();
    }

    /**
     * Setup for Symfony HttpFoundation
     */
    private function symfonyHttpFoundationSetup() {
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

    protected function hasLeaderPrivileges(): bool {
        $session = new Session();
        $role = $session->get('role');
        // if the members role is "leader"
        if ($this->getMemberSession() && $role == 'leader') {
            return true;
        } else {
            return false;
        }
    }

    private function getMemberSession(): bool {
        $session = new Session();
        $memberID = $session->get('memberID');
        if ($memberID) {
            return true;
        } else {
            return false;
        }

    }

    protected function hasMemberPrivileges(): bool {
        $session = new Session();
        $role = $session->get('role');
        // if the members role is "member"
        if ($this->getMemberSession() && $role == 'member') {
            return true;
        } else {
            return false;
        }
    }

    protected function methodNotAllowed(): Response {
        return new Response(
            $this->twig->render('pages/errors/405.html.twig'));
    }

    /**
     * Setup for Symfony Validation.
     */
    private function symfonyValidatorSetup() {
        $this->validator = Validation::createValidator();
    }


}