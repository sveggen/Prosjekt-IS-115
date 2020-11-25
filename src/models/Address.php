<?php


namespace App\models;


class Address extends Database
{


    /**
     * Returns true if zipcode is valid, false if not.
     */
    public function checkForValidZipCode($zipCode){
        $result = $this->getZipcode($zipCode)->fetch_assoc();
        if ($result['zipCode']){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Adds an address to the database.
     */
    public function addAddress($streetAddress, $zipCode)
    {
        $sql = "INSERT INTO address (street_address, fk_zip_code_register) VALUES (?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param("ss", $streetAddress, $zipCode);
        $stmt->execute();
        $insertId = $this->getConnection()->insert_id;
        $stmt->close();
        return $insertId;
    }

    /**
     * Returns data from Zipcoderegister base on input as zipcode.
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