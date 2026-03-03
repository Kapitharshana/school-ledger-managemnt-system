<?php
session_start();
include '../db_connect.php';

if(!isset($_SESSION['username']) || $_SESSION['role'] != 'Principal'){
    header("Location: login.html");
    exit();
}

$msg = "";

if(isset($_POST['add_staff'])){
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'Staff';

    $sql = "INSERT INTO Users (Username, Password, Role)
            VALUES ('$username','$password','$role')";

if($conn->query($sql)){
    $_SESSION['success_msg'] = "Staff added successfully!";
    header("Location: manage_staff.php");
    exit();
}

     else {
        $msg = "Error: ".$conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Staff</title>
    <link rel="stylesheet" href="../header_style.css">
    <link rel="stylesheet" href="content.css">
</head>
<body>

<?php $pageTitle = "Add Staff"; ?>
<div class="header-bar">
    <div class="header-left"><?php include '../sidebar.php'; ?></div>
    <div class="header-title"><?php echo $pageTitle; ?></div>
    <div class="header-right"><img src="../newschool.png" alt="School Logo"></div>
</div>

<div class="main-content">
   

    <?php if($msg != "") echo "<p style='color:red;'>$msg</p>"; ?>

    <form method="post" >
        <div>
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="button-group">
<button type="submit" name="add_staff" class="primary-btn">Add staff</button>
<button type="button" class="cancel-btn" onclick="window.location.href='manage_staff.php'">Cancel</button>
</div>
       
     
    </form>
</div>

</body>
</html>
