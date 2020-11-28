<?php


namespace App\models;


class Activity extends Database {

    /**
     * @return false|mysqli_result Containing MySQL of array containing
     * all future activities.
     */
    public function getAllFutureActivities() {
        $sql = "SELECT * FROM activity WHERE activity.start_time >= CURTIME() ORDER BY activity.start_time";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

}