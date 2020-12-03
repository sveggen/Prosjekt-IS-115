<?php


namespace App\models;

use mysqli_result;

/**
 * Class User
 * @package App\models
 *
 * Handles all the logic-related operations for a user,
 * which is the entity related to all authentication in the Youth Club.
 * All users are connected to a "member" in the database (which holds all personal information).
 */
class User extends Database {

    private const PEPPER = 'j8OkjXs804GSp2J7';

    /**
     * @return array | bool true on login success.
     */
    public function login($username, $password) {
        $user = $this->getUserCredentials($username);
        if (password_verify($this->pepperPassword($password), $user['password'])){
            return $user;
        } else {
            return false;
        }
    }

    /**
     * Hashes password and calls on addUser()-function.
     * @return bool true on registration success.
     */
    public function registerUser($username, $password, $memberID) {
        $hashedPassword = password_hash($this->pepperPassword($password), PASSWORD_BCRYPT);
        $user = $this->addUser($username, $hashedPassword, $memberID);
        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array|null with user's credentials.
     */
    public function getUserCredentials($username) {
        $sql = "SELECT user.user_id, username, password, fk_member_id 
                FROM user WHERE user.username = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    /**
     * @return string with peppered password.
     */
    private function pepperPassword($password) {
        return hash_hmac("sha256", $password, self::PEPPER);
    }

    /**
     * Add user to DB.
     * @return int above 1 if user was added to DB.
     */
    private function addUser($username, $password, $memberID) {
        $sql = "INSERT INTO user (username, password, fk_member_id) 
                VALUES (?, ?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('ssi', $username, $password, $memberID);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    }

    /**
     * @return false|mysqli_result Array of all users in DB.
     */
    public function getAllUsers() {
        $sql = "SELECT * FROM user";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Removes user from DB.
     * @return int above 1 if user was removed from DB.
     */
    public function removeUser($userID) {
        $sql = "DELETE FROM user WHERE user_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $userID);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    }

    public function checkUserExistence($memberID){
        $sql = "SELECT COUNT(1) AS SUM
                FROM user 
                WHERE fk_member_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $memberID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)$result['SUM'];
    }
}