<?php


namespace App\helpers;


class PasswordGenerator {


    /**
     * Generates a temporary password with a length of 8 chars and/or numbers.
     * @return false|string Temporary password
     */
    function generatePassword() {
        $alphabet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        // adds 8 random chars/numbers from "alphabet" to
        // the string and shuffles them randomly
        $temporaryPassword = substr(str_shuffle($alphabet), 0, 8);
        return $temporaryPassword;
    }



}