<?php


// Database credentials
$host = "localhost";       
$dbname = "school_db";     
$user = "root";            
$pass = "*******";                

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 //echo "Connected successfully"; 


?>


