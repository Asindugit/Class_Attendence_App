<?php
session_start();

if (!isset($_SESSION["uidx"]) || $_SESSION["uidx"] == "") {
    header('Location:userLogin');
    exit();
}

include('allhead.php');
include_once("connection.php");

$userid = $_SESSION["uidx"];
$fname  = $_SESSION["uname"];

$selectedGrade   = isset($_GET['grade']) ? $_GET['grade'] : "";
$selectedSubject = isset($_GET['subject']) ? $_GET['subject'] : "";
$selectedStudent = isset($_GET['student_id']) ? $_GET['student_id'] : "";
?>

<style>
    /* ================================
   PAGE
================================ */

    body {
        background: #f4f6f9;
    }

    .id-card-container {
        max-width: 1100px;
        margin: 25px auto;
    }

    .main-box {
        background: #ffffff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
    }

    .page-title {
        text-align: center;
        margin-bottom: 25px;
        font-weight: 600;
    }


    /* ================================
   STUDENT TABLE
================================ */

    .student-table {
        margin-top: 25px;
    }

    .student-table th {
        background: #f1f3f5;
    }


    /* ================================
   ID CARD PREVIEW
   EXACT SIZE
   90mm x 54mm
================================ */

    .card-preview-area {
        margin-top: 35px;
        padding: 25px;
        background: #f1f3f5;
        border-radius: 10px;
    }

    .preview-title {
        text-align: center;
        margin-bottom: 20px;
    }


    /*
   ID CARD
   Width  = 90mm
   Height = 54mm
*/

    .student-id-card {

        width: 90mm;
        height: 54mm;

        background: #ffffff;

        margin: 0 auto;

        border: 1px solid #d0d0d0;

        border-radius: 3mm;

        box-sizing: border-box;

        padding: 4mm;

        position: relative;

        font-family: Arial, Helvetica, sans-serif;

        overflow: hidden;

        color: #000000;
    }


    /* ================================
   CARD HEADER
================================ */

    .card-header {
        text-align: center;

        border-bottom: 0.4mm solid currentColor;

        padding-bottom: 1.5mm;

        margin-bottom: 2.5mm;
    }

    .card-institute {
        font-size: 4mm;
        font-weight: bold;
        line-height: 1.1;
    }

    .card-title {
        font-size: 2.7mm;
        font-weight: bold;

        margin-top: 1mm;

        letter-spacing: 0.5px;
    }


    /* ================================
   CARD BODY
================================ */

    .card-body {
        display: flex;

        width: 100%;
        height: 35mm;
    }


    /* ================================
   DETAILS
================================ */

    .card-details {
        width: 62%;

        padding-right: 2mm;

        box-sizing: border-box;
    }

    .detail-row {
        display: flex;

        margin-bottom: 1.8mm;

        font-size: 2.7mm;

        line-height: 1.1;
    }

    .detail-label {
        width: 27mm;

        font-weight: bold;
    }

    .detail-colon {
        width: 2mm;
    }

    .detail-value {
        flex: 1;

        font-weight: 500;

        word-break: break-word;
    }


    /* ================================
   QR AREA
================================ */

    .card-qr {
        width: 40%;

        display: flex;

        flex-direction: column;

        justify-content: center;

        align-items: center;
    }

    #qrcode {
        width: 30mm;
        height: 30mm;

        display: flex;

        align-items: center;
        justify-content: center;
        padding-bottom: 8mm;
    }

    #qrcode img,
    #qrcode canvas {
        width: 28mm !important;
        height: 28mm !important;
    }

    .qr-text {
        font-size: 2mm;

        margin-top: 1mm;

        font-weight: bold;

        text-align: center;
    }


    /* ================================
   CARD FOOTER
================================ */

    .card-footer {
        position: absolute;

        bottom: 2.5mm;

        left: 4mm;

        right: 4mm;

        border-top: 0.3mm solid currentColor;

        padding-top: 1mm;

        text-align: center;

        font-size: 1.8mm;

        font-weight: bold;
    }


    /* ================================
   COLOR PICKER
================================ */

    .color-section {
        margin-top: 25px;

        padding: 15px;

        background: #f8f9fa;

        border-radius: 8px;

        border: 1px solid #ddd;
    }

    .color-input {
        width: 55px;
        height: 40px;

        padding: 2px;

        border: 1px solid #ccc;

        border-radius: 5px;

        cursor: pointer;
    }


    /* ================================
   PRINT BUTTON
================================ */

    .print-section {
        text-align: center;

        margin-top: 25px;
    }


    /* ================================
   MOBILE
================================ */

    @media(max-width:576px) {

        .main-box {
            padding: 15px;
        }

        .student-id-card {
            transform-origin: top center;

            /*
           Scale card visually on small screens.
           Actual print size remains 90mm x 54mm.
        */
            transform: scale(0.9);

            margin-bottom: -5mm;
        }

        .card-preview-area {
            overflow-x: auto;
        }
    }


    /* ================================
   PRINT
================================ */

    @media print {

        @page {
            size: 90mm 54mm;
            margin: 0;
        }

        html,
        body {

            width: 90mm;
            height: 54mm;

            margin: 0;
            padding: 0;

            background: #ffffff !important;
        }

        body * {
            visibility: hidden;
        }

        #printCard,
        #printCard * {
            visibility: visible;
        }

        #printCard {

            position: absolute;

            left: 0;
            top: 0;

            width: 90mm;
            height: 54mm;

            margin: 0;

            border: 1px solid #d0d0d0;

            border-radius: 3mm;

            box-shadow: none;
        }

        .no-print {
            display: none !important;
        }
    }
