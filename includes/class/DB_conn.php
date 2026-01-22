<?php

include_once("DB_interface.php");

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'munsoft_polanco');

class DB_conn implements DB {

	/* Properties */
        private $conn;

        // Constructor:
        function __construct()
        {
            // At instantiation of the class/object, we will connect to our database.
            $this->connect();
        }

        /* Methods */

        // Method for connecting to database
        public function connect()
        {
            $this->conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD,DB_DATABASE);
		
			if ($this->conn->connect_error) die('Database error -> ' . $this->conn->connect_error);
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
        public function fetch_array($result){
        	return $result->fetch_array(MYSQLI_ASSOC);
        } 

        // Method for checking number of returned rows (when doing select queries)
        public function num_rows($result)
        {
            $numRows = $result->num_rows;

            if($numRows > 0) {
                return $numRows;
            } else {
                return false;
            }
        }

        // Method for checking number of affected rows (when doing insert/update/delete queries etc..)
        public function affectedRows()
        {
	            $affectedRows = $this->conn->affected_rows;

	            if($affectedRows > 0) {
	                return $affectedRows;
	            } else {
	                return false;
	            }
	    }

		public function close(){
			return mysqli_close($this->conn);
		} 

   	    public function last_id(){
            return $this->conn->insert_id;
        }		
   
}

?>

	
