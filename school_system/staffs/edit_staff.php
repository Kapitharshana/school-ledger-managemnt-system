<?php
session_start();
include '../db_connect.php';

// Only Principal can access
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'Principal'){
    header("Location: login.html");
    exit();
}

$msg = "";

// Get the User_ID from GET
if(!isset($_GET['id'])){
    header("Location: manage_staff.php");
    exit();
}
$user_id = $_GET['id'];

// Fetch existing staff data
$result = $conn->query("SELECT * FROM Users WHERE User_ID='$user_id' AND Role='Staff'");
if($result->num_rows == 0){
    header("Location: manage_staff.php");
    exit();
}
$staff = $result->fetch_assoc();

// Handle form submission
if(isset($_POST['update_staff'])){
    $username = $_POST['username'];

    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE Users SET Username='$username', Password='$password' WHERE User_ID='$user_id' AND Role='Staff'";
    } else {
        $sql = "UPDATE Users SET Username='$username' WHERE User_ID='$user_id' AND Role='Staff'";
    }

    if($conn->query($sql)){
        $_SESSION['success_msg'] = "Staff updated successfully!";
        header("Location: manage_staff.php");
        exit();
    } else {
        $msg = "Error updating staff: ".$conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Staff</title>
    <link rel="stylesheet" href="../header_style.css">
    <link rel="stylesheet" href="content.css">
    
</head>
<body>

<?php $pageTitle = "Edit Staff"; ?>
<div class="header-bar">
    <div class="header-left"><?php include '../sidebar.php'; ?></div>
    <div class="header-title"><?php echo $pageTitle; ?></div>
    <div class="header-right"><img src="../newschool.png" alt="School Logo"></div>
</div>

<div class="main-content">

    <?php if($msg != "") echo "<p class='error-msg'>".htmlspecialchars($msg)."</p>"; ?>

    <form method="post">
        <div>
            <label>User ID </label>
            <input type="text" value="<?php echo htmlspecialchars($staff['User_ID']); ?>" readonly>
        </div>

        <div>
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($staff['Username']); ?>" required>
        </div>

        <div>
            <label>New Password</label>
            <input type="password" name="password" placeholder="Leave blank to keep current password">
        </div>

        <div class="button-group">
            <button type="submit" name="update_staff" class="primary-btn">Update Staff</button>
            <button type="button" class="cancel-btn" onclick="window.location.href='manage_staff.php'">Cancel</button>
        </div>
    </form>
</div>

</body>
</html>
