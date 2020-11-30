<?php


namespace App\models;


use mysqli_result;

class Activity extends Database {

    /**
     * @return false|mysqli_result Containing MySQL of array containing
     * all future activities.
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

    public function addActivityMember($memberID, $activityID){
        //CHECK if max_attendees treshold has been met
        $sql = "INSERT INTO member_activity (fk_member_id, fk_activity_id)
                VALUES (?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('ii', $memberID, $activityID);
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
                WHERE member_activity.fk_activity_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getEmptySlotsInActivity($id){
        $attendees = $this->getActivityAttendees($id)->num_rows;
        $activity = $this->getActivity($id)->fetch_assoc();
        $maxAttendees = (int)$activity['max_attendees'];
        return $maxAttendees - $attendees;
    }


}