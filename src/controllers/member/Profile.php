<?php


namespace App\controllers\member;


use App\controllers\BaseController;
use App\helpers\FileHandler;
use App\models\activity\Activity;
use App\models\interest\Interest;
use App\models\member\Member;
use App\models\role\Role;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Profile extends BaseController {

    public function renderMemberProfile($memberID): Response {
        if ($this->hasLeaderPrivileges() == true){

            return $this->renderProfile($memberID['id']);
        }

        if ($this->hasMemberPrivileges() == true) {

            $session = new Session();
            $sessionMemberID = $session->get('memberID');

            // if the ID in the request belong to the
            // logged in member - render profile.
            if ($memberID['id'] == $sessionMemberID) {
                return $this->renderProfile($sessionMemberID);

            }
        }
        return $this->methodNotAllowed();

    }

    private function renderProfile($memberID): Response {

        $memberModel = new Member();
        $member = $memberModel->getSingleMemberAndAddress($memberID);
        $membersInterests = $memberModel->getAllMembersInterests($memberID);

        $roleModel = new Role();
        $membersRoles = $roleModel->getSingleMemberRoles($memberID);

        $interestModel = new Interest();
        $allAvailableActivities = $interestModel->getAllInterests();

        $activityModel = new Activity();
        $membersActivities = $activityModel->getAllMembersActivities($memberID);

        $uploadFile = new FileHandler;
        $membersProfileImage = $uploadFile->getProfileImage($memberID);

        return new Response(
            $this->twig->render('pages/member/edit_member_profile.html.twig',
                ['member' => $member,
                    'profileImage' => $membersProfileImage,
                    'interests' => $membersInterests,
                    'activity' => $membersActivities,
                    'roles' => $membersRoles,
                'allAvailableActivities' => $allAvailableActivities])
        );
    }

    public function updateProfile($profileID) {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

    }


    /**
     * Updates the users profile image.
     *
     * @param array $profileID
     * @return Response
     */
    public function updateProfileImage(array $profileID): Response {
        $session = new Session();
        $memberID = $session->get('memberID');
        $image = $this->request->files->get('image');
        $uploadFile = new FileHandler;
        $uploadProfileImage = $uploadFile->uploadProfileImage($image, $memberID);

        if ($uploadProfileImage) {
            $session->getFlashBag()->add('UpdateSuccess', 'Uploaded image successfully');
        } else {
            $errorMessages = $uploadFile->getErrorMessages();
            foreach ($errorMessages as $message) {
                $session->getFlashBag()->add('profileUpdateError', $message);
            }
        }
        return $this->renderMemberProfile($profileID);
    }

    /**
     * Deletes a member from the Database.
     *
     * @param $memberID array The members identification.
     * @return RedirectResponse|Response
     */
    public function deleteMember(array $memberID){
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $session = new Session();

        $memberModel = new Member();
        $deleteMember = $memberModel->deleteMember($memberID['id']);

        if ($deleteMember){
            return new RedirectResponse("/");
        } else {
            $session->getFlashBag()->add('profileError','Could not delete profile, please try again');
            return $this->renderMemberProfile($memberID);
        }


    }
}