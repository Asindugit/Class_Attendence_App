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

$secret_key = "my_secret_key_12345";

/* DECRYPT FUNCTION */
function decryptData($data, $key)
{
    $decoded = base64_decode($data);

    if (strpos($decoded, '::') === false) {
        return $data; // fallback if not encrypted
    }

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

/* (YOUR EXISTING CSS - NO CHANGE) */

body{
    background:#f4f6f9;
}

.report-container{
    background:white;
    padding:40px;
    border-radius:10px;
    box-shadow:0px 0px 15px rgba(0,0,0,0.1);
}

.report-header{
    text-align:center;
    border-bottom:3px solid #000;
    padding-bottom:15px;
    margin-bottom:25px;
}

.report-header h1{
    font-size:34px;
    font-weight:bold;
}

.report-header h4{
    color:#666;
}

.summary-box{
    background:#198754;
    color:white;
    padding:25px;
    border-radius:10px;
    text-align:center;
    margin-bottom:25px;
}

.summary-box h2{
    font-size:40px;
}

.report-table th{
    background:#212529;
    color:white;
    text-align:center;
}

.report-table td{
    text-align:center;
    vertical-align:middle;
}

.footer-sign{
    margin-top:60px;
}

.footer-sign .line{
    border-top:1px solid black;
    width:250px;
    margin-top:60px;
}

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

<!-- SEARCH FORM -->
<div class="card mb-4 no-print">

<div class="card-header bg-dark text-white">
<h4>Subject Monthly Income Summary Report</h4>
</div>

<div class="card-body">

<form method="GET">

<!-- (YOUR FILTERS - NO CHANGE) -->
<div class="row">

<!-- GRADE -->
<div class="col-md-3">

<label><strong>Grade</strong></label>

<select name="grade" class="form-control">

<option value="">-- Select Grade --</option>

<option value="ALL"
<?php if(isset($_GET['grade']) && $_GET['grade']=="ALL") echo "selected"; ?>>
ALL GRADES
</option>

<?php
$grade_query = mysqli_query($link,"SELECT DISTINCT grade FROM classess ORDER BY grade ASC");
while($g = mysqli_fetch_assoc($grade_query))
{
?>
<option value="<?php echo $g['grade']; ?>"
<?php if(isset($_GET['grade']) && $_GET['grade']==$g['grade']) echo "selected"; ?>>
<?php echo $g['grade']; ?>
</option>
<?php } ?>

</select>
</div>

<!-- SUBJECT / YEAR / MONTH (UNCHANGED) -->
<div class="col-md-3">
<label><strong>Subject</strong></label>
<select name="subject" class="form-control">

<option value="">-- Select Subject --</option>

<?php
$subject_query = mysqli_query($link,"SELECT DISTINCT subject FROM classess ORDER BY subject ASC");
while($s = mysqli_fetch_assoc($subject_query))
{
?>
<option value="<?php echo $s['subject']; ?>"
<?php if(isset($_GET['subject']) && $_GET['subject']==$s['subject']) echo "selected"; ?>>
<?php echo $s['subject']; ?>
</option>
<?php } ?>

</select>
</div>

<div class="col-md-3">
<label><strong>Year</strong></label>
<select name="year" class="form-control">

<option value="">-- Select Year --</option>

<?php for($y=date("Y"); $y>=2020; $y--) { ?>
<option value="<?php echo $y; ?>"
<?php if(isset($_GET['year']) && $_GET['year']==$y) echo "selected"; ?>>
<?php echo $y; ?>
</option>
<?php } ?>

</select>
</div>

<div class="col-md-3">
<label><strong>Month</strong></label>
<select name="month" class="form-control">

<option value="">-- Select Month --</option>

<?php
$months = ["January","February","March","April","May","June",
"July","August","September","October","November","December"];

foreach($months as $m)
{
?>
<option value="<?php echo $m; ?>"
<?php if(isset($_GET['month']) && $_GET['month']==$m) echo "selected"; ?>>
<?php echo $m; ?>
</option>
<?php } ?>

</select>
</div>

</div>

<br>

<button type="submit" class="btn btn-primary">Generate Report</button>
<button type="button" onclick="window.print();" class="btn btn-success">Print A4 Report</button>
<a href="income.php"><input type="button" value="Back" class="btn btn-warning"></a>

</form>

</div>
</div>

<?php

$where = " WHERE payment.status='Paid' ";

if(isset($_GET['grade']) && $_GET['grade']!="" && $_GET['grade']!="ALL")
{
    $grade = mysqli_real_escape_string($link,$_GET['grade']);
    $where .= " AND payment.grade='$grade' ";
}

if(isset($_GET['subject']) && $_GET['subject']!="")
{
    $subject = mysqli_real_escape_string($link,$_GET['subject']);
    $where .= " AND payment.subject='$subject' ";
}

if(isset($_GET['year']) && $_GET['year']!="")
{
    $year = mysqli_real_escape_string($link,$_GET['year']);
    $where .= " AND payment.year='$year' ";
}

if(isset($_GET['month']) && $_GET['month']!="")
{
    $month = mysqli_real_escape_string($link,$_GET['month']);
    $where .= " AND payment.month='$month' ";
}

/* TOTAL */
$total_sql = mysqli_query($link,"
    SELECT SUM(amount) as total_income
    FROM payment
    $where
");

$total_row = mysqli_fetch_assoc($total_sql);
$total_income = $total_row['total_income'] ?? 0;

?>

<div class="report-container">

<div class="report-header">
<h1>ASD EDUCATION CENTRE</h1>
<h4>Subject Monthly Income Summary Report</h4>
</div>

<table width="100%" class="mb-4">

<tr>
<td><strong>Grade :</strong> <?php echo $_GET['grade'] ?? "All"; ?></td>
<td style="text-align:end;"><strong>Subject :</strong> <?php echo $_GET['subject'] ?? "All"; ?></td>
</tr>

<tr>
<td><strong>Month :</strong> <?php echo $_GET['month'] ?? "All"; ?></td>
<td style="text-align:end;"><strong>Year :</strong> <?php echo $_GET['year'] ?? "All"; ?></td>
</tr>

</table>

<div class="summary-box">
<h4>TOTAL INCOME</h4>
<h2>Rs. <?php echo number_format($total_income,2); ?></h2>
</div>

<div class="table-responsive">

<table class="table table-bordered report-table">

<tr>
<th>No</th>
<th>Grade</th>
<th>Subject</th>
<th>Teacher</th>
<th>Month</th>
<th>Year</th>
<th>Total Students Paid</th>
<th>Total Income</th>
</tr>

<?php

$sql = "

SELECT
    payment.grade,
    payment.subject,
    payment.month,
    payment.year,
    classess.teacher_id,
    COUNT(payment.payment_id) AS total_students,
    SUM(payment.amount) AS total_amount

FROM payment

INNER JOIN classess
ON payment.class_id = classess.class_id

$where

GROUP BY
    payment.grade,
    payment.subject,
    payment.month,
    payment.year,
    classess.teacher_id

ORDER BY payment.year DESC
";

$result = mysqli_query($link,$sql);

$no = 1;

if(mysqli_num_rows($result) > 0)
{
    while($row = mysqli_fetch_assoc($result))
    {

        /* GET TEACHER NAME (ENCRYPTED) */
        $tid = $row['teacher_id'];

        $tq = mysqli_query($link,"SELECT name FROM teachers WHERE teacher_id='$tid'");
        $trow = mysqli_fetch_assoc($tq);

        $teacher_name = decryptData($trow['name'], $secret_key);

?>

<tr>
<td><?php echo $no++; ?></td>
<td><?php echo $row['grade']; ?></td>
<td><?php echo $row['subject']; ?></td>
<td><?php echo $teacher_name; ?></td>
<td><?php echo $row['month']; ?></td>
<td><?php echo $row['year']; ?></td>
<td><?php echo $row['total_students']; ?></td>
<td>Rs. <?php echo number_format($row['total_amount'],2); ?></td>
</tr>

<?php
    }
}
else
{
?>

<tr>
<td colspan="8" style="color:red;text-align:center;">
No Income Records Found
</td>
</tr>

<?php } ?>

</table>

</div>

<div class="footer-sign">

<div style="float:left;">
<div class="line"></div>
<strong>Prepared By</strong>
</div>

<div style="float:right;">
<div class="line"></div>
<strong>Authorized Signature</strong>
</div>

<div style="clear:both;"></div>

</div>

</div>

</div>

</div>

</div>

<?php include('allfoot.php'); ?>