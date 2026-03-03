<?php
session_start();
include '../db_connect.php';

$errors = [];
$admission_id = "";
$payment_date = "";
$amount = "";

/* ---------------- FORM SUBMIT ---------------- */
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $admission_id = trim($_POST['admission_id']);
    $payment_date = trim($_POST['payment_date']);
    $amount = trim($_POST['amount']);

    /* -------- VALIDATIONS -------- */

    // 1️⃣ Required fields
    if(empty($admission_id)){
        $errors['admission_id'] = "Admission ID is required";
    }

    if(empty($payment_date)){
        $errors['payment_date'] = "Payment date is required";
    }

    
    // 2️⃣ Amount validation
    if(!empty($amount)){
        if(!is_numeric($amount) || $amount <= 0){
            $errors['amount'] = "Amount must be positive";
        }
         elseif($amount > 30000){
            $errors['amount'] = "Maximum total payment is 30,000 Rs";
        }
    }

    // 3️⃣ Check if Admission ID exists in Admissions table
    if(empty($errors['admission_id'])){
        $check_adm = $conn->query("SELECT * FROM Admissions WHERE Admission_ID='$admission_id'");
        if($check_adm->num_rows == 0){
            $errors['admission_id'] = "This Admission ID does not exist!";
        }
    }

    // 4️⃣ Duplicate Payment check
    if(empty($errors)){
        $check = $conn->query("SELECT * FROM Payments WHERE Admission_ID='$admission_id'");
        if($check->num_rows > 0){
            $errors['admission_id'] = "Payment already exists for this Admission ID";
        }
    }

    // 5️⃣ Payment date validation
    if(empty($errors)){
        $admission = $conn->query("SELECT Admission_Date, Date_of_Leaving FROM Admissions WHERE Admission_ID='$admission_id'");
        if($admission->num_rows > 0){
            $row = $admission->fetch_assoc();
            $admission_date = $row['Admission_Date'];
            $leaving_date = $row['Date_of_Leaving'];
            $today = date('Y-m-d');

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

    /* -------- INSERT IF NO ERRORS -------- */
    if(empty($errors)){
        // Determine Payment Status
        if($amount >= 30000){
            $status = 'Paid';
        } elseif($amount > 0 && $amount < 30000){
            $status = 'Partial';
        } else {
            $status = 'Pending';
        }

        $sql = "INSERT INTO Payments (Admission_ID, Payment_Date, Amount, Payment_Status)
                VALUES ('$admission_id', '$payment_date', '$amount', '$status')";

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
    <title>Add Payment</title>
    <link rel="stylesheet" href="../header_style.css">
    <style>
        .error { color: red; font-size: 13px; }
        .invalid { border: 2px solid red; }
        input { padding: 6px; width: 250px; }
        button { padding: 8px 15px; margin-top: 10px; }

        .main-content {
            margin-top: 120px;
            padding: 20px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        label { font-weight: bold; margin-bottom: 5px; }
        input, select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
            box-sizing: border-box;
            font-size: 14px;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 15px;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
            width: 100px;
            align-self: flex-start;
        }
        .btn-blue { background-color: #007bff; }
        .btn-blue:hover { background-color: #0056b3; }
        .btn-gray { width: 70px; background-color: #6c757d; }
        .btn-gray:hover { background-color: #5a6268; }

        .button-group {
            display: flex;
            justify-content: center;
            align-items:center;
            gap: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<?php
$pageTitle = "Add Payment";
include '../header.php';
?>

<div class="header-bar">
    <div class="header-left">
        <?php include '../sidebar.php'; ?>
    </div>
    <div class="header-title"><?= $pageTitle ?></div>
    <div class="header-right">
        <img src="../newschool.png" alt="School Logo">
    </div>
</div>

<div class="main-content">
<form method="POST">
    <!-- Admission ID -->
    <div>
        <label>Admission ID</label><br>
        <input type="number" name="admission_id"
            value="<?= htmlspecialchars($admission_id) ?>"
            class="<?= isset($errors['admission_id']) ? 'invalid' : '' ?>">
        <br>
        <span class="error"><?= $errors['admission_id'] ?? '' ?></span>
    </div>

    <!-- Payment Date -->
    <div>
        <label>Payment Date</label><br>
        <input type="date" name="payment_date"
            value="<?= htmlspecialchars($payment_date) ?>"
            class="<?= isset($errors['payment_date']) ? 'invalid' : '' ?>">
        <br>
        <span class="error"><?= $errors['payment_date'] ?? '' ?></span>
    </div>

    <!-- Amount -->
    <div>
        <label>Amount (Rs)</label><br>
        <input type="number" name="amount"
            value="<?= htmlspecialchars($amount) ?>"
            class="<?= isset($errors['amount']) ? 'invalid' : '' ?>">
        <br>
        <span class="error"><?= $errors['amount'] ?? '' ?></span>
    </div>

    <div class="button-group">
        <button type="submit" name="add_payment_form" class="btn btn-blue">Add</button>
        <a href="view_payments.php" class="btn btn-gray">Cancel</a>
    </div>
</form>
</div>
</body>
</html>
