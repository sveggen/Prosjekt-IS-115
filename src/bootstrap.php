<?php declare(strict_types=1);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

/*
* Retrieval of routes
*/

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $routes = require __DIR__ . '/routes.php';
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
});

/*
 * Twig
 */
$loader = new FilesystemLoader(__DIR__ . '/views/pages');
$twig = new Environment($loader);

/*
* Dispatcher using a request with an accompanying route
*/

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $response = new Response(
            $twig->render('404.html.twig'), Response::HTTP_NOT_FOUND
        );
        $response->prepare($request);
        $response->send();
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response = new Response(
            $twig->render('405.html.twig'), Response::HTTP_METHOD_NOT_ALLOWED
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
