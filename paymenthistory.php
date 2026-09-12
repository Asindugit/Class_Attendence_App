<?php
session_start();

if ($_SESSION["uidx"] == "" || $_SESSION["uidx"] == NULL) {
	header('Location:fuserLogin');
}

include('allhead.php');
include_once("connection.php");

$student = "";
$payments = [];
$subjects = [];
$grade = "";

/* =========================
   SEARCH STUDENT
========================= */

if (isset($_POST['search_student']) || isset($_POST['subject_filter'])) {

	$search = mysqli_real_escape_string($link, $_POST['search']);

	$query = "SELECT * FROM student
			  WHERE s_id='$search'
			  OR qr_code='$search'";

	$result = mysqli_query($link, $query);

	if (mysqli_num_rows($result) > 0) {

		$student = mysqli_fetch_assoc($result);
		$sid = $student['s_id'];

		/* =========================
		   GET REGISTERED SUBJECTS + GRADE
		========================= */

		$sub_q = "SELECT c.grade, c.subject, c.class_id
				  FROM student_classes sc
				  INNER JOIN classess c ON sc.class_id = c.class_id
				  WHERE sc.s_id='$sid'";

		$sub_res = mysqli_query($link, $sub_q);

		while($s = mysqli_fetch_assoc($sub_res)){
			$subjects[] = $s;
		}

		// FIX: GET GRADE PROPERLY
		$gq = "SELECT c.grade
			   FROM student_classes sc
			   INNER JOIN classess c ON sc.class_id = c.class_id
			   WHERE sc.s_id='$sid'
			   LIMIT 1";

		$gr = mysqli_query($link, $gq);

		if($row = mysqli_fetch_assoc($gr)){
			$grade = $row['grade'];
		}

		/* =========================
		   PAYMENT HISTORY
		========================= */

		$subject_filter = isset($_POST['subject_filter']) ? mysqli_real_escape_string($link, $_POST['subject_filter']) : '';

		$payment_query = "SELECT * FROM payment
						  WHERE s_id='$sid'";

		if($subject_filter != ""){
			$payment_query .= " AND subject='$subject_filter'";
		}

		$payment_query .= " ORDER BY year DESC,
		FIELD(month,
		'January','February','March','April','May','June',
		'July','August','September','October','November','December') DESC";

		$payment_result = mysqli_query($link, $payment_query);

		while($row = mysqli_fetch_assoc($payment_result)){
			$payments[] = $row;
		}

	} else {

		echo "<div class='alert alert-danger mt-3'>Student not found</div>";
	}
}
?>

<style>
#reader{
	width:300px;
	display:none;
	margin-top:10px;
}

.receipt{
	width:80mm;
	margin:auto;
	padding:10px;
	font-family:Arial;
	font-size:12px;
	color:#000;
}

.center{text-align:center;}
.line{border-top:1px dashed #000;margin:8px 0;}

.receipt table{
	width:100%;
	font-size:12px;
}

.receipt td{
	padding:2px 0;
}

.amount{
	font-size:18px;
	font-weight:bold;
}

@media print{
	body *{visibility:hidden;}
	#printReceiptArea, #printReceiptArea *{visibility:visible;}
	#printReceiptArea{
		position:absolute;
		left:0;
		top:0;
		width:80mm;
	}
}
</style>

<div class="container mt-4">

	<h3 class="text-center mb-4">Payment History System</h3>

	<!-- SEARCH -->
	<div class="card mb-3">
		<div class="card-body">

			<form method="post">

				<label>Search Student ID / QR</label>

				<div class="input-group">

					<input type="text" name="search" id="search" class="form-control">

					<button type="submit" name="search_student" id="searchBtn" class="btn btn-primary" disabled>
						Search
					</button>

					<button type="button" class="btn btn-success" onclick="startScanner()">
						Scan QR
					</button>

				</div>

			</form>
			

			<div id="reader"></div>

		</div>
	</div>

