<?php


namespace App\helpers;


class PasswordGenerator {


    /**
     * Generates a temporary password with a length of 8 signs.
     *
     * @return false|string
     */
    function generatePassword() {
        $alphabet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $seed = substr(str_shuffle($alphabet), 0, 8);
        return $seed;
    }



}