<?php
session_start();

if ($_SESSION["uidx"] == "" || $_SESSION["uidx"] == NULL) {
    header('Location:fuserLogin');
    exit;
}

include('../connection.php');

$name = $_SESSION["uname"];

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$month = date('m', strtotime($date));
$year  = date('Y', strtotime($date));

/* KPI */
$dailyIncome = mysqli_fetch_assoc(mysqli_query($link,
"SELECT SUM(amount) as t FROM payment WHERE date='$date'"))['t'] ?? 0;

$todayAttendance = mysqli_fetch_assoc(mysqli_query($link,
"SELECT COUNT(*) as t FROM attendance WHERE date='$date' AND status='Present'"))['t'] ?? 0;

$totalStudents = mysqli_fetch_assoc(mysqli_query($link,
"SELECT COUNT(*) as t FROM student"))['t'] ?? 0;

$monthlyIncome = mysqli_fetch_assoc(mysqli_query($link,
"SELECT SUM(amount) as t FROM payment WHERE month='$month' AND year='$year'"))['t'] ?? 0;

/* SUBJECTS */
$subjects = [];
$res = mysqli_query($link, "SELECT DISTINCT subject FROM classess");
while ($row = mysqli_fetch_assoc($res)) {
    $subjects[] = $row['subject'];
}

