<?php
session_start();
include '../db_connect.php';

// Only Principal and Staff can access
if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: ../login.html");
    exit();
}

// Get Payment ID from URL
if(!isset($_GET['id'])){
    die("Error: Payment ID is required.");
}

$payment_id = $_GET['id'];

// Fetch payment, student, and admission info
$sql = "
    SELECT p.Payment_ID, p.Amount, p.Payment_Date, p.Payment_Status,
           s.Full_Name, s.Admission_No,
           a.Course, a.Academic_Year
    FROM Payments p
    JOIN Admissions a ON p.Admission_ID = a.Admission_ID
    JOIN Students s ON a.Student_ID = s.Student_ID
    WHERE p.Payment_ID='$payment_id'
";
$result = $conn->query($sql);

if($result->num_rows == 0){
    die("Payment not found!");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
    <link rel="stylesheet" href="receipt.css">

    
</head>
<body>

<div class="receipt">
    <h2>Payment Receipt</h2><br>
    <h3>KT/VSR COLLEGE</h3><br>
   

    <table>
        <tr>
            <th>Payment ID </th>
            <td><?= 'PAY'.$row['Payment_ID'] ?></td>
        </tr>
        <tr>
            <th>Student Name </th>
            <td><?= $row['Full_Name'] ?></td>
        </tr>
        <tr>
            <th>Admission No </th>
            <td><?= $row['Admission_No'] ?></td>
        </tr>
        <tr>
            <th>Course </th>
            <td><?= $row['Course'] ?></td>
        </tr>
        <tr>
            <th>Academic Year </th>
            <td><?= $row['Academic_Year'] ?></td>
        </tr>
        <tr>
            <th>Payment Date </th>
            <td><?= $row['Payment_Date'] ?></td>
        </tr>
        <tr>
            <th>Amount Paid </th>
            <td><?= $row['Amount'] ?></td>
        </tr>
        <tr>
            <th>Status </th>
            <td><?= $row['Payment_Status'] ?></td>
        </tr>
    </table>

    <div class="print-btn">
        <button onclick="window.print()">Print Receipt</button>
    </div>
</div>

</body>
</html>
