<?php

include_once("DB_interface.php");

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'munsoft_polanco');

class DB_conn implements DB
{

    /* Properties */
    public $conn;

    // Constructor:
    function __construct()
    {
        // At instantiation of the class/object, we will connect to our database.
        $this->connect();
    }

    // Magic method to proxy properties to $conn (mysqli)
    public function __get($name)
    {
        if ($name === 'error') {
            return $this->conn->error;
        }
        if ($name === 'connect_error') {
            return $this->conn->connect_error;
        }
        if ($name === 'num_rows' && isset($this->last_result)) {
            return $this->last_result->num_rows;
        }
        return null;
    }

    /* Methods */

    // Method for connecting to database
    public function connect()
    {
        $this->conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

        if ($this->conn->connect_error)
            die('Database error -> ' . $this->conn->connect_error);
    }

    // Method for doing query
    public function query($query)
    {
        $resultSet = $this->conn->query($query);

        return $resultSet;
    }

    // Method for real escaping string (we will call it from User class)
    public function escape_string($string)
    {
        $escapedString = $this->conn->real_escape_string($string);

        return $escapedString;
    }

    // Method for fetching array from database results
    public function fetch_array($result)
    {
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    // Method for checking number of returned rows (when doing select queries)
    public function num_rows($result)
    {
        $numRows = $result->num_rows;

        if ($numRows > 0) {
            return $numRows;
        } else {
            return false;
        }
    }

    // Method for checking number of affected rows (when doing insert/update/delete queries etc..)
    public function affectedRows()
    {
        $affectedRows = $this->conn->affected_rows;

        if ($affectedRows > 0) {
            return $affectedRows;
        } else {
            return false;
        }
    }

    // Method for preparing statements (for parameterized queries)
    public function prepare($query)
    {
        return $this->conn->prepare($query);
    }

    public function close()
    {
        return mysqli_close($this->conn);
    }

    public function last_id()
    {
        return $this->conn->insert_id;
    }

}

?>