<?php


namespace App\models;



class Member extends Database
{

    /**
     * @param $memberData mixed all fields of from the member registration
     */
    public function registerMember($memberData){
        $addressID = (new Address)->addAddress($memberData['streetAddress'], $memberData['zipCode']);
        $memberID = $this->addMember ($memberData['firstName'], $memberData['lastName'],
            $memberData['email'], $memberData['phoneNumber'], $addressID);
        // adds user if password has been entered (so that function can be used when admin adds members too)
        if ($memberData['password']) {
            (new User)->registerUser($memberData['email'], $memberData['password'], $memberID);
        }
        $this->addMemberInterests($memberID, $memberData['interests']);
    }

    /**
     * @return false|mysqli_result Retrieve MySQL-object containing
     * array of members.
     */
    public function getAllMembers()
    {
        $sql = "SELECT * FROM member";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;

    }


    /**
     * Adds a member to the DB.
     */
    public function addMember($firstName, $lastName, $email, $phoneNumber, $addressID){
        $sql = "INSERT INTO member (first_name, last_name, email, 
                     phone_number, subscription_status, fk_address_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param("ssssii", $firstName, $lastName, $email,
            $phoneNumber, $paid, $addressID);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Adds a members interests to the DB.
     */
    public function addMemberInterests($memberID, $interests)
    {
        $sql = "INSERT INTO member_interest (fk_member_id, fk_interest_id) VALUES (?, ?)";
        $this->getConnection()->begin_transaction();
        foreach ($interests as $interest) {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bind_param('ss', $memberID, $interest);
            $stmt->execute();
            $stmt->close();
        }
        if ($this->getConnection()->commit()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns all members and their corresponding interests.
     */
    public function getAllMemberInterests()
    {
        $sql = "SELECT * FROM member_interest JOIN interest i on member_interest.fk_interest_id = i.interest_id
    JOIN member m on member_interest.fk_member_id = m.member_id ORDER BY I.type";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;

    }
}