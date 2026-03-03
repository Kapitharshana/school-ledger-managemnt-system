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
        .main-content {
            margin-top: 100px; /* same as header height */
            padding: 20px;
            min-height: calc(100vh - 100px);
            display: flex;
            flex-direction: column;
        }

        .center-message {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
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
<div class="center-message">
    <div>
    <h2>Welcome Principal: <?php echo $_SESSION['username']; ?></h2>
    <p>Welcome to the School management system.</p>
    </div>
</div>
    </div>

</body>
</html>
