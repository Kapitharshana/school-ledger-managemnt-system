<?php
session_start();

// Only Principal and Staff can access
if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: login.html");
    exit();
}

include 'db_connect.php';

if(isset($_POST['submit'])){
    $errors = [];
    $old = $_POST;

    $full_name = trim($_POST['full_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $birth_cert = trim($_POST['birth_cert'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $religion = trim($_POST['religion'] ?? '');
    $guardian = trim($_POST['guardian'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Required fields
    if($full_name === '') $errors['full_name'] = 'Full name is required.';
    if($dob === '') $errors['dob'] = 'Date of birth is required.';
    if($birth_cert === '') $errors['birth_cert'] = 'Birth certificate number is required.';
    if($gender === '') $errors['gender'] = 'Gender is required.';
    if($religion === '') $errors['religion'] = 'Religion is required.';
    if($contact === '') $errors['contact'] = 'Contact number is required.';
    if($whatsapp === '') $errors['whatsapp'] = 'WhatsApp number is required.';

    // Validate DOB age: 15 <= age <= 20
    if($dob !== ''){
        try {
            $today = new DateTime();
            $birthDate = new DateTime($dob);
            $age = $today->diff($birthDate)->y;

            if($birthDate > $today){
                $errors['dob'] = 'Date of birth cannot be in the future.';
            } elseif($age < 15 || $age > 20){
                $errors['dob'] = 'Student age must be between 15 and 20 years.';
            }
        } catch(Exception $e){
            $errors['dob'] = 'Invalid date of birth.';
        }
    }

    // Contact & WhatsApp validation
    if($contact !== '' && !preg_match('/^\d{10}$/', $contact)){
        $errors['contact'] = 'Contact number must be exactly 10 digits.';
    }
    if($whatsapp !== '' && !preg_match('/^\d{10}$/', $whatsapp)){
        $errors['whatsapp'] = 'WhatsApp number must be exactly 10 digits.';
    }

    // Email validation
    if($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = 'Please enter a valid email address.';
    }

    // If any errors, redirect back
    if(!empty($errors)){
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_input'] = $old;
        header("Location: add_student_from.php");
        exit();
    }

    // Escape for SQL
    $emailEsc = mysqli_real_escape_string($conn, $email);
    $contactEsc = mysqli_real_escape_string($conn, $contact);
    $whatsappEsc = mysqli_real_escape_string($conn, $whatsapp);
    $birthCertEsc = mysqli_real_escape_string($conn, $birth_cert);

    // Duplicate check for email, contact, whatsapp, birth certificate
    $dupSql = "SELECT * FROM Students 
               WHERE Email='$emailEsc' 
                  OR Contact_Number='$contactEsc' 
                  OR WhatsApp_No='$whatsappEsc' 
                  OR Birth_Certificate_No='$birthCertEsc'
               LIMIT 1";
    $check = mysqli_query($conn, $dupSql);

    if($check && mysqli_num_rows($check) > 0){
        $dup = mysqli_fetch_assoc($check);
        $errors_dup = [];

        if($email !== '' && !empty($dup['Email'])) $errors_dup['email'] = 'Email already exists!';
        if($contact !== '' && !empty($dup['Contact_Number'])) $errors_dup['contact'] = 'Contact number already exists!';
        if($whatsapp !== '' && !empty($dup['WhatsApp_No'])) $errors_dup['whatsapp'] = 'WhatsApp number already exists!';
        if($birth_cert !== '' && !empty($dup['Birth_Certificate_No'])) $errors_dup['birth_cert'] = 'Birth certificate number already exists!';

        $_SESSION['form_errors'] = $errors_dup;
        $_SESSION['old_input'] = $old;
        header("Location: add_student_from.php");
        exit();
    }

    // Auto-generate Admission Number
    $admission_no = 'ADM'.rand(1000,9999);

    // Insert into DB
    $sql = "INSERT INTO Students 
        (Admission_No, Full_Name, DOB, Birth_Certificate_No, Gender, Religion, Guardian_Name, Address, Contact_Number, WhatsApp_No, Email) 
        VALUES ('$admission_no','$full_name','$dob','$birth_cert','$gender','$religion','$guardian','$address','$contact','$whatsapp','$email')";

    if($conn->query($sql) === TRUE){
        unset($_SESSION['form_errors'], $_SESSION['old_input']);
        header("Location: add_student_from.php?success=$admission_no");
        exit();
    } else {
        $_SESSION['form_errors'] = ['general' => 'Error: '.$conn->error];
        $_SESSION['old_input'] = $old;
        header("Location: add_student_from.php");
        exit();
    }

} else {
    header("Location: add_student_from.php");
    exit();
}
?>
