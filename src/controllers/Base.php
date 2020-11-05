<?php


namespace App\controllers;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class Base
{

    protected $twig;

    /**
     * Base constructor.
     */
    public function __construct()

    {
        $loader = new FilesystemLoader(__DIR__ . '/../../src/views/pages');
        $this->twig= new Environment($loader);
    }

}