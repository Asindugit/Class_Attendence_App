<?php
session_start();

if ($_SESSION["uidx"] == "" || $_SESSION["uidx"] == NULL) {
    header('Location:fuserLogin');
    exit;
}

include('connection.php');

$name = $_SESSION["uname"];

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$month = date('m', strtotime($date));
$year  = date('Y', strtotime($date));

$dailyIncome = mysqli_fetch_assoc(mysqli_query(
    $link,
    "SELECT SUM(amount) as t FROM payment WHERE date='$date'"
))['t'] ?? 0;

$todayAttendance = mysqli_fetch_assoc(mysqli_query(
    $link,
    "SELECT COUNT(*) as t FROM attendance WHERE date='$date' AND status='Present'"
))['t'] ?? 0;

$totalStudents = mysqli_fetch_assoc(mysqli_query(
    $link,
    "SELECT COUNT(*) as t FROM student"
))['t'] ?? 0;

$monthlyIncome = mysqli_fetch_assoc(mysqli_query(
    $link,
    "SELECT SUM(amount) as t FROM payment WHERE MONTH(date) = MONTH('$date') AND YEAR(date) = YEAR('$date')
"
))['t'] ?? 0;

$subjects = [];
$res = mysqli_query($link, "SELECT DISTINCT subject FROM classess");

while ($row = mysqli_fetch_assoc($res)) {
    $subjects[] = $row['subject'];
}

$attendanceData = [];
$incomeData = [];

foreach ($subjects as $sub) {

    $attendanceData[] = mysqli_fetch_assoc(mysqli_query(
        $link,
        "SELECT COUNT(*) as t 
     FROM attendance a
     JOIN classess c ON a.class_id = c.class_id
     WHERE c.subject='$sub'
     AND a.status='Present'
     AND DATE(a.date) >= DATE_SUB('$date', INTERVAL 7 DAY)
    "
    ))['t'] ?? 0;

    $incomeData[] = mysqli_fetch_assoc(mysqli_query(
        $link,
        "SELECT SUM(p.amount) as t
     FROM payment p
     JOIN classess c ON p.class_id = c.class_id
     WHERE c.subject='$sub'
     AND DATE(p.date) >= DATE_SUB('$date', INTERVAL 7 DAY)
    "
    ))['t'] ?? 0;
}

