<?php
session_start();
include '../db_connect.php';

// Only Principal and Staff can access
if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: ../login.html");
    exit();
}

// Fetch students who have not fully paid (Pending or Partial)
$query = "
SELECT a.Admission_ID, s.Full_Name, s.Admission_No, a.Course, a.Academic_Year, a.Admission_Date,
       p.Payment_ID,
       IFNULL(p.Amount,0) AS Amount_Paid,
       (30000 - IFNULL(p.Amount,0)) AS Balance,
       p.Payment_Status
FROM Admissions a
JOIN Students s ON a.Student_ID = s.Student_ID
LEFT JOIN Payments p ON a.Admission_ID = p.Admission_ID
WHERE p.Payment_Status IN ('Pending','Partial') OR p.Payment_Status IS NULL
ORDER BY a.Admission_ID DESC
";

$result = $conn->query($query);

// Excel Export
if(isset($_GET['export']) && $_GET['export'] == 'excel'){
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=outstanding_fee_report.xls");

    echo "<table border='1'>";
    echo "<tr>
            <th>Payment ID</th>
            <th>Admission ID</th>
            <th>Student Name</th>
            <th>Admission No</th>
            <th>Course</th>
            <th>Academic Year</th>
            <th>Admission Date</th>            
            <th>Amount Paid</th>
            <th>Balance</th>
            <th>Payment Status</th>
          </tr>";

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            echo "<tr>";
            echo "<td>PAY".$row['Payment_ID']."</td>";
            echo "<td>AD".$row['Admission_ID']."</td>";
            echo "<td>".$row['Full_Name']."</td>";
            echo "<td>".$row['Admission_No']."</td>";
            echo "<td>".$row['Course']."</td>";
            echo "<td>".$row['Academic_Year']."</td>";
            echo "<td>".$row['Admission_Date']."</td>";
            
            echo "<td>".$row['Amount_Paid']."</td>";
            echo "<td>".$row['Balance']."</td>";
            echo "<td>".$row['Payment_Status']."</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='10' align='center'>No outstanding payments</td></tr>";
    }

    echo "</table>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Outstanding Fee Report</title>
    <link rel="stylesheet" href="../style.css">
    
    <style>
        body { font-family: Arial, sans-serif; }
        .container{ margin-top:100px;  text-align: center; padding: 20px;}
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        th { background-color: #ddd }
        .add-button{  
            display: inline-block;
            padding: 10px 10px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            border-color:#007bff;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .add-button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<?php
$pageTitle = "Outstanding Fee Report (Pending & Partial)";
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

<div class="container">

<?php if(!isset($_GET['export'])){ ?>
    <form method="get" style="margin:10px 0;">
        <button  type="submit" name="export"  class="add-button" value="excel">EXPORT</button>
    </form>
<?php } ?><br>

<table>
<tr>
    <th>Payment ID</th>
    <th>Admission ID</th>
    <th>Student Name</th>
    <th>Admission No</th>
    <th>Course</th>
    <th>Academic Year</th>
    <th>Admission Date</th>    
    <th>Amount Paid</th>
    <th>Balance</th>
    <th>Payment Status</th>
</tr>

<?php
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td>PAY".$row['Payment_ID']."</td>";
        echo "<td>AD".$row['Admission_ID']."</td>";
        echo "<td>".$row['Full_Name']."</td>";
        echo "<td>".$row['Admission_No']."</td>";
        echo "<td>".$row['Course']."</td>";
        echo "<td>".$row['Academic_Year']."</td>";
        echo "<td>".$row['Admission_Date']."</td>";
        echo "<td>".$row['Amount_Paid']."</td>";
        echo "<td>".$row['Balance']."</td>";
        echo "<td>".$row['Payment_Status']."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10' align='center'>No outstanding payments</td></tr>";
}
?>

</table>
</div>
</body>
</html>
