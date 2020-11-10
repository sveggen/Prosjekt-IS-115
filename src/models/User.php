<?php


namespace App\models;


class User extends Database
{

    public function login($email){
        $user = $this->getSingleUser($email);
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    public function registerUser($email, $password){
        $user = $this->addUser($email, $password);
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    public function addUser($email, $password)
    {
        $connection = $this->getConnection();
        $sql = "INSERT INTO Users(email, password) VALUES(?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('ss', $email,$password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getSingleUser($email){
        $connection = $this->getConnection();
        $sql = "SELECT Users.id, email, password FROM Users WHERE 
                            Users.email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAllUsers(){
        $connection = $this->getConnection();
        $sql = "SELECT * FROM Users";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = array();
        while ($row = $result->fetch_assoc()) {
            array_push($users, $row);
        }
        return $users;
    }

    public function removeUser($email){
        $connection = $this->getConnection();
        $sql = "DELETE FROM Users WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

}