<?php


namespace App\models\member;


use App\models\address\Address;
use App\models\authentication\User;
use App\models\Database;
use App\models\interest\Interest;
use App\models\role\Role;


class Member extends Database {

    /**
     * @param $memberData mixed all fields of from the member registration
     * @return false|int|string
     */
    public function registerMember($memberData) {
        try {

            //start transaction
            //$this->getConnection()->autocommit(false);

            $addressID = (new Address)->addAddress($memberData['streetAddress'], $memberData['zipCode']);

            $memberID = $this->addMember($memberData['firstName'], $memberData['lastName'],
                $memberData['email'], $memberData['phoneNumber'], $memberData['paymentStatus'],
                $addressID, $memberData['gender'], $memberData['birthDate']);

            (new User)->registerUser($memberData['password'], $memberID);

            (new Role)->addMemberRole($memberID, $memberData['role']);

            (new Interest)->addMemberInterests($memberID, $memberData['interests']);

             // commit transaction if no exceptions are thrown
            $this->getConnection()->commit();
            $this->getConnection()->autocommit(true);
            return $memberID;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function adminRegisterMember($memberData){
        try {

            //start transaction
            //$this->getConnection()->autocommit(false);

            $addressID = (new Address)->addAddress($memberData['streetAddress'], $memberData['zipCode']);

            $memberID = $this->addMember($memberData['firstName'], $memberData['lastName'],
                $memberData['email'], $memberData['phoneNumber'], $memberData['paymentStatus'],
                $addressID, $memberData['gender'], $memberData['birthDate']);


            (new Role)->addMemberRoles($memberID, $memberData['roles']);
            (new Interest)->addMemberInterests($memberID, $memberData['interests']);

            // commit transaction if no exceptions are thrown
            //$this->getConnection()->commit();
            //$this->getConnection()->autocommit(true);
            return $memberID;
        } catch (\Exception $e) {
            return false;
        }

    }



    /**
     * Returns all data from member, address and zip_code_register
     * JOIN-ed on member.
     */
    public function getAllMembersAndAddress(){
        $sql = "SELECT * FROM member
                LEFT JOIN address a on member.fk_address_id = a.address_id
                LEFT JOIN zip_code_register zcr on a.fk_zip_code_register = zcr.zip_code";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getSingleMemberAndAddress($memberID){
        $sql = "SELECT * FROM member
                LEFT JOIN address a on member.fk_address_id = a.address_id
                LEFT JOIN zip_code_register zcr on a.fk_zip_code_register = zcr.zip_code
                WHERE member_id = (?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $memberID);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }


    public function getAllMembersInterests($memberID) {
        $sql = "SELECT * FROM member_interest
                JOIN interest i on member_interest.fk_interest_id = i.interest_id
                JOIN member m on member_interest.fk_member_id = m.member_id 
                WHERE member_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $memberID);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getAllMembersWithSpecificInterest($interestID) {
        $sql = "SELECT * FROM member_interest
                JOIN interest i on member_interest.fk_interest_id = i.interest_id
                JOIN member m on member_interest.fk_member_id = m.member_id 
                LEFT JOIN address a on m.fk_address_id = a.address_id
                LEFT JOIN zip_code_register zcr on a.fk_zip_code_register = zcr.zip_code
                WHERE interest_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $interestID);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Adds a member to the DB.
     */
    public function addMember($firstName, $lastName, $email, $phoneNumber,
                               $paymentStatus, $addressID, $gender, $birthDate) {
        $timeOfRegistration = date('Y-m-d H:i:s');
        $sql = "INSERT INTO member (first_name, last_name, email, 
                phone_number, subscription_status, fk_address_id, 
                time_of_registration, gender, birth_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param("ssssiisss", $firstName, $lastName, $email,
            $phoneNumber, $paymentStatus, $addressID, $timeOfRegistration, $gender, $birthDate);
        $stmt->execute();
        $insertId = $this->getConnection()->insert_id;
        $stmt->close();
        return $insertId;
    }

    public function getTotalMembers(): int {

        $this->getConnection()->autocommit(true);
        $sql = "SELECT COUNT(*) AS SUM FROM member_role 
                WHERE fk_role_id = 3";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)$result['SUM'];
    }

    public function getTotalLeaders(): int {
        $sql = "SELECT COUNT(*) AS SUM FROM member_role 
                WHERE fk_role_id = 4";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)$result['SUM'];
    }


    public function getSingleMemberID(string $email){
        $sql = "SELECT member_id FROM member 
                WHERE email = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['member_id'];
}


    public function getMembersSortPaymentStatus(int $paymentStatus){
        $sql = "SELECT * FROM member 
                LEFT JOIN address a on member.fk_address_id = a.address_id
                LEFT JOIN zip_code_register zcr on a.fk_zip_code_register = zcr.zip_code
                WHERE subscription_status = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $paymentStatus);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }


    /**
     * Deletes a member from the Database.
     *
     * @param $memberID
     * @return int
     */
    public function deleteMember($memberID): int {
        $sql = "DELETE FROM member
                WHERE member.member_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $memberID);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;

    }


}