<?php
session_start();
include '../db_connect.php';

// Only Principal and Staff can access
if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: ../login.html");
    exit();
}

/* -------- FETCH TOTAL -------- */
$totalQuery = "
    SELECT COUNT(*) AS total_admissions
    FROM Admissions
";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();

/* -------- FETCH FULL LIST -------- */
$listQuery = "
    SELECT s.Student_ID,
           a.Admission_ID, 
           s.Full_Name, 
           s.Admission_No,
           a.Course, 
           a.Academic_Year, 
           a.Admission_Date,
           IFNULL(p.Payment_Status, 'No Payment') AS Payment_Status
    FROM Admissions a
    JOIN Students s ON a.Student_ID = s.Student_ID
    LEFT JOIN Payments p ON a.Admission_ID = p.Admission_ID
    ORDER BY a.Admission_ID DESC
";

$listResult = $conn->query($listQuery);

// Excel Export
if(isset($_GET['export']) && $_GET['export'] == 'excel'){
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=total_admissions.xls");

    echo "<table border='1'>";
    echo "<tr>
            <th>Student ID </th>
            <th>Admission ID</th>
            <th>Student Name</th>
            <th>Admission No</th>
            <th>Course</th>
            <th>Academic Year</th>
            <th>Admission Date</th>
            <th>Payment Status</th>
          </tr>";

    if($listResult->num_rows > 0){
        while($row = $listResult->fetch_assoc()){
            echo "<tr>";
            echo "<td>"."STU".$row['Student_ID']."</td>";
            echo "<td>"."AD".$row['Admission_ID']."</td>";
            echo "<td>".$row['Full_Name']."</td>";
            echo "<td>".$row['Admission_No']."</td>";
            echo "<td>".$row['Course']."</td>";
            echo "<td>".$row['Academic_Year']."</td>";
            echo "<td>".$row['Admission_Date']."</td>";
            echo "<td>".$row['Payment_Status']."</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8' align='center'>No admissions found</td></tr>";
    }

    echo "</table>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Total Admissions Report</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../header_style.css">

    

<style>

/* ---------- MAIN CONTENT ---------- */

.container{ margin-top:100px;  text-align: center; padding: 20px;}

body {
    font-family: Arial, sans-serif;
    
}

table {
    border-collapse: collapse;
    width: 100%;
   
}

th, td {
    border: 1px solid #aaa;
    padding: 8px;
    text-align: left;
}

th {
    background-color: #ddd;
}

        
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        th { background-color: #ddd; }

        .add-button{  
            
            display: inline-block;
            padding: 10px 10px 10px 10px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            border-color:#007bff;
            text-decoration: none;
            transition: background-color 0.3s;
                   
                }
        
                .add-button:hover {
                    background-color: #0056b3;
                }
    </style>


</head>
<body>
<!-- SIDEBAR NAVIGATION -->
<?php
$pageTitle = "Total Admission Report";
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



<!-- MAIN CONTENT -->

<div class="container">


<?php if(!isset($_GET['export'])){ ?>
    <form method="get" style="margin:10px 0;">
        <button type="submit" name="export" class=add-button value="excel">EXPORT</button>
    </form>
<?php } ?>


<h3 text-align="center">Total Admissions: <?= $totalRow['total_admissions']; ?></h3>

<table>
<tr>
    <th>Student ID </th>
    <th>Admission ID</th>
    <th>Student Name</th>
    <th>Admission No</th>
    <th>Course</th>
    <th>Academic Year</th>
    <th>Admission Date</th>
    <th>Payment Status</th>
</tr>

<?php
if($listResult->num_rows > 0){
    while($row = $listResult->fetch_assoc()){
        echo "<tr>";
        echo "<td>"."STU".$row['Student_ID']."</td>";
        echo "<td>"."AD".$row['Admission_ID']."</td>";
        echo "<td>".$row['Full_Name']."</td>";
        echo "<td>".$row['Admission_No']."</td>";
        echo "<td>".$row['Course']."</td>";
        echo "<td>".$row['Academic_Year']."</td>";
        echo "<td>".$row['Admission_Date']."</td>";
        echo "<td>".$row['Payment_Status']."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8' align='center'>No admissions found</td></tr>";
}
?>

</table>

</div>


</body>
</html>
