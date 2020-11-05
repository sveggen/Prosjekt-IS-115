<?php declare(strict_types = 1);

return [
    ['GET', '/', ['App\controllers\Index', 'index']],
    ['GET', '/login', ['App\controllers\Login', 'login']],
    ['GET', '/register', ['App\controllers\Register', 'register']],
];