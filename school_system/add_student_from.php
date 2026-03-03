<?php
session_start();

// Only Principal and Staff can access
if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: login.html");
    exit();
}

$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['old_input']);

function old_value($old, $key) {
    return htmlspecialchars($old[$key] ?? '', ENT_QUOTES, 'UTF-8');
}

function field_error($errors, $key) {
    return isset($errors[$key]) ? htmlspecialchars($errors[$key], ENT_QUOTES, 'UTF-8') : '';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="header_style.css">
    <style>
/* Main content below header */
.main-content {
    margin-top: 120px; /* leave space for fixed header */
    padding: 20px;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
    
}

/* Page heading */
h2 {
    text-align: center;
    margin-bottom: 20px;
}

/* Success message */
.success-msg {
    color: green;
    text-align: center;
    margin-bottom: 20px;
}

/* Error message */
.general-error {
    color: #dc3545;
    text-align: center;
    margin-bottom: 20px;
}

/* Form styling */
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

input {
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
    width: 100%;
    box-sizing: border-box; 
}

select.input-error,
input.input-error {
    border-color: #dc3545;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15);
    outline: none;
}

.field-error {
    color: #dc3545;
    font-size: 13px;
    margin-top: 6px;
}

/* Submit button */
button {
    padding: 10px;
    border: none;
    border-radius: 5px;
    background-color: #007bff; /* Blue */
    color: #fff;
    font-size: 16px;
    cursor: pointer;
   /* width: 150px;*/
    align-self: center;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #0056b3;
}
/* Make select look like input */
select {
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
    width: 100%;
}

/* Radio group styling */
.radio-group {
    display: flex;
    gap: 20px;
    align-items: center;
    padding: 8px 0;
}

.radio-option input {
    width: auto;
    margin-right: 5px;
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

.button-group {
    display: flex;
    justify-content: center;
    gap: 15px;   /* space between buttons */
}

</style>

</head>
<body>
<?php $pageTitle = "ADD STUDENT"; ?>

<!-- Header Bar -->
<div class="header-bar">
    <div class="header-left">
        <?php include 'sidebar.php'; ?>
    </div>

    <div class="header-title">
        <?php 
            if (isset($pageTitle)) {
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
    if(isset($_GET['success'])){
        echo "<p class='success-msg'>Student added successfully! Admission No: ".$_GET['success']."</p>";
    }
    ?>

    <?php if(isset($errors['general'])): ?>
        <p class="general-error"><?php echo htmlspecialchars($errors['general']); ?></p>
    <?php endif; ?>

    <form method="post" action="add_student.php" id="addStudentForm" novalidate>
        <div>
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" required value="<?php echo old_value($old,'full_name'); ?>" class="<?php echo field_error($errors,'full_name') ? 'input-error' : ''; ?>">
            <?php if(field_error($errors,'full_name')): ?>
                <div class="field-error"><?php echo field_error($errors,'full_name'); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="dob">Date of Birth</label>
            <input type="date" name="dob" id="dob" required value="<?php echo old_value($old,'dob'); ?>" class="<?php echo field_error($errors,'dob') ? 'input-error' : ''; ?>">
            <?php if(field_error($errors,'dob')): ?>
                <div class="field-error" id="dobError"><?php echo field_error($errors,'dob'); ?></div>
            <?php else: ?>
                <div class="field-error" id="dobError" style="display:none;"></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="birth_cert">Birth Certificate No</label>
            <input type="text" name="birth_cert" id="birth_cert" required value="<?php echo old_value($old,'birth_cert'); ?>" class="<?php echo field_error($errors,'birth_cert') ? 'input-error' : ''; ?>">
            <?php if(field_error($errors,'birth_cert')): ?>
                <div class="field-error"><?php echo field_error($errors,'birth_cert'); ?></div>
            <?php endif; ?>
        </div>

        
        <div>
    <label>Gender</label>
    <div class="radio-group">
        <label class="radio-option">
            <input type="radio" name="gender" value="Male" required <?php echo (($old['gender'] ?? '') === 'Male') ? 'checked' : ''; ?>> Male
        </label>
        <label class="radio-option">
            <input type="radio" name="gender" value="Female" required <?php echo (($old['gender'] ?? '') === 'Female') ? 'checked' : ''; ?>> Female
        </label>
    </div>
    <?php if(field_error($errors,'gender')): ?>
        <div class="field-error"><?php echo field_error($errors,'gender'); ?></div>
    <?php endif; ?>
</div>

        
        <div>
    <label for="religion">Religion</label>
    <select name="religion" id="religion" required class="<?php echo field_error($errors,'religion') ? 'input-error' : ''; ?>">
        <option value="">-- Select Religion --</option>
        <option value="Hinduism" <?php echo (($old['religion'] ?? '') === 'Hinduism') ? 'selected' : ''; ?>>Hinduism</option>
        <option value="Islam" <?php echo (($old['religion'] ?? '') === 'Islam') ? 'selected' : ''; ?>>Islam</option>
        <option value="Christianity" <?php echo (($old['religion'] ?? '') === 'Christianity') ? 'selected' : ''; ?>>Christianity</option>
        <option value="Buddhism" <?php echo (($old['religion'] ?? '') === 'Buddhism') ? 'selected' : ''; ?>>Buddhism</option>
    </select>
    <?php if(field_error($errors,'religion')): ?>
        <div class="field-error"><?php echo field_error($errors,'religion'); ?></div>
    <?php endif; ?>
</div>


<div>
    <label for="guardian">Guardian Name</label>
    <input type="text" name="guardian" id="guardian" required
        value="<?php echo old_value($old,'guardian'); ?>"
        class="<?php echo field_error($errors,'guardian') ? 'input-error' : ''; ?>">
    <?php if(field_error($errors,'guardian')): ?>
        <div class="field-error"><?php echo field_error($errors,'guardian'); ?></div>
    <?php endif; ?>
</div>

<div>
    <label for="address">Address</label>
    <input type="text" name="address" id="address" required
        value="<?php echo old_value($old,'address'); ?>"
        class="<?php echo field_error($errors,'address') ? 'input-error' : ''; ?>">
    <?php if(field_error($errors,'address')): ?>
        <div class="field-error"><?php echo field_error($errors,'address'); ?></div>
    <?php endif; ?>
</div>

        <div>
            <label for="contact">Contact Number</label>
            <input type="text" name="contact" id="contact" pattern="[0-9]{10}" maxlength="10" required value="<?php echo old_value($old,'contact'); ?>" class="<?php echo field_error($errors,'contact') ? 'input-error' : ''; ?>">
            <?php if(field_error($errors,'contact')): ?>
                <div class="field-error"><?php echo field_error($errors,'contact'); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="whatsapp">WhatsApp Number</label>
            <input type="text" name="whatsapp" id="whatsapp" pattern="[0-9]{10}" maxlength="10" required value="<?php echo old_value($old,'whatsapp'); ?>" class="<?php echo field_error($errors,'whatsapp') ? 'input-error' : ''; ?>">
            <?php if(field_error($errors,'whatsapp')): ?>
                <div class="field-error"><?php echo field_error($errors,'whatsapp'); ?></div>
            <?php endif; ?>
        </div>

        <div>
    <label for="email">Email</label>
    <input type="email" name="email" id="email" required
        value="<?php echo old_value($old,'email'); ?>"
        class="<?php echo field_error($errors,'email') ? 'input-error' : ''; ?>">
    <?php if(field_error($errors,'email')): ?>
        <div class="field-error"><?php echo field_error($errors,'email'); ?></div>
    <?php endif; ?>
</div>

        <div class="button-group">
        <button type="submit" name="submit"  >Add Student</button>
        <button type="button" class="cancel-btn" onclick="window.location.href='view_students.php'">Cancel</button>
        </div>
    
    </form>
</div>

<script>
function calculateAge(dobStr) {
    const dob = new Date(dobStr);
    if (Number.isNaN(dob.getTime())) return null;

    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    return age;
}

function setFieldError(input, message, errorEl) {
    input.classList.add('input-error');
    if (errorEl) {
        errorEl.textContent = message;
        errorEl.style.display = 'block';
    }
}

function clearFieldError(input, errorEl) {
    input.classList.remove('input-error');
    if (errorEl) {
        errorEl.textContent = '';
        errorEl.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addStudentForm');
    const dobInput = document.getElementById('dob');
    const dobError = document.getElementById('dobError');

    function validateDob() {
        clearFieldError(dobInput, dobError);
        if (!dobInput.value) return true;

        const age = calculateAge(dobInput.value);
        if (age === null) {
            setFieldError(dobInput, 'Invalid date of birth.', dobError);
            return false;
        }
        if (age < 15 || age > 20) {
            setFieldError(dobInput, 'Student age must be between 15 and 20 years.', dobError);
            return false;
        }
        return true;
    }

    dobInput.addEventListener('change', validateDob);
    dobInput.addEventListener('input', validateDob);

    form.addEventListener('submit', (e) => {
        let ok = true;

        // Required fields + browser validity (pattern/email/etc)
        const fields = form.querySelectorAll('input, select');
        fields.forEach((el) => {
            if (el.type === 'radio') return;
            el.classList.remove('input-error');
        });

        // DOB age rule
        if (!validateDob()) ok = false;

        // Highlight other invalid fields (required/pattern/email) without leaving page
       // const check = ['full_name','birth_cert','contact','whatsapp','email','religion'];
       const check = [  'full_name',
    'birth_cert',
    'contact',
    'whatsapp',
    'email',
    'religion',
    'guardian',
    'address'
];
       check.forEach((id) => {
            const el = document.getElementById(id);
            if (!el) return;
            if (!el.checkValidity()) {
                el.classList.add('input-error');
                ok = false;
            }
        });

        if (!ok) {
            e.preventDefault();
        }
    });
});
</script>

</body>
</html>
