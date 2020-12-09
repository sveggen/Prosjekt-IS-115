<?php


namespace App\controllers\leader;

use App\helpers\Validate;
use App\helpers\validation\ContainsAlphanumeric;
use App\helpers\validation\ContainsAlphanumericValidator;
use Symfony\Component\Validator\Constraints as Assert;

use App\controllers\BaseController;
use App\models\interest\Interest;
use App\models\member\Member;
use App\models\role\Role;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class AddMember extends BaseController {

    /**
     * Adds a new member to the youth club.
     */
    public function addMember(): Response {
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        // get data from all the fields in the "add member-form
        // from the POST-request.
        $memberData = [
            'firstName' => $this->request->get('first-name'),
            'lastName' => $this->request->get('last-name'),
            'email' => $this->request->get('email'),
            'phoneNumber' => $this->request->get('phone-number'),
            'birthDate' => $this->request->get('birth-date'),
            'gender' => $this->request->get('gender'),
            'streetAddress' => $this->request->get('street-address'),
            'zipCode' => $this->request->get('zip-code'),
            'interests' => (array)$this->request->get('interests'),
            'paymentStatus' => $this->request->get('payment-status'),
            'roles' => (array)$this->request->get('roles'),
        ];

        $session = new Session();

        // validates all the members of the memberData array.
        $errorMessages = $this->validateFields($memberData);

        // checks if the list of error messages is not empty
        if (!empty($errorMessages)) {

            // adds each error message as a flash message to the session object
            foreach ($errorMessages as $message) {
                $session->getFlashBag()->add('addMemberError', $message);
            }
            return $this->renderAddMemberPage();
        }

        $memberModel = new Member();
        $addMember = $memberModel->adminRegisterMember($memberData);

        // adds the member to the youth club.
        if ($addMember) {
            $session->getFlashBag()->add('addMemberSuccess', 'Member was successfully added');
        } else {
            $session->getFlashBag()->add('addMemberError', 'There occurred an error and the member was not added.');
        }
        return $this->renderAddMemberPage();
    }

    /**
     * Renders the "Add Member" page.
     *
     * @return Response
     */
    public function renderAddMemberPage(): Response {
        // only leaders have access to this function
        if ($this->hasLeaderPrivileges() == false) {
            return $this->methodNotAllowed();
        }

        $interestModel = new Interest();
        $interests = $interestModel->getAllInterests();

        $rolesModel = new Role();
        $roles = $rolesModel->getAllRoles();

        return new Response(
            $this->twig->render('pages/leader/add_member.html.twig',
                ['interests' => $interests,
                    'roles' => $roles])
        );
    }

    /**
     * Validates the every field from the "Add member" form.
     *
     * @param array $memberData An array containing all the field values.
     * @return array|false A list of error message,
     * or false if all tests passed.
     */
    private function validateFields(array $memberData){
        $validator = new Validate();

        //validate all the fields from the form
        $validator->validateFirstName($memberData['firstName']);
        $validator->validateLastName($memberData['lastName']);
        $validator->validateEmailInUse($memberData['email']);
        $validator->validatePhoneNumber($memberData['phoneNumber']);
        $validator->validateFormerDate($memberData['birthDate']);
        $validator->validateGender($memberData['gender']);
        $validator->validateAddress($memberData['streetAddress']);
        $validator->validateZipCode($memberData['zipCode']);
        $validator->validateInterests($memberData['interests']);
        $validator->validatePaymentStatus($memberData['paymentStatus']);
        $validator->validateRoles($memberData['roles']);

        // get the list of error messages
        $errorMessages = $validator->getErrorMessages();

        if (!empty($errorMessages)){
            return $errorMessages;
        } else{
            return false;}

    }

}