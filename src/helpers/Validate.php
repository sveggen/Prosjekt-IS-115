<?php


namespace App\helpers;


use App\models\address\Address;
use App\models\member\Member;
use DateTime;

class Validate {

    private $errorMessages = array();



    /**
     * Validates a given email.
     *
     * @param $email mixed Email to check.
     * @return bool true if email is valid and set, false if not.
     */
    public function validateEmail($email){
        if (filter_var($email, FILTER_VALIDATE_EMAIL)){
            return true;
        } else {
            $error = "The email address is not valid";
            return array_push($this->errorMessages, $error);
        }
    }

    public function validateEmailInUse($email){
        if ($this->validateEmail($email)){
            $memberModel = new Member();
            $emailInUse = $memberModel->getSingleMemberID($email);
            if ($emailInUse) {
                $error = "The email address is already taken";
                return array_push($this->errorMessages, $error);
            }
        } return false;
    }

    public function validatePassword($password){
        // TO DO
    }

    /**
     * Validates if a given phone number has 8 digits.
     *
     * @param $phoneNumber mixed Norwegian phone number to validate.
     * @return bool|int
     */
    public function validatePhoneNumber($phoneNumber) {
        if (preg_match('/[0-9]{8}/', $phoneNumber)){
            return true;
        } else {
            $error = "The phone number must be 8 digits long";
            return array_push($this->errorMessages, $error);
        }
    }

    /**
     * Checks if a given date is in the future.
     *
     * @param $futureDate mixed date in the future
     * @return bool true if date is valid and in the future, false if not.
     * @throws \Exception
     */
    public function validateFutureDate($futureDate) {
        $date = new DateTime($futureDate);
        $now = new DateTime();

        if ($date > $now) {
            return true;
        } else {
            $error = "The date must be in the future";
            return array_push($this->errorMessages, $error);
        }
    }


    /**
     * Checks if a given date is in the past.
     *
     * @param $pastDate mixed date that has been
     * @return bool true if date is valid and in the past, false if not.
     * @throws \Exception
     */
    public function validateFormerDate($pastDate){
        $date = new DateTime($pastDate);
        $now = new DateTime();

        if ($date < $now){
            return true;
        } else {
            $error = "The date must be in the past";
            return array_push($this->errorMessages, $error);
        }
    }


    public function validateFirstName($firstName){
        if (strlen($firstName) > 2){
            return true;
        } else {
            $error =  "The first name must be longer than 2 characters";
            return array_push($this->errorMessages, $error);
        }
    }

    public function validateLastName($lastName){
        $characters = 2;
        if (strlen($lastName) > $characters){
            return true;
        } else {
            $error = "The last name must be longer than $characters characters";
            return array_push($this->errorMessages, $error);
        }
    }

    public function validateTitle($title){
        $characters = 2;
        if (strlen($title) > $characters){
            return true;
        } else {
            $error = "The title must be longer than $characters characters";
            return array_push($this->errorMessages, $error);
        }
    }

    public function validateGender($gender){
        if ($gender == "male" or $gender == "female"
        or $gender == "other") {
            return true;
        } else {
            $error = "There must be selected a gender";
            return array_push($this->errorMessages, $error);
        }
    }

    public function validateAddress($adress){
        $characters = 4;
        if (strlen($adress) > $characters){
            return true;
        } else {
            $error = "The street address must be longer than $characters characters";
            return array_push($this->errorMessages, $error);
        }
    }

    public function validateZipCode($zipCode){
        $addressModel = new Address();
        $findZipCode = $addressModel->checkForValidZipCode($zipCode);
        if ($findZipCode) {
            return true;
        } else {
            $error = "The zip code is not valid";
            return array_push($this->errorMessages, $error);
        }
    }

    public function validatePaymentStatus($paymentStatus){
        $min = 0;
        $max = 1;
        if (($min <= $paymentStatus) && ($paymentStatus <= $max)){
            return true;
        } else {
            $error = "There must be chosen a payment status of paid / not paid";
            return array_push($this->errorMessages, $error);
        }
    }

    public function validateInterests (array $interests) {
        if (empty($interests)) {
            $error = "There must be selected at least one interest";
            return array_push($this->errorMessages, $error);
        } else {
            return true;
        }
    }

    public function validateRoles ($roles){
        if (empty($roles)) {
            $error = "There must be selected at least one role";
            return array_push($this->errorMessages, $error);
        } else {
            return true;
        }
    }


    /**
     * @return array Containing all the error messages.
     */
    public function getErrorMessages(): array {
        return $this->errorMessages;
    }

    public function validateActivityDescription($description){
        if (str_word_count($description) > 4){
            return true;
        } else {
            $error = "The description must contain at least 4 words.";
            return array_push($this->errorMessages, $error);
        }
    }

    public function validatePositiveNumber($number){
        if ($number > 0) {
            return true;
        } else {
            $error = "The number must be above 1.";
            return array_push($this->errorMessages, $error);
        }
    }

    public function validatePasswordNotEmpty($password) {
        if (!empty($password)) {
            return true;
        } else {
            $error = "The password cant be empty.";
            return array_push($this->errorMessages, $error);
        }
    }

}