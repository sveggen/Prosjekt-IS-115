<?php


namespace App\models;

use mysqli;

class DatabaseConn
{


    private $database;

    /**
     * DatabaseConn constructor.
     */
    public function __construct()
    {
        $this->database = $this->connect();
    }


    public function connect()
    {
        define('MYSQL_VERT', 'db');
        define('MYSQL_BRUKER', 'root');
        define('MYSQL_PASSORD', 'BSBACIT2020');
        define('MYSQL_DB', 'ergotests');

        $connection = new mysqli(MYSQL_VERT, MYSQL_BRUKER, MYSQL_PASSORD, MYSQL_DB);
        if ($connection->connect_error) {
            die('Tilkoblingen til databasen feilet. Vennligst forsøk igjen senere.');
        }
        else {
            $this->database = $connection;
        }
        return $this->database;
    }


    public function getConnection()
    {
        return $this->database;
    }
}