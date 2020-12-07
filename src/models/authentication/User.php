<?php


namespace App\models\authentication;

use App\helpers\PasswordGenerator;
use App\models\Database;
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

    /**
     * @param $username
     * @param $password
     * @return array | bool True on login success.
     */
    public function login($username, $password) {
        $user = $this->getUserCredentials($username);
        if (password_verify($this->pepperPassword($password), $user['password'])) {
            return $user;
        } else {
            return false;
        }
    }

    /**
     * @param $username
     * @return array|null User's credentials.
     */
    public function getUserCredentials($username): ?array {
        $sql = "SELECT user_id, fk_member_id, password, username
                FROM user WHERE username = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    /**
     * @param $password
     * @return string with peppered password.
     */
    private function pepperPassword($password): string {
        return hash_hmac("sha256", $password, $_ENV['HASH_PEPPER']);
    }

    /**
     * Creates a User and generates a temporary password.
     *
     * @param $memberID
     * @param $email
     * @return false|string
     */
    public function createUserAndPassword($memberID, $email) {
        $passwordGenerator = new PasswordGenerator();
        //generates temporary password
        $temporaryPassword = $passwordGenerator->generatePassword();

        $userModel = new User();
        $createUser = $userModel->registerUser($email, $temporaryPassword, $memberID);

        if ($createUser) {
            return $temporaryPassword;
        } else {
            return false;
        }
    }

    /**
     * Hashes password and calls on addUser()-function.
     * @param $username
     * @param $password
     * @param $memberID
     * @return bool true on registration success.
     */
    public function registerUser($username, $password, $memberID): bool {
        $hashedPassword = password_hash($this->pepperPassword($password), PASSWORD_BCRYPT);
        $user = $this->addUser($username, $hashedPassword, $memberID);
        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add user to DB.
     *
     * @param $username
     * @param $password
     * @param $memberID
     * @return int above 1 if authentication was added to DB.
     */
    private function addUser($username, $password, $memberID): int {
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
     * @param $userID
     * @return int above 1 if user was removed from DB.
     */
    public function removeUser($userID): int {
        $sql = "DELETE FROM user WHERE user_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('i', $userID);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    }

    public function checkUserExistence($memberID): int {
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