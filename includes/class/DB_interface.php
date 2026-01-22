<?php

interface DB 
{ 
    public function connect();
    public function query($query);
    public function escape_string($string);  
    public function fetch_array($result); 
    public function num_rows($result); 
    public function affectedRows(); 
    public function close(); 
    public function last_id();
} 


?>