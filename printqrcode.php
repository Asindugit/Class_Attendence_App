<?php
session_start();

if ($_SESSION["uidx"] == "" || $_SESSION["uidx"] == NULL) {
	header('Location:userLogin');
}

$userid = $_SESSION["uidx"];
$fname  = $_SESSION["uname"];

include('allhead.php');
include('connection.php');
?>

<style>

body{
	background:#f4f6f9;
}

.card-box{
	background:#fff;
	padding:20px;
	border-radius:10px;
	box-shadow:0 2px 10px rgba(0,0,0,0.08);
	margin-top:20px;
}

.print-area{
	display:none;
	width:80mm;
	margin:20px auto;
	padding:10px;
	text-align:center;
	background:#fff;
    border-radius: 10px;
}

.qr-box{
	display:flex;
	justify-content:center;
	margin:10px 0;
}

.duplicate{
	font-size:14px;
	margin-bottom:5px;
}

@media print{

	body *{
		visibility:hidden;
	}

	.print-area, .print-area *{
		visibility:visible;
	}

	.print-area{
		position:absolute;
		left:0;
		top:0;
		width:80mm;
	}

	/* hide button in print */
	.print-area button{
		display:none !important;
	}
}

.print-btn{
	padding:5px 10px;
	font-size:13px;
}

</style>

<div class="container-fluid">

	<div class="row">

		<div class="col-md-12">

			<div class="card-box">

				<h3>Welcome : <?php echo $fname; ?></h3>

				<hr>

				<form method="GET">

					<div class="row">

						<div class="col-md-6">

							<label>Grade</label>

							<select name="grade" class="form-control" required>

								<option value="">-- Select Grade --</option>

								<?php
								$gq = mysqli_query($link,"SELECT DISTINCT grade FROM classess");
								while($g = mysqli_fetch_array($gq)){
									$sel = (isset($_GET['grade']) && $_GET['grade']==$g['grade']) ? "selected" : "";
									echo "<option value='{$g['grade']}' $sel>{$g['grade']}</option>";
								}
								?>

							</select>

						</div>

						<div class="col-md-6">

							<label>Subject</label>

							<select name="subject" class="form-control" required>

								<option value="">-- Select Subject --</option>

								<?php
								$sq = mysqli_query($link,"SELECT DISTINCT subject FROM classess");
								while($s = mysqli_fetch_array($sq)){
									$sel = (isset($_GET['subject']) && $_GET['subject']==$s['subject']) ? "selected" : "";
									echo "<option value='{$s['subject']}' $sel>{$s['subject']}</option>";
								}
								?>

							</select>

						</div>

					</div>

					<br>

					<button class="btn btn-primary">
						Search
					</button>
                    

				</form>

			</div>

		</div>

	</div>

	<?php
	if(!empty($_GET['grade']) && !empty($_GET['subject'])){

		$grade   = $_GET['grade'];
		$subject = $_GET['subject'];

		$sql = "SELECT 
					s.s_id,
					s.SName,
					c.grade,
					c.subject,
					s.Register_date
				FROM student s
				INNER JOIN student_classes sc ON s.s_id = sc.s_id
				INNER JOIN classess c ON sc.class_id = c.class_id
				WHERE c.grade='$grade'
				AND c.subject='$subject'";

		$result = mysqli_query($link,$sql);
	?>

	<div class="card-box">

		<h4>Student Details</h4>

		<div class="table-responsive">

		<table class="table table-bordered">

			<tr>
				<th>Student ID</th>
				<th>Name</th>
				<th>Grade</th>
				<th>Subject</th>
				<th>Registered Date</th>
				<th>Action</th>
			</tr>

			<?php while($row = mysqli_fetch_array($result)){ ?>

			<tr>
				<td><?php echo $row['s_id']; ?></td>
				<td><?php echo $row['SName']; ?></td>
				<td><?php echo $row['grade']; ?></td>
				<td><?php echo $row['subject']; ?></td>
				<td><?php echo $row['Register_date']; ?></td>

				<td>
					<button class="btn btn-success print-btn"
						onclick="showPreview(
							'<?php echo $row['s_id']; ?>',
							'<?php echo htmlspecialchars($row['SName'],ENT_QUOTES); ?>',
							'<?php echo $row['Register_date']; ?>'
						)">
						Print QR
					</button>
				</td>

			</tr>

			<?php } ?>

		</table>

		</div>
        <a href="welcomeuser.php"><button class="btn btn-warning">Back</button></a>
	</div>
    

	<?php } ?>

	<!-- PRINT AREA -->
	<div id="printArea" class="print-area">

		<h3><strong>Student QR Card</strong></h3>

		<div class="duplicate">DUPLICATE</div>

		<div id="qrcode" class="qr-box"></div>

		<p>Student ID: <span id="p_id"></span></p>
		<p>Name: <span id="p_name"></span></p>
		<p>Date: <span id="p_date"></span></p>

		<button onclick="printCard()" class="btn btn-primary" style="margin-top:10px;">
			Print Now
		</button>

	</div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>

function showPreview(id, name, date){

	document.getElementById("printArea").style.display = "block";

	document.getElementById("p_id").innerText = id;
	document.getElementById("p_name").innerText = name;
	document.getElementById("p_date").innerText = date;

	document.getElementById("qrcode").innerHTML = "";

	new QRCode(document.getElementById("qrcode"), {
		text: id,
		width: 160,
		height: 160
	});

	window.scrollTo(0, document.body.scrollHeight);
}

function printCard(){
	window.print();
}
window.onafterprint = function () {
	window.location.href = "managestudent.php";
};

</script>

<?php include('allfoot.php'); ?>