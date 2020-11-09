<?php


namespace App\models;

use mysqli;

abstract class Database
{


    protected $database;

    /**
     * Database constructor.
     */
    public function __construct()
    {
        $this->database = $this->connect();
    }


    public function connect()
    {
        define('HOST', 'db');
        define('USER', 'root');
        define('PASSWORD', 'BSBACIT2020');
        define('DB', 'ergotests');

        $connection = new mysqli(HOST, USER, PASSWORD, DB);
        if ($connection->connect_error) {
            die('Database connection failed');
        } else {
            $this->database = $connection;
        }
        return $this->database;
    }


    public function getConnection()
    {
        return $this->database;
    }
}