/* ATTENDANCE */
$attendanceData = [];
foreach ($subjects as $sub) {
    $attendanceData[$sub] = mysqli_fetch_assoc(mysqli_query($link,
    "SELECT COUNT(*) as t 
     FROM attendance a
     JOIN classess c ON a.class_id = c.class_id
     WHERE c.subject='$sub'
     AND a.status='Present'
     AND DATE(a.date) >= DATE_SUB('$date', INTERVAL 7 DAY)
    "))['t'] ?? 0;
}

/* INCOME */
$incomeData = [];
foreach ($subjects as $sub) {
    $incomeData[$sub] = mysqli_fetch_assoc(mysqli_query($link,
    "SELECT SUM(p.amount) as t
     FROM payment p
     JOIN classess c ON p.class_id = c.class_id
     WHERE c.subject='$sub'
     AND DATE(p.date) >= DATE_SUB('$date', INTERVAL 7 DAY)
    "))['t'] ?? 0;
}

include('allhead.php');
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* BODY */
body{
    background:#eef2f7;
}

/* SIDEBAR */
.sidebar{
    position:fixed;
    left:0;
    top:50px;
    width:240px;
    height:100%;
    background:#111827;
    padding-top:20px;
    transition:0.3s;
    z-index:1500;
    overflow-y:auto;
    overflow-x:hidden;
}

/* SIDEBAR LINKS */
.sidebar a{
    display:flex;
    align-items:center;
    gap:10px;
    padding:12px 20px;
    color:#cbd5e1;
    text-decoration:none;
    font-size:14px;
    cursor:pointer;
}

.sidebar a i{
    width:18px;
    text-align:center;
}

.sidebar a:hover{
    background:#4e73df;
    color:white;
}

/* MENU ITEM */
.menu-item{
    position:relative;
}

/* ARROW */
.menu-link .arrow{
    margin-left:auto;
    width:auto;
    transition:0.3s;
}

/* DROPDOWN */
.dropdown{
    display:none;
    background:#1f2937;
}

/* SHOW DROPDOWN */
.menu-item.active .dropdown{
    display:block;
}

/* DROPDOWN LINKS */
.dropdown a{
    padding:10px 15px 10px 50px;
    font-size:13px;
    color:#cbd5e1;
}

.dropdown a:hover{
    background:#374151;
    color:white;
}

/* ROTATE ARROW */
.menu-item.active .arrow{
    transform:rotate(180deg);
}

/* DESKTOP COLLAPSE */
.sidebar.collapsed{
    width:70px;
}

/* Hide menu text */
.sidebar.collapsed a span{
    display:none;
}

/* Center main icons */
.sidebar.collapsed a{
    justify-content:center;
}

/* Hide arrows when collapsed */
.sidebar.collapsed .arrow{
    display:none;
}

/* Hide dropdown */
.sidebar.collapsed .dropdown{
    display:none !important;
}

/* MAIN */
.main{
    margin-left:240px;
    padding:20px;
    transition:0.3s;
}

.main.expanded{
    margin-left:70px;
}

/* TOGGLE */
.sidebar-toggle{
    position:fixed;
    top:10px;
    left:10px;
    z-index:2000;
}

/* OVERLAY */
.sidebar-overlay{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.4);
    display:none;
    z-index:1400;
}

/* =========================
   GAP FIX (IMPORTANT)
========================= */

/* MORE GAP BELOW FILTER */
form{
    margin-bottom:30px;
}

/* MORE GAP BETWEEN KPI AND CHARTS */
.row.kpi-row{
    margin-bottom:35px;
}

/* CARDS */
.card{
    padding:18px;
    border-radius:14px;
    color:white;
    box-shadow:0 6px 18px rgba(0,0,0,0.15);
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.c1{background:linear-gradient(135deg,#4e73df,#224abe);}
.c2{background:linear-gradient(135deg,#1cc88a,#0f9d58);}
.c3{background:linear-gradient(135deg,#f6c23e,#e0a800);}
.c4{background:linear-gradient(135deg,#e74a3b,#c0392b);}

/* CHART */
.chart-box{
    background:white;
    padding:15px;
    border-radius:14px;
    box-shadow:0 6px 18px rgba(0,0,0,0.08);
    margin-top:10px;
}

/* DESKTOP CHART SIZE */
canvas{
    width:100% !important;
    height:360px !important;
}

/* MOBILE RESPONSIVE FIX */
@media(max-width:768px){

    .sidebar{
        left:-250px;
        width:240px;
    }

    .sidebar.active{
        left:0;
    }

    .main{
        margin-left:0;
        padding:12px;
    }

    /* SMALLER GAP ON MOBILE */
    form{
        margin-bottom:20px;
    }

    .chart-box{
        margin-bottom:25px;
    }

    canvas{
        height:280px !important;
    }

}

</style>


<!-- TOGGLE -->
<button class="sidebar-toggle btn btn-primary btn-sm">
    <i class="fa fa-bars"></i>
</button>


<!-- =========================
     SIDEBAR
========================= -->

<div class="sidebar">

    <!-- DASHBOARD -->
    <a href="welcome.php">
        <i class="fa fa-gauge"></i>
        <span>Dashboard</span>
    </a>


    <!-- ================= STUDENT ================= -->

    <div class="menu-item">

        <a class="menu-link">
            <i class="fa fa-user-graduate"></i>
            <span>Student</span>
            <i class="fa fa-chevron-down arrow"></i>
        </a>

        <div class="dropdown">

            <a href="addstudent.php">
                <i class="fa fa-user-plus"></i>
                <span>Add Student</span>
            </a>

            <a href="managestudent.php">
                <i class="fa fa-user-pen"></i>
                <span>Edit / Delete Student</span>
            </a>

            <a href="viewstudentdetails.php">
                <i class="fa fa-users"></i>
                <span>View Students</span>
            </a>

            <a href="exportstudent.php"> 
                <i class="fa fa-file-export"></i> 
                <span>Export Student Details</span> 
            </a>

        </div>

    </div>


    <!-- ================= CLASS ================= -->

    <div class="menu-item">

        <a class="menu-link">
            <i class="fa fa-chalkboard"></i>
            <span>Class</span>
            <i class="fa fa-chevron-down arrow"></i>
        </a>

        <div class="dropdown">

            <a href="addclass.php">
                <i class="fa fa-plus"></i>
                <span>Add Class</span>
            </a>

            <a href="manageclass.php">
                <i class="fa fa-pen-to-square"></i>
                <span>Edit / Delete Class</span>
            </a>

            <a href="viewclass.php">
                <i class="fa fa-list"></i>
                <span>View Class</span>
            </a>

        </div>

    </div>


    <!-- ================= ATTENDANCE ================= -->

    <div class="menu-item">

        <a class="menu-link">
            <i class="fa fa-clipboard-check"></i>
            <span>Attendence</span>
            <i class="fa fa-chevron-down arrow"></i>
        </a>

        <div class="dropdown">

            <a href="viewattendance.php">
                <i class="fa fa-eye"></i>
                <span>View Attendence</span>
            </a>

            <a href="printattendance.php">
                <i class="fa fa-print"></i>
                <span>Print Attendence</span>
            </a>

            <a href="manageattendance.php">
                <i class="fa fa-pen-to-square"></i>
                <span>Edit / Delete Attendence</span>
            </a>

        </div>

    </div>


    <!-- ================= TEACHERS ================= -->

    <div class="menu-item">

        <a class="menu-link">
            <i class="fa fa-chalkboard-user"></i>
            <span>Teachers</span>
            <i class="fa fa-chevron-down arrow"></i>
        </a>

        <div class="dropdown">

            <a href="addteacher.php">
                <i class="fa fa-user-plus"></i>
                <span>Add Teachers</span>
            </a>

            <a href="manageteacher.php">
                <i class="fa fa-user-pen"></i>
                <span>Edit/Delete Teachers</span>
            </a>

        </div>

    </div>


    <!-- ================= USERS ================= -->

    <div class="menu-item">

        <a class="menu-link">
            <i class="fa fa-users"></i>
            <span>Users</span>
            <i class="fa fa-chevron-down arrow"></i>
        </a>

        <div class="dropdown">

            <a href="adduser.php">
                <i class="fa fa-user-plus"></i>
                <span>Add User</span>
            </a>

            <a href="manageuser.php">
                <i class="fa fa-user-pen"></i>
                <span>Edit / Delete User</span>
            </a>

        </div>

    </div>


    <!-- ================= PAYMENT ================= -->

    <div class="menu-item">

        <a class="menu-link">
            <i class="fa fa-money-bill-wave"></i>
            <span>Payment</span>
            <i class="fa fa-chevron-down arrow"></i>
        </a>

        <div class="dropdown">

            <a href="payment.php">
                <i class="fa fa-eye"></i>
                <span>View Payment</span>
            </a>

            <a href="managepayment.php">
                <i class="fa fa-pen-to-square"></i>
                <span>Edit/Delete Payment</span>
            </a>

            <a href="printpayment.php">
                <i class="fa fa-print"></i>
                <span>Print Payment Summary</span>
            </a>

        </div>

    </div>


    <!-- ================= REPORTS ================= -->

    <div class="menu-item">

        <a class="menu-link">
            <i class="fa fa-chart-line"></i>
            <span>Reports</span>
            <i class="fa fa-chevron-down arrow"></i>
        </a>

        <div class="dropdown">

            <a href="viewreport.php">
                <i class="fa fa-eye"></i>
                <span>View Report</span>
            </a>

            <a href="generatereport.php">
                <i class="fa fa-file-circle-plus"></i>
                <span>Generate Reports</span>
            </a>

            <a href="managereport.php">
                <i class="fa fa-pen-to-square"></i>
                <span>Edit/Delete Reports</span>
            </a>

        </div>

    </div>


    <!-- ================= LOGOUT ================= -->

    <a href="../index.php">
        <i class="fa fa-right-from-bracket"></i>
        <span>Logout</span>
    </a>

</div>


<div class="sidebar-overlay"></div>


<!-- =========================
     MAIN
========================= -->

<div class="main">

<h3>Welcome, <b><?php echo $name; ?></b></h3>


<!-- FILTER -->

<form method="GET">

    <div class="row mb-3">

        <div class="col-md-4 col-sm-12">

            <input
                type="date"
                name="date"
                value="<?php echo $date; ?>"
                class="form-control">

        </div>

        <div class="col-md-2 col-sm-12 d-flex align-items-end">

            <button class="btn btn-primary w-100">

                <i class="fa fa-filter"></i> Filter

            </button>

        </div>

    </div>

</form>


<!-- KPI -->

<div class="row kpi-row">

<div class="col-md-3 col-sm-6">

<div class="card c1">

    <div>

        <h5>Daily Income</h5>

        <h3>
            Rs. <?php echo $dailyIncome; ?>
        </h3>

    </div>

</div>

</div>


<div class="col-md-3 col-sm-6">

<div class="card c2">

    <div>

        <h5>Today Attendance</h5>

        <h3>
            <?php echo $todayAttendance; ?>
        </h3>

    </div>

</div>

</div>


<div class="col-md-3 col-sm-6">

<div class="card c3">

    <div>

        <h5>Total Students</h5>

        <h3>
            <?php echo $totalStudents; ?>
        </h3>

    </div>

</div>

</div>


<div class="col-md-3 col-sm-6">

<div class="card c4">

    <div>

        <h5>Monthly Income</h5>

        <h3>
            Rs. <?php echo $monthlyIncome; ?>
        </h3>

    </div>

</div>

</div>

</div>


<!-- CHARTS -->

<div class="row">

<div class="col-md-6 col-sm-12">

<div class="chart-box">

    <h4>Weekly Attendance (Subject Wise)</h4>

    <canvas id="attendanceChart"></canvas>

</div>

</div>


<div class="col-md-6 col-sm-12">

<div class="chart-box">

    <h4>Weekly Income (Subject Wise)</h4>

    <canvas id="incomeChart"></canvas>

</div>

</div>

</div>


</div>


<script>

/* =========================
   SIDEBAR
========================= */

const sidebar = document.querySelector('.sidebar');
const toggle = document.querySelector('.sidebar-toggle');
const overlay = document.querySelector('.sidebar-overlay');
const main = document.querySelector('.main');


/* =========================
   TOGGLE SIDEBAR
========================= */

toggle.addEventListener('click', function () {

    if (window.innerWidth <= 768) {

        sidebar.classList.toggle('active');

        overlay.style.display =
            sidebar.classList.contains('active')
            ? 'block'
            : 'none';

    } else {

        sidebar.classList.toggle('collapsed');

        main.classList.toggle('expanded');

    }

});


/* =========================
   MOBILE OVERLAY
========================= */

overlay.addEventListener('click', function () {

    sidebar.classList.remove('active');

    overlay.style.display = 'none';

});


/* =========================
   DROPDOWN MENUS
========================= */

const menuLinks = document.querySelectorAll('.menu-link');

menuLinks.forEach(function(link) {

    link.addEventListener('click', function() {

        const menuItem = this.parentElement;

        /*
         * Close other menus
         */
        document.querySelectorAll('.menu-item').forEach(function(item) {

            if (item !== menuItem) {
                item.classList.remove('active');
            }

        });

        /*
         * Open / close selected menu
         */
        menuItem.classList.toggle('active');

    });

});


/* =========================
   CHART DATA
========================= */

const subjects = <?php echo json_encode($subjects); ?>;


new Chart(document.getElementById('attendanceChart'), {

    type: 'bar',

    data: {

        labels: subjects,

        datasets: [{

            label: 'Attendance',

            data: <?php echo json_encode(array_values($attendanceData)); ?>,

            backgroundColor:'#4e73df'

        }]

    }

});


new Chart(document.getElementById('incomeChart'), {

    type: 'bar',

    data: {

        labels: subjects,

        datasets: [{

            label: 'Income',

            data: <?php echo json_encode(array_values($incomeData)); ?>,

            backgroundColor:'#1cc88a'

        }]

    }

});

</script>

<?php include('allfoot.php'); ?>