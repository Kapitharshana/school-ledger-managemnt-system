<?php
session_start();
include '../db_connect.php';

// Only Principal and Staff can access
if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: ../login.html");
    exit();
}

// Fetch all payments with student and course info
$query = "
    SELECT  s.Student_ID,p.Payment_ID, s.Full_Name, s.Admission_No, a.Admission_ID, a.Course, a.Academic_Year, 
           p.Payment_Date, p.Amount, p.Payment_Status
    FROM Payments p
    JOIN Admissions a ON p.Admission_ID = a.Admission_ID
    JOIN Students s ON a.Student_ID = s.Student_ID
    ORDER BY p.Payment_ID DESC
";

$result = $conn->query($query);

// Fetch summary counts by Payment_Status
$summaryQuery = "
    SELECT Payment_Status, COUNT(*) AS total_students
    FROM Payments
    GROUP BY Payment_Status
";
$summaryResult = $conn->query($summaryQuery);

// Excel Export
if(isset($_GET['export']) && $_GET['export'] == 'excel'){
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=payment_report.xls");

    echo "<table border='1'>";
    echo "<tr>
            <th>Student ID </th>
            <th>Payment ID</th>
            <th>Student Name</th>
            <th>Admission No</th>
            <th>Admission ID</th>
            <th>Course</th>
            <th>Academic Year</th>
            <th>Payment Date</th>
            <th>Amount</th>
            <th>Payment Status</th>
          </tr>";

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            echo "<tr>";
            echo "<td>"."PAY".$row['Payment_ID']."</td>";
            echo "<td>".$row['Full_Name']."</td>";
            echo "<td>".$row['Admission_No']."</td>";
            echo "<td>"."AD".$row['Admission_ID']."</td>";
            echo "<td>".$row['Course']."</td>";
            echo "<td>".$row['Academic_Year']."</td>";
            echo "<td>".$row['Payment_Date']."</td>";
            echo "<td>".$row['Amount']."</td>";
            echo "<td>".$row['Payment_Status']."</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='9' align='center'>No payments found</td></tr>";
    }
    echo "</table><br>";

    // Summary table for Excel
    echo "<table border='1'>";
    echo "<tr><th>Payment Status</th><th>Total Students</th></tr>";
    if($summaryResult->num_rows > 0){
        while($row = $summaryResult->fetch_assoc()){
            echo "<tr>";
            echo "<td>".$row['Payment_Status']."</td>";
            echo "<td>".$row['total_students']."</td>";
            echo "</tr>";
        }
    }
    echo "</table>";

    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Report</title>
    <link rel="stylesheet" href="../style.css">
    
    <style>
        body { font-family: Arial, sans-serif; }
        .container{ margin-top:100px;  text-align: center; padding: 20px;}
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        th { background-color: #ddd; }
        .summary-table th { background-color: #bbb; }

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
$pageTitle = "Payment Report Summary";
include '../header.php';
?>

<div class="header-bar">
    <div class="header-left">
        <?php include '../sidebar.php'; ?>
        <link rel="stylesheet" href="../header_style.css">
    </div>
    <div class="header-title">
        <?php echo $pageTitle; ?>
    </div>
    <div class="header-right">
        <img src="../newschool.png" alt="School Logo">
    </div>
</div>


<div class=container>


<?php if(!isset($_GET['export'])){ ?>
    <form method="get" style="margin:10px 0;">
    
        <button  type="submit" name="export"  class=add-button value="excel" >EXPORT</button>

    </form>
<?php } ?> <br>


<table>
<tr>
    <th>Payment ID</th>
    <th>Student Name</th>
    <th>Admission No</th>
    <th>Admission ID</th>
    <th>Course</th>
    <th>Academic Year</th>
    <th>Payment Date</th>
    <th>Amount</th>
    <th>Payment Status</th>
</tr>

<?php
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td>"."PAY".$row['Payment_ID']."</td>";
        echo "<td>".$row['Full_Name']."</td>";
        echo "<td>".$row['Admission_No']."</td>";
        echo "<td>"."AD".$row['Admission_ID']."</td>";
        echo "<td>".$row['Course']."</td>";
        echo "<td>".$row['Academic_Year']."</td>";
        echo "<td>".$row['Payment_Date']."</td>";
        echo "<td>".$row['Amount']."</td>";
        echo "<td>".$row['Payment_Status']."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='9' align='center'>No payments found</td></tr>";
}
?>

</table>

<h3>Summary by Payment Status</h3>
<table class="summary-table">
<tr>
    <th>Payment Status</th>
    <th>Total Students</th>
</tr>

<?php
if($summaryResult->num_rows > 0){
    $summaryResult->data_seek(0);
    while($row = $summaryResult->fetch_assoc()){
        echo "<tr>";
        echo "<td>".$row['Payment_Status']."</td>";
        echo "<td>".$row['total_students']."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='2' align='center'>No data found</td></tr>";
}
?>
</div>
</table>
</body>
</html>
