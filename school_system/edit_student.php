<?php
session_start();
include 'db_connect.php';

// Only Principal and Staff
if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: login.html");
    exit();
}

if(!isset($_GET['id'])){
    header("Location: view_students.php");
    exit();
}

$student_id = (int)$_GET['id'];

// Fetch student
$result = $conn->query("SELECT * FROM Students WHERE Student_ID='$student_id'");
if($result->num_rows != 1){
    header("Location: view_students.php");
    exit();
}
$student = $result->fetch_assoc();

$errors = [];
$old = $student;

// Handle Update
if(isset($_POST['update'])){

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

    // Required validations
    if($full_name === '') $errors['full_name'] = 'Full name is required.';
    if($dob === '') $errors['dob'] = 'Date of birth is required.';
    if($birth_cert === '') $errors['birth_cert'] = 'Birth certificate number is required.';
    if($gender === '') $errors['gender'] = 'Gender is required.';
    if($religion === '') $errors['religion'] = 'Religion is required.';
    if($guardian === '') $errors['guardian'] = 'Guardian name is required.';
    if($address === '') $errors['address'] = 'Address is required.';
    if($contact === '') $errors['contact'] = 'Contact number is required.';
    if($whatsapp === '') $errors['whatsapp'] = 'WhatsApp number is required.';
    if($email === '') $errors['email'] = 'Email is required.';

    // DOB validation
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

    // Contact validations
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

    // Duplicate checks (ignore current student)
    $birthCertEsc = mysqli_real_escape_string($conn, $birth_cert);
    $dupBC = mysqli_query($conn, "SELECT 1 FROM Students WHERE Birth_Certificate_No='$birthCertEsc' AND Student_ID!='$student_id' LIMIT 1");
    if($dupBC && mysqli_num_rows($dupBC) > 0){
        $errors['birth_cert'] = 'Birth certificate number already exists!';
    }

    $emailEsc = mysqli_real_escape_string($conn, $email);
    $contactEsc = mysqli_real_escape_string($conn, $contact);
    $whatsappEsc = mysqli_real_escape_string($conn, $whatsapp);
    $dupContact = mysqli_query($conn, "SELECT 1 FROM Students WHERE (Email='$emailEsc' OR Contact_Number='$contactEsc' OR WhatsApp_No='$whatsappEsc') AND Student_ID!='$student_id' LIMIT 1");
    if($dupContact && mysqli_num_rows($dupContact) > 0){
        if(mysqli_fetch_assoc($dupContact)) {
            // Assign error to the exact field
            $dupCheck = mysqli_query($conn, "SELECT * FROM Students WHERE (Email='$emailEsc' OR Contact_Number='$contactEsc' OR WhatsApp_No='$whatsappEsc') AND Student_ID!='$student_id' LIMIT 1");
            $dupRow = mysqli_fetch_assoc($dupCheck);
            if($dupRow['Email']==$email) $errors['email'] = 'Email already exists!';
            if($dupRow['Contact_Number']==$contact) $errors['contact'] = 'Contact number already exists!';
            if($dupRow['WhatsApp_No']==$whatsapp) $errors['whatsapp'] = 'WhatsApp number already exists!';
        }
    }

    if(empty($errors)){
        $sql = "UPDATE Students SET 
            Full_Name='".mysqli_real_escape_string($conn,$full_name)."',
            DOB='$dob',
            Birth_Certificate_No='".mysqli_real_escape_string($conn,$birth_cert)."',
            Gender='".mysqli_real_escape_string($conn,$gender)."',
            Religion='".mysqli_real_escape_string($conn,$religion)."',
            Guardian_Name='".mysqli_real_escape_string($conn,$guardian)."',
            Address='".mysqli_real_escape_string($conn,$address)."',
            Contact_Number='$contact',
            WhatsApp_No='$whatsapp',
            Email='".mysqli_real_escape_string($conn,$email)."'
            WHERE Student_ID='$student_id'";

        if($conn->query($sql)){
            header("Location: view_students.php?success=updated");
            exit();
        } else {
            $errors['general'] = "Database error: ".$conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Student</title>
<link rel="stylesheet" href="header_style.css">
<style>

.main-content{
    margin-top:120px;
    padding:20px;
    max-width:700px;
    margin:auto;
}

form{
    display:flex;
    flex-direction:column;
    gap:15px;
    margin-top:150px
}

label{
    font-weight:bold;
    margin-bottom:10px; 
    display:block;
}

input, select{
    padding:8px;
    border:1px solid #ccc;
    border-radius:4px;
    width:100%;
}

.input-error{
    border-color:#dc3545;
    box-shadow:0 0 0 3px rgba(220,53,69,0.15);
}

.field-error{
    color:#dc3545;
    font-size:13px;
    margin-top:4px;
}

.button-group{
    display:flex;
    justify-content:center;
    gap:15px;
    margin-top:10px;
}

.primary-btn{
    padding:10px 18px;
    background:#007bff;
    color:#fff;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

.primary-btn:hover{
    background:#0056b3;
}

.cancel-btn{
    padding:10px 18px;
    background:#6c757d;
    color:#fff;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

.cancel-btn:hover{
    background:#5a6268;
}

</style>
</head>

<body>

<?php $pageTitle="Edit Student"; ?>
<div class="header-bar">
    <div class="header-left"><?php include 'sidebar.php'; ?></div>
    <div class="header-title"><?php echo $pageTitle; ?></div>
    <div class="header-right"><img src="newschool.png"></div>
</div>

<div class="main-content">

<?php if(isset($errors['general'])): ?>
<div class="field-error"><?php echo $errors['general']; ?></div>
<?php endif; ?>

<form method="post">

<div>
<label>Full Name</label>
<input type="text" name="full_name"
value="<?php echo htmlspecialchars($old['full_name'] ?? $student['Full_Name']); ?>"
class="<?php echo isset($errors['full_name'])?'input-error':''; ?>">
<?php if(isset($errors['full_name'])): ?>
<div class="field-error"><?php echo $errors['full_name']; ?></div>
<?php endif; ?>
</div>

<div>
<label>Date of Birth</label>
<input type="date" name="dob"
value="<?php echo htmlspecialchars($old['dob'] ?? $student['DOB']); ?>"
class="<?php echo isset($errors['dob'])?'input-error':''; ?>">
<?php if(isset($errors['dob'])): ?>
<div class="field-error"><?php echo $errors['dob']; ?></div>
<?php endif; ?>
</div>

<div>
<label>Birth Certificate No</label>
<input type="text" name="birth_cert"
value="<?php echo htmlspecialchars($old['birth_cert'] ?? $student['Birth_Certificate_No']); ?>"
class="<?php echo isset($errors['birth_cert'])?'input-error':''; ?>">
<?php if(isset($errors['birth_cert'])): ?>
<div class="field-error"><?php echo $errors['birth_cert']; ?></div>
<?php endif; ?>
</div>

<div>
<label>Gender</label>
<select name="gender" class="<?php echo isset($errors['gender'])?'input-error':''; ?>">
<option value="">-- Select --</option>
<option value="Male" <?php echo (($old['gender'] ?? $student['Gender'])=='Male')?'selected':''; ?>>Male</option>
<option value="Female" <?php echo (($old['gender'] ?? $student['Gender'])=='Female')?'selected':''; ?>>Female</option>
</select>
<?php if(isset($errors['gender'])): ?>
<div class="field-error"><?php echo $errors['gender']; ?></div>
<?php endif; ?>
</div>

<div>
<label>Religion</label>
<select name="religion" class="<?php echo isset($errors['religion'])?'input-error':''; ?>">
<option value="">-- Select --</option>
<option value="Hinduism" <?php echo (($old['religion'] ?? $student['Religion'])=='Hinduism')?'selected':''; ?>>Hinduism</option>
<option value="Islam" <?php echo (($old['religion'] ?? $student['Religion'])=='Islam')?'selected':''; ?>>Islam</option>
<option value="Christianity" <?php echo (($old['religion'] ?? $student['Religion'])=='Christianity')?'selected':''; ?>>Christianity</option>
<option value="Buddhism" <?php echo (($old['religion'] ?? $student['Religion'])=='Buddhism')?'selected':''; ?>>Buddhism</option>
</select>
<?php if(isset($errors['religion'])): ?>
<div class="field-error"><?php echo $errors['religion']; ?></div>
<?php endif; ?>
</div>

<div>
<label>Guardian Name</label>
<input type="text" name="guardian"
value="<?php echo htmlspecialchars($old['guardian'] ?? $student['Guardian_Name']); ?>"
class="<?php echo isset($errors['guardian'])?'input-error':''; ?>">
<?php if(isset($errors['guardian'])): ?>
<div class="field-error"><?php echo $errors['guardian']; ?></div>
<?php endif; ?>
</div>

<div>
<label>Address</label>
<input type="text" name="address"
value="<?php echo htmlspecialchars($old['address'] ?? $student['Address']); ?>"
class="<?php echo isset($errors['address'])?'input-error':''; ?>">
<?php if(isset($errors['address'])): ?>
<div class="field-error"><?php echo $errors['address']; ?></div>
<?php endif; ?>
</div>

<div>
<label>Contact Number</label>
<input type="text" name="contact"
value="<?php echo htmlspecialchars($old['contact'] ?? $student['Contact_Number']); ?>"
class="<?php echo isset($errors['contact'])?'input-error':''; ?>">
<?php if(isset($errors['contact'])): ?>
<div class="field-error"><?php echo $errors['contact']; ?></div>
<?php endif; ?>
</div>

<div>
<label>WhatsApp Number</label>
<input type="text" name="whatsapp"
value="<?php echo htmlspecialchars($old['whatsapp'] ?? $student['WhatsApp_No']); ?>"
class="<?php echo isset($errors['whatsapp'])?'input-error':''; ?>">
<?php if(isset($errors['whatsapp'])): ?>
<div class="field-error"><?php echo $errors['whatsapp']; ?></div>
<?php endif; ?>
</div>

<div>
<label>Email</label>
<input type="email" name="email"
value="<?php echo htmlspecialchars($old['email'] ?? $student['Email']); ?>"
class="<?php echo isset($errors['email'])?'input-error':''; ?>">
<?php if(isset($errors['email'])): ?>
<div class="field-error"><?php echo $errors['email']; ?></div>
<?php endif; ?>
</div>

<div class="button-group">
<button type="submit" name="update" class="primary-btn">Update</button>
<button type="button" class="cancel-btn" onclick="window.location.href='view_students.php'">Cancel</button>
</div>

</form>
</div>
</body>
</html>
