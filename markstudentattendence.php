<?php
session_start();

if ($_SESSION["uidx"] == "" || $_SESSION["uidx"] == NULL) {
	header('Location:userLogin');
}

$name = $_SESSION["uname"];

include("connection.php");

/* SAVE ATTENDANCE */
if(isset($_POST['save_attendance']))
{
	$s_id     = mysqli_real_escape_string($link, $_POST['s_id']);
	$class_id = mysqli_real_escape_string($link, $_POST['class_id']);
	$date     = mysqli_real_escape_string($link, $_POST['date']);
	$status   = mysqli_real_escape_string($link, $_POST['status']);

	$check = mysqli_query($link,
		"SELECT * FROM attendance 
		 WHERE s_id='$s_id' 
		 AND class_id='$class_id' 
		 AND date='$date'"
	);

	if(mysqli_num_rows($check) > 0)
	{
		echo "<script>alert('Attendance Already Marked');</script>";
	}
	else
	{
		$sql = "INSERT INTO attendance(s_id,class_id,date,status)
				VALUES('$s_id','$class_id','$date','$status')";

		if(mysqli_query($link, $sql))
		{
			echo "<script>alert('Attendance Saved Successfully');</script>";
		}
		else
		{
			echo "<script>alert('Error Saving Attendance');</script>";
		}
	}
}
?>

<?php include('allhead.php'); ?>

<script src="https://unpkg.com/html5-qrcode"></script>

<div class="container">

	<h3>Welcome : <span style="color:red;"><?php echo $name; ?></span></h3>

	<table class="card mb-3">
		<tr>
			<td>
				<input class="form-control" id="student_id" placeholder="Enter Student ID" onkeyup="toggleSearchButton()">
			</td>
		</tr>

		<tr>
			<td>

				<button class="btn btn-success" id="searchBtn" type="button" disabled onclick="loadStudent()">
					Search
				</button>

				<button class="btn btn-primary" type="button" onclick="startScanner()">
					Scan QR
				</button>

				<a href="welcomeUser.php">
					<button class="btn btn-warning">Back</button>
				</a>

			</td>
		</tr>
	</table>

	<!-- CAMERA VIEW -->
	<div id="reader" style="width:100%; max-width:350px;"></div>

	<form method="POST">

	<div id="studentData" style="display:none; margin-top:20px;">

		<input type="hidden" name="s_id" id="s_id">

		<table class="table table-bordered">

			<tr>
				<th>Student Name</th>
				<td id="show_name"></td>
			</tr>

			<tr>
				<th>Parent Name</th>
				<td id="show_pname"></td>
			</tr>

			<tr>
				<th>School</th>
				<td id="show_school"></td>
			</tr>

			<tr>
				<th>Class</th>
				<td>
					<select name="class_id" id="class_id" class="form-control" required>
						<option value="">Select Class</option>
					</select>
				</td>
			</tr>

			<tr>
				<th>Date</th>
				<td>
					<input type="date" name="date" class="form-control"
						   value="<?php echo date('Y-m-d'); ?>" required>
				</td>
			</tr>

			<tr>
				<th>Attendance</th>
				<td>
					<select name="status" class="form-control" required>
						<option value="">Select</option>
						<option value="Present">Present</option>
						<option value="Absent">Absent</option>
					</select>
				</td>
			</tr>

		</table>

	</div>

	<div id="paymentData" style="display:none; margin-top:20px;">

		<h4>Payment Details</h4>

		<div id="paymentStatus"></div>

		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Month</th>
					<th>Year</th>
					<th>Status</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody id="paymentTable"></tbody>
		</table>

		<button type="submit" name="save_attendance" class="btn btn-success">
			Save Attendance
		</button>

	</div>

	</form>

</div>

<script>

function toggleSearchButton()
{
	let val = document.getElementById("student_id").value;
	document.getElementById("searchBtn").disabled = (val.trim() === "");
}

/* LOAD STUDENT */
function loadStudent()
{
	let id = document.getElementById("student_id").value;

	fetch("get_student.php?id=" + id)
	.then(res => res.json())
	.then(data => {

		if(data.success)
		{
			document.getElementById("studentData").style.display = "block";
			document.getElementById("paymentData").style.display = "block";

			document.getElementById("s_id").value = data.s_id;
			document.getElementById("show_name").innerText = data.SName;
			document.getElementById("show_pname").innerText = data.PName;
			document.getElementById("show_school").innerText = data.School;

			let classDropdown = document.getElementById("class_id");
			classDropdown.innerHTML = '<option value="">Select Class</option>';

			data.classes.forEach(cls => {
				classDropdown.innerHTML += `
					<option value="${cls.class_id}">
						${cls.grade} - ${cls.subject}
					</option>
				`;
			});

			loadPayments(id);
		}
		else
		{
			alert("Student Not Found");
		}
	});
}

/* LOAD PAYMENTS */
function loadPayments(id)
{
	fetch("get_payment.php?id=" + id)
	.then(res => res.json())
	.then(p => {

		let table = "";

		p.payments.forEach(row => {
			table += `
				<tr>
					<td>${row.month}</td>
					<td>${row.year}</td>
					<td>${row.status}</td>
					<td>${row.amount}</td>
				</tr>
			`;
		});

		document.getElementById("paymentTable").innerHTML = table;

		document.getElementById("paymentStatus").innerHTML =
			p.paid_this_month
			? "✅ Paid for " + p.current_month
			: "❌ Not Paid for " + p.current_month;

		document.getElementById("paymentStatus").style.color =
			p.paid_this_month ? "green" : "red";
	});
}

/* =========================
   ✅ MOBILE QR SCANNER FIX
========================= */

let html5QrCode;

function startScanner()
{
	html5QrCode = new Html5Qrcode("reader");

	const config = {
		fps: 10,
		qrbox: 250
	};

	Html5Qrcode.getCameras()
	.then(devices => {

		if (!devices || devices.length === 0)
		{
			alert("No camera found");
			return;
		}

		// ✅ FIND BACK CAMERA (BEST MOBILE FIX)
		let cameraId = devices[0].id;

		for (let i = 0; i < devices.length; i++)
		{
			if (devices[i].label.toLowerCase().includes("back") ||
				devices[i].label.toLowerCase().includes("rear"))
			{
				cameraId = devices[i].id;
				break;
			}
		}

		html5QrCode.start(
			cameraId,
			config,
			(decodedText) => {

				document.getElementById("student_id").value = decodedText;

				toggleSearchButton();
				loadStudent();

				html5QrCode.stop();
			},
			(errorMessage) => {
				// ignore scan errors
			}
		)
		.catch(err => {
			alert("Camera Start Error: " + err);
		});

	})
	.catch(err => {
		alert("Camera Permission Error: " + err);
	});
}

</script>

<?php include('allfoot.php'); ?>