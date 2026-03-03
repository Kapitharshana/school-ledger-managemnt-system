<?php
session_start();
include 'db_connect.php';

// Only Principal and Staff can access
if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: login.html");
    exit();
}

/* ---------------- SEARCH ---------------- */

$search = "";
$query = "SELECT s.*
          FROM Students s
          LEFT JOIN Admissions a ON s.Student_ID = a.Student_ID";

if(isset($_GET['search']) && $_GET['search'] != ""){
    $search = trim($_GET['search']);

    if(is_numeric($search)){
        $query .= " WHERE a.Academic_Year = '$search'";
    } else {
        $query .= " WHERE 
            s.Full_Name LIKE '%$search%' 
            OR s.Admission_No LIKE '%$search%'";
    }
}

$query .= " GROUP BY s.Student_ID ORDER BY s.Student_ID DESC";




$result = $conn->query($query);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Students List</title>
    <link rel="stylesheet" href="header_style.css">
    <link rel="stylesheet" href="searchbox.css">
    <style>
        /* Internal CSS for main content positioning */
        .main-content {
            margin-top: 100px; /* Same as header height */
            padding: 20px;
            
        }
        .page-header {
    text-align: center;
    margin-bottom: 20px;
}


.add-button {
    
    display: inline-block;
    padding: 10px 10px ;
    background-color: #007bff;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
    margin-right: 100px;
    transition: background-color 0.3s;
}

.add-button:hover{
    background-color: #0056b3;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 40px; /* Added space below button */
}

table th, table td {
    border: 1px solid #ccc;
    padding: 10px; /* slightly bigger padding */
    text-align: left;
}

table th {
    background-color: #f2f2f2;
}

table tr:nth-child(even) {
    background-color: #f9f9f9; /* alternate row color */
}

a {
   
    text-decoration: none;
    color: #007bff;
    
}



/* Table action buttons */
table a {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 4px;
    color: #fff;
    text-decoration: none;
    font-size: 14px;
    margin-right: 5px;
}

table a[href*="edit_student.php"] {
    background-color: #28a745; /* Green for Edit */
}

table a[href*="delete_student.php"] {
    background-color: #dc3545; /* Red for Delete */
}

table a[href*="edit_student.php"]:hover {
    background-color: #218838;
}

table a[href*="delete_student.php"]:hover {
    background-color: #c82333;
}
table td:last-child {
    text-align: center;
    white-space: nowrap;   /* ADD THIS */
}
/* Center action buttons in table cell */
/*table td:last-child {
    text-align: center;
}*/


    </style>
</head>

<body>


<?php
$pageTitle = "Students List ";
include 'header.php';
?>

<!-- Top Header Bar -->
<div class="header-bar">

    <div class="header-left">
    <?php include 'sidebar.php'; ?>
    </div>
    
    <div class="header-title">
    <?php 
        if(isset($pageTitle)){
            echo $pageTitle;
        }
    ?>
    </div>

    <div class="header-right">
        <img src="newschool.png" alt="School Logo">
    </div>
    
</div>





<!-- Page Content -->
<div class="main-content">
<div class="page-header">

<!-- SEARCH -->

<form method="get" class="search-box">
    <input type="text" name="search"
        placeholder="Student Name, Admission No, or Academic Year"
        value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    
    <button type="submit" class="btn-blue">Search</button>
    <a href="view_students.php" class="btn-gray">Reset</a>
</form>

<br>
    
<a href="add_student_from.php" class="add-button">Add New Student</a>
    
       
<div style="overflow-x:auto;">   
<table>
    <tr>
        <th>Student ID</th>
        <th>Admission No</th>
        <th>Full Name</th>
        <th>DOB</th>
        <th>Birth Certificate No</th>
        <th>Gender</th>
        <th>Religion</th>
        <th>Guardian Name</th>
        <th>Address</th>
        <th>Contact</th>
        <th>WhatsApp</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>

    <?php
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            echo "<tr>";
            echo "<td>"."STU" . $row['Student_ID']."</td>";
            echo "<td>".$row['Admission_No']."</td>";
            echo "<td>".$row['Full_Name']."</td>";
            echo "<td>".$row['DOB']."</td>";
            echo "<td>".$row['Birth_Certificate_No']."</td>";
            echo "<td>".$row['Gender']."</td>";
            echo "<td>".$row['Religion']."</td>";
            echo "<td>".$row['Guardian_Name']."</td>";
            echo "<td>".$row['Address']."</td>";
            echo "<td>".$row['Contact_Number']."</td>";
            echo "<td>".$row['WhatsApp_No']."</td>";
            echo "<td>".$row['Email']."</td>";

            // Actions
            echo "<td>";
            echo "<a href='edit_student.php?id=".$row['Student_ID']."'>Edit</a> | ";
            
            if($_SESSION['role'] == 'Principal'){
                echo "<a href='delete_student.php?id=".$row['Student_ID']."' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
            }

            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='13' style='text-align:center;'>No students found</td></tr>";
    }
    ?>
</table>
</div>
    

    </div>    
</div>

</body>
</html>
