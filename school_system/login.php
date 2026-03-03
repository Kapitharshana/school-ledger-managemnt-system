<?php
session_start();
include 'db_connect.php'; // Database connection

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Users WHERE Username='$username'";
    $result = $conn->query($sql);

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();

        if(password_verify($password, $user['Password'])){
            // Save session
            $_SESSION['username'] = $user['Username'];
            $_SESSION['role'] = $user['Role'];

            // Role-based redirect
            if($user['Role'] == 'Principal'){
                header("Location: principal_dashboard.php");
                exit();
            } elseif($user['Role'] == 'Staff'){
                header("Location: staff_dashboard.php");
                exit();
            } else {
                // Unknown role, log out
                session_destroy();
                header("Location: login.html");
                exit();
            }

        } else {
            echo "<script>
            alert('User not found! or Password is incorrect!');
            window.location.href='login.html';
          </script>";
exit();
       }
    } else {
        
        echo "<script>
        alert('User not found! or Password is incorrect!');
        window.location.href='login.html';
      </script>";
        
exit();
        
    }
    
}
?>
