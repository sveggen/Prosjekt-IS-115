<?php


namespace App\controllers;


use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Loader\FilesystemLoader;

abstract class BaseController
{

    protected $request;
    protected $twig;

    /**
     * BaseController constructor.
     */
    public function __construct()

    {
        $loader = new FilesystemLoader(__DIR__ . '/../../src/views/');
        $this->twig= new Environment($loader);
        $this->twig->getExtension(CoreExtension::class)->setTimezone('Europe/Paris');
        $this->request = Request::createFromGlobals();
    }


}