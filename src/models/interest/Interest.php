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

}