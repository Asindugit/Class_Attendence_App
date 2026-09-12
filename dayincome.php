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

<?php

/* =========================================
   SECRET KEY
========================================= */

$secret_key = "my_secret_key_12345";

/* =========================================
   DECRYPT FUNCTION
========================================= */

function decryptData($data, $key)
{
    $decoded = base64_decode($data);

    list($encrypted_data, $iv) = explode('::', $decoded, 2);

    return openssl_decrypt(
        $encrypted_data,
        'AES-256-CBC',
        $key,
        0,
        $iv
    );
}

?>

<style>

body{
    background:#f4f6f9;
}

/* REPORT BOX */

.report-container{
    background:white;
    padding:40px;
    border-radius:10px;
    box-shadow:0px 0px 15px rgba(0,0,0,0.1);
}

/* HEADER */

.report-header{
    text-align:center;
    border-bottom:3px solid #000;
    padding-bottom:15px;
    margin-bottom:25px;
}

.report-header h1{
    font-size:34px;
    font-weight:bold;
    margin-bottom:5px;
}

.report-header h4{
    margin:0;
    color:#555;
}

/* INFO TABLE */

.info-table td{
    padding:8px;
    font-size:15px;
}

/* SUMMARY BOX */

.summary-box{
    background:#0d6efd;
    color:white;
    padding:20px;
    border-radius:8px;
    text-align:center;
    margin-bottom:25px;
}

.summary-box h2{
    font-size:38px;
    margin:10px 0;
}

/* TABLE */

.report-table th{
    background:#212529;
    color:white;
    text-align:center;
}

.report-table td{
    vertical-align:middle;
    text-align:center;
}

/* FOOTER */

.footer-sign{
    margin-top:60px;
}

.footer-sign .line{
    border-top:1px solid black;
    width:250px;
    margin-top:60px;
}

/* PRINT */

@media print {

    .no-print{
        display:none !important;
    }

    body{
        background:white;
    }

    .report-container{
        box-shadow:none;
        border:none;
        padding:0;
    }

}

</style>

<div class="container mt-4 mb-5">

    <div class="row">

        <div class="col-md-12">

            <!-- SEARCH -->

            <div class="card mb-4 no-print">

                <div class="card-header bg-dark text-white">

                    <h4>
                        Daily Income Summary Report
                    </h4>

                </div>

                <div class="card-body">

                    <form method="GET">

                        <div class="row">

                            <div class="col-md-4">

                                <label>
                                    <strong>Select Date</strong>
                                </label>

                                <input type="date"
                                       name="date"
                                       class="form-control"

                                       value="<?php

                                       if(isset($_GET['date']))
                                       {
                                           echo $_GET['date'];
                                       }
                                       else
                                       {
                                           echo date("Y-m-d");
                                       }

                                       ?>">

                            </div>

                            <div class="col-md-4">

                                <br>

                                <button type="submit"
                                        class="btn btn-primary">

                                    <i class="fa fa-search"></i>
                                    Generate Report

                                </button>

                                <button type="button"
                                        onclick="window.print();"
                                        class="btn btn-success">

                                    <i class="fa fa-print"></i>
                                    Print A4 Report

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

                <a href="income.php">
                    <input type="button"
                           value="Back"
                           class="btn btn-warning"
                           style="border-radius:0%">
                </a>

            </div>

<?php

$date = "";

if(isset($_GET['date']))
{
    $date = mysqli_real_escape_string(
        $link,
        $_GET['date']
    );
}
else
{
    $date = date("Y-m-d");
}

/* TOTAL INCOME */

$total_sql = mysqli_query(

    $link,

    "SELECT SUM(amount) as total_income

     FROM payment

     WHERE date='$date'
     AND status='Paid'"

);

$total_row = mysqli_fetch_assoc($total_sql);

$total_income = $total_row['total_income'];

if($total_income == "")
{
    $total_income = 0;
}

?>

            <!-- REPORT -->

            <div class="report-container">

                <!-- HEADER -->

                <div class="report-header">

                    <h1>
                        ASD EDUCATION CENTRE
                    </h1>

                    <h4>
                        Daily Income Summary Report
                    </h4>

                </div>

                <!-- REPORT INFO -->

                <table width="100%"
                       class="info-table mb-4">

                    <tr>

                        <td>

                            <strong>Report Date :</strong>

                            <?php echo $date; ?>

                        </td>

                        <td align="right">

                            <strong>Printed Date :</strong>

                            <?php echo date("Y-m-d"); ?>

                        </td>

                    </tr>

                    <tr>

                        <td>

                            <strong>Prepared By :</strong>

                            <?php echo $fname; ?>

                        </td>

                        <td align="right">

                            <strong>Status :</strong>

                            Official Institute Report

                        </td>

                    </tr>

                </table>

                <!-- TOTAL INCOME -->

                <div class="summary-box">

                    <h4>
                        TOTAL DAILY INCOME
                    </h4>

                    <h2>

                        Rs.
                        <?php
                        echo number_format(
                            $total_income,
                            2
                        );
                        ?>

                    </h2>

                </div>

                <!-- TABLE -->

                <h4 class="mb-3">

                    Subject Wise Income Details

                </h4>

                <div class="table-responsive">

                <table class="table
                              table-bordered
                              report-table">

                    <tr>

                        <th>No</th>
                        <th>Grade</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Students Paid</th>
                        <th>Total Income (Rs)</th>

                    </tr>

<?php

$sql = "

SELECT

    payment.grade,
    payment.subject,

    teachers.name AS teacher_name,

    COUNT(payment.payment_id)
    AS total_students,

    SUM(payment.amount)
    AS total_amount

FROM payment

INNER JOIN classess
ON payment.class_id = classess.class_id

INNER JOIN teachers
ON classess.teacher_id = teachers.teacher_id

WHERE payment.date='$date'
AND payment.status='Paid'

GROUP BY payment.grade,
         payment.subject,
         teachers.name

ORDER BY payment.grade ASC

";

$result = mysqli_query($link, $sql);

if(mysqli_num_rows($result) > 0)
{

    $no = 1;

    while($row = mysqli_fetch_assoc($result))
    {

?>

                    <tr>

                        <td>
                            <?php echo $no++; ?>
                        </td>

                        <td>
                            <?php echo $row['grade']; ?>
                        </td>

                        <td>
                            <?php echo $row['subject']; ?>
                        </td>

                        <td>

                            <?php

                            echo decryptData(
                                $row['teacher_name'],
                                $secret_key
                            );

                            ?>

                        </td>

                        <td>
                            <?php echo $row['total_students']; ?>
                        </td>

                        <td>

                            <?php
                            echo number_format(
                                $row['total_amount'],
                                2
                            );
                            ?>

                        </td>

                    </tr>

<?php

    }

}
else
{

?>

                    <tr>

                        <td colspan="6"
                            style="color:red;
                                   text-align:center;">

                            No Income Records Found

                        </td>

                    </tr>

<?php

}

?>

                </table>

                </div>

                <!-- FOOTER -->

                <div class="footer-sign">

                    <div style="float:left;">

                        <div class="line"></div>

                        <strong>
                            Prepared By
                        </strong>

                    </div>

                    <div style="float:right;">

                        <div class="line"></div>

                        <strong>
                            Authorized Signature
                        </strong>

                    </div>

                    <div style="clear:both;"></div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include('allfoot.php'); ?>