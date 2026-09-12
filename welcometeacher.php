
<?php
session_start();

/* =====================================================
   CHECK LOGIN
===================================================== */

if (!isset($_SESSION["uidx"]) || $_SESSION["uidx"] == "" || $_SESSION["uidx"] == NULL) {

    header('Location:fuserLogin');
    exit;
}

include('connection.php');

$teacherID = $_SESSION["uidx"];
$name = $_SESSION["uname"];

$today = date('Y-m-d');
$currentMonth = date('m');
$currentYear = date('Y');


/* =====================================================
   TOTAL STUDENTS FOR LOGGED-IN TEACHER
===================================================== */

/*
   student.s_id
        ↓
   attendance.s_id
        ↓
   attendance.class_id
        ↓
   classess.class_id
        ↓
   classess.teacher_id
*/

$queryStudents = mysqli_query(
    $link,
    "SELECT COUNT(DISTINCT s.s_id) AS t
     FROM student s
     JOIN attendance a
        ON s.s_id = a.s_id
     JOIN classess c
        ON a.class_id = c.class_id
     WHERE c.teacher_id = '$teacherID'"
);

$totalStudents = mysqli_fetch_assoc($queryStudents)['t'] ?? 0;


/* =====================================================
   TODAY'S ATTENDANCE
===================================================== */

$queryAttendance = mysqli_query(
    $link,
    "SELECT COUNT(*) AS t
     FROM attendance a
     JOIN classess c
        ON a.class_id = c.class_id
     WHERE c.teacher_id = '$teacherID'
     AND DATE(a.date) = '$today'
     AND a.status = 'Present'"
);

$todayAttendance =
    mysqli_fetch_assoc($queryAttendance)['t'] ?? 0;


/* =====================================================
   TOTAL PAYMENTS FOR THIS TEACHER
===================================================== */

$queryPayments = mysqli_query(
    $link,
    "SELECT SUM(p.amount) AS t
     FROM payment p
     JOIN classess c
        ON p.class_id = c.class_id
     WHERE c.teacher_id = '$teacherID'"
);

$totalPayments =
    mysqli_fetch_assoc($queryPayments)['t'] ?? 0;


/* =====================================================
   THIS MONTH INCOME
===================================================== */

$queryMonthlyIncome = mysqli_query(
    $link,
    "SELECT SUM(p.amount) AS t
     FROM payment p
     JOIN classess c
        ON p.class_id = c.class_id
     WHERE c.teacher_id = '$teacherID'
     AND MONTH(p.date) = '$currentMonth'
     AND YEAR(p.date) = '$currentYear'"
);

$monthlyIncome =
    mysqli_fetch_assoc($queryMonthlyIncome)['t'] ?? 0;


/* =====================================================
   GET ONLY TEACHER'S SUBJECTS
===================================================== */

$subjects = [];

$querySubjects = mysqli_query(
    $link,
    "SELECT DISTINCT subject
     FROM classess
     WHERE teacher_id = '$teacherID'
     ORDER BY subject"
);

while ($row = mysqli_fetch_assoc($querySubjects)) {

    $subjects[] = $row['subject'];

}


/* =====================================================
   ATTENDANCE DATA BY SUBJECT
===================================================== */

$attendanceData = [];

foreach ($subjects as $sub) {

    $safeSubject =
        mysqli_real_escape_string($link, $sub);

    $query = mysqli_query(
        $link,
        "SELECT COUNT(*) AS t
         FROM attendance a
         JOIN classess c
            ON a.class_id = c.class_id
         WHERE c.teacher_id = '$teacherID'
         AND c.subject = '$safeSubject'
         AND a.status = 'Present'
         AND DATE(a.date) >= DATE_SUB('$today', INTERVAL 7 DAY)"
    );

    $attendanceData[] =
        mysqli_fetch_assoc($query)['t'] ?? 0;

}


/* =====================================================
   INCOME DATA BY SUBJECT
===================================================== */

$incomeData = [];

