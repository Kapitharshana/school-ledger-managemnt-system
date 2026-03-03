<?php
session_start();
include 'db_connect.php';

// Only Principal can access
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'Principal'){
    header("Location: login.html");
    exit();
}

$msg = "";

/* ---------------- ADD STAFF ---------------- */
if(isset($_POST['add_staff'])){
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'Staff';

    $sql = "INSERT INTO Users (Username, Password, Role)
            VALUES ('$username','$password','$role')";

    if($conn->query($sql)){
        $msg = "Staff added successfully!";
    } else {
        $msg = "Error: ".$conn->error;
    }
}

/* ---------------- DELETE STAFF ---------------- */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM Users WHERE User_ID='$id' AND Role='Staff'");
    header("Location: manage_staff.php");
    exit();
}

/* ---------------- LOAD STAFF FOR EDIT ---------------- */
$edit_user = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $result_edit = $conn->query("SELECT * FROM Users WHERE User_ID='$id' AND Role='Staff'");
    $edit_user = $result_edit->fetch_assoc();
}

/* ---------------- UPDATE STAFF ---------------- */
if(isset($_POST['update_staff'])){
    $id = $_POST['user_id'];
    $username = $_POST['username'];

    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE Users SET Username='$username', Password='$password'
                WHERE User_ID='$id' AND Role='Staff'";
    } else {
        $sql = "UPDATE Users SET Username='$username'
                WHERE User_ID='$id' AND Role='Staff'";
    }

    if($conn->query($sql)){
        header("Location: manage_staff.php");
        exit();
    } else {
        $msg = "Error updating staff!";
    }
}

/* ---------------- FETCH ALL STAFF ---------------- */
$result = $conn->query("SELECT * FROM Users WHERE Role='Staff'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Staff</title>
    <link rel="stylesheet" href="header_style.css">
    <link rel="stylesheet" href="searchbox.css">

    <style>
         /* Main content below header */
         .main-content {
            margin-top: 120px;
            padding: 20px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .success-msg {
            color: green;
            text-align: center;
            margin-bottom: 20px;
        }

        .error-msg {
            color: #dc3545;
            text-align: center;
            margin-bottom: 20px;
        }
 
        
        /* Form styling (like add_student_from.php) */
        .staff-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 40px;
        }

        .staff-form label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .staff-form input {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
            box-sizing: border-box;
        }

        .staff-form button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            align-self: center;
            transition: background-color 0.3s;
        }

        .staff-form button:hover {
            background-color: #0056b3;
        }

        .cancel-link {
            display: inline-block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }

        .cancel-link:hover {
            text-decoration: underline;
        }

        /* Table styling (like view_students.php) */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        }

        hr.section-divider {
            border: none;
            border-top: 2px solid #e5e7eb;
            margin: 30px 0;
            width: 100vw;
            margin-left: calc(50% - 50vw);
        }
    </style>
</head>
<body>

<?php $pageTitle = "Manage Staff"; ?>

<!-- Header Bar -->
<div class="header-bar">
    <div class="header-left">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="header-title">
        <?php if(isset($pageTitle)) echo $pageTitle; ?>
    </div>
    <div class="header-right">
        <img src="newschool.png" alt="School Logo">
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2>Manage Staff Accounts</h2>

    <?php
    if($msg != "") {
        $msgClass = (strpos($msg, 'Error') !== false) ? 'error-msg' : 'success-msg';
        echo "<p class='$msgClass'>".htmlspecialchars($msg)."</p>";
    }
    ?>

    <?php if($edit_user): ?>
    <h3>Edit Staff</h3>
    <form method="post" class="staff-form">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($edit_user['User_ID']); ?>">
        <div>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($edit_user['Username']); ?>" required>
        </div>
        <div>
            <label for="password">New Password</label>
            <input type="password" name="password" id="password" placeholder="Leave blank to keep current password">
        </div>
        <button type="submit" name="update_staff">Update Staff</button>
        <a href="manage_staff.php" class="cancel-link">Cancel Edit</a>
    </form>
    <?php else: ?>
    <h3>Add New Staff</h3>
    <form method="post" class="staff-form">
        <div>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Username" required>
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Password" required>
        </div>
        <button type="submit" name="add_staff">Add Staff</button>
    </form>
    <?php endif; ?>

    <hr class="section-divider">


    <!-- SEARCH -->

<form method="get" class="search-box">
    <input type="text" name="search"
        placeholder="Student Name, Admission No, or Academic Year"
        value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    
    <button type="submit" class="btn-blue">Search</button>
    <a href="view_payments.php" class="btn-gray">Reset</a>
</form>

<br>

    <h3>Existing Staff</h3>
    <table>
        <tr>
            <th>Username</th>
            <th>Actions</th>
        </tr>
        <?php
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                echo "<tr>";
                echo "<td>".htmlspecialchars($row['Username'])."</td>";
                echo "<td>
                        <a href='manage_staff.php?edit=".$row['User_ID']."'>Edit</a> 
                        
                        <a href='manage_staff.php?delete=".$row['User_ID']."' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2' style='text-align:center;'>No staff found</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
