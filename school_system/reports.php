<?php
session_start();
include 'db_connect.php';

// Only Principal and Staff can access
if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports Dashboard</title>
    <link rel="stylesheet" href="header_style.css">

    <style>

.main-content {
    margin-top: 100px;
    padding: 20px;
}


       
        .report-container {
            max-width: 600px;
            margin: auto;
            margin-top: 100px;
        }

        .report-container h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .report-list {
            list-style: none;
            padding: 0;
        }

        .report-list li {
            margin-bottom: 15px;
        }

        .report-list a {
            display: block;
            padding: 20px 20px;
            background-color:  #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
            transition: 0.3s;
            font-size:20px;
            font-weight:bold;
        }

        .report-list a:hover {
            background-color: #0f172a;
        }
    </style>
</head>

<body>


<?php
$pageTitle = "Payments List";
include 'header.php';
?>

<div class="header-bar">
    <div class="header-left">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="header-title">
        <?php echo $pageTitle; ?>
    </div>
    <div class="header-right">
        <img src="newschool.png" alt="School Logo">
    </div>
</div>


<div class="main-content">
    <div class="report-container">
       

        <ul class="report-list">
            <li><a href="reports/total_admissions.php">Total Admissions Report</a></li>
            <li><a href="reports/course_wise.php">Course-wise Report</a></li>
            <li><a href="reports/year_wise.php">Year-wise Intake Report</a></li>
            <li><a href="reports/payment_report.php">Payment Report</a></li>
            <li><a href="reports/outstanding_fees.php">Outstanding Fee Report</a></li>
        </ul>
    </div>
</div>

</body>
</html>
