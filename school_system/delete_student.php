<?php
session_start();
include 'db_connect.php';

// Only Principal can delete
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'Principal'){
    header("Location: login.html");
    exit();
}

// Get Student ID from URL
if(!isset($_GET['id'])){
    header("Location: view_students.php");
    exit();
}

$student_id = $_GET['id'];

// Delete from DB
$sql = "DELETE FROM Students WHERE Student_ID='$student_id'";

if($conn->query($sql) === TRUE){
    header("Location: view_students.php?success=deleted");
    exit();
} else {
    echo "Error: ".$conn->error;
}
?>
