<?php


namespace App\helpers;


class PasswordGenerator {


    /**
     * Generates a temporary password with a length of 8 signs.
     *
     * @return false|string
     */
    function generatePassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $hash = md5();
        $passwordLength = 8;
        $hashLength = strlen($hash);

         //Starting point in hashed value
        $start = rand(0, ($hashLength - $passwordLength - 1));
        $temporaryPassword = substr($hash, $start, $passwordLength);
        return $temporaryPassword;
    }

}