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

    public function registerUser($email, $password)
    {
        $connection = $this->getConnection();
        $sql = "INSERT INTO Users(email, password) VALUES(?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('ss', $email,$password);
        $result = $stmt->execute();
        if ($result) {
            return true;
        } else {
            return false;
        }
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
        return $result = $stmt->fetch();
    }

}