<?php


namespace App\models;


class Member extends Database {

    /**
     * @param $memberData mixed all fields of from the member registration
     * @return bool
     */
    public function registerMember($memberData, $role) {
        try {
            $addressID = (new Address)->addAddress($memberData['streetAddress'], $memberData['zipCode']);
            $memberID = $this->addMember($memberData['firstName'], $memberData['lastName'],
                $memberData['email'], $memberData['phoneNumber'], $addressID);
            // adds user if registration has has been done by member self

            if ($memberData['password']) {
                (new User)->registerUser($memberData['email'], $memberData['password'], $memberID);
            }
            $this->addMemberRoles($memberID, $role);
            $this->addMemberInterests($memberID, $memberData['interests']);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Returns all data from member, interest, address and zip_code_register
     * JOIN-ed on member.
     */
    public function getAllMembersAndMemberData(){
        $sql = "SELECT * FROM member
                LEFT JOIN address a on member.fk_address_id = a.address_id
                LEFT JOIN zip_code_register zcr on a.fk_zip_code_register = zcr.zip_code";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getSingleMemberAndMemberData($memberID){
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

    /**
     * Adds a members interests to the DB.
     */
    public function addMemberInterests($memberID, $interests) {
        $sql = "INSERT INTO member_interest (fk_member_id, fk_interest_id) VALUES (?, ?)";
        $this->getConnection()->begin_transaction();
        foreach ($interests as $interest) {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bind_param('is', $memberID, $interest);
            $stmt->execute();
            $stmt->close();
        }
        if ($this->getConnection()->commit()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return false|\mysqli_result Returns all members.
     */
    public function getAllMembers() {
        $sql = "SELECT * FROM member";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * @return array containing all members with
     * their corresponding interests.
     */
    public function getAllMemberInterests() {
        $sql = "SELECT * FROM member_interest
    JOIN interest i on member_interest.fk_interest_id = i.interest_id
    JOIN member m on member_interest.fk_member_id = m.member_id ORDER BY I.type";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    /**
     * Adds a member to the DB.
     */
    private function addMember($firstName, $lastName, $email, $phoneNumber, $addressID) {
        $paid = 0;
        $sql = "INSERT INTO member (first_name, last_name, email, 
                     phone_number, subscription_status, fk_address_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param("ssssii", $firstName, $lastName, $email,
            $phoneNumber, $paid, $addressID);
        $stmt->execute();
        $insertId = $this->getConnection()->insert_id;
        $stmt->close();
        return $insertId;
    }

    public function getTotalMembers(){
        $sql = "SELECT COUNT(*) AS SUM FROM member_role 
                WHERE fk_role_id = 3";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)$result['SUM'];
    }

    public function getTotalLeaders(){
        $sql = "SELECT COUNT(*) AS SUM FROM member_role 
                WHERE fk_role_id = 4";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)$result['SUM'];
    }


public function getSingleMemberID($email){
    $sql = "SELECT member_id FROM member 
                WHERE email = ?";
    $stmt = $this->getConnection()->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result['member_id'];
}


public function addMemberRoles($memberID, $roleID){
    $sql = "INSERT INTO member_role (fk_member_id, fk_role_id) VALUES (?, ?)";
    $stmt = $this->getConnection()->prepare($sql);
    $stmt->bind_param("ii", $memberID, $roleID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}
}