<?php


namespace App\models;


class Activity extends Database {

    /**
     * @return false|mysqli_result Containing MySQL of array containing
     * all future activities.
     */
    public function getAllFutureActivities() {
        $sql = "SELECT * FROM activity JOIN member m on m.member_id = activity.fk_member_id
                WHERE activity.start_time >= CURTIME() ORDER BY activity.start_time";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function addActivity($title, $startTime, $endTime, $memberID){
        $sql = "INSERT INTO activity (title, start_time, end_time, fk_member_id) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('sssi', $title, $startTime, $endTime, $memberID);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    }

}