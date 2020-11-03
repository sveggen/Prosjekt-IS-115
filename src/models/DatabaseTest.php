<?php


namespace App\models;


class DatabaseTest
{


    public function getAllMembers()
    {
        $db = new DatabaseConn();
        $connection = $db->getConnection();

        $sql = "SELECT * FROM Members";
        $stmt = $connection->prepare($sql);
        $stmt->execute();

        return $stmt->get_result();
    }
}