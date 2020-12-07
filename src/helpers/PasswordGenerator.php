<?php


namespace App\helpers;


class PasswordGenerator {


    /**
     * Generates a temporary password with a length of 8 chars and/or numbers.
     *
     * @return false|string
     */
    function generatePassword() {
        $alphabet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        // adds 8 random chars/numbers from the alphabet to the string.
        $seed = substr(str_shuffle($alphabet), 0, 8);
        return $seed;
    }



}