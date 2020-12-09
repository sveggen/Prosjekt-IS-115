<?php


namespace App\models\member;


use App\models\address\Address;
use App\models\user\User;
use App\models\Database;
use App\models\interest\Interest;
use App\models\role\Role;


class Member extends Database {

    /**
     * Register a member with associated
     * address, user, roles and interests to the database.
     *
     * @param $memberData mixed all fields of from the member registration
     * @return false|int The user's member ID on success and false if not.
     */
    public function registerMember($memberData) {
        try {

            $addressID = (new Address)->addAddress($memberData['streetAddress'], $memberData['zipCode']);

            $memberID = $this->addMember($memberData['firstName'], $memberData['lastName'],
                $memberData['email'], $memberData['phoneNumber'], $memberData['paymentStatus'],
                $addressID, $memberData['gender'], $memberData['birthDate']);

            (new User)->registerUser($memberData['password'], $memberID);

            (new Role)->addMemberRole($memberID, $memberData['role']);

            (new Interest)->addMemberInterests($memberID, $memberData['interests']);

            return (int)$memberID;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Adds a member to the DB.
     */
    public function addMember($firstName, $lastName, $email, $phoneNumber,
                              $paymentStatus, $addressID, $gender, $birthDate) {
        // sets the current time as the registration date.
        $timeOfRegistration = date('Y-m-d H:i:s');
        $sql = "INSERT INTO member (first_name, last_name, email, 
                phone_number, subscription_status, fk_address_id, 
                time_of_registration, gender, birth_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param("ssssiisss", $firstName, $lastName, $email,
            $phoneNumber, $paymentStatus, $addressID, $timeOfRegistration, $gender, $birthDate);
        $stmt->execute();
        $memberID = $this->getConnection()->insert_id;
        $stmt->close();
        // return the generated member ID.
        return (int)$memberID;
    }

    /**
     * Adds a member to the database with an address, interests, one or more roles,
     * and without an associated user.
     *
     * @param $memberData
     * @return false|int
     */
    public function adminRegisterMember($memberData) {
        try {

            $addressID = (new Address)->addAddress($memberData['streetAddress'], $memberData['zipCode']);

            $memberID = $this->addMember($memberData['firstName'], $memberData['lastName'],
                $memberData['email'], $memberData['phoneNumber'], $memberData['paymentStatus'],
                $addressID, $memberData['gender'], $memberData['birthDate']);

            (new Role)->addMemberRoles($memberID, $memberData['roles']);

            (new Interest)->addMemberInterests($memberID, $memberData['interests']);

            return $memberID;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Updates data in the member, address and interests-table
     * associated with a given member.
     *
     * @param $memberData
     * @return false|\mysqli_result
     */
    public function updateMemberInformation($memberData) {
        $this->getConnection()->begin_transaction();
        // delete all existing interests from the DB.
        $deleteInterestsSql = "DELETE FROM member_interest WHERE fk_member_id = ?";
        $deleteInterestsStmt = $this->getConnection()->prepare($deleteInterestsSql);
        $deleteInterestsStmt->bind_param('i', $memberData['memberID']);
        $deleteInterestsStmt->execute();
        $deleteInterestsStmt->close();

        $addInterestsSql = "INSERT INTO member_interest (fk_interest_id, fk_member_id)
                            VALUES (?, ?)";
        // insert all the new interests into the DB.
        foreach ($memberData['interests'] as $interestID) {
            $addInterestsStmt = $this->getConnection()->prepare($addInterestsSql);
            $addInterestsStmt->bind_param('ii', $interestID, $memberData['memberID']);
            $addInterestsStmt->execute();
            $addInterestsStmt->close();
        }
        // updates the member's personalia and address in the database.
        $updateMemberSql = "UPDATE member m
                inner join address a on m.fk_address_id = a.address_id
                SET a.street_address= ?, a.fk_zip_code_register = ?,
                    m.first_name = ?, m.last_name = ?, m.email = ?,
                    m.phone_number = ?, m.gender = ?, m.birth_date = ?
                WHERE member_id = ?";
        $updateMemberStmt = $this->getConnection()->prepare($updateMemberSql);
        $updateMemberStmt->bind_param('ssssssssi', $memberData['streetAddress'], $memberData['zipCode'],
            $memberData['firstName'], $memberData['lastName'],
            $memberData['email'], $memberData['phoneNumber'], $memberData['gender'],
            $memberData['birthDate'], $memberData['memberID']);
        $updateMemberStmt->execute();
        $updateMemberStmt->close();
        //  commit transaction
        if ($this->getConnection()->commit() == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns all members with their registered address.
     */
    public function getAllMembersAndAddress() {
        $sql = "SELECT * FROM member
                LEFT JOIN address a on member.fk_address_id = a.address_id
                LEFT JOIN zip_code_register zcr on a.fk_zip_code_register = zcr.zip_code";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Get a single member and their address from the DB.
     *
     * @param $memberID
     * @return false|\mysqli_result
     */
    public function getSingleMemberAndAddress($memberID) {
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
     * Get a single members email address from the DB.
     *
     * @param $memberID
     * @return mixed
     */
    public function getSingleMemberEmail($memberID) {
        $sql = "SELECT email FROM member
                WHERE member_id = (?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('s', $memberID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['email'];
    }

    /**
     * Get all interests of a given member.
     *
     * @param $memberID
     * @return false|\mysqli_result
     */
    public function getSingleMemberInterests($memberID) {
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

    /**
     * Get all members which share the same interest.
     *
     * @param $interestID
     * @return false|\mysqli_result
     */
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
     * Get the number of total members registered.
     *
     * @return int
     */
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

    /**
     * Get the number of total leader registered.
     *
     * @return int
     */
    public function getTotalLeaders(): int {
        $sql = "SELECT COUNT(*) AS SUM FROM member_role 
                WHERE fk_role_id = 4";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)$result['SUM'];
    }


    /**
     * Get a single members ID of off a given email.
     *
     * @param string $email
     * @return mixed
     */
    public function getSingleMemberID(string $email) {
        $sql = "SELECT member_id FROM member 
                WHERE email = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['member_id'];
    }

    /**
     * Get all members with the same membership payment status.
     *
     * @param int $paymentStatus
     * @return false|\mysqli_result
     */
    public function getMembersSortPaymentStatus(int $paymentStatus) {
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

    /**
     * Get all members who share the same
     * specific gender.
     *
     * @param $gender
     * @return false|\mysqli_result
     */
    public function getMembersWithSpecificGender($gender) {
        $sql = "SELECT * FROM member 
                LEFT JOIN address a on member.fk_address_id = a.address_id
                LEFT JOIN zip_code_register zcr on a.fk_zip_code_register = zcr.zip_code
                WHERE gender = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('s', $gender);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }


}