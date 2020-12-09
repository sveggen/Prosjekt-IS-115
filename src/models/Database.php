<?php


namespace App\models;

use mysqli;

/**
 * Parent class for all models - all logic for connecting to the Database
 * belongs here.
 *
 * Class Database
 * @package App\models
 */
abstract class Database {

    private $database;

    public function __construct() {
        $this->database = $this->connect();
    }

    /**
     * Opens a new connection to the database.
     *
     * @return mysqli
     */
    private function connect(): mysqli {
        // uses credentials from the .env file and connects to the databse.
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
     * Connection entrypoint to the database
     * which can be used by child classes.
     *
     * @return mysqli
     */
    protected function getConnection(): mysqli {
        return $this->database;
    }

}