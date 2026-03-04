<?php
session_start();

// Only Principal can access
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'Principal'){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Principal Dashboard</title>
    <link rel="stylesheet" href="header_style.css">

    <style>
        /* Internal CSS for content centering only */
        html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    overflow: hidden; /* Unscrollable */
    font-family: Arial, sans-serif;
}

.main-content {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center; /* vertical center */
    align-items: center;     /* horizontal center */
    background-color: #f0f2f5;
}

.update-credentials {
    width: 350px;
    padding: 25px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-align: center;
    margin-bottom: 30px;
}

.update-credentials h3 {
    margin-bottom: 20px;
    color: #333;
}

.update-credentials label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
    color: #555;
    text-align: left;
}

.update-credentials input {
    width: 100%;
    padding: 8px 10px;
    margin-top: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

.update-credentials button {
    margin-top: 20px;
    width: 100%;
    padding: 10px;
    background-color: #28a745; /* green button */
    color: white;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.update-credentials button:hover {
    background-color: #218838;
}

.center-message {
    text-align: center;
    color: #333;
}

    </style>
</head>
<body>

<?php
$pageTitle = "Dashboard";
include 'header.php';
?>

<!-- Top Header Bar -->
<div class="header-bar">
    <div class="header-left">
        
    <?php include 'sidebar.php'; ?>
    </div>
    
    <div class="header-title">
    <?php 
        if(isset($pageTitle)){
            echo $pageTitle;
        }
    ?>

</div>


    <div class="header-right">
        <img src="newschool.png" alt="School Logo">
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <?php
    include 'db_connect.php';
    $msg = "";
    $msg_color = "red";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $newUsername = trim($_POST['username']);
        $newPassword = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirm_password']);

        if(empty($newUsername) || empty($newPassword) || empty($confirmPassword)) {
            $msg = "All fields are required!";
        } elseif ($newPassword !== $confirmPassword) {
            $msg = "Passwords do not match!";
        } else {
            // Check if new username is already taken
            $check = $conn->query("SELECT * FROM Users WHERE Username='$newUsername' AND Username!='{$_SESSION['username']}'");
            if($check->num_rows > 0) {
                $msg = "Username already taken!";
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $update = $conn->query("UPDATE Users SET Username='$newUsername', Password='$hashedPassword' WHERE Username='{$_SESSION['username']}' AND Role='Principal'");
                if($update) {
                    $_SESSION['username'] = $newUsername; // Update session
                    $msg = "Username & Password updated successfully!";
                    $msg_color = "green";
                } else {
                    $msg = "Error updating credentials!";
                }
            }
        }
    }
    ?>

<div class="update-credentials">
        <h3>Update Username & Password</h3>

        <?php if($msg != ""): ?>
            <p class="update-msg" style="color: <?php echo $msg_color; ?>;"><?php echo $msg; ?></p>
        <?php endif; ?>

        <form method="post" class="update-form">
            <label>New Username:</label>
            <input type="text" name="username" value="<?php echo $_SESSION['username']; ?>" required>

            <label>New Password:</label>
            <input type="password" name="password" required>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Update</button>
        </form>
    </div>

    <div class="center-message">
        <div>
            <h1>Welcome Principal: <?php echo $_SESSION['username']; ?></h1>
            <h2>Welcome to the School management system.</h2>
        </div>
    </div>
</div>
</body>
</html>


