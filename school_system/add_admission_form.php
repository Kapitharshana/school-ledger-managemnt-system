<?php
session_start();

// Only Principal and Staff can access
if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: login.html");
    exit();
}

$errors = $_SESSION['admission_form_errors'] ?? [];
$old = $_SESSION['admission_old_input'] ?? [];
unset($_SESSION['admission_form_errors'], $_SESSION['admission_old_input']);

function adm_old_value($old, $key) {
    return htmlspecialchars($old[$key] ?? '', ENT_QUOTES, 'UTF-8');
}

function adm_field_error($errors, $key) {
    return isset($errors[$key]) ? htmlspecialchars($errors[$key], ENT_QUOTES, 'UTF-8') : '';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Admission</title>
    <link rel="stylesheet" href="header_style.css">
    <style>
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

        .general-error {
            color: #dc3545;
            text-align: center;
            margin-bottom: 20px;
        }

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

        input, select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
            box-sizing: border-box;
        }

        input.input-error,
        select.input-error {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15);
            outline: none;
        }

        .field-error {
            color: #dc3545;
            font-size: 13px;
            margin-top: 6px;
        }

        button {
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

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<?php $pageTitle = "Add Admission"; ?>

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
    

    <?php if(isset($errors['general'])): ?>
        <p class="general-error"><?php echo htmlspecialchars($errors['general']); ?></p>
    <?php endif; ?>

    <form method="post" action="add_admission.php" id="addAdmissionForm" novalidate>
        <div>
            <label for="student_id">Student ID</label>
            <input
                type="number"
                name="student_id"
                id="student_id"
                required
                value="<?php echo adm_old_value($old, 'student_id'); ?>"
                class="<?php echo adm_field_error($errors,'student_id') ? 'input-error' : ''; ?>"
            >
            <?php if(adm_field_error($errors,'student_id')): ?>
                <div class="field-error"><?php echo adm_field_error($errors,'student_id'); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="course">Course</label>
            <select
                name="course"
                id="course"
                required
                class="<?php echo adm_field_error($errors,'course') ? 'input-error' : ''; ?>"
            >
                <option value="">-- Select Course --</option>
                <option value="Engineering Technology" <?php echo (($old['course'] ?? '') === 'Engineering Technology') ? 'selected' : ''; ?>>Engineering Technology</option>
                <option value="Bio System Technology" <?php echo (($old['course'] ?? '') === 'Bio System Technology') ? 'selected' : ''; ?>>Bio System Technology</option>
                <option value="Arts" <?php echo (($old['course'] ?? '') === 'Arts') ? 'selected' : ''; ?>>Arts</option>
                <option value="Commerce" <?php echo (($old['course'] ?? '') === 'Commerce') ? 'selected' : ''; ?>>Commerce</option>
                <option value="Science and Maths" <?php echo (($old['course'] ?? '') === 'Science and Maths') ? 'selected' : ''; ?>>Science and Maths</option>
            </select>
            <?php if(adm_field_error($errors,'course')): ?>
                <div class="field-error"><?php echo adm_field_error($errors,'course'); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="academic_year">Academic Year</label>
            <select
    name="academic_year"
    id="academic_year"
    required
    class="<?php echo adm_field_error($errors,'academic_year') ? 'input-error' : ''; ?>"
>
    <option value="">-- Select Year --</option>
    <option value="1" <?php echo (($old['academic_year'] ?? '') === '1') ? 'selected' : ''; ?>>1</option>
    <option value="2" <?php echo (($old['academic_year'] ?? '') === '2') ? 'selected' : ''; ?>>2</option>
    <option value="3" <?php echo (($old['academic_year'] ?? '') === '3') ? 'selected' : ''; ?>>3</option>
</select>

            <?php if(adm_field_error($errors,'academic_year')): ?>
                <div class="field-error"><?php echo adm_field_error($errors,'academic_year'); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="admission_date">Admission Date</label>
            <input
                type="date"
                name="admission_date"
                id="admission_date"
                required
                value="<?php echo adm_old_value($old, 'admission_date'); ?>"
                class="<?php echo adm_field_error($errors,'admission_date') ? 'input-error' : ''; ?>"
            >
            <?php if(adm_field_error($errors,'admission_date')): ?>
                <div class="field-error"><?php echo adm_field_error($errors,'admission_date'); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="date_of_leaving">Date of Leaving</label>
            <input
                type="date"
                name="date_of_leaving"
                id="date_of_leaving"
                value="<?php echo adm_old_value($old, 'date_of_leaving'); ?>"
                class="<?php echo adm_field_error($errors,'date_of_leaving') ? 'input-error' : ''; ?>"
            >
            <?php if(adm_field_error($errors,'date_of_leaving')): ?>
                <div class="field-error" id="leavingError"><?php echo adm_field_error($errors,'date_of_leaving'); ?></div>
            <?php else: ?>
                <div class="field-error" id="leavingError" style="display:none;"></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="reason_for_leaving">Reason for Leaving</label>
            <input
                type="text"
                name="reason_for_leaving"
                id="reason_for_leaving"
                value="<?php echo adm_old_value($old, 'reason_for_leaving'); ?>"
                class="<?php echo adm_field_error($errors,'reason_for_leaving') ? 'input-error' : ''; ?>"
            >
            
            
            <?php if(adm_field_error($errors,'reason_for_leaving')): ?>
                <div class="field-error" id="reasonError"><?php echo adm_field_error($errors,'reason_for_leaving'); ?></div>
            <?php else: ?>
                <div class="field-error" id="reasonError" style="display:none;"></div>
            <?php endif; ?>
        </div>


        <div>
            <label for="dropout_last_date">Dropout Last Attendance Date</label>
            <input
                type="date"
                name="dropout_last_date"
                id="dropout_last_date"
                value="<?php echo adm_old_value($old, 'dropout_last_date'); ?>"
                class="<?php echo adm_field_error($errors,'dropout_last_date') ? 'input-error' : ''; ?>"
            >
            <?php if(adm_field_error($errors,'dropout_last_date')): ?>
                <div class="field-error" id="dropoutError"><?php echo adm_field_error($errors,'dropout_last_date'); ?></div>
            <?php else: ?>
                <div class="field-error" id="dropoutError" style="display:none;"></div>
            <?php endif; ?>
        </div>

        <button type="submit" name="add_admission">Add Admission</button>
    </form>
</div>

<script>
function setFieldError(input, message, errorEl) {
    if (!input) return;
    input.classList.add('input-error');
    if (errorEl) {
        errorEl.textContent = message;
        errorEl.style.display = 'block';
    }
}

function clearFieldError(input, errorEl) {
    if (!input) return;
    input.classList.remove('input-error');
    if (errorEl) {
        errorEl.textContent = '';
        errorEl.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addAdmissionForm');
    const admissionDate = document.getElementById('admission_date');
    const leavingDate = document.getElementById('date_of_leaving');
    const reasonInput = document.getElementById('reason_for_leaving');
    const dropoutDate = document.getElementById('dropout_last_date');

    const leavingError = document.getElementById('leavingError');
    const reasonError = document.getElementById('reasonError');
    const dropoutError = document.getElementById('dropoutError');

    // Prevent future admission and leaving dates (client-side)
    const todayStr = new Date().toISOString().split('T')[0];
    if (admissionDate) {
        admissionDate.setAttribute('max', todayStr);
    }
    if (leavingDate) {
        leavingDate.setAttribute('max', todayStr);
    }

    function validateLeavingBlock() {

    clearFieldError(leavingDate, leavingError);
    clearFieldError(reasonInput, reasonError);
    clearFieldError(dropoutDate, dropoutError);

    let ok = true;

    // CASE 1: Date of Leaving NOT entered
    if (!leavingDate.value) {

        // If user fills Reason without Leaving Date
        if (reasonInput.value) {
            setFieldError(reasonInput, 'Enter Date of Leaving first.', reasonError);
            ok = false;
        }

        // If user fills Dropout Date without Leaving Date
        if (dropoutDate.value) {
            setFieldError(dropoutDate, 'Enter Date of Leaving first.', dropoutError);
            ok = false;
        }

        return ok;
    }

    // CASE 2: Date of Leaving entered → both fields required
    if (!reasonInput.value) {
        setFieldError(reasonInput, 'Reason is required when Date of Leaving is entered.', reasonError);
        ok = false;
    }

    if (!dropoutDate.value) {
        setFieldError(dropoutDate, 'Dropout last attendance date is required when Date of Leaving is entered.', dropoutError);
        ok = false;
    }

    // Validate date relationships
    if (admissionDate.value) {
        const ad = new Date(admissionDate.value);
        const dl = new Date(leavingDate.value);

        if (!isNaN(ad) && !isNaN(dl)) {

            // Leaving must be after Admission
            if (dl <= ad) {
                setFieldError(leavingDate, 'Date of leaving must be AFTER admission date.', leavingError);
                ok = false;
            }

            // Dropout date must be between Admission & Leaving
            if (dropoutDate.value) {
                const drop = new Date(dropoutDate.value);

                if (drop < ad || drop >= dl) {
                    setFieldError(
                        dropoutDate,
                        'Dropout last date must be BETWEEN Admission Date and Date of Leaving.',
                        dropoutError
                    );
                    ok = false;
                }
            }
        }
    }

    return ok;
}
function toggleLeavingFields() {
    const disabled = !leavingDate.value;
    reasonInput.disabled = disabled;
    dropoutDate.disabled = disabled;
}

toggleLeavingFields();
leavingDate.addEventListener('change', toggleLeavingFields);


    

    if (leavingDate) {
        leavingDate.addEventListener('change', validateLeavingBlock);
        leavingDate.addEventListener('input', validateLeavingBlock);
    }
    if (reasonInput) {
        reasonInput.addEventListener('input', validateLeavingBlock);
    }
    if (dropoutDate) {
        dropoutDate.addEventListener('change', validateLeavingBlock);
    }

    form.addEventListener('submit', (e) => {
        let ok = true;

        // reset basic errors
        ['student_id','course','academic_year','admission_date'].forEach((id) => {
            const el = document.getElementById(id);
            if (el) el.classList.remove('input-error');
        });

        // HTML required checks
        ['student_id','course','academic_year','admission_date'].forEach((id) => {
            const el = document.getElementById(id);
            if (el && !el.checkValidity()) {
                el.classList.add('input-error');
                ok = false;
            }
        });

        if (!validateLeavingBlock()) {
            ok = false;
        }

        if (!ok) {
            e.preventDefault();
        }
    });
});
</script>

</body>
</html>
