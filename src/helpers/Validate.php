<?php


namespace App\helpers;


use DateTime;

class Validate {

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
            return false;
        }
    }

    public function validateUsername($username){
        // TO DO
    }

    public function validatePassword($password){
        // TO DO
    }

    /**
     * Validates if a given phone number has 8 digits.
     *
     * @param $phoneNumber mixed Norwegian phone number to validate.
     * @return bool true if phone number is valid and set, false if not.
     */
    public function validatePhoneNumber($phoneNumber){
        if (preg_match('/[0-9]{8}/', $phoneNumber)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if a given date is in the future.
     *
     * @param $futureDate mixed date in the future
     * @return bool true if date is valid and in the future, false if not.
     */
    public function validateFutureDate($futureDate){
        try {
            $date = new DateTime($futureDate);
        } catch (\Exception $e) {
            return false;
        }

        $now = new DateTime();

        if ($date > $now){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if a given date is in the past.
     *
     * @param $pastDate mixed date that has been
     * @return bool true if date is valid and in the past, false if not.
     */
    public function validateFormerDate($pastDate){
        try {
            $date = new DateTime($pastDate);
        } catch (\Exception $e) {
            return false;
        }

        $now = new DateTime();

        if ($date < $now){
            return true;
        } else {
            return false;
        }
    }

}