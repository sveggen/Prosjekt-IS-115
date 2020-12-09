<?php


namespace App\models\role;


use App\models\Database;

/**
 * Model for the "role"-table in the Database.
 *
 * Class Role
 * @package App\models\role
 */
class Role extends Database {


    /**
     * @return \mysqli_result All roles from the DB.
     */
    public function getAllRoles() : \mysqli_result {
        $sql = "SELECT * FROM role";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Adds multiple roles linked to a given user to the DB.
     *
     * @param $memberID
     * @param $roles array A list of role ID's,
     * @return bool
     */
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

    /**
     * Returns all the roles a given member has.
     *
     * @param int $memberID
     * @return false|\mysqli_result
     */
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

    /**
     * Adds a single role to a single member in the database.
     *
     * @param int $memberID
     * @param int $roleID
     * @return false|\mysqli_result
     */
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

    /**
     * Updates a given members roles in the database.
     *
     * @param $memberID
     * @param $roles
     * @return bool
     */
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
        // commit both SQL-queries
        if ($this->getConnection()->commit()) {
            return true;
        } else {
            return false;
        }
    }

}