<?php if($student != "") { ?>

	<!-- STUDENT INFO -->
	<div class="card mb-3">
		<div class="card-body">

			<div class="row">

				<div class="col-md-3">
					<label>Student ID</label>
					<input class="form-control" value="<?php echo $student['s_id']; ?>" readonly>
				</div>

				<div class="col-md-3">
					<label>Name</label>
					<input class="form-control" value="<?php echo $student['SName']; ?>" readonly>
				</div>

				<div class="col-md-3">
					<label>Grade</label>
					<input class="form-control" value="<?php echo $grade; ?>" readonly>
				</div>

			</div>

		</div>
	</div>

	<!-- SUBJECT FILTER -->
	<div class="card mb-3" style="margin-top: 1.5rem;">
		<div class="card-body">

			<form method="post">
				<input type="hidden" name="search" value="<?php echo $student['s_id']; ?>">

				<label><b>Select Subject</b></label>

				<select name="subject_filter" class="form-control" onchange="this.form.submit()">

					<option value="">All Subjects</option>

					<?php foreach($subjects as $sub){ ?>
						<option value="<?php echo $sub['subject']; ?>"
						<?php if(isset($_POST['subject_filter']) && $_POST['subject_filter']==$sub['subject']) echo "selected"; ?>>
							<?php echo $sub['grade']." - ".$sub['subject']; ?>
						</option>
					<?php } ?>

				</select>

			</form>

		</div>
	</div>

	<!-- PAYMENT HISTORY -->
	<div class="card">
		<div class="card-body" style="margin-top: 1.5rem;">

			<h4><strong>Payment History</strong></h4>

			<div class="table-responsive">

				<table class="table table-bordered">

					<tr class="table-dark">
						<th>Receipt</th>
						<th>Month</th>
						<th>Year</th>
						<th>Subject</th>
						<th>Amount</th>
						<th>Date</th>
						<th>Print</th>
					</tr>

					<?php if(count($payments) > 0){ ?>

						<?php foreach($payments as $pay){ ?>

						<tr>
							<td><?php echo $pay['receipt_no']; ?></td>
							<td><?php echo $pay['month']; ?></td>
							<td><?php echo $pay['year']; ?></td>
							<td><?php echo $pay['subject']; ?></td>
							<td>Rs. <?php echo number_format($pay['amount'],2); ?></td>
							<td><?php echo $pay['date']; ?></td>

							<td>
								<button class="btn btn-primary btn-sm"
								onclick="printReceipt(
									'<?php echo $pay['receipt_no']; ?>',
									'<?php echo $pay['s_id']; ?>',
									'<?php echo $pay['name']; ?>',
									'<?php echo $grade; ?>',
									'<?php echo $pay['subject']; ?>',
									'<?php echo $pay['month']; ?>',
									'<?php echo $pay['year']; ?>',
									'<?php echo $pay['amount']; ?>',
									'<?php echo $pay['status']; ?>',
									'<?php echo $pay['date']; ?>'
								)">
									Print
								</button>
							</td>

						</tr>

						<?php } ?>

					<?php } else { ?>

						<tr>
							<td colspan="7" class="text-center text-danger">
								No Payment History Found
							</td>
						</tr>

					<?php } ?>

				</table>
				<a href="payment.php"><button class="btn btn-warning">Back</button></a>

			</div>

		</div>
	</div>

<?php } ?>

</div>

<!-- PRINT AREA -->
<div id="printReceiptArea" style="display:none;"></div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
const searchInput = document.getElementById("search");
const searchBtn = document.getElementById("searchBtn");

searchInput.addEventListener("input", function(){
	searchBtn.disabled = this.value.trim().length === 0;
});

let html5QrCode;

function startScanner()
{
	document.getElementById("reader").style.display = "block";

	html5QrCode = new Html5Qrcode("reader");

	Html5Qrcode.getCameras().then(devices => {

		let cameraId = devices[0].id;

		html5QrCode.start(
			cameraId,
			{ fps: 10, qrbox: 250 },
			function(decodedText){

				document.getElementById('search').value = decodedText;
				searchBtn.disabled = false;

				html5QrCode.stop().then(() => {
					document.getElementById("reader").style.display = "none";
					document.getElementById("searchBtn").click();
				});
			}
		);

	});
}

function printReceipt(receipt_no,sid,name,grade,subject,month,year,amount,status,date)
{
	let html = `
	<div class="receipt">

		<div class="center">
			<h3>ASD Education Centre</h3>
			<p>Payment Receipt</p>
		</div>

		<div class="line"></div>

		<table>
			<tr><td>Receipt</td><td>${receipt_no}</td></tr>
			<tr><td>Date</td><td>${date}</td></tr>
		</table>

		<div class="line"></div>

		<table>
			<tr><td>ID</td><td>${sid}</td></tr>
			<tr><td>Name</td><td>${name}</td></tr>
			<tr><td>Grade</td><td>${grade}</td></tr>
			<tr><td>Subject</td><td>${subject}</td></tr>
			<tr><td>Month</td><td>${month}</td></tr>
			<tr><td>Year</td><td>${year}</td></tr>
			<tr><td>Status</td><td>${status}</td></tr>
		</table>

		<div class="line"></div>

		<table>
			<tr>
				<td><b>Amount</b></td>
				<td style="text-align:right;">Rs. ${parseFloat(amount).toFixed(2)}</td>
			</tr>
		</table>

		<div class="center">
			<p>Thank You</p>
		</div>

	</div>`;

	document.getElementById("printReceiptArea").innerHTML = html;
	document.getElementById("printReceiptArea").style.display = "block";
	window.print();
	document.getElementById("printReceiptArea").style.display = "none";
}
</script>

<?php include('allfoot.php'); ?>