include('allhead.php');
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    body {
        background: #eef2f7;
    }

    /* TOPBAR */
    .topbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 55px;
        background: #fff;
        display: flex;
        align-items: center;
        padding: 0 15px;
        z-index: 2000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    /* SIDEBAR */
    .sidebar {
        position: fixed;
        top: 55px;
        left: 0;
        width: 220px;
        height: 100%;
        background: #111827;
        transition: 0.3s;
        overflow: hidden;
        z-index: 3000;
    }

    .sidebar a {
        display: flex;
        gap: 10px;
        padding: 12px 18px;
        color: #cbd5e1;
        text-decoration: none;
    }

    .sidebar a:hover {
        background: #4e73df;
        color: white;
    }

    .sidebar.collapsed {
        width: 70px;
    }

    .sidebar.collapsed span {
        display: none;
    }

    /* =========================
   SIDEBAR DROPDOWN
========================= */

    .dropdown-btn {
        cursor: pointer;
        position: relative;
    }

    .dropdown-arrow {
        margin-left: auto;
        font-size: 11px;
        transition: transform 0.3s ease;
    }

    .dropdown-btn.active .dropdown-arrow {
        transform: rotate(180deg);
    }

    .dropdown-container {
        display: none;
        background: #0b1220;
    }

    .dropdown-container.show {
        display: block;
    }

    .dropdown-container a {
        padding: 10px 18px 10px 48px;
        font-size: 14px;
    }

    .dropdown-container a i {
        width: 18px;
        font-size: 13px;
    }

    /* MAIN */
    .main {
        margin-left: 220px;
        margin-top: 30px;
        padding: 20px;
        transition: 0.3s;
    }

    .main.expanded {
        margin-left: 70px;
    }

    /* CARDS */
    .kpi-card {
        border-radius: 16px;
        padding: 20px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        transition: 0.3s;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
    }

    .c1 {
        background: linear-gradient(135deg, #4e73df, #224abe);
    }

    .c2 {
        background: linear-gradient(135deg, #1cc88a, #0f9d58);
    }

    .c3 {
        background: linear-gradient(135deg, #f6c23e, #e0a800);
    }

    .c4 {
        background: linear-gradient(135deg, #e74a3b, #c0392b);
    }

    /* BOX */
    .box {
        background: #fff;
        padding: 18px;
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
    }

    .text {
        margin-left: 2rem;
    }

    .onlyBtn {
        margin-top: 1rem;
    }

    .username {
        color: #224abe;
    }

    /* MOBILE */
    @media(max-width:768px) {

        .sidebar {
            left: -220px;
            z-index: 3000;
        }

        .sidebar.active {
            left: 0;
        }

        .main {
            margin-left: 0;
        }

        .text {
            margin-left: 2rem;
        }

    }
</style>


<!-- TOPBAR -->
<div class="topbar d-flex align-items-center gap-4">

    <button class="btn btn-primary btn-sm" id="toggle">
        <i class="fa fa-bars"></i>
    </button>

    <h4 class="mb-0 text">
        Welcome,
        <strong class="username">
            <?php echo $name; ?>
        </strong>
    </h4>

</div>


<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">


    <!-- DASHBOARD -->
    <a href="#">
        <i class="fa fa-gauge"></i>
        <span> Dashboard</span>
    </a>

    <!--PROFILE -->
    <a href="mydetailsuser.php">
        <i class="fa-solid fa-user"></i>
        <span> Profile</span>
    </a>


    <!-- STUDENTS -->
    <a href="javascript:void(0)" class="dropdown-btn">

        <i class="fa fa-user-graduate"></i>

        <span> Students</span>

        <i class="fa fa-chevron-down dropdown-arrow"></i>

    </a>

    <div class="dropdown-container">

        <a href="studentRegistration.php">
            <i class="fa fa-user-plus"></i>
            <span>Add Student</span>
        </a>

        <a href="viewstudentdetails.php">
            <i class="fa fa-users"></i>
            <span>Student Details</span>
        </a>

        <a href="printqrcode.php">
            <i class="fa fa-qrcode"></i>
            <span>Print QR Code</span>
        </a>

        <a href="generateidcard.php">
            <i class="fa-solid fa-id-card"></i>
            <span>Generate ID Card</span>
        </a>

    </div>


    <!-- ATTENDANCE -->
    <a href="javascript:void(0)" class="dropdown-btn">

        <i class="fa fa-clipboard"></i>

        <span> Attendance</span>

        <i class="fa fa-chevron-down dropdown-arrow"></i>

    </a>

    <div class="dropdown-container">

        <a href="markstudentattendence.php">
            <i class="fa fa-user-check"></i>
            <span>Mark Attendance</span>
        </a>

        <a href="viewattendance.php">
            <i class="fa fa-eye"></i>
            <span>View Attendance</span>
        </a>

    </div>


    <!-- PAYMENTS -->
    <a href="javascript:void(0)" class="dropdown-btn">

        <i class="fa fa-money-bill"></i>

        <span> Payments</span>

        <i class="fa fa-chevron-down dropdown-arrow"></i>

    </a>

    <div class="dropdown-container">

        <a href="makepayment.php">
            <i class="fa fa-credit-card"></i>
            <span>Make Payment</span>
        </a>

        <a href="viewpayment.php">
            <i class="fa fa-eye"></i>
            <span>View Payment</span>
        </a>

        <a href="paymenthistory.php">
            <i class="fa fa-clock-rotate-left"></i>
            <span>Payment History</span>
        </a>

    </div>


    <!-- REPORTS -->
    <a href="javascript:void(0)" class="dropdown-btn">

        <i class="fa fa-chart-line"></i>

        <span> Reports</span>

        <i class="fa fa-chevron-down dropdown-arrow"></i>

    </a>

    <div class="dropdown-container">

        <a href="dayincome.php">
            <i class="fa fa-calendar-day"></i>
            <span>Daily Income</span>
        </a>

        <a href="subjectwiseincome.php">
            <i class="fa fa-book"></i>
            <span>Subject Wise</span>
        </a>

    </div>


    <!-- LOGOUT - UNCHANGED -->
    <a href="index.php">
        <i class="fa fa-right-from-bracket"></i>
        <span> Logout</span>
    </a>

</div>


<!-- MAIN -->
<div class="main" id="main">


    <div class="row button">

        <div class="col-md-6">

            <form method="GET" class="mb-5">

                <label class="form-label">
                    <strong>Select Month or Day</strong>
                </label>

                <div class="gap-2">

                    <input
                        type="date"
                        name="date"
                        value="<?php echo $date; ?>"
                        class="form-control py-1">

                    <button class="btn btn-primary mt-1 onlyBtn">
                        <i class="fa fa-filter"></i>
                        Filter
                    </button>

                </div>

            </form>

        </div>

    </div>


    <div class="row g-3 button">


        <div class="col-md-3">

            <div class="kpi-card c1">

                <div>

                    <h6>Daily Income</h6>

                    <h3>
                        Rs. <?php echo $dailyIncome; ?>
                    </h3>

                </div>

                <i class="fa fa-sack-dollar fa-2x"></i>

            </div>

        </div>


        <div class="col-md-3">

            <div class="kpi-card c2">

                <div>

                    <h6>Today Attendance</h6>

                    <h3>
                        <?php echo $todayAttendance; ?>
                    </h3>

                </div>

                <i class="fa fa-user-check fa-2x"></i>

            </div>

        </div>


        <div class="col-md-3">

            <div class="kpi-card c3">

                <div>

                    <h6>Total Students</h6>

                    <h3>
                        <?php echo $totalStudents; ?>
                    </h3>

                </div>

                <i class="fa fa-users fa-2x"></i>

            </div>

        </div>


        <div class="col-md-3">

            <div class="kpi-card c4">

                <div>

                    <h6>Monthly Income</h6>

                    <h3>
                        Rs. <?php echo $monthlyIncome; ?>
                    </h3>

                </div>

                <i class="fa fa-chart-line fa-2x"></i>

            </div>

        </div>


    </div>


    <div class="row mt-4">


        <div class="col-md-6">

            <div class="box">

                <h5>Attendance (Last 7 Days)</h5>

                <canvas id="chart1"></canvas>

            </div>

        </div>


        <div class="col-md-6">

            <div class="box">

                <h5>Income (Last 7 Days)</h5>

                <canvas id="chart2"></canvas>

            </div>

        </div>


    </div>


</div>


<script>
    /* =========================
   SIDEBAR TOGGLE
========================= */

    const sidebar = document.getElementById("sidebar");
    const main = document.getElementById("main");

    document.getElementById("toggle").onclick = () => {

        if (window.innerWidth <= 768) {

            sidebar.classList.toggle("active");

        } else {

            sidebar.classList.toggle("collapsed");

            main.classList.toggle("expanded");

        }

    };


    /* =========================
       SIDEBAR DROPDOWNS
    ========================= */

    const dropdownButtons = document.querySelectorAll(".dropdown-btn");

    dropdownButtons.forEach(function(button) {

        button.addEventListener("click", function() {

            const dropdown = this.nextElementSibling;

            this.classList.toggle("active");

            dropdown.classList.toggle("show");

        });

    });


    /* =========================
       CHART DATA
    ========================= */

    const subjects = <?php echo json_encode($subjects); ?>;


    /* =========================
       ATTENDANCE CHART
    ========================= */

    new Chart(document.getElementById("chart1"), {

        type: "bar",

        data: {

            labels: subjects,

            datasets: [{

                label: "Attendance",

                data: <?php echo json_encode($attendanceData); ?>,

                backgroundColor: "#4e73df"

            }]

        }

    });


    /* =========================
       INCOME CHART
    ========================= */

    new Chart(document.getElementById("chart2"), {

        type: "bar",

        data: {

            labels: subjects,

            datasets: [{

                label: "Income",

                data: <?php echo json_encode($incomeData); ?>,

                backgroundColor: "#1cc88a"

            }]

        }

    });
</script>


<?php include('allfoot.php'); ?>