<?php declare(strict_types = 1);


return [
    ['GET', '/', ['App\controllers\IndexController', 'index']],
    ['GET', '/exception', ['App\controllers\IndexController', 'exception' ]],
];