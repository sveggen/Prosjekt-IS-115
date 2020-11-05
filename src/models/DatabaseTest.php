<?php


namespace App\models;


class DatabaseTest extends DatabaseConnection
{


    public function getAllMembers()
    {

        $connection = $this->getConnection();

        $sql = "SELECT * FROM Members";
        $stmt = $connection->prepare($sql);
        $stmt->execute();

        return $stmt->get_result();
    }
}