<?php
session_start();

if ($_SESSION["uidx"] == "" || $_SESSION["uidx"] == NULL) {
    header('Location:facultylogin');
}

$userid = $_SESSION["uidx"];
$fname  = $_SESSION["uname"];

include("connection.php");
?>

<?php include('allhead.php'); ?>

<div class="container">

    <div class="row">

        <div class="col-md-12">

            <h3>
                Welcome :
                <a href="welcomeuser.php" style="text-decoration:none;">
                    <span style="color:#FF0004">
                        <?php echo $fname; ?>
                    </span>
                </a>
            </h3>

            <!-- SEARCH FORM -->

            <form method="GET">

                <div class="row">

                    <!-- GRADE -->

                    <div class="col-md-3">

                        <div class="form-group" style="padding-top:0.5rem;">

                            <label><strong>Grade :</strong></label>

                            <select name="grade" class="form-control">

                                <option value="">--Select Grade--</option>

                                <?php

                                $grade_query = mysqli_query(
                                    $link,
                                    "SELECT DISTINCT grade
                                     FROM classess
                                     ORDER BY grade ASC"
                                );

                                while ($g = mysqli_fetch_assoc($grade_query)) {
                                ?>

                                    <option
                                        value="<?php echo $g['grade']; ?>"

                                        <?php
                                        if (
                                            isset($_GET['grade']) &&
                                            $_GET['grade'] == $g['grade']
                                        ) {
                                            echo "selected";
                                        }
                                        ?>>

                                        <?php echo $g['grade']; ?>

                                    </option>

                                <?php
                                }
                                ?>

                            </select>

                        </div>

                    </div>

                    <!-- SUBJECT -->

                    <div class="col-md-3">

                        <div class="form-group" style="padding-top:0.5rem;">

                            <label><strong>Subject :</strong></label>

                            <select name="subject" class="form-control">

                                <option value="">--Select Subject--</option>

                                <?php

                                $subject_query = mysqli_query(
                                    $link,
                                    "SELECT DISTINCT subject
                                     FROM classess
                                     ORDER BY subject ASC"
                                );

                                while ($s = mysqli_fetch_assoc($subject_query)) {
                                ?>

                                    <option
                                        value="<?php echo $s['subject']; ?>"

                                        <?php
                                        if (
                                            isset($_GET['subject']) &&
                                            $_GET['subject'] == $s['subject']
                                        ) {
                                            echo "selected";
                                        }
                                        ?>>

                                        <?php echo $s['subject']; ?>

                                    </option>

                                <?php
                                }
                                ?>

                            </select>

                        </div>

                    </div>

                    <!-- MONTH -->

                    <div class="col-md-3">

                        <div class="form-group" style="padding-top:0.5rem;">

                            <label><strong>Month :</strong></label>

                            <select name="month" class="form-control">

                                <option value="">--Select Month--</option>

                                <?php

                                $months = array(
                                    "01" => "January",
                                    "02" => "February",
                                    "03" => "March",
                                    "04" => "April",
                                    "05" => "May",
                                    "06" => "June",
                                    "07" => "July",
                                    "08" => "August",
                                    "09" => "September",
                                    "10" => "October",
                                    "11" => "November",
                                    "12" => "December"
                                );

                                foreach ($months as $num => $name) {
                                ?>

                                    <option
                                        value="<?php echo $num; ?>"

                                        <?php
                                        if (
                                            isset($_GET['month']) &&
                                            $_GET['month'] == $num
                                        ) {
                                            echo "selected";
                                        }
                                        ?>>

                                        <?php echo $name; ?>

                                    </option>

                                <?php
                                }
                                ?>

                            </select>

                        </div>

                    </div>

                    <!-- YEAR -->

                    <div class="col-md-3">

                        <div class="form-group" style="padding-top:0.5rem;">

                            <label><strong>Year :</strong></label>

                            <select name="year" class="form-control">

                                <option value="">--Select Year--</option>

                                <?php

                                for ($y = date("Y"); $y >= 2020; $y--) {
                                ?>

                                    <option
                                        value="<?php echo $y; ?>"

                                        <?php
                                        if (
                                            isset($_GET['year']) &&
                                            $_GET['year'] == $y
                                        ) {
                                            echo "selected";
                                        }
                                        ?>>

                                        <?php echo $y; ?>

                                    </option>

                                <?php
                                }
                                ?>

                            </select>

                        </div>

                    </div>

                </div>

                <br>

                <button type="submit" class="btn btn-primary">

                    <i class="fa fa-search"></i>
                    Search

                </button>

            </form>

            <br>

            <?php

            if (
                isset($_GET['grade']) &&
                isset($_GET['subject']) &&
                isset($_GET['month']) &&
                isset($_GET['year'])
            ) {

                $grade = mysqli_real_escape_string($link, $_GET['grade']);
                $subject = mysqli_real_escape_string($link, $_GET['subject']);
                $month = mysqli_real_escape_string($link, $_GET['month']);
                $year = mysqli_real_escape_string($link, $_GET['year']);

                if (
                    $grade != "" &&
                    $subject != "" &&
                    $month != "" &&
                    $year != ""
                ) {

                    $sql = "

                    SELECT

                        attendance.attend_id,
                        attendance.s_id,
                        attendance.date,
                        attendance.status,

                        student.SName,

                        classess.grade,
                        classess.subject

                    FROM attendance

                    INNER JOIN student
                    ON attendance.s_id = student.s_id

                    INNER JOIN classess
                    ON attendance.class_id = classess.class_id

                    WHERE classess.grade = '$grade'
                    AND classess.subject = '$subject'

                    AND MONTH(attendance.date) = '$month'
                    AND YEAR(attendance.date) = '$year'

                    ORDER BY attendance.date DESC

                    ";

                    $result = mysqli_query($link, $sql);

            ?>

                    <h2 class='page-header'>
                        Attendance Details
                    </h2>

                    <div class='table-responsive'>

                        <table class='table table-striped table-hover table-bordered'>

                            <tr class='table-dark'>

                                <th>Attendance ID</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Grade</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Status</th>

                            </tr>

                            <?php

                            if (mysqli_num_rows($result) > 0) {

                                while ($row = mysqli_fetch_assoc($result)) {
                            ?>

                                    <tr>

                                        <td>
                                            <?php echo $row['attend_id']; ?>
                                        </td>

                                        <td>
                                            <?php echo $row['s_id']; ?>
                                        </td>

                                        <td>
                                            <?php echo $row['SName']; ?>
                                        </td>

                                        <td>
                                            <?php echo $row['grade']; ?>
                                        </td>

                                        <td>
                                            <?php echo $row['subject']; ?>
                                        </td>

                                        <td>
                                            <?php echo $row['date']; ?>
                                        </td>

                                        <td>

                                            <?php

                                            $status = strtolower(trim($row['status']));

                                            if ($status == "present") {
                                                echo "<span style='color:green; font-weight:bold;'>✅ Present</span>";
                                            } else {
                                                echo "<span style='color:red; font-weight:bold;'>❌ Absent</span>";
                                            }

                                            ?>

                                        </td>

                                    </tr>

                                <?php
                                }
                            } else {
                                ?>

                                <tr>

                                    <td colspan='7'
                                        style='text-align:center; color:red;'>

                                        No Attendance Records Found

                                    </td>

                                </tr>

                            <?php
                            }

                            ?>

                        </table>
                    </div>

            <?php
                }
            }

            ?>

            <br>

            <a href="welcomeuser.php">

                <input type="button"
                    value="Back"
                    class="btn btn-warning"
                    style="border-radius:0%">

            </a>

        </div>

    </div>

</div>

<?php include('allfoot.php'); ?>