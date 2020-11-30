<?php declare(strict_types = 1);

return [
    ['GET', '/', ['App\controllers\Index', 'index']],
    ['GET', '/login', ['App\controllers\Login', 'index']],
    ['POST', '/login', ['App\controllers\Login', 'login']],
    ['GET', '/register', ['App\controllers\Register', 'register']],
    ['POST', '/register', ['App\controllers\Register', 'newUser']],
    ['GET', '/dashboard', ['App\controllers\Dashboard', 'dashboard']],
    ['GET', '/profile', ['App\controllers\Profile', 'profile']],
    ['GET', '/activities', ['App\controllers\Activities', 'renderAllActivities']],
    ['POST', '/activities', ['App\controllers\Activities', 'newActivity']],
    ['GET', '/activities/{id:\d+}', ['App\controllers\Activities', 'renderSingleActivity']],
    ['GET', '/activities/{id:\d+}/{join}', ['App\controllers\Activities', 'joinActivity']],
];