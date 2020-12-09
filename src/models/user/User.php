<?php


namespace App\models\user;

use App\helpers\PasswordGenerator;
use App\models\Database;

/**
 * Handles all the logic-related operations for a user -
 * which is connecting.
 *
 * Class User
 * @package App\models
 *
 */
class User extends Database {

    /**
     * Compares password against hashed password.
     *
     * @param $email
     * @param $password
     * @return array | bool
     */
    public function login($email, $password) {
        $user = $this->getUserCredentials($email);
        if (password_verify($this->pepperPassword($password), $user['password'])) {
            return $user;
        } else {
            return false;
        }
    }

    /**
     *
     *
     * @param $newPassword
     * @param $memberID
     * @return bool
     */
  public function updatePassword($newPassword, $memberID): bool {
      $hashedPassword = password_hash($this->pepperPassword($newPassword), PASSWORD_BCRYPT);
      $user = $this->changePassword($hashedPassword, $memberID);
      if ($user) {
          return true;
      } else {
          return false;
      }
  }

  public function changePassword($hashedPassword, $memberID): int {
      $sql = "UPDATE user 
                SET password = ? 
                WHERE fk_member_id = ?";
      $stmt = $this->getConnection()->prepare($sql);
      $stmt->bind_param('ss', $hashedPassword, $memberID);
      $stmt->execute();
      $result = $stmt->affected_rows;
      $stmt->close();
      return $result;
  }




    /**
     * @param $email
     * @return array|null User's credentials.
     */
    public function getUserCredentials($email): ?array {
        $sql = "SELECT u.fk_member_id, u.password, m.email
                FROM user u
                LEFT JOIN member m on m.member_id = u.fk_member_id
                WHERE email = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('s', $email);
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
        $createUser = $userModel->registerUser($temporaryPassword, $memberID);

        if ($createUser) {
            return $temporaryPassword;
        } else {
            return false;
        }
    }

    /**
     * Hashes password and calls on addUser()-function.
     * @param $password
     * @param $memberID
     * @return bool true on registration success.
     */
    public function registerUser($password, $memberID): bool {
        $hashedPassword = password_hash($this->pepperPassword($password), PASSWORD_BCRYPT);
        $user = $this->addUser($hashedPassword, $memberID);
        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add user to DB.
     *
     * @param $password
     * @param $memberID
     * @return int above 1 if authentication was added to DB.
     */
    private function addUser($password, $memberID): int {
        $sql = "INSERT INTO user (password, fk_member_id) 
                VALUES (?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('si', $password, $memberID);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    }


    /**
     * Checks if a user exists in the database.
     *
     * @param $memberID mixed Members ID.
     * @return int
     */
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