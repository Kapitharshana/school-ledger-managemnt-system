<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
}

/* Hamburger button */
.menu-btn {
    font-size: 20px;
    cursor: pointer;
    padding: 10px 15px;
    /*position: fixed;
    /*background-color: #333;*/
    color: black;
    border: none;
    z-index: 1001;
   
    padding: 10px;
    border: none;
    border-radius: 5px;
    background-color:#fff; 
   /* color: #fff;
    font-size: 16px;
    cursor: pointer;
    width: 150px;
    align-self: center;*/
    transition: background-color 0.3s;
}

.menu-btn:hover {
    background-color:  #0b5ed7;
} 

.inside-btn{
    align-self: center;
    margin-bottom: 24px;
    width: 50px;
}

/* Sidebar (hidden by default) */
.sidebar {
    height: 100vh;
    width: 0;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #0f172a;
    overflow-x: hidden;
    transition: 0.3s;
    padding-top: 60px;
    display: flex;
    flex-direction: column;
    z-index: 2000;
    
}

.sidebar.active{
    width: 220px;
}

.inside-btn{
    
    align-self: center;
    margin-bottom: 30px;
}
/* Sidebar buttons */
.sidebar button {

    background-color: transparent;
    color:  #e5e7eb;
    border:  1px solid rgba(148, 163, 184, 0.6);
    border-radius: 6px;
    padding: 10px 16px;
    text-align: center;
    cursor: pointer;
    margin: 8px 16px;
    font-size: 15px;
    display: block;
    width: calc(100% - 32px);
    

}

.sidebar button:hover {
    background-color:#1d4ed8;
    border-color: #1d4ed8;
    color: #ffffff;
}

/* Main content */
.main-content {
    padding: 20px;
}

/* Shift content when sidebar active */
.main-content.shift {
    margin-left: 220px;
}
</style>

<button id="headerBtn" class="menu-btn " onclick="toggleSidebar()">☰</button>

<!-- Sidebar -->
<div id="mySidebar" class="sidebar">

<!-- Hamburger Icon -->
<button id = "insideBtn" class="menu-btn inside-btn" onclick="toggleSidebar()">☰</button>


<?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'Principal') { ?>
    <button onclick="location.href='/school_system/principal_dashboard.php'">Dashboard</button>
    <button onclick="location.href='/school_system/staffs/manage_staff.php'">Users</button>
<?php } else { ?>
    <button onclick="location.href='/school_system/staff_dashboard.php'">Dashboard</button>
<?php } ?>

    
    <button onclick="location.href='/school_system/view_students.php'">Students</button>
    <button onclick="location.href='/school_system/view_admissions.php'">Admissions</button>
    <button onclick="location.href='/school_system/payment/view_payments.php'">Payments</button>
    <button onclick="location.href='/school_system/reports.php'">Reports</button>
    <button onclick="location.href='/school_system/logout.php'">Logout</button>
</div>

<script>
function toggleSidebar() {
    var sidebar = document.getElementById("mySidebar");
    var headerBtn = document.getElementById("headerBtn");

    sidebar.classList.toggle("active");

    if (sidebar.classList.contains("active")) {
        headerBtn.style.display = "none";
    } else {
        headerBtn.style.display = "block";
    }
}
</script>
