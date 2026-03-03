<?php
session_start();
include '../db_connect.php';

// Only Principal can access
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'Principal'){
    header("Location: login.html");
    exit();
}

// ---------------- Flash message ----------------
$msg = $_SESSION['success_msg'] ?? "";
unset($_SESSION['success_msg']);

// ---------------- Fetch staff ----------------
$search = "";
$query = "SELECT * FROM Users WHERE Role='Staff'";

if(isset($_GET['search']) && $_GET['search'] != ""){
    $search = trim($_GET['search']);
    $query .= " AND Username LIKE '%$search%'";
}

$query .= " ORDER BY User_ID DESC";
$result = $conn->query($query);

// ---------------- Handle delete ----------------
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM Users WHERE User_ID='$id' AND Role='Staff'");
    $_SESSION['success_msg'] = "Staff deleted successfully!";
    header("Location: manage_staff.php");
    exit();
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Manage Staff</title>
    <link rel="stylesheet" href="../header_style.css">
    <link rel="stylesheet" href="../searchbox.css">
    <style>
        .main-content {
            margin-top: 100px;
            padding: 20px;
            justify-content: center;
        }

        .page-header {
    text-align: center;
    margin-bottom: 20px;
}
        .success-msg { color: green; text-align:center; margin-bottom:20px; }
        .error-msg { color: #dc3545; text-align:center; margin-bottom:20px; }

        .add-button {
    display: inline-block;
    padding: 8px 15px;
   padding: 10px 10px 10px 10px;
    background-color: #007bff;
    font-size: 14px;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s;
    cursor: pointer; 
    

        }   

        .add-button:hover {
            background-color: #0056b3;
        }

        table {
    width: 70%; /* or whatever width you want */
    border-collapse: collapse;
    margin: 30px auto 0 auto; /* top margin 30px, left/right auto to center */
}
       
        table th, table td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        table th { background-color: #f2f2f2; }
        table tr:nth-child(even) { background-color: #f9f9f9; }
        table td:last-child { text-align:center; }

        table a { display:inline-block; padding:5px 10px; border-radius:4px; color:#fff; text-decoration:none; margin-right:5px; }
        table a.edit-btn { background-color:#28a745; }
        table a.edit-btn:hover { background-color:#218838; }
        table a.delete-btn { background-color:#dc3545; }
        table a.delete-btn:hover { background-color:#c82333; }

        
    </style>
</head>
<body>

<?php $pageTitle = "Manage Staff"; ?>
<div class="header-bar">
    <div class="header-left"><?php include '../sidebar.php'; ?></div>
    <div class="header-title"><?php echo $pageTitle; ?></div>
    <div class="header-right"><img src="../newschool.png" alt="School Logo"></div>
</div>

<div class="main-content">
<div class="page-header">
    

<?php if($msg != "") echo "<p class='success-msg'>".htmlspecialchars($msg)."</p>"; ?>


    <!-- SEARCH -->

    <form method="get"class="search-box" >
        <input type="text" name="search" placeholder="Search by username" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-blue">Search</button>
        <a href="manage_staff.php" class="btn-gray">Reset</a>
    </form>
 
    <a href="add_staff.php" class="add-button" >Add New Staff</a>
    


    <table>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Actions</th>
        </tr>
        <?php
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td>".htmlspecialchars($row['User_ID'])."</td>"; // display User_ID
        echo "<td>".htmlspecialchars($row['Username'])."</td>";
        echo "<td>
                <a href='edit_staff.php?id=".$row['User_ID']."' class='edit-btn'>Edit</a>
                <a href='manage_staff.php?delete=".$row['User_ID']."' class='delete-btn' onclick='return confirm(\"Are you sure?\")'>Delete</a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3' style='text-align:center;'>No staff found</td></tr>";
}
?>

    </table>


<?php
// Handle delete
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM Users WHERE User_ID='$id' AND Role='Staff'");
    header("Location: manage_staff.php?msg=Staff+deleted+successfully");
    exit();
}
?>
</div></div>
</body>
</html>
