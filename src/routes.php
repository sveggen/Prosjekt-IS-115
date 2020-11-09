<?php declare(strict_types = 1);

return [
    ['GET', '/', ['App\controllers\Index', 'index']],
    ['GET', '/login', ['App\controllers\Login', 'login']],
    ['POST', '/login', ['App\controllers\Login', 'authenticate']],
    ['GET', '/register', ['App\controllers\Register', 'register']],
    ['POST', '/register', ['App\controllers\Register', 'newUser']],
];