<?php declare(strict_types = 1);


return [
    ['GET', '/', ['App\controllers\IndexController', 'index']],
    ['GET', '/login', ['App\controllers\LoginController', 'login']],
    ['GET', '/exception', ['App\controllers\IndexController', 'exception' ]],
];