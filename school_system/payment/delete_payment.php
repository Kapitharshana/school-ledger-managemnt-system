<?php
session_start();
include '../db_connect.php';

if($_SESSION['role'] != 'Principal'){
    die("Access Denied");
}

$id = $_GET['id'];
$conn->query("DELETE FROM Payments WHERE Payment_ID='$id'");

header("Location: view_payments.php");
exit();
?>