</style>


<div class="container-fluid">

    <div class="id-card-container">

        <div class="main-box">

            <h2 class="page-title">
                Generate Student ID Card
            </h2>
            <!-- 
            <p style="text-align:center;">
                Welcome : <strong><?php echo htmlspecialchars($fname); ?></strong>
            </p> -->

            <hr>


            <!-- =========================================
                 SEARCH FORM
            ========================================== -->

            <form method="GET">

                <div class="row">

                    <!-- GRADE -->

                    <div class="col-md-6">

                        <label>
                            <strong>Grade</strong>
                        </label>

                        <select
                            name="grade"
                            class="form-control"
                            required>

                            <option value="">
                                -- Select Grade --
                            </option>

                            <?php

                            $gradeQuery = mysqli_query(
                                $link,
                                "SELECT DISTINCT grade
                                 FROM classess
                                 ORDER BY grade"
                            );

                            while ($gradeRow = mysqli_fetch_assoc($gradeQuery)) {

                                $grade = $gradeRow['grade'];

                                $selected =
                                    ($selectedGrade == $grade)
                                    ? "selected"
                                    : "";

                            ?>

                                <option
                                    value="<?php echo htmlspecialchars($grade); ?>"
                                    <?php echo $selected; ?>>
                                    Grade <?php echo htmlspecialchars($grade); ?>
                                </option>

                            <?php
                            }
                            ?>

                        </select>

                    </div>


                    <!-- SUBJECT -->

                    <div class="col-md-6">

                        <label>
                            <strong>Subject</strong>
                        </label>

                        <select
                            name="subject"
                            class="form-control"
                            required>

                            <option value="">
                                -- Select Subject --
                            </option>

                            <?php

                            $subjectQuery = mysqli_query(
                                $link,
                                "SELECT DISTINCT subject
                                 FROM classess
                                 ORDER BY subject"
                            );

                            while ($subjectRow = mysqli_fetch_assoc($subjectQuery)) {

                                $subject = $subjectRow['subject'];

                                $selected =
                                    ($selectedSubject == $subject)
                                    ? "selected"
                                    : "";

                            ?>

                                <option
                                    value="<?php echo htmlspecialchars($subject); ?>"
                                    <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($subject); ?>
                                </option>

                            <?php
                            }
                            ?>

                        </select>

                    </div>

                </div>


                <br>

                <button
                    type="submit"
                    class="btn btn-primary">
                    Search Students
                </button>

                <a
                    href="welcomeuser.php"
                    class="btn btn-warning">
                    Back
                </a>

            </form>


            <?php

            /* =========================================
               FIND STUDENTS
            ========================================== */

            if (
                !empty($selectedGrade) &&
                !empty($selectedSubject)
            ) {

                $gradeSafe =
                    mysqli_real_escape_string(
                        $link,
                        $selectedGrade
                    );

                $subjectSafe =
                    mysqli_real_escape_string(
                        $link,
                        $selectedSubject
                    );


                $studentSQL = "
                    SELECT DISTINCT
                        s.s_id,
                        s.SName,
                        c.grade,
                        c.subject,
                        s.Register_date

                    FROM student s

                    INNER JOIN student_classes sc
                        ON s.s_id = sc.s_id

                    INNER JOIN classess c
                        ON sc.class_id = c.class_id

                    WHERE c.grade = '$gradeSafe'

                    AND c.subject = '$subjectSafe'

                    ORDER BY s.SName ASC
                ";


                $studentResult =
                    mysqli_query(
                        $link,
                        $studentSQL
                    );

            ?>


                <hr>

                <h4>
                    Students
                </h4>


                <?php

                if (
                    $studentResult &&
                    mysqli_num_rows($studentResult) > 0
                ) {

                ?>

                    <div class="table-responsive student-table">

                        <table class="table table-bordered table-hover">

                            <thead>

                                <tr>

                                    <th>
                                        Student Reg No.
                                    </th>

                                    <th>
                                        Student Name
                                    </th>

                                    <th>
                                        Grade
                                    </th>

                                    <th>
                                        Subject
                                    </th>

                                    <th>
                                        Registration Date
                                    </th>

                                    <th>
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php

                                while (
                                    $student =
                                    mysqli_fetch_assoc(
                                        $studentResult
                                    )
                                ) {

                                    $studentID =
                                        $student['s_id'];

                                    $studentName =
                                        $student['SName'];

                                    $studentGrade =
                                        $student['grade'];

                                    $studentSubject =
                                        $student['subject'];

                                    $registerDate =
                                        $student['Register_date'];

                                ?>

                                    <tr>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $studentID
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $studentName
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $studentGrade
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $studentSubject
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $registerDate
                                            );
                                            ?>
                                        </td>

                                        <td>

                                            <a
                                                href="generateidcard.php?grade=<?php echo urlencode($selectedGrade); ?>&subject=<?php echo urlencode($selectedSubject); ?>&student_id=<?php echo urlencode($studentID); ?>"
                                                class="btn btn-success btn-sm">
                                                Generate ID Card
                                            </a>

                                        </td>

                                    </tr>

                                <?php
                                }

                                ?>

                            </tbody>

                        </table>

                    </div>

                <?php

                } else {

                ?>

                    <div class="alert alert-warning">

                        No students found for

                        <strong>
                            Grade <?php echo htmlspecialchars($selectedGrade); ?>
                        </strong>

                        -

                        <strong>
                            <?php echo htmlspecialchars($selectedSubject); ?>
                        </strong>

                    </div>

            <?php

                }
            }

            ?>


            <?php

            /* =========================================
               LOAD SELECTED STUDENT
            ========================================== */

            if (!empty($selectedStudent)) {

                $studentSafe =
                    mysqli_real_escape_string(
                        $link,
                        $selectedStudent
                    );

                $gradeSafe =
                    mysqli_real_escape_string(
                        $link,
                        $selectedGrade
                    );

                $subjectSafe =
                    mysqli_real_escape_string(
                        $link,
                        $selectedSubject
                    );


                $cardSQL = "
                    SELECT DISTINCT

                        s.s_id,
                        s.SName,
                        c.grade,
                        c.subject,
                        s.Register_date

                    FROM student s

                    INNER JOIN student_classes sc
                        ON s.s_id = sc.s_id

                    INNER JOIN classess c
                        ON sc.class_id = c.class_id

                    WHERE s.s_id = '$studentSafe'

                    AND c.grade = '$gradeSafe'

                    AND c.subject = '$subjectSafe'

                    LIMIT 1
                ";


                $cardResult =
                    mysqli_query(
                        $link,
                        $cardSQL
                    );


                if (
                    $cardResult &&
                    mysqli_num_rows($cardResult) > 0
                ) {

                    $cardData =
                        mysqli_fetch_assoc(
                            $cardResult
                        );


                    $cardStudentID =
                        $cardData['s_id'];

                    $cardStudentName =
                        $cardData['SName'];

                    $cardGrade =
                        $cardData['grade'];

                    $cardSubject =
                        $cardData['subject'];

                    $cardRegisterDate =
                        $cardData['Register_date'];

            ?>



                    <!-- =====================================
                         CARD CUSTOMIZATION
                    ====================================== -->

                    <div class="card-preview-area">

                        <h4 class="preview-title">
                            Student ID Card Preview
                        </h4>


                        <div class="color-section no-print">

                            <label>
                                <strong>
                                    Select Letter Color
                                </strong>
                            </label>

                            <br>

                            <input
                                type="color"
                                id="letterColor"
                                class="color-input"
                                value="#1d3557"
                                onchange="changeLetterColor()">

                            <span
                                id="colorCode"
                                style="margin-left:10px;">
                                #1d3557
                            </span>

                        </div>


                        <br>


                        <!-- =================================
                             ID CARD
                        ================================== -->

                        <div
                            id="printCard"
                            class="student-id-card">


                            <!-- HEADER -->

                            <div class="card-header">

                                <div class="card-institute">
                                    ASD EDUCATION CENTER
                                </div>

                                <div class="card-title">
                                    STUDENT ID CARD
                                </div>

                            </div>


                            <!-- BODY -->

                            <div class="card-body">


                                <!-- DETAILS -->

                                <div class="card-details">


                                    <div class="detail-row">

                                        <div class="detail-label">
                                            Reg No.
                                        </div>

                                        <div class="detail-colon">
                                            :
                                        </div>

                                        <div class="detail-value">
                                            <?php
                                            echo htmlspecialchars(
                                                $cardStudentID
                                            );
                                            ?>
                                        </div>

                                    </div>


                                    <div class="detail-row">

                                        <div class="detail-label">
                                            Name
                                        </div>

                                        <div class="detail-colon">
                                            :
                                        </div>

                                        <div class="detail-value">
                                            <?php
                                            echo htmlspecialchars(
                                                $cardStudentName
                                            );
                                            ?>
                                        </div>

                                    </div>


                                    <div class="detail-row">

                                        <div class="detail-label">
                                            Grade
                                        </div>

                                        <div class="detail-colon">
                                            :
                                        </div>

                                        <div class="detail-value">
                                            <?php
                                            echo htmlspecialchars(
                                                $cardGrade
                                            );
                                            ?>
                                        </div>

                                    </div>


                                    <div class="detail-row">

                                        <div class="detail-label">
                                            Subject
                                        </div>

                                        <div class="detail-colon">
                                            :
                                        </div>

                                        <div class="detail-value">
                                            <?php
                                            echo htmlspecialchars(
                                                $cardSubject
                                            );
                                            ?>
                                        </div>

                                    </div>


                                    <div class="detail-row">

                                        <div class="detail-label">
                                            Reg Date
                                        </div>

                                        <div class="detail-colon">
                                            :
                                        </div>

                                        <div class="detail-value">
                                            <?php
                                            echo htmlspecialchars(
                                                $cardRegisterDate
                                            );
                                            ?>
                                        </div>

                                    </div>


                                </div>


                                <!-- QR CODE -->

                                <div class="card-qr">

                                    <div id="qrcode"></div>

                                    <!-- <div class="qr-text">
                                        Scan to verify
                                    </div> -->

                                </div>

                            </div>


                            <!-- FOOTER -->

                            <div class="card-footer">

                                This card is issued by the
                                Student Management System

                            </div>


                        </div>


                        <!-- PRINT BUTTON -->

                        <div class="print-section no-print">

                            <button
                                type="button"
                                class="btn btn-primary"
                                onclick="printCard()">
                                <i class="fa fa-print"></i>
                                Print ID Card
                            </button>

                            <button
                                type="button"
                                class="btn btn-secondary"
                                onclick="window.location.reload()">
                                Change Student
                            </button>

                        </div>

                    </div>


            <?php

                }
            }

            ?>

        </div>

    </div>

</div>


<!-- =========================================
     QR CODE LIBRARY
========================================== -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>


<script>
    /* =========================================
   GENERATE QR CODE
========================================= */

    document.addEventListener(
        "DOMContentLoaded",
        function() {

            var qrElement =
                document.getElementById("qrcode");

            if (qrElement) {

                qrElement.innerHTML = "";

                new QRCode(
                    qrElement, {
                        text: "<?php echo htmlspecialchars($cardStudentID ?? '', ENT_QUOTES); ?>",

                        width: 100,

                        height: 100,

                        correctLevel: QRCode.CorrectLevel.H
                    }
                );

            }

        }
    );


    /* =========================================
       CHANGE LETTER COLOR
    ========================================= */

    function changeLetterColor() {

        var color =
            document.getElementById(
                "letterColor"
            ).value;


        var card =
            document.getElementById(
                "printCard"
            );


        card.style.color = color;


        document.getElementById(
            "colorCode"
        ).innerText = color;
    }


    /* =========================================
       PRINT CARD
    ========================================= */

    function printCard() {

        window.print();

    }
</script>


<?php include('allfoot.php'); ?>