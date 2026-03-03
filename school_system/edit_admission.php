<?php
session_start();
include 'db_connect.php';

if(!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Principal','Staff'])){
    header("Location: login.html");
    exit();
}

if(!isset($_GET['edit'])){
    header("Location: view_admissions.php");
    exit();
}

$edit_id = (int)$_GET['edit'];
$edit_result = $conn->query("SELECT * FROM Admissions WHERE Admission_ID='$edit_id'");
if($edit_result->num_rows == 0){
    die("Error: Admission not found!");
}
$edit_data = $edit_result->fetch_assoc();

$students_result = $conn->query("SELECT Student_ID, Full_Name FROM Students ORDER BY Full_Name ASC");

if(isset($_POST['update_admission'])){

    $admission_id = $_POST['admission_id'];
    $student_id = $_POST['student_id'];
    $course = trim($_POST['course']);
    $academic_year = trim($_POST['academic_year']);
    $admission_date = $_POST['admission_date'];
    $date_of_leaving = $_POST['date_of_leaving'] ?? '';
    $reason = trim($_POST['reason_for_leaving'] ?? '');
    $dropout_last_date = $_POST['dropout_last_date'] ?? '';

    if($date_of_leaving !== ''){
        if($reason === '' || $dropout_last_date === ''){
            die("Error: Reason and Dropout Last Date required.");
        }

        $ad = new DateTime($admission_date);
        $dl = new DateTime($date_of_leaving);
        $today = new DateTime('today');
        $drop = new DateTime($dropout_last_date);

        if($dl <= $ad) die("Error: Leaving date must be after Admission date.");
        if($dl > $today) die("Error: Leaving date cannot be future.");
        if($drop < $ad || $drop > $dl) die("Error: Dropout date invalid.");
    } else {
        $reason = null;
        $dropout_last_date = null;
    }

    $date_of_leaving_sql = $date_of_leaving !== '' ? "'$date_of_leaving'" : "NULL";
    $reason_sql = !empty($reason) ? "'".mysqli_real_escape_string($conn,$reason)."'" : "NULL";
    $dropout_last_date_sql = !empty($dropout_last_date) ? "'$dropout_last_date'" : "NULL";

    $sql = "UPDATE Admissions SET
                Student_ID='$student_id',
                Course='".mysqli_real_escape_string($conn,$course)."',
                Academic_Year='".mysqli_real_escape_string($conn,$academic_year)."',
                Admission_Date='$admission_date',
                Date_of_Leaving=$date_of_leaving_sql,
                Reason_for_Leaving=$reason_sql,
                Dropout_Last_Date=$dropout_last_date_sql
            WHERE Admission_ID='$admission_id'";

    if($conn->query($sql)){
        header("Location: view_admissions.php?success=updated");
        exit();
    } else {
        die("Error: ".$conn->error);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Admission</title>
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
    margin-top:150px;
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
    box-sizing:border-box;
}

.input-error{
    border:2px solid #dc3545 !important;
}

.error-message{
    color:#dc3545;
    font-size:13px;
    margin-top:4px;
}

.button-group{
    display:flex;
    justify-content:center;
    gap:15px;
    margin-top:20px;
}

.btn-primary{
    background:#007bff;
    color:#fff;
    border:none;
    padding:8px 18px;
    border-radius:6px;
    cursor:pointer;
}

.btn-secondary{
    background:#6c757d;
    color:#fff;
    border:none;
    padding:8px 18px;
    border-radius:6px;
    cursor:pointer;
}

.btn-primary:hover{ background:#0056b3; }
.btn-secondary:hover{ background:#545b62; }

</style>
</head>
<body>

<?php $pageTitle="Edit Admission"; ?>
<div class="header-bar">
    <div class="header-left"><?php include 'sidebar.php'; ?></div>
    <div class="header-title"><?php echo $pageTitle; ?></div>
    <div class="header-right"><img src="newschool.png" alt="School Logo"></div>
</div>

<div class="main-content">

<form method="post" id="editAdmissionForm">

<input type="hidden" name="admission_id" value="<?php echo $edit_data['Admission_ID']; ?>">

<div>
<label>Student</label>
<select name="student_id" required>
<?php
while($student=$students_result->fetch_assoc()){
$selected=($student['Student_ID']==$edit_data['Student_ID'])?"selected":"";
echo "<option value='{$student['Student_ID']}' $selected>
STU{$student['Student_ID']} - {$student['Full_Name']}
</option>";
}
?>
</select>
</div>

<div>
<label>Course</label>
<select name="course" required>
<?php
$courses=['Engineering Technology','Bio System Technology','Arts','Commerce','Science and Maths'];
foreach($courses as $c){
$selected=($edit_data['Course']==$c)?"selected":"";
echo "<option value='$c' $selected>$c</option>";
}
?>
</select>
</div>

<div>
<label>Academic Year</label>
<select name="academic_year" required>
<option value="1" <?php if($edit_data['Academic_Year']=='1') echo 'selected'; ?>>1</option>
<option value="2" <?php if($edit_data['Academic_Year']=='2') echo 'selected'; ?>>2</option>
<option value="3" <?php if($edit_data['Academic_Year']=='3') echo 'selected'; ?>>3</option>
</select>
</div>

<div>
<label>Admission Date</label>
<input type="date" name="admission_date" value="<?php echo $edit_data['Admission_Date']; ?>" required>
</div>

<div>
<label>Date of Leaving</label>
<input type="date" name="date_of_leaving" id="date_of_leaving" value="<?php echo $edit_data['Date_of_Leaving']; ?>">
</div>

<div>
<label>Reason for Leaving</label>
<input type="text" name="reason_for_leaving" id="reason_for_leaving" value="<?php echo $edit_data['Reason_for_Leaving']; ?>">
</div>

<div>
<label>Dropout Last Date</label>
<input type="date" name="dropout_last_date" id="dropout_last_date" value="<?php echo $edit_data['Dropout_Last_Date']; ?>">
</div>

<div class="button-group">
<button type="submit" name="update_admission" class="btn-primary">Update </button>
<button type="button" class="btn-secondary" onclick="window.location.href='view_admissions.php'">Cancel</button>
</div>

</form>
</div>

<script>
document.addEventListener("DOMContentLoaded",function(){

const admissionDate=document.querySelector("input[name='admission_date']");
const leavingDate=document.querySelector("input[name='date_of_leaving']");
const reason=document.querySelector("input[name='reason_for_leaving']");
const dropoutDate=document.querySelector("input[name='dropout_last_date']");
const form=document.getElementById("editAdmissionForm");

function showError(input,message){
removeError(input);
input.classList.add("input-error");
const error=document.createElement("div");
error.className="error-message";
error.innerText=message;
input.parentNode.appendChild(error);
}

function removeError(input){
input.classList.remove("input-error");
const old=input.parentNode.querySelector(".error-message");
if(old) old.remove();
}

function clearErrors(){
document.querySelectorAll(".error-message").forEach(e=>e.remove());
document.querySelectorAll(".input-error").forEach(e=>e.classList.remove("input-error"));
}

function toggleFields(){
if(leavingDate.value===""){
reason.value="";
dropoutDate.value="";
reason.disabled=true;
dropoutDate.disabled=true;
}else{
reason.disabled=false;
dropoutDate.disabled=false;
}
}

toggleFields();
leavingDate.addEventListener("change",toggleFields);

form.addEventListener("submit",function(e){

clearErrors();
let valid=true;

const ad=new Date(admissionDate.value);
const dl=leavingDate.value?new Date(leavingDate.value):null;
const drop=dropoutDate.value?new Date(dropoutDate.value):null;
const today=new Date();
today.setHours(0,0,0,0);

if(dl){

if(!reason.value){
showError(reason,"Reason is required.");
valid=false;
}

if(!dropoutDate.value){
showError(dropoutDate,"Dropout Last Date is required.");
valid=false;
}

if(dl<=ad){
showError(leavingDate,"Leaving date must be after Admission Date.");
valid=false;
}

if(dl>today){
showError(leavingDate,"Leaving date cannot be in the future.");
valid=false;
}

if(drop && (drop<ad || drop>dl)){
showError(dropoutDate,"Dropout date must be between Admission and Leaving dates.");
valid=false;
}

}

if(!valid) e.preventDefault();

});

});
</script>

</body>
</html>
