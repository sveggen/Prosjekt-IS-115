<?php

// all the websites routes, listed in the format of request-type, URL-path with
// accepted parameters and their format, path to handling controller, name of handling function.
return [

    // --------------- unauthenticated ------------
    ['GET', '/', ['App\controllers\home\Index', 'renderStartPage']],

    ['GET', '/login', ['App\controllers\authentication\Login', 'renderLoginPage']],
    ['POST', '/login', ['App\controllers\authentication\Login', 'login']],

    ['GET', '/register', ['App\controllers\member\Register', 'renderRegisterPage']],
    ['POST', '/register', ['App\controllers\member\Register', 'newUser']],

    ['GET', '/retrieve-account', ['App\controllers\member\RetrieveAccount', 'renderRetrieveAccountPage']],
    ['POST', '/retrieve-account', ['App\controllers\member\RetrieveAccount', 'retrieveAccount']],

    // ---------------- authenticated -  member/leader ----------------
    ['GET', '/logout', ['App\controllers\authentication\Logout', 'logout']],

    ['POST', '/profile/{id:\d+}', ['App\controllers\member\Profile', 'updateProfileImage']],
    ['GET', '/profile/{id:\d+}', ['App\controllers\member\Profile', 'renderMemberProfile']],
    ['GET', '/profile/{id:\d+}/delete', ['App\controllers\member\Profile', 'deleteMember']],

    ['GET', '/activities', ['App\controllers\activity\Activities', 'renderAllActivities']],
    ['POST', '/activities', ['App\controllers\activity\Activities', 'newActivity']],
    ['GET', '/activities/{id:\d+}', ['App\controllers\activity\SingleActivity', 'renderSingleActivity']],
    ['GET', '/activities/{id:\d+}/join', ['App\controllers\activity\SingleActivity', 'joinActivity']],
    ['GET', '/activities/{id:\d+}/leave', ['App\controllers\activity\SingleActivity', 'leaveActivity']],

    // ------------- authenticated - leader --------------------
    ['GET', '/add-member', ['App\controllers\member\AddMember', 'renderAddMemberPage']],
    ['POST', '/add-member', ['App\controllers\member\AddMember', 'addMember']],

    ['GET', '/activities/{id:\d+}/remove', ['App\controllers\activity\SingleActivity', 'removeActivity']],

    ['GET', '/dashboard', ['App\controllers\member\MemberDashboard', 'renderMemberDashboard']],
    ['POST', '/dashboard/send-email', ['App\controllers\member\MemberDashboard', 'sendEmailToMarked']],
    ['GET', '/dashboard/search', ['App\controllers\member\MemberDashboard', 'distributeSearchQuery']]
    // ------------------------------------------------
];