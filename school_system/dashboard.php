<?php
session_start();

// If not logged in, go back to login
if(!isset($_SESSION['username'])){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h2>Welcome <?php echo $_SESSION['username']; ?></h2>
<p>Your Role: <?php echo $_SESSION['role']; ?></p>

<a href="logout.php">Logout</a>

</body>
</html>