foreach ($subjects as $sub) {

    $safeSubject =
        mysqli_real_escape_string($link, $sub);

    $query = mysqli_query(
        $link,
        "SELECT SUM(p.amount) AS t
         FROM payment p
         JOIN classess c
            ON p.class_id = c.class_id
         WHERE c.teacher_id = '$teacherID'
         AND c.subject = '$safeSubject'
         AND DATE(p.date) >= DATE_SUB('$today', INTERVAL 7 DAY)"
    );

    $incomeData[] =
        mysqli_fetch_assoc($query)['t'] ?? 0;

}


include('allhead.php');

?>


<!-- =====================================================
     FONT AWESOME
===================================================== -->

<link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    rel="stylesheet"
>


<!-- =====================================================
     CHART JS
===================================================== -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<style>

/* =====================================================
   BODY
===================================================== */

body {

    background: #eef2f7;

}


/* =====================================================
   TOPBAR
===================================================== */

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

    box-shadow:
        0 2px 10px rgba(0, 0, 0, 0.08);

}


/* =====================================================
   SIDEBAR
===================================================== */

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


/* =====================================================
   COLLAPSED SIDEBAR
===================================================== */

.sidebar.collapsed {

    width: 70px;

}


.sidebar.collapsed span {

    display: none;

}


/* =====================================================
   MAIN
===================================================== */

.main {

    margin-left: 220px;

    margin-top: 30px;

    padding: 20px;

    transition: 0.3s;

}


.main.expanded {

    margin-left: 70px;

}


/* =====================================================
   KPI CARDS
===================================================== */

.kpi-card {

    border-radius: 16px;

    padding: 20px;

    color: white;

    display: flex;

    justify-content: space-between;

    align-items: center;

    box-shadow:
        0 8px 20px rgba(0, 0, 0, 0.12);

    transition: 0.3s;

}


.kpi-card:hover {

    transform: translateY(-5px);

}


/* =====================================================
   SAME COLORS AS USER DASHBOARD
===================================================== */

.c1 {

    background:
        linear-gradient(
            135deg,
            #4e73df,
            #224abe
        );

}


.c2 {

    background:
        linear-gradient(
            135deg,
            #1cc88a,
            #0f9d58
        );

}


.c3 {

    background:
        linear-gradient(
            135deg,
            #f6c23e,
            #e0a800
        );

}


.c4 {

    background:
        linear-gradient(
            135deg,
            #e74a3b,
            #c0392b
        );

}


/* =====================================================
   CHART BOX
===================================================== */

.box {

    background: #fff;

    padding: 18px;

    border-radius: 14px;

    box-shadow:
        0 4px 14px rgba(0, 0, 0, 0.08);

}


.box h5 {

    margin-bottom: 20px;

}


/* =====================================================
   TEXT
===================================================== */

.text {

    margin-left: 2rem;

}


.username {

    color: #224abe;

}


/* =====================================================
   MOBILE
===================================================== */

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


    .kpi-card {

        margin-bottom: 5px;

    }

}

</style>


<!-- =====================================================
     TOPBAR
===================================================== -->

<div class="topbar d-flex align-items-center gap-4">


    <button
        class="btn btn-primary btn-sm"
        id="toggle"
    >

        <i class="fa fa-bars"></i>

    </button>


    <h4 class="mb-0 text">

        Welcome,

        <strong class="username">

            <?php
            echo htmlspecialchars($name);
            ?>

        </strong>

    </h4>


</div>



<!-- =====================================================
     SIDEBAR
===================================================== -->

<div class="sidebar" id="sidebar">


    <!-- DASHBOARD -->

    <a href="welcometeacher.php">

        <i class="fa fa-gauge"></i>

        <span>
            Dashboard
        </span>

    </a>


    <!-- PROFILE -->

    <a href="mydetailsteacher.php">

        <i class="fa-solid fa-user"></i>

        <span>
            Profile
        </span>

    </a>


    <!-- STUDENT DETAILS -->

    <a href="teacherviewstudent.php">

        <i class="fa fa-users"></i>

        <span>
            Student Details
        </span>

    </a>


    <!-- ATTENDANCE -->

    <a href="viewteacherattend.php">

        <i class="fa fa-clipboard-check"></i>

        <span>
            Attendance
        </span>

    </a>


    <!-- PAYMENTS -->

    <a href="viewteacherpayment.php">

        <i class="fa fa-money-bill"></i>

        <span>
            Payments
        </span>

    </a>


    <!-- INCOME SUMMARY -->

    <a href="teacherincome.php">

        <i class="fa fa-chart-line"></i>

        <span>
            Income Summary
        </span>

    </a>


    <!-- LOGOUT -->

    <a href="index.php">

        <i class="fa fa-right-from-bracket"></i>

        <span>
            Logout
        </span>

    </a>


