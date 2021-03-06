<?php


namespace App\models\address;


use App\models\Database;
use mysqli_result;

class Address extends Database
{

    /**
     * Checks if a given zip code is valid.
     *
     * @param $zipCode
     * @return bool
     */
    public function checkForValidZipCode($zipCode): bool {
        $result = $this->getZipcode($zipCode)->fetch_assoc();
        if ($result['zip_code']){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Adds an address to the database.
     *
     * @param $streetAddress
     * @param $zipCode
     * @return int
     */
    public function addAddress($streetAddress, $zipCode): int {
        $sql = "INSERT INTO address (street_address, fk_zip_code_register) VALUES (?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param("ss", $streetAddress, $zipCode);
        $stmt->execute();
        $addressID = $this->getConnection()->insert_id;
        $stmt->close();
        return (int) $addressID;
    }

    /**
     * Returns the identical zip code to the inputted zip code, if
     * the zip code exists in the database.
     *
     * @param $zipCode
     * @return false|mysqli_result
     */
    public function getZipcode($zipCode){
        $sql = "SELECT * FROM zip_code_register WHERE zip_code = ? LIMIT 1";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('s',$zipCode);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

}