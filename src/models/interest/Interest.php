<?php


namespace App\models\interest;


use App\models\Database;

class Interest extends Database {

    public function getAllInterests(){
        $sql = "SELECT interest_id, type FROM interest";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getInterestName($interestID): ?array {
        $sql = "SELECT type FROM interest
                WHERE interest_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $interestID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result;
    }

    /**
     * Adds a members interests to the DB.
     * @param $memberID
     * @param $interests
     * @return bool
     */
    public function addMemberInterests($memberID, $interests): bool {
        $sql = "INSERT INTO member_interest (fk_member_id, fk_interest_id) 
                VALUES (?, ?)";
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



}