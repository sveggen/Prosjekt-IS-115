<?php


namespace App\models;


class User extends Database
{

    public function login($username, $password){
        $user = $this->getSingleUser($username);
        if (password_verify($password, $user['password'])) {
            return true;
        } else {
            return false;
        }
    }


    public function registerUser($username, $password, $memberID){
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $user = $this->addUser($username, $hashedPassword);
        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    public function addUser($username, $password){
        $sql = "INSERT INTO user (username, password) VALUES (?, ?)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('ss', $username,$password);
        return $stmt->execute();
    }

    public function getSingleUser($username){
        $sql = "SELECT user.user_id, username, password FROM user WHERE 
                            user.username = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('s',$username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAllUsers(){
        $sql = "SELECT * FROM user";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = array();
        while ($row = $result->fetch_assoc()) {
            array_push($users, $row);
        }
        return $users;
    }

    public function removeUser($username){
        $sql = "DELETE FROM user WHERE username = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bind_param('s',$username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

}