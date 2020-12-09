<?php


namespace App\controllers\member;

use App\controllers\BaseController;
use App\helpers\Validate;
use App\models\interest\Interest;
use App\models\member\Member;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\helpers\FileHandler;
use Symfony\Component\HttpFoundation\Session\Session;

class Register extends BaseController {

    /**
     *
     *
     * @return Response
     */
    public function renderRegisterPage(): Response {
        if ($this->hasMemberPrivileges() == true
            and $this->hasLeaderPrivileges() == true){
            return $this->methodNotAllowed();
        }

        $interestModel = new Interest();
        $interests = $interestModel->getAllInterests();

        return new Response(
            $this->twig->render('pages/member/register.html.twig',
            ['interests' => $interests])
        );
    }

    public function registerMember() {
        if ($this->hasMemberPrivileges() == true
            and $this->hasLeaderPrivileges() == true){
            return $this->methodNotAllowed();
        }

        $image = $this->request->files->get('image');

        $memberData = [
            'firstName' => $this->request->get('first-name'),
            'lastName' => $this->request->get('last-name'),
            'email' => $this->request->get('email'),
            'password' => $this->request->get('password'),
            'phoneNumber' => $this->request->get('phone-number'),
            'birthDate' => $this->request->get('birth-date'),
            'gender' => $this->request->get('gender'),
            'streetAddress' => $this->request->get('street-address'),
            'zipCode' => $this->request->get('zip-code'),
            'interests' => (array)$this->request->get('interests'),
            'paymentStatus' => 0, // not paid
                'role' => 3, // ordinary member,
        ];

        $session = new Session();

        // validates all the members of the memberData array.
        $errorMessages = $this->validateRegisterMemberForm($memberData);

        // checks if the list of error messages is not empty
        if (!empty($errorMessages)) {
            // adds each error message as a flash message to the session object
            foreach ($errorMessages as $message) {
                $session->getFlashBag()->add('registrationError', $message);
            }
            return $this->renderRegisterPage();
        }

        $memberModel = new Member();
        $registerMember = $memberModel->registerMember($memberData);

        if ($registerMember) {
            $fileUpload = new FileHandler();
            $fileUpload->uploadProfileImage($image, $registerMember);
            return new RedirectResponse('http://localhost:8081/login');
        } else {
            $session->getFlashBag()->add('registrationError', 'Account could not be created');
            return $this->renderRegisterPage();
        }
    }

    /**
     * Validates each of the fields from the "register member"-form.
     *
     * @param array $memberData
     * @return array|false
     * @throws \Exception
     */
    private function validateRegisterMemberForm(array $memberData){
        $validator = new Validate();

        //validate all the fields from the form
        $validator->validateFirstName($memberData['firstName']);
        $validator->validateLastName($memberData['lastName']);
        $validator->validateEmailInUse($memberData['email']);
        $validator->validatePassword($memberData['password']);
        $validator->validatePhoneNumber($memberData['phoneNumber']);
        $validator->validateFormerDate($memberData['birthDate']);
        $validator->validateGender($memberData['gender']);
        $validator->validateAddress($memberData['streetAddress']);
        $validator->validateZipCode($memberData['zipCode']);
        $validator->validateInterests($memberData['interests']);

        // get the list of error messages
        $errorMessages = $validator->getErrorMessages();

        if (!empty($errorMessages)){
            return $errorMessages;
        } else{
            return false;}

    }
}