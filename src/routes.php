<?php

// all the websites routes, listed in the format of 1. request-type, 2. URL-path with
// accepted parameters and their format, 3. path to handling controller, 4. name of handling function.
return [

    // --------------- unauthenticated ------------
    ['GET', '/', ['App\controllers\home\Index', 'renderStartPage']],

    ['GET', '/login', ['App\controllers\authentication\Login', 'renderLoginPage']],
    ['POST', '/login', ['App\controllers\authentication\Login', 'login']],

    ['GET', '/register', ['App\controllers\member\Register', 'renderRegisterPage']],
    ['POST', '/register', ['App\controllers\member\Register', 'registerMember']],

    ['GET', '/retrieve-account', ['App\controllers\member\RetrieveAccount', 'renderRetrieveAccountPage']],
    ['POST', '/retrieve-account', ['App\controllers\member\RetrieveAccount', 'retrieveAccount']],

    // ---------------- authenticated -  member/leader ----------------
    ['GET', '/logout', ['App\controllers\authentication\Logout', 'logout']],

    ['POST', '/profile/{memberID:\d+}', ['App\controllers\member\Profile', 'updateProfileImage']],
    ['POST', '/profile/{memberID:\d+}/update', ['App\controllers\member\Profile', 'updateProfile']],
    ['POST', '/profile/{memberID:\d+}/update-password', ['App\controllers\member\Profile', 'updatePassword']],
    ['GET', '/profile/{memberID:\d+}', ['App\controllers\member\Profile', 'renderMemberProfile']],

    ['GET', '/activities', ['App\controllers\activity\Activities', 'renderAllActivities']],
    ['GET', '/activities/{activityID:\d+}', ['App\controllers\activity\SingleActivity', 'renderSingleActivity']],
    ['GET', '/activities/{activityID:\d+}/join', ['App\controllers\activity\SingleActivity', 'joinActivity']],
    ['GET', '/activities/{activityID:\d+}/leave', ['App\controllers\activity\SingleActivity', 'leaveActivity']],

    // ------------- authenticated - leader --------------------
    ['GET', '/add-member', ['App\controllers\leader\AddMember', 'renderAddMemberPage']],
    ['POST', '/add-member', ['App\controllers\leader\AddMember', 'addMember']],

    ['POST', '/profile/{memberID:\d+}/update-role', ['App\controllers\member\Profile', 'updateRoles']],
    ['GET', '/profile/{memberID:\d+}/delete', ['App\controllers\member\Profile', 'deleteMember']],

    ['GET', '/activities/{activityID:\d+}/remove/{memberID:\d+}', ['App\controllers\activity\SingleActivity', 'removeMemberFromActivity']],
    ['GET', '/activities/{activityID:\d+}/remove', ['App\controllers\activity\SingleActivity', 'removeActivity']],
    ['POST', '/activities/add', ['App\controllers\activity\Activities', 'newActivity']],

    ['GET', '/dashboard', ['App\controllers\leader\MemberDashboard', 'renderMemberDashboard']],
    ['POST', '/dashboard/send-email', ['App\controllers\leader\MemberDashboard', 'sendEmailToMarked']],
    ['GET', '/dashboard/search', ['App\controllers\leader\MemberDashboard', 'distributeSearchQuery']]
    // ------------------------------------------------
];