</div>



<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<div class="main" id="main">


    <!-- =================================================
         KPI CARDS
    ================================================= -->

    <div class="row g-3">


        <!-- MY STUDENTS -->

        <div class="col-md-3">

            <div class="kpi-card c1">

                <div>

                    <h6>
                        My Students
                    </h6>

                    <h3>

                        <?php
                        echo $totalStudents;
                        ?>

                    </h3>

                </div>


                <i class="fa fa-users fa-2x"></i>

            </div>

        </div>



        <!-- TODAY ATTENDANCE -->

        <div class="col-md-3">

            <div class="kpi-card c2">

                <div>

                    <h6>
                        Today's Attendance
                    </h6>

                    <h3>

                        <?php
                        echo $todayAttendance;
                        ?>

                    </h3>

                </div>


                <i class="fa fa-user-check fa-2x"></i>

            </div>

        </div>



        <!-- MY PAYMENTS -->

        <div class="col-md-3">

            <div class="kpi-card c3">

                <div>

                    <h6>
                        My Payments
                    </h6>

                    <h3>

                        Rs.
                        <?php
                        echo number_format(
                            $totalPayments,
                            2
                        );
                        ?>

                    </h3>

                </div>


                <i class="fa fa-money-bill-wave fa-2x"></i>

            </div>

        </div>



        <!-- MONTHLY INCOME -->

        <div class="col-md-3">

            <div class="kpi-card c4">

                <div>

                    <h6>
                        This Month Income
                    </h6>

                    <h3>

                        Rs.
                        <?php
                        echo number_format(
                            $monthlyIncome,
                            2
                        );
                        ?>

                    </h3>

                </div>


                <i class="fa fa-chart-line fa-2x"></i>

            </div>

        </div>


    </div>



    <!-- =================================================
         CHARTS
    ================================================= -->

    <div class="row mt-4">


        <!-- ATTENDANCE -->

        <div class="col-md-6">

            <div class="box">

                <h5>
                    My Subject Attendance
                </h5>


                <canvas id="chart1"></canvas>


            </div>

        </div>



        <!-- INCOME -->

        <div class="col-md-6">

            <div class="box">

                <h5>
                    My Subject Income
                </h5>


                <canvas id="chart2"></canvas>


            </div>

        </div>


    </div>


</div>



<script>

/* =====================================================
   SIDEBAR TOGGLE
===================================================== */

const sidebar =
    document.getElementById("sidebar");

const main =
    document.getElementById("main");


document.getElementById("toggle").onclick = () => {

    if (window.innerWidth <= 768) {

        sidebar.classList.toggle("active");

    } else {

        sidebar.classList.toggle("collapsed");

        main.classList.toggle("expanded");

    }

};



/* =====================================================
   CHART DATA
===================================================== */

const subjects =
    <?php
    echo json_encode($subjects);
    ?>;


const attendanceData =
    <?php
    echo json_encode($attendanceData);
    ?>;


const incomeData =
    <?php
    echo json_encode($incomeData);
    ?>;



/* =====================================================
   ATTENDANCE CHART
===================================================== */

new Chart(

    document.getElementById("chart1"),

    {

        type: "bar",

        data: {

            labels: subjects,

            datasets: [

                {

                    label: "Attendance",

                    data: attendanceData,

                    backgroundColor: "#4e73df"

                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: true,

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {

                        precision: 0

                    }

                }

            }

        }

    }

);



/* =====================================================
   INCOME CHART
===================================================== */

new Chart(

    document.getElementById("chart2"),

    {

        type: "bar",

        data: {

            labels: subjects,

            datasets: [

                {

                    label: "Income",

                    data: incomeData,

                    backgroundColor: "#1cc88a"

                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: true,

            scales: {

                y: {

                    beginAtZero: true

                }

            }

        }

    }

);

</script>


<?php

include('allfoot.php');

?>

