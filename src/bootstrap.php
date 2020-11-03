<?php declare(strict_types = 1);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

/*
* Routing
*/

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $routes = require __DIR__ . '/routes.php';
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
});

/*
* Dispatcher
*/

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $response = new Response(
            "404", Response::HTTP_NOT_FOUND
        );
        $response->prepare($request);
        $response->send();
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response = new Response(
            "405", Response::HTTP_METHOD_NOT_ALLOWED
        );
        $response->prepare($request);
        $response->send();
        break;

    case FastRoute\Dispatcher::FOUND:
        $classPath = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2];

        $controller = new $classPath;
        $response = $controller->$method($vars);

        if ($response instanceof Response) {
                $response
                    ->prepare($request)
                    ->send();
}

break;
default:

    $response = new Response('Received unexpected response from dispatcher.', Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->prepare($request);
        $response->send();
    return;
}
