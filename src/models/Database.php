<?php


namespace App\models;

use mysqli;

abstract class Database {

    private $database;

    public function __construct() {
        $this->database = $this->connect();
    }

    /**
     * Connects to the database.
     *
     * @return mysqli
     */
    private function connect(): mysqli {
        // uses credentials from the .env file to sign in to the DB.
        $connection = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER_1_USERNAME'],
            $_ENV['DB_USER_1_PASSWORD'], $_ENV['DB_NAME']);
        if ($connection->connect_error) {
            die('Database connection failed');
        } else {
            $this->database = $connection;
        }
        return $this->database;
    }

    /**
     * @return mysqli
     */
    protected function getConnection(): mysqli {
        return $this->database;
    }

}