<?php
session_start();
include '../db_connect.php';

// Only Principal and Staff can access
if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: ../login.html");
    exit();
}

// Fetch all years
$yearResult = $conn->query("
    SELECT DISTINCT Academic_Year
    FROM Admissions
    ORDER BY Academic_Year DESC
");

// Fetch summary totals
$summaryResult = $conn->query("
    SELECT Academic_Year, COUNT(*) AS total_students
    FROM Admissions
    GROUP BY Academic_Year
    ORDER BY Academic_Year DESC
");

// Excel Export: include detailed tables
if(isset($_GET['export']) && $_GET['export'] == 'excel'){
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=year_wise_detailed.xls");

    if($yearResult->num_rows > 0){
        while($yearRow = $yearResult->fetch_assoc()){
            $year = $yearRow['Academic_Year'];
            echo "<h3>Academic Year: $year</h3>";
            
            $detailsResult = $conn->query("
                SELECT s.Student_ID, a.Admission_ID, s.Full_Name, s.Admission_No, a.Course, a.Admission_Date,
                       IFNULL(p.Payment_Status, 'No Payment') AS Payment_Status
                FROM Admissions a
                JOIN Students s ON a.Student_ID = s.Student_ID
                LEFT JOIN Payments p ON a.Admission_ID = p.Admission_ID
                WHERE a.Academic_Year = '$year'
                ORDER BY a.Admission_ID DESC
            ");
            
            echo "<table border='1'>";
            echo "<tr>
                    <th>Student ID</th>
                    <th>Admission ID</th>
                    <th>Student Name</th>
                    <th>Admission No</th>
                    <th>Course</th>
                    <th>Admission Date</th>
                    <th>Payment Status</th>
                  </tr>";
            
            if($detailsResult->num_rows > 0){
                while($row = $detailsResult->fetch_assoc()){
                    echo "<tr>";
                    echo "<td>"."STU".$row['Student_ID']."</td>";
                    echo "<td>"."AD".$row['Admission_ID']."</td>";
                    echo "<td>".$row['Full_Name']."</td>";
                    echo "<td>".$row['Admission_No']."</td>";
                    echo "<td>".$row['Course']."</td>";
                    echo "<td>".$row['Admission_Date']."</td>";
                    echo "<td>".$row['Payment_Status']."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' align='center'>No students found for this year</td></tr>";
            }
            echo "</table><br><br>";
        }
    }

    // Summary table
    echo "<h2>Summary: Total Students per Academic Year</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Academic Year</th><th>Total Students</th></tr>";
    if($summaryResult->num_rows > 0){
        while($row = $summaryResult->fetch_assoc()){
            echo "<tr>";
            echo "<td>".$row['Academic_Year']."</td>";
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
    <title>Year-wise Report</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../header_style.css">
    <style>
        body { font-family: Arial, sans-serif; }
        .container{ margin-top:100px;  text-align: center; padding: 20px;}
        .year-section { 
            border: 1px solid #ccc; 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .year-section h3 { margin-top: 0; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
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
$pageTitle = "Year-wise Admission Report";
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


<div class=container>

<?php if(!isset($_GET['export'])){ ?>
    <form method="get" style="margin:10px 0;">
        <button  type="submit" name="export" class="add-button" value="excel">EXPORT</button>
    </form>
<?php } ?><br>

<?php
if($yearResult->num_rows > 0){
    $yearResult->data_seek(0);

    while($yearRow = $yearResult->fetch_assoc()){
        $year = $yearRow['Academic_Year'];
        echo "<div class='year-section'>";
        echo "<h3>Academic Year: $year</h3>";
        
        $detailsResult = $conn->query("
            SELECT s.Student_ID, a.Admission_ID, s.Full_Name, s.Admission_No, a.Course, a.Admission_Date,
                   IFNULL(p.Payment_Status, 'No Payment') AS Payment_Status
            FROM Admissions a
            JOIN Students s ON a.Student_ID = s.Student_ID
            LEFT JOIN Payments p ON a.Admission_ID = p.Admission_ID
            WHERE a.Academic_Year = '$year'
            ORDER BY a.Admission_ID DESC
        ");
        
        echo "<table>";
        echo "<tr>
                <th>Student ID </th>
                <th>Admission ID</th>
                <th>Student Name</th>
                <th>Admission No</th>
                <th>Course</th>
                <th>Admission Date</th>
                <th>Payment Status</th>
              </tr>";
        
        if($detailsResult->num_rows > 0){
            while($row = $detailsResult->fetch_assoc()){
                echo "<tr>";
                echo "<td>"."STU".$row['Student_ID']."</td>";
                echo "<td>"."AD".$row['Admission_ID']."</td>";
                echo "<td>".$row['Full_Name']."</td>";
                echo "<td>".$row['Admission_No']."</td>";
                echo "<td>".$row['Course']."</td>";
                echo "<td>".$row['Admission_Date']."</td>";
                echo "<td>".$row['Payment_Status']."</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7' align='center'>No students found for this year</td></tr>";
        }
        echo "</table>";
        echo "</div>";
    }
}
?>

<h2>Summary: Total Students per Academic Year</h2>
<table class="summary-table">
<tr>
    <th>Academic Year</th>
    <th>Total Students</th>
</tr>
<?php
if($summaryResult->num_rows > 0){
    $summaryResult->data_seek(0);
    while($row = $summaryResult->fetch_assoc()){
        echo "<tr>";
        echo "<td>".$row['Academic_Year']."</td>";
        echo "<td>".$row['total_students']."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='2' align='center'>No data found</td></tr>";
}
?>
</table>

</body>
</html>
