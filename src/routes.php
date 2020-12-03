<?php declare(strict_types = 1);

return [
    ['GET', '/', ['App\controllers\Index', 'renderStartPage']],
    ['GET', '/login', ['App\controllers\Login', 'renderLoginPage']],
    ['GET', '/logout', ['App\controllers\Logout', 'logout']],
    ['GET', '/retrieve-user', ['App\controllers\RetrieveUser', 'renderRetrieveUserPage']],
    ['POST', '/retrieve-user', ['App\controllers\RetrieveUser', 'retrieveUser']],
    ['POST', '/login', ['App\controllers\Login', 'login']],
    ['GET', '/register', ['App\controllers\Register', 'renderRegisterPage']],
    ['POST', '/register', ['App\controllers\Register', 'newUser']],
    ['GET', '/dashboard', ['App\controllers\Dashboard', 'dashboard']],
    ['GET', '/profile', ['App\controllers\Profile', 'renderMyProfile']],
    ['GET', '/profile/{id:\d+}', ['App\controllers\Profile', 'renderMemberProfile']],
    ['GET', '/activities', ['App\controllers\Activities', 'renderAllActivities']],
    ['POST', '/activities', ['App\controllers\Activities', 'newActivity']],
    ['GET', '/activities/{id:\d+}', ['App\controllers\Activities', 'renderSingleActivity']],
    ['GET', '/activities/{id:\d+}/join', ['App\controllers\Activities', 'joinActivity']],
    ['GET', '/activities/{id:\d+}/leave', ['App\controllers\Activities', 'leaveActivity']]
];