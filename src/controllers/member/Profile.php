<?php


namespace App\controllers\member;


use App\controllers\BaseController;
use App\helpers\FileHandler;
use App\helpers\Validate;
use App\models\activity\Activity;
use App\models\interest\Interest;
use App\models\member\Member;
use App\models\role\Role;
use App\models\user\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Profile extends BaseController {

    /**
     * Redirects request to view a given member profile based
     * on the requesters permissions/privileges.
     *
     * @param $requestParameters
     * @return Response
     */
    public function renderMemberProfile($requestParameters): Response {
        $memberID = $requestParameters['memberID'];

        if ($this->hasLeaderPrivileges() == true){
            // render profile regardless if member is leader
            return $this->renderProfile($memberID);
        }

        if ($this->hasMemberPrivileges() == true) {
            $session = new Session();
            $sessionMemberID = $session->get('memberID');

            // if the ID in the request belongs to the
            // logged in member, it will render the profile.
            if ($memberID == $sessionMemberID) {
                return $this->renderProfile($sessionMemberID);

            }
        }
        return $this->methodNotAllowed();
    }

    /**
     * Renders a "Member profile" belonging to a given member,
     * based on the MemberID in the request.
     *
     * @param $requestParameters
     * @return Response
     */
    private function renderProfile($requestParameters): Response {

        $memberModel = new Member();
        $member = $memberModel->getSingleMemberAndAddress($requestParameters);
        // the member's interests
        $membersInterests = $memberModel->getSingleMemberInterests($requestParameters);

        $roleModel = new Role();
        // the members currently assigned roles
        $membersRoles = $roleModel->getSingleMemberRoles($requestParameters);

        // all available roles in the database
        $availableRoles = $roleModel->getAllRoles();

        $interestModel = new Interest();
        // all availble interests in the database
        $availableInterests = $interestModel->getAllInterests();

        $activityModel = new Activity();
        // the activities the member has joined
        $membersActivities = $activityModel->getSingleMemberActivities($requestParameters);

        $uploadFile = new FileHandler;
        // the members current profile image
        $membersProfileImage = $uploadFile->getProfileImage($requestParameters);

        return new Response(
            $this->twig->render('pages/member/profile.html.twig',
                ['member' => $member,
                    'profileImage' => $membersProfileImage,
                    'interests' => $membersInterests,
                    'activities' => $membersActivities,
                    'memberRoles' => $membersRoles,
                'availableInterests' => $availableInterests,
                    'availableRoles' => $availableRoles])
        );
    }

    /**
     * Updates the members currently saved member information
     * This includes personalia, address and interests.
     *
     * @param $requestParameters
     * @return Response
     */
    public function updateProfile($requestParameters): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $memberID = $requestParameters['memberID'];

        $memberData = [
            'memberID' => $memberID,
            'firstName' => $this->request->get('first-name'),
            'lastName' => $this->request->get('last-name'),
            'email' => $this->request->get('email'),
            'phoneNumber' => $this->request->get('phone-number'),
            'birthDate' => $this->request->get('birth-date'),
            'gender' => $this->request->get('gender'),
            'streetAddress' => $this->request->get('street-address'),
            'zipCode' => $this->request->get('zip-code'),
            'interests' => (array)$this->request->get('interests'),
        ];

        $session = new Session();

        // validates all the members of the memberData array.
        $errorMessages = $this->validateUpdateForm($memberData);

        // checks if the list of error messages is not empty
        if (!empty($errorMessages)) {
            // adds each error message as a flash message to the session object
            foreach ($errorMessages as $message) {
                $session->getFlashBag()->add('profileUpdateError', $message);
            }
            return $this->renderMemberProfile($requestParameters);
        }

        $memberModel = new Member();
        // updates the member info in the database.
        if ($memberModel->updateMemberInformation($memberData)){
            $session->getFlashBag()->add('profileUpdateSuccess', 'Profile successfully updated');
        }
        // renders the profile regardless of success/error.
        return $this->renderMemberProfile($requestParameters);
    }

    /**
     * Updates a member's password.
     *
     * NOTE: Current password is required to perform this action.
     *
     * @param $requestParameters
     * @return Response
     */
    public function updatePassword($requestParameters): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }
        $oldPassword = $this->request->get('old-password');
        $newPassword = $this->request->get('new-password');

        $memberID = $requestParameters['memberID'];

        $memberModel = new Member();
        // get members email from databases
        $email = $memberModel->getSingleMemberEmail($memberID);

        $userModel = new User();
        // check if password and email is indeed the members credentials
        $validatePassword = $userModel->login($email, $oldPassword);

        $session = new Session();
        if ($validatePassword == true) {
            // update the password
            $userModel->updatePassword($newPassword, $memberID);

            $session->getFlashBag()
                ->add('profileUpdateSuccess', 'Password changed');
        } else {
            $session->getFlashBag()
                ->add('profileUpdateError', 'Old password was incorrect');
        }
        return $this->renderMemberProfile($requestParameters);
    }

    /**
     * Updates the users profile image.
     *
     * @param $requestParameters
     * @return Response
     */
    public function updateProfileImage($requestParameters): Response {
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $memberID = $requestParameters['memberID'];
        $image = $this->request->files->get('image');

        $uploadFile = new FileHandler;
        // exchanges the currently saved profile image for the member
        // with the submitted one
        $uploadProfileImage = $uploadFile->uploadProfileImage($image, $memberID);

        $session = new Session();

        if ($uploadProfileImage) {
            $session->getFlashBag()->add('profileUpdateSuccess', 'Uploaded image successfully');
        } else {
            $errorMessages = $uploadFile->getErrorMessages();
            foreach ($errorMessages as $message) {
                $session->getFlashBag()->add('profileUpdateError', $message);
            }
        }
        return $this->renderMemberProfile($requestParameters);
    }

    /**
     * Deletes a member from the Database.
     *
     * @param $requestParameters array The members identification.
     * @return RedirectResponse|Response
     */
    public function deleteMember($requestParameters){
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $memberID = $requestParameters['memberID'];

        $session = new Session();

        $memberModel = new Member();
        // removes a member from the youth club (and database)
        $deleteMember = $memberModel->deleteMember($memberID);

        if ($deleteMember){
            return new RedirectResponse("/");
        } else {
            $session->getFlashBag()->add('profileUpdateError','Could not delete profile, please try again');
            return $this->renderMemberProfile($requestParameters);
        }
    }

    private function validateUpdateForm(array $memberData){
        $validator = new Validate();

        //validate all the fields from the form
        $validator->validateFirstName($memberData['firstName']);
        $validator->validateLastName($memberData['lastName']);

        $memberModel = new Member();
        $ownEmail = $memberModel->getSingleMemberEmail($memberData['memberID']);
        $validator->validateOwnEmailOrNotInUse($ownEmail, $memberData['email']);

        $validator->validatePhoneNumber($memberData['phoneNumber']);
        $validator->validateFormerDate($memberData['birthDate']);
        $validator->validateGender($memberData['gender']);

        // get the list of error messages
        $errorMessages = $validator->getErrorMessages();

        if (!empty($errorMessages)){
            return $errorMessages;
        } else{
            return false;}

    }

    /**
     * Update a members assigned roles.
     *
     * @param $requestParameters
     * @return Response
     */
    public function updateRoles($requestParameters){
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $memberID = $requestParameters['memberID'];

        // get all the submitted roleID's
        $roles = $this->request->get('roles');

        $roleModel = new Role();

        //update the roles in the database
        $updateRoles = $roleModel->updateMemberRoles($memberID, $roles);

        $session = new Session();

        if ($updateRoles == true){
            $session->getFlashBag()->add('profileUpdateSuccess','Roles updated');
        } else {
            $session->getFlashBag()->add('profileUpdateError','Could not update roles');
        }

        return $this->renderMemberProfile($requestParameters);
    }
}