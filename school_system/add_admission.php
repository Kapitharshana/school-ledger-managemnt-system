<?php
session_start();
include 'db_connect.php';

// Only Principal and Staff can access
if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: login.html");
    exit();
}

if(isset($_POST['add_admission'])) {

    $errors = [];
    $old = $_POST;

    // Trim inputs
    $student_id = trim($_POST['student_id'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $academic_year = trim($_POST['academic_year'] ?? '');
    $admission_date = trim($_POST['admission_date'] ?? '');
    $date_of_leaving = trim($_POST['date_of_leaving'] ?? '');
    $reason = trim($_POST['reason_for_leaving'] ?? '');
    $dropout_last_date = trim($_POST['dropout_last_date'] ?? '');

    // ----------- VALIDATIONS -----------

    // Student ID
    if($student_id === ''){
        $errors['student_id'] = 'Student ID is required.';
    } elseif(!ctype_digit($student_id) || (int)$student_id <= 0){
        $errors['student_id'] = 'Student ID must be a positive number.';
    } else {
        $student_id_int = (int)$student_id;
        $check_student = $conn->query("SELECT 1 FROM Students WHERE Student_ID='$student_id_int' LIMIT 1");
        if(!$check_student || $check_student->num_rows == 0){
            $errors['student_id'] = 'Student does not exist.';
        } else {
            // Check duplicate admission
            $check_dup = $conn->query("SELECT 1 FROM Admissions WHERE Student_ID='$student_id_int'");
            if($check_dup && $check_dup->num_rows > 0){
                $errors['student_id'] = 'This student already has an admission.';
            }
        }
    }

    // Course
    $allowedCourses = ['Engineering Technology','Bio System Technology','Arts','Commerce','Science and Maths'];
    if($course === '' || !in_array($course, $allowedCourses, true)){
        $errors['course'] = 'Please select a valid course.';
    }

    // Academic year
    if($academic_year === '' || !in_array($academic_year, ['1','2','3'], true)){
        $errors['academic_year'] = 'Please select a valid academic year.';
    }

    // Admission date
    if($admission_date === ''){
        $errors['admission_date'] = 'Admission date is required.';
    } else {
        try {
            $ad = new DateTime($admission_date);
            $today = new DateTime('today');
            if($ad > $today){
                $errors['admission_date'] = 'Admission date cannot be in the future.';
            }
        } catch(Exception $e){
            $errors['admission_date'] = 'Invalid admission date.';
        }
    }

    // Date of leaving & reason/dropout
    if($date_of_leaving !== ''){
        if($reason === '') $errors['reason_for_leaving'] = 'Reason is required when Date of Leaving is entered.';
        if($dropout_last_date === '') $errors['dropout_last_date'] = 'Dropout last attendance date is required.';

        try {
            $dl = new DateTime($date_of_leaving);
            $today = new DateTime('today');
            $ad = new DateTime($admission_date);

            if($dl <= $ad) $errors['date_of_leaving'] = 'Date of leaving must be AFTER admission date.';
            if($dl > $today) $errors['date_of_leaving'] = 'Date of leaving cannot be in the future.';

            if($dropout_last_date !== ''){
                $drop = new DateTime($dropout_last_date);
                if($drop < $ad || $drop > $dl){
                    $errors['dropout_last_date'] = 'Dropout last date must be BETWEEN Admission Date and Date of Leaving.';
                }
            }
        } catch(Exception $e){
            $errors['date_of_leaving'] = 'Invalid date values.';
        }
    }

    // ----------- REDIRECT BACK IF ERRORS -----------
    if(!empty($errors)){
        $_SESSION['admission_form_errors'] = $errors;
        $_SESSION['admission_old_input'] = $old;
        header("Location: add_admission_form.php");
        exit();
    }

    // ----------- PREPARE INSERT -----------

    $student_id_int = (int)$student_id;
    $course_esc = mysqli_real_escape_string($conn, $course);
    $academic_year_esc = mysqli_real_escape_string($conn, $academic_year);
    $admission_date_esc = mysqli_real_escape_string($conn, $admission_date);

    $date_of_leaving_sql = $date_of_leaving !== '' ? "'".mysqli_real_escape_string($conn, $date_of_leaving)."'" : "NULL";
    $reason_sql = $reason !== '' ? "'".mysqli_real_escape_string($conn, $reason)."'" : "NULL";
    $dropout_last_date_sql = $dropout_last_date !== '' ? "'".mysqli_real_escape_string($conn, $dropout_last_date)."'" : "NULL";

    $sql = "INSERT INTO Admissions
            (Student_ID, Course, Academic_Year, Admission_Date, Date_of_Leaving, Reason_for_Leaving, Dropout_Last_Date)
            VALUES
            ('$student_id_int', '$course_esc', '$academic_year_esc', '$admission_date_esc', $date_of_leaving_sql, $reason_sql, $dropout_last_date_sql)";

    if($conn->query($sql)){
        unset($_SESSION['admission_form_errors'], $_SESSION['admission_old_input']);
        header("Location: view_admissions.php?success=added");
        exit();
    } else {
        $_SESSION['admission_form_errors'] = ['general' => 'Database Error: '.$conn->error];
        $_SESSION['admission_old_input'] = $old;
        header("Location: add_admission_form.php");
        exit();
    }

} else {
    // Direct access → go to form
    header("Location: add_admission_form.php");
    exit();
}
?>
