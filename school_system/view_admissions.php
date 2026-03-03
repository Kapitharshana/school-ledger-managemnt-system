<?php
session_start();
include 'db_connect.php';

if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: login.html");
    exit();
}

$search_sql = "SELECT a.*, s.Full_Name 
               FROM Admissions a
               JOIN Students s ON a.Student_ID = s.Student_ID";

$search_term = '';
if(isset($_GET['search']) && $_GET['search']!==''){
    $search_term = $_GET['search'];
    $search_sql .= " WHERE a.Admission_ID LIKE '%$search_term%' 
                     OR s.Full_Name LIKE '%$search_term%'
                     OR a.Academic_Year LIKE '%$search_term%'";
}

$search_sql .= " ORDER BY a.Admission_ID DESC";
$result = $conn->query($search_sql);

/* Delete */
if(isset($_GET['delete']) && $_SESSION['role']=='Principal'){
    $del_id = $_GET['delete'];
    $conn->query("DELETE FROM Admissions WHERE Admission_ID='$del_id'");
    header("Location: view_admissions.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admissions List</title>
    <link rel="stylesheet" href="header_style.css">
    <link rel="stylesheet" href="searchbox.css">

    <style>
  .main-content {
            margin-top: 100px;
            padding: 20px;
        }

  .page-header {
    text-align: center;
    margin-bottom: 20px;
}
 
.add-button {
    display: inline-block;
    padding: 8px 15px;
    /*padding: 10px 10px 10px 10px;*/
    background-color: #007bff;
    font-size: 14px;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
    margin-right: 100px;
    transition: background-color 0.3s;
    cursor: pointer; 
    

        }   

        .add-button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
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

        table td:last-child {
            text-align: center;
        }

        table a {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            margin-right: 5px;
        }

        table a[href*="edit="] {
            background-color: #28a745;
        }

        table a[href*="delete="] {
            background-color: #dc3545;
        }

        table a[href*="edit="]:hover {
            background-color: #218838;
        }

        table a[href*="delete="]:hover {
            background-color: #c82333;
        }

        table td:last-child {
    text-align: center;
    white-space: nowrap;   /* ADD THIS */
}

    </style>

</head>
<body>

<?php $pageTitle="Admissions List"; ?>
<div class="header-bar">
    
    <div class="header-left"><?php include 'sidebar.php'; ?></div>
    <div class="header-title"><?php echo $pageTitle; ?></div>
    <div class="header-right"><img src="newschool.png" alt="School Logo"></div>
</div>

<div class="main-content">
<div class="page-header">


<!-- SEARCH -->

<form method="get" class="search-box">
    <input type="text" name="search"
        placeholder="Student Name, Admission No, or Academic Year"
        value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    
    <button type="submit" class="btn-blue">Search</button>
    <a href="view_admissions.php" class="btn-gray">Reset</a>
</form>

<br>

    <a href="add_admission_form.php" class="add-button">Add New Admission</a>
    

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
        <th>Admission ID</th>
<th>Student ID</th>
<th>Student Name</th>
<th>Course</th>
<th>Academic Year</th>
<th>Admission Date</th>
<th>Date of Leaving</th>
<th>Reason for Leaving</th>
<th>Dropout Last Date</th>
<th>Actions</th>

        </tr>
        <?php while($row=$result->fetch_assoc()): ?>
            <tr>
            <td>AD<?php echo $row['Admission_ID'];?></td>
<td>STU<?php echo $row['Student_ID'];?></td>
<td><?php echo $row['Full_Name'];?></td>
<td><?php echo $row['Course'];?></td>
<td><?php echo $row['Academic_Year'];?></td>
<td><?php echo $row['Admission_Date'];?></td>
<td><?php echo $row['Date_of_Leaving'];?></td>
<td><?php echo $row['Reason_for_Leaving'];?></td>
<td><?php echo $row['Dropout_Last_Date'];?></td>
<td>
    
<a href="edit_admission.php?edit=<?php echo $row['Admission_ID']; ?>">Edit</a>

 
    <?php if($_SESSION['role']=='Principal'): ?>
        | <a href="view_admissions.php?delete=<?php echo $row['Admission_ID'];?>" onclick="return confirm('Are you sure?')">Delete</a>
    <?php endif; ?>
</td>

            </tr>
        <?php endwhile; ?>
    </table>
</div>
    </div>
</body>
</html>
