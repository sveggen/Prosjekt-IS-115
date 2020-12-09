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

    public function renderMemberProfile($requestParameters): Response {
        $memberID = $requestParameters['memberID'];

        if ($this->hasLeaderPrivileges() == true){
            return $this->renderProfile($memberID);
        }

        if ($this->hasMemberPrivileges() == true) {

            $session = new Session();
            $sessionMemberID = $session->get('memberID');

            // if the ID in the request belong to the
            // logged in member - render profile.
            if ($memberID == $sessionMemberID) {
                return $this->renderProfile($sessionMemberID);

            }
        }
        return $this->methodNotAllowed();

    }

    private function renderProfile($requestParameters): Response {

        $memberModel = new Member();
        $member = $memberModel->getSingleMemberAndAddress($requestParameters);
        $membersInterests = $memberModel->getSingleMemberInterests($requestParameters);

        $roleModel = new Role();
        $membersRoles = $roleModel->getSingleMemberRoles($requestParameters);
        $availableRoles = $roleModel->getAllRoles();


        $interestModel = new Interest();
        $availableInterests = $interestModel->getAllInterests();

        $activityModel = new Activity();
        $membersActivities = $activityModel->getSingleMemberActivities($requestParameters);

        $uploadFile = new FileHandler;
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
        $memberModel->updateMemberInformation($memberData);
        return $this->renderMemberProfile($requestParameters);

    }

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
        // check if password and email is the members credentials
        $validatePassword = $userModel->login('monsterman@norge.no', $oldPassword);

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
        $memberID = $requestParameters['memberID'];
        $image = $this->request->files->get('image');
        $uploadFile = new FileHandler;
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
        if ($this->hasMemberPrivileges() == false
            and $this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $memberID = $requestParameters['memberID'];

        $session = new Session();

        $memberModel = new Member();
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
     * Update a members roles.
     *
     * @param $requestParameters
     * @return Response
     */
    public function updateRoles($requestParameters){
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $memberID = $requestParameters['memberID'];

        // get all the roles
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