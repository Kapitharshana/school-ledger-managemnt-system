<?php
session_start();
include '../db_connect.php';

if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: ../login.html");
    exit();
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM Payments WHERE Payment_ID='$id'");
if($result->num_rows == 0){
    die("Payment not found");
}
$data = $result->fetch_assoc();

$errors = [];
$payment_date = $data['Payment_Date'];
$amount = $data['Amount'];

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_payment'])){

    $payment_date = trim($_POST['payment_date']);
    $amount = trim($_POST['amount']);

    // 1️⃣ Required fields
    if(empty($payment_date)){
        $errors['payment_date'] = "Payment date is required";
    }

    if(empty($amount)){
        $errors['amount'] = "Amount is required";
    }

    // 2️⃣ Amount validation
    if(!empty($amount)){
        if(!is_numeric($amount) || $amount <= 0){
            $errors['amount'] = "Amount must be positive";
        } elseif($amount < 500){
            $errors['amount'] = "Minimum payment is 500 Rs";
        } 
    }

    
   
   
/// 3️⃣ Payment date must be between Admission_Date and Date_of_Leaving, and not in future
$admission_id = $data['Admission_ID'];
$admission = $conn->query("SELECT Admission_Date, Date_of_Leaving FROM Admissions WHERE Admission_ID='$admission_id'");
if($admission->num_rows > 0){
    $row = $admission->fetch_assoc();
    $admission_date = $row['Admission_Date'];
    $leaving_date = $row['Date_of_Leaving'];
    $today = date('Y-m-d');

    if(!empty($payment_date)){
        if($payment_date < $admission_date){
            $errors['payment_date'] = "Payment date must be after Admission Date";
        }
        if(!empty($leaving_date) && $payment_date > $leaving_date){
            $errors['payment_date'] = "Payment date must be before Date of Leaving";
        }
        if(empty($leaving_date) && $payment_date > $today){
            $errors['payment_date'] = "Payment date cannot be in the future";
        }
    }
}



    // 4️⃣ If no errors, calculate total payments and determine status
    if(empty($errors)){
        // Get current amount of this payment
        $current = $data['Amount']; // existing amount in this row
    
        // Add new entered amount to current amount
        $new_amount = $current + (float)$amount;
    
        // Determine status based on cumulative amount
        if($new_amount >= 30000){
            $status = 'Paid';
            $new_amount=30000;
        } elseif($new_amount > 0){
            $status = 'Partial';
        } else {
            $status = 'Pending';
        }

        // Update payment
        $sql = "UPDATE Payments SET
        Payment_Date='$payment_date',
        Amount='$new_amount',
        Payment_Status='$status'
        WHERE Payment_ID='$id'";
        
        if($conn->query($sql)){
            header("Location: view_payments.php");
            exit();
        } else {
            $errors['general'] = "Database error: " . $conn->error;
        }
    
    }
    
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Payment</title>
    <link rel="stylesheet" href="../header_style.css">
    <style>
        .main-content { margin-top: 120px; padding: 20px; max-width: 600px; margin-left:auto; margin-right:auto; }
        form { display:flex; flex-direction:column; gap:15px; background:#f9f9f9; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);}
        label { font-weight:bold; margin-bottom:5px;}
        input { padding:10px; border-radius:5px; border:1px solid #ccc; width:100%; box-sizing:border-box;}
        input:focus { outline:none; border-color:#007bff; box-shadow:0 0 5px rgba(0,123,255,0.3);}
        .error { color:red; font-size:13px; }
        .invalid { border:2px solid red; }
        .btn { display:inline-block; padding:10px 18px; border-radius:5px; text-decoration:none; font-size:15px; color:#fff; border:none; cursor:pointer; transition:background-color 0.3s; margin-top:10px; width:100px; align-self:flex-start;}
        .btn-blue { background-color:#007bff; }
        .btn-blue:hover { background-color:#0056b3; }
        .btn-gray { background-color:#6c757d; width:70px; }
        .btn-gray:hover { background-color:#5a6268; }
        .button-group { display:flex; justify-content:center; align-items:center; gap:15px; margin-top:20px; }
    </style>
</head>
<body>

<?php
$pageTitle = "Edit Payment";
include '../header.php';
?>

<div class="header-bar">
    <div class="header-left"><?php include '../sidebar.php'; ?></div>
    <div class="header-title"><?php echo $pageTitle; ?></div>
    <div class="header-right"><img src="../newschool.png" alt="School Logo"></div>
</div>

<div class="main-content">

<form method="post">

    <?php if(isset($errors['general'])): ?>
        <div class="error"><?= $errors['general'] ?></div>
    <?php endif; ?>

    <!-- Payment Date -->
    <div>
        <label>Payment Date</label>
        <input type="date" name="payment_date"
               value="<?= htmlspecialchars($payment_date) ?>"
               class="<?= isset($errors['payment_date']) ? 'invalid' : '' ?>">
        <span class="error"><?= $errors['payment_date'] ?? '' ?></span>
    </div>

    <!-- Amount -->
    <div>
        <label>Balance Amount (Rs)</label>
        <input type="number" step="0.01" name="amount"
               value="<?= htmlspecialchars($amount) ?>"
               class="<?= isset($errors['amount']) ? 'invalid' : '' ?>">
        <span class="error"><?= $errors['amount'] ?? '' ?></span>
    </div>

    <div class="button-group">
        <button type="submit" name="update_payment" class="btn btn-blue">Update</button>
        <a href="view_payments.php" class="btn btn-gray">Cancel</a>
    </div>

</form>
</div>
</body>
</html>
