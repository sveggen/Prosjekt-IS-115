<?php


namespace App\models\role;


use App\models\Database;

class Role extends Database {


    public function getAllRoles() : \mysqli_result {
        $sql = "SELECT * FROM role";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function addMemberRoles($memberID, $roles): bool {
        $sql = "INSERT INTO member_role (fk_member_id, fk_role_id) 
                VALUES (?, ?)";
        $this->getConnection()->begin_transaction();
        foreach ($roles as $roleID) {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bind_param('ii', $memberID, $roleID);
            $stmt->execute();
            $stmt->close();
        }
        if ($this->getConnection()->commit()) {
            return true;
        } else {
            return false;
        }
    }

    public function getSingleMemberRoles(int $memberID){
        $sql = "SELECT * FROM member_role 
                JOIN role i on member_role.fk_role_id = i.role_id
                JOIN member m on m.member_id = member_role.fk_member_id
                WHERE member_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $memberID);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function addMemberRole(int $memberID, int $roleID){
        $sql = "INSERT INTO member_role (fk_member_id, fk_role_id) 
                VALUES (?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param("ii", $memberID, $roleID);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function updateMemberRoles($memberID, $roles): bool {
        $this->getConnection()->begin_transaction();
        // delete all existing roles from the DB.
        $deleteSql = "DELETE FROM member_role WHERE fk_member_id = ?";
        $deleteStmt = $this->getConnection()->prepare($deleteSql);
        $deleteStmt->bind_param('i', $memberID);
        $deleteStmt->execute();
        $deleteStmt->close();

        // insert all the roles into the DB.
        $addSql = "INSERT INTO member_role (fk_role_id, fk_member_id)
                    VALUES (?, ?)";
        foreach ($roles as $roleID) {
            $addStmt = $this->getConnection()->prepare($addSql);
            $addStmt->bind_param('ii',  $roleID, $memberID);
            $addStmt->execute();
            $addStmt->close();
        }
        if ($this->getConnection()->commit()) {
            return true;
        } else {
            return false;
        }
    }

}