<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/../vendor/autoload.php';

// Initialises the request variable with all the PHP-super globals.
$request = Request::createFromGlobals();

// Links Environment variables set in the dotenv-file
// in the root-dir, to the $_ENV global variable.
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');


// Fetches all the website's routes
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $routes = require __DIR__ . '/routes.php';
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
});

// Twig setup for the dispatcher
$loader = new FilesystemLoader(__DIR__ . '/views/');
$twig = new Environment($loader);


// Dispatcher that redirects the request to the correct Controller.
$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());
switch ($routeInfo[0]) {

    // if the page can not be found an error page will be rendered.
    case FastRoute\Dispatcher::NOT_FOUND:
        $response = new Response(
            $twig->render('pages/errors/404.html.twig'), Response::HTTP_NOT_FOUND
        );
        $response->prepare($request);
        $response->send();
        break;

        // if the page is unavailable for the user an error page will be rendered.
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response = new Response(
            $twig->render('/pages/errors/405.html.twig'), Response::HTTP_METHOD_NOT_ALLOWED
        );
        $response->prepare($request);
        $response->send();
        break;

        // if the page is found, the linked routes will be
        // used to redirect the request to the correct function
    case FastRoute\Dispatcher::FOUND:
        $controllerPath = $routeInfo[1][0]; //controller to be called
        $function = $routeInfo[1][1]; // function to be called
        $parameters = $routeInfo[2]; // parameters to be passed

        $controller = new $controllerPath;
        $response = $controller->$function($parameters);

        if ($response instanceof Response) {
            $response
                ->prepare($request)
                ->send(); // redirect request to the correct controller + function.
        }

        break;
    default:

        // Renders the 404 error page if all previous redirections fail.
        $response = new Response(
            $twig->render('pages/errors/404.html.twig'), Response::HTTP_NOT_FOUND
        );
        $response->prepare($request);
        $response->send();
        break;
}
