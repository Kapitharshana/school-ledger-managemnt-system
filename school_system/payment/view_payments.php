<?php
session_start();
include '../db_connect.php';

if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: ../login.html");
    exit();
}

/* ---------------- SEARCH ---------------- */
$search = "";
if(isset($_GET['search'])){
    $search = trim($_GET['search']);

    // Base query
    $query = "
        SELECT p.*, s.Full_Name, s.Admission_No, a.Academic_Year
        FROM Payments p
        JOIN Admissions a ON p.Admission_ID = a.Admission_ID
        JOIN Students s ON a.Student_ID = s.Student_ID
    ";

    if($search !== ""){
        if(is_numeric($search)){
            // Search by exact Academic Year
            $query .= " WHERE a.Academic_Year = '$search'";
        } else {
            // Search by Name or Admission No
            $query .= " WHERE s.Full_Name LIKE '%$search%' OR s.Admission_No LIKE '%$search%'";
        }
    }

    $query .= " ORDER BY p.Payment_ID DESC";

} else {
    $query = "
        SELECT p.*, s.Full_Name, s.Admission_No, a.Academic_Year
        FROM Payments p
        JOIN Admissions a ON p.Admission_ID = a.Admission_ID
        JOIN Students s ON a.Student_ID = s.Student_ID
        ORDER BY p.Payment_ID DESC
    ";
}


$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Payments List</title>
<link rel="stylesheet" href="../header_style.css">

<style>

/* ---------- MAIN CONTENT ---------- */
.main-content {
    margin-top: 100px;
    padding: 20px;
}

.page-header {
    text-align: center;
    margin-bottom: 20px;
}

/* ---------- BUTTONS ---------- */
.btn {
    display: inline-block;
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    color: #fff;
    border: none;
    cursor: pointer;
}

.btn-blue {
    background-color: #007bff;
}

.btn-blue:hover {
    background-color: #0056b3;
}

.btn-gray {
    background-color: #6c757d;
}

.btn-gray:hover {
    background-color: #5a6268;
}

/* ---------- SEARCH FORM ---------- */
.search-box input {
    padding: 8px;
    width: 300px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

.search-box button {
    padding: 8px 12px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
}

/* ---------- TABLE ---------- */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
}

table th, table td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: left;
}

table th {
    background-color: #f2f2f2;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* ---------- ACTION BUTTONS ---------- */
.action-btn {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 13px;
    text-decoration: none;
    color: #fff;
    margin-right: 5px;
    display: inline-block;
}

.edit-btn {
    background-color: #28a745;
}

.edit-btn:hover {
    background-color: #218838;
}

.delete-btn {
    background-color: #dc3545;
}

.delete-btn:hover {
    background-color: #c82333;
}

.print-btn {
    background-color: #17a2b8;
}

.print-btn:hover {
    background-color: #117a8b;
}

table td:last-child {
    text-align: center;
}

</style>
</head>

<body>

<?php
$pageTitle = "Payments List";
include '../header.php';
?>

<div class="header-bar">
    <div class="header-left">
        <?php include '../sidebar.php'; ?>
    </div>
    <div class="header-title">
        <?php echo $pageTitle; ?>
    </div>
    <div class="header-right">
        <img src="../newschool.png" alt="School Logo">
    </div>
</div>

<div class="main-content">

<div class="page-header">

<!-- SEARCH -->
<form method="get" class="search-box">
    <input type="text" name="search"
        placeholder="Student Name, Admission No, or Academic Year"
        value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    
    <button type="submit" class="btn btn-blue">Search</button>
    <a href="view_payments.php" class="btn btn-gray">Reset</a>
</form>

<br>

<!-- ADD BUTTON -->
<a href="add_payment_form.php" class="btn btn-blue">Add Payment</a>

</div>

<!-- TABLE -->
<table>
<tr>
    <th>Payment ID</th>
    <th>Student Name</th>
    <th>Admission No</th>
    <th>Admission ID</th>
    <th>Payment Date</th>
    <th>Amount</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td>PAY".$row['Payment_ID']."</td>";
        echo "<td>".$row['Full_Name']."</td>";
        echo "<td>".$row['Admission_No']."</td>";
        echo "<td>AD".$row['Admission_ID']."</td>";
        echo "<td>".$row['Payment_Date']."</td>";
        echo "<td>".$row['Amount']."</td>";
        echo "<td>".$row['Payment_Status']."</td>";
        echo "<td>";

        echo "<a href='edit_payment.php?id=".$row['Payment_ID']."' class='action-btn edit-btn'>Edit </a>";
        echo "| ";
        if($_SESSION['role']=='Principal'){
            echo "<a href='delete_payment.php?id=".$row['Payment_ID']."' class='action-btn delete-btn' onclick='return confirm(\"Delete?\")'>Delete </a>";
        }
        echo "| ";
        echo "<a href='receipt_payment.php?id=".$row['Payment_ID']."' target='_blank' class='action-btn print-btn'>Print</a>";

        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8' align='center'>No payments found</td></tr>";
}
?>

</table>

</div>
</body>
</html>
