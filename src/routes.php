<?php

// all the websites routes, listed in the format of request-type, URL-path with
// accepted parameters, path to handling controller, name of handling function.
return [
    ['GET', '/', ['App\controllers\home\Index', 'renderStartPage']],

    ['GET', '/login', ['App\controllers\authentication\Login', 'renderLoginPage']],
    ['POST', '/login', ['App\controllers\authentication\Login', 'login']],

    ['GET', '/logout', ['App\controllers\authentication\Logout', 'logout']],

    ['GET', '/retrieve-account', ['App\controllers\member\RetrieveAccount', 'renderRetrieveAccountPage']],
    ['POST', '/retrieve-account', ['App\controllers\member\RetrieveAccount', 'retrieveAccount']],

    ['GET', '/register', ['App\controllers\member\Register', 'renderRegisterPage']],
    ['POST', '/register', ['App\controllers\member\Register', 'newUser']],

    ['GET', '/dashboard', ['App\controllers\member\MemberDashboard', 'dashboard']],
    ['POST', '/dashboard/send-email', ['App\controllers\member\MemberDashboard', 'sendEmailToMarked']],
    ['GET', '/dashboard/search', ['App\controllers\member\MemberDashboard', 'renderMemberOnPaymentStatus']],

    ['POST', '/profile/{id:\d+}', ['App\controllers\member\Profile', 'updateProfile']],
    ['GET', '/profile/{id:\d+}', ['App\controllers\member\Profile', 'renderMemberProfile']],

    ['GET', '/activities', ['App\controllers\activity\Activities', 'renderAllActivities']],
    ['POST', '/activities', ['App\controllers\activity\Activities', 'newActivity']],
    ['GET', '/activities/{id:\d+}', ['App\controllers\activity\Activities', 'renderSingleActivity']],
    ['GET', '/activities/{id:\d+}/join', ['App\controllers\activity\Activities', 'joinActivity']],
    ['GET', '/activities/{id:\d+}/leave', ['App\controllers\activity\Activities', 'leaveActivity']]
];