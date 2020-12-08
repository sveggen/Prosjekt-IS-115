<?php


namespace App\models\activity;


use App\models\Database;
use mysqli_result;

class Activity extends Database {

    /**
     * @return false|mysqli_result Containing MySQL of array containing
     * all future activity.
     */
    public function getAllFutureActivities() {
        $sql = "SELECT a.title, a.activity_id, a.start_time, a.end_time,
                m.first_name, m.last_name, m.email
                FROM activity a JOIN member m on m.member_id = a.fk_member_id
                WHERE a.start_time >= CURTIME() ORDER BY a.start_time";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getTotalFutureActivities(): int {
        $sql = "SELECT COUNT(*) AS SUM FROM activity
                WHERE start_time >= CURTIME()";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)$result['SUM'];
    }

    public function addActivity($title, $startTime, $endTime, $description, $memberID, $maxAttendees){
        $sql = "INSERT INTO activity (title, start_time, end_time, description, fk_member_id, max_attendees) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('ssssii', $title, $startTime, $endTime, $description, $memberID, $maxAttendees);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function addActivityMember($memberID, $activityID, $joinTime): int {
        //CHECK if max_attendees threshold has been met
        $sql = "INSERT INTO member_activity (fk_member_id, fk_activity_id, join_time)
                VALUES (?, ?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('iis', $memberID, $activityID, $joinTime);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    }

    public function getActivity($id) {
        $sql = "SELECT * FROM activity
                JOIN member m on m.member_id = activity.fk_member_id
                WHERE activity.activity_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getActivityAttendees($id){
        $sql = "SELECT * FROM member_activity 
                JOIN member m on member_activity.fk_member_id = m.member_id
                LEFT JOIN address a on m.fk_address_id = a.address_id
                LEFT JOIN zip_code_register zcr on a.fk_zip_code_register = zcr.zip_code
                WHERE member_activity.fk_activity_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getEmptySlotsInActivity($id): int {
        $attendees = $this->getActivityAttendees($id)->num_rows;
        $activity = $this->getActivity($id)->fetch_assoc();
        $maxAttendees = (int)$activity['max_attendees'];
        return $maxAttendees - $attendees;
    }

    public function leaveActivity($memberID, $activityID): int {
        $sql = "DELETE FROM member_activity WHERE fk_member_id = ? AND fk_activity_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('ii', $memberID, $activityID);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    }

    public function getMemberActivityAttendanceStatus($memberID, $activityID): int {
        $sql = "SELECT COUNT(1) AS SUM
                FROM member_activity 
                WHERE fk_member_id = ?
                AND fk_activity_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('ii', $memberID, $activityID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)$result['SUM'];
    }

    public function getAllMembersActivities($memberID) {
        $sql = "SELECT * FROM member_activity
                JOIN activity i on member_activity.fk_activity_id = i.activity_id
                JOIN member m on member_activity.fk_member_id = m.member_id
                WHERE member_id = ? AND  start_time >= CURTIME()";
        $stmt = $this->getConnection()->prepare($sql);
         $stmt->bind_param('i', $memberID);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function removeActivity($activityID): int {
        $sql = "DELETE FROM activity
                WHERE activity.activity_id= ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $activityID);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    }
}