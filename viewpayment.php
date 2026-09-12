<?php
session_start();

if ($_SESSION["uidx"] == "" || $_SESSION["uidx"] == NULL) {
    header('Location:facultylogin');
    exit;
}

$userid = $_SESSION["uidx"];
$fname = $_SESSION["uname"];

include('allhead.php');
include("connection.php");
?>

<div class="container">

<div class="row">
<div class="col-md-12">

<h3>
Welcome :
<a href="welcomeuser.php" style="text-decoration:none;">
<span style="color:#FF0004"><?php echo $fname; ?></span>
</a>
</h3>

<!-- SEARCH FORM -->
<form method="GET">

<div class="row">

<!-- GRADE -->
<div class="col-md-4">
<label><strong>Grade :</strong></label>
<select name="grade" class="form-control">
<option value="">--Select Grade--</option>

<?php
$gq = mysqli_query($link, "SELECT DISTINCT grade FROM classess ORDER BY grade");
while($g = mysqli_fetch_array($gq)) {
?>
<option value="<?php echo $g['grade']; ?>"
<?php if(isset($_GET['grade']) && $_GET['grade']==$g['grade']) echo "selected"; ?>>
<?php echo $g['grade']; ?>
</option>
<?php } ?>

</select>
</div>

<!-- SUBJECT -->
<div class="col-md-4">
<label><strong>Subject :</strong></label>
<select name="subject" class="form-control">
<option value="">--Select Subject--</option>

<?php
$sq = mysqli_query($link, "SELECT DISTINCT subject FROM classess ORDER BY subject");
while($s = mysqli_fetch_array($sq)) {
?>
<option value="<?php echo $s['subject']; ?>"
<?php if(isset($_GET['subject']) && $_GET['subject']==$s['subject']) echo "selected"; ?>>
<?php echo $s['subject']; ?>
</option>
<?php } ?>

</select>
</div>

<!-- MONTH -->
<div class="col-md-4">
<label><strong>Month :</strong></label>
<select name="month" class="form-control">
<option value="">--Select Month--</option>

<?php
$months = [
"January","February","March","April","May","June",
"July","August","September","October","November","December"
];

foreach($months as $m) {
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

<button type="submit" class="btn btn-primary">
Search
</button>

</form>

<br>

<?php
if(isset($_GET['grade'], $_GET['subject'], $_GET['month'])) {

$grade = mysqli_real_escape_string($link, $_GET['grade']);
$subject = mysqli_real_escape_string($link, $_GET['subject']);
$month = mysqli_real_escape_string($link, $_GET['month']);

if($grade != "" && $subject != "" && $month != "") {

$sql = "SELECT 
p.payment_id,
p.s_id,
p.name,
p.grade,
p.subject,
p.month,
p.amount,
p.reason,
p.date,
r.receipt_no
FROM payment p
LEFT JOIN receipt r ON p.payment_id = r.payment_id
WHERE p.grade='$grade'
AND p.subject='$subject'
AND p.month='$month'
ORDER BY p.s_id DESC";

$result = mysqli_query($link, $sql);

echo "<h2 class='page-header'>Payment Details</h2>";

echo "<div class='table-responsive'>
<table class='table table-striped table-bordered'>

<tr class='table-dark'>
<th>Receipt No</th>
<th>Student ID</th>
<th>Name</th>
<th>Grade</th>
<th>Subject</th>
<th>Month</th>
<th>Amount</th>
<th>Reason</th>
<th>Date</th>
</tr>";

if(mysqli_num_rows($result) > 0) {

while($row = mysqli_fetch_array($result)) {
?>
<tr>
<td><?php echo $row['receipt_no'] ?? 'Not Generated'; ?></td>
<td><?php echo $row['s_id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['grade']; ?></td>
<td><?php echo $row['subject']; ?></td>
<td><?php echo $row['month']; ?></td>
<td>Rs. <?php echo $row['amount']; ?></td>
<td><?php echo $row['reason']; ?></td>
<td><?php echo $row['date']; ?></td>
</tr>
<?php
}

} else {
echo "<tr>
<td colspan='9' class='text-center text-danger'>
No Payment Records Found
</td>
</tr>";
}

echo "</table></div>";
}
}
?>

<br>

<a href="payment.php">
<input type="button" value="Back" class="btn btn-warning">
</a>

</div>
</div>
</div>

<?php include('allfoot.php'); ?>