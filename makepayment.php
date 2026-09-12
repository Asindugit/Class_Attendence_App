
<?php
session_start();

if ($_SESSION["uidx"] == "" || $_SESSION["uidx"] == NULL) {
	header('Location:fuserLogin');
}

include('allhead.php');
include_once("connection.php");

$student = "";
$class_result = null;
$message = "";

/* =========================
   SEARCH STUDENT
========================= */

if (isset($_POST['search_student'])) {

	$search = mysqli_real_escape_string($link, $_POST['search']);

	$query = "SELECT * FROM student
			  WHERE s_id='$search'
			  OR qr_code='$search'";

	$result = mysqli_query($link, $query);

	if (mysqli_num_rows($result) > 0) {

		$student = mysqli_fetch_assoc($result);

		$class_query = "SELECT c.class_id, c.grade, c.subject, c.monthly_fee
						FROM student_classes sc
						JOIN classess c ON sc.class_id = c.class_id
						WHERE sc.s_id='$search'";

		$class_result = mysqli_query($link, $class_query);

	} else {
		$message = "<div class='alert alert-danger mt-3'>Student not found.</div>";
	}
}

/* =========================
   SAVE PAYMENT + RECEIPT
========================= */

if (isset($_POST['save_payment'])) {

	$s_id     = $_POST['s_id'];
	$class_id = $_POST['class_id'];
	$date     = $_POST['date'];
	$amount   = $_POST['amount'];
	$month    = $_POST['month'];
	$year     = $_POST['year'];
	$status   = $_POST['status'];

	$reason = NULL;

	if (isset($_POST['reason_enable'])) {
		$reason = $_POST['reason'];
		if ($reason == "") $reason = NULL;
	}

	/* GET CLASS DATA */
	$sub_q = mysqli_query($link, "SELECT grade, subject FROM classess WHERE class_id='$class_id'");
	$sub = mysqli_fetch_assoc($sub_q);

	$grade   = $sub['grade'];
	$subject = $sub['subject'];

	/* GET STUDENT NAME */
	$stu_q = mysqli_query($link, "SELECT SName FROM student WHERE s_id='$s_id'");
	$stu = mysqli_fetch_assoc($stu_q);
	$name = $stu['SName'];

	/* DUPLICATE CHECK */
	$check_sql = "SELECT payment_id FROM payment 
	WHERE s_id='$s_id'
	AND class_id='$class_id'
	AND month='$month'
	AND year='$year'
	LIMIT 1";

	$check_result = mysqli_query($link, $check_sql);

	if (mysqli_num_rows($check_result) > 0) {

		$message = "<div class='alert alert-warning mt-3'>
		❌ Already paid for <b>$subject</b> in <b>$month $year</b>
		</div>";

	} else {

		$receipt_no = "R" . date("YmdHis") . rand(100,999);

		mysqli_begin_transaction($link);

		try {

			/* PAYMENT */
			$payment_sql = "INSERT INTO payment
			(s_id,class_id,name,grade,subject,date,amount,reason,month,year,status,receipt_no)
			VALUES
			('$s_id','$class_id','$name','$grade','$subject','$date','$amount','$reason','$month','$year','Paid','$receipt_no')";

			if (!mysqli_query($link, $payment_sql)) {
				throw new Exception("Payment insert failed");
			}

			$payment_id = mysqli_insert_id($link);

			/* RECEIPT */
			$receipt_sql = "INSERT INTO receipt
			(payment_id,receipt_no,s_id,class_id,name,grade,subject,amount,month,year,payment_date,status)
			VALUES
			('$payment_id','$receipt_no','$s_id','$class_id','$name','$grade','$subject','$amount','$month','$year',NOW(),'Paid')";

			if (!mysqli_query($link, $receipt_sql)) {
				throw new Exception("Receipt insert failed");
			}

			mysqli_commit($link);

?>

<script>
function printReceipt()
{
	let printContents = document.getElementById("receipt").innerHTML;
	let original = document.body.innerHTML;

	document.body.innerHTML = printContents;
	window.print();
	document.body.innerHTML = original;

	window.location.href = "makepayment.php";
}
</script>

<div id="receipt">

<style>
.receipt{
	width:80mm;
	margin:auto;
	font-family:Arial;
	font-size:12px;
	padding:10px;
}
.center{text-align:center;}
.line{border-top:1px dashed #000;margin:6px 0;}
table{width:100%;font-size:12px;}
td{padding:2px 0;}
.amount{font-size:16px;font-weight:bold;}

@media print{
	@page{size:80mm auto;margin:0;}
	body{width:80mm;}
}
</style>

<div class="receipt">

<div class="center">
<h3>ASD Education Centre</h3>
<p>Payment Receipt</p>
</div>

<div class="line"></div>

<table>
<tr><td>Receipt No</td><td><?php echo $receipt_no; ?></td></tr>
<tr><td>Date</td><td><?php echo date("Y-m-d H:i"); ?></td></tr>
</table>

<div class="line"></div>

<table>
<tr><td>ID</td><td><?php echo $s_id; ?></td></tr>
<tr><td>Name</td><td><?php echo $name; ?></td></tr>
<tr><td>Grade</td><td><?php echo $grade; ?></td></tr>
<tr><td>Subject</td><td><?php echo $subject; ?></td></tr>
<tr><td>Month</td><td><?php echo $month; ?></td></tr>
<tr><td>Year</td><td><?php echo $year; ?></td></tr>
</table>

<div class="line"></div>

<table>
<tr>
<td class="amount">Amount</td>
<td class="amount" style="text-align:right;">
Rs. <?php echo number_format($amount,2); ?>
</td>
</tr>
</table>

<div class="line"></div>

<div class="center">
<p>Thank You</p>
</div>

</div>
</div>

<div class="text-center mt-3 no-print">
<button onclick="printReceipt()" class="btn btn-primary">
Print Receipt
</button>
</div>

<?php

		} catch (Exception $e) {
			mysqli_rollback($link);
			$message = "<div class='alert alert-danger'>".$e->getMessage()."</div>";
		}
	}
}
?>

<!-- ================= UI ================= -->

<style>
#reader{
	width:300px;
	max-width:100%;
	display:none;
	margin-top:10px;
}

#stopCameraBtn{
	display:none;
	margin-top:10px;
}

@media(max-width:576px){

	#reader{
		width:100%;
		max-width:350px;
		margin-left:auto;
		margin-right:auto;
	}

}
</style>

<div class="container mt-4">

<h2 class="text-center">Student Payment System</h2>

<?php if($message != "") echo $message; ?>

<!-- SEARCH -->
<div class="card mb-3">
<div class="card-body">

<form method="post">

<input
	type="text"
	name="search"
	id="search"
	class="form-control mb-3"
	placeholder="Enter Student ID"
>

<button
	name="search_student"
	id="searchBtn"
	class="btn btn-primary"
	disabled
>
Search
</button>

<button
	type="button"
	class="btn btn-success"
	onclick="startScanner()"
>
Scan QR
</button>

<!-- STOP CAMERA BUTTON -->
<button
	type="button"
	id="stopCameraBtn"
	class="btn btn-danger"
	onclick="stopScanner()"
>
Stop Camera
</button>

</form>

<a href="payment.php">
<button class="btn btn-warning">Back</button>
</a>

<div id="reader"></div>

</div>
</div>

<?php if($student!=""){ ?>

<!-- PAYMENT FORM -->
<div class="card">
<div class="card-body">

<form method="post">

<input type="hidden" name="s_id" value="<?php echo $student['s_id']; ?>">

<div class="row">

<div class="col-md-6 mb-2">
<label>Name</label>
<input class="form-control" value="<?php echo $student['SName']; ?>" readonly>
</div>

<div class="col-md-6 mb-2">
<label>Select Subject</label>

<select
	name="class_id"
	id="class_id"
	class="form-control"
	required
	onchange="setAmount()"
>

<option value="">-- Select Subject --</option>

<?php while($row = mysqli_fetch_assoc($class_result)) { ?>

<option
	value="<?php echo $row['class_id']; ?>"
	data-fee="<?php echo $row['monthly_fee']; ?>"
>
	<?php echo $row['grade']." - ".$row['subject']; ?>
</option>

<?php } ?>

</select>

</div>

<div class="col-md-6 mb-2">
<label>Date</label>
<input
	type="date"
	name="date"
	value="<?php echo date('Y-m-d'); ?>"
	class="form-control"
>
</div>

<div class="col-md-6 mb-2">
<label>Amount</label>
<input
	type="number"
	name="amount"
	id="amount"
	class="form-control"
>
</div>

<div class="col-md-6 mb-2">
<label>Month</label>

<select name="month" class="form-control">

<option>--Select Month--</option>

<option>January</option>
<option>February</option>
<option>March</option>
<option>April</option>
<option>May</option>
<option>June</option>
<option>July</option>
<option>August</option>
<option>September</option>
<option>October</option>
<option>November</option>
<option>December</option>

</select>

</div>

<div class="col-md-6 mb-2">
<label>Year</label>

<input
	name="year"
	value="<?php echo date('Y'); ?>"
	class="form-control"
>

</div>

<div class="col-md-6 mb-2">
<label>Status</label>

<select name="status" class="form-control">

<option>Paid</option>
<option>Not Paid</option>

</select>

</div>

<div class="col-md-6 mb-2">

<input
	type="checkbox"
	id="reason_enable"
	onchange="toggleFields()"
>

<label>Enable Edit (Amount + Reason)</label>

<textarea
	name="reason"
	id="reason"
	class="form-control"
	disabled
></textarea>

</div>

</div>

<button name="save_payment" class="btn btn-success">
Save Payment
</button>

</form>

<a href="payment.php">
<button class="btn btn-warning">Back</button>
</a>

</div>
</div>

<?php } ?>

</div>

<!-- ================= SCRIPTS ================= -->

<script src="https://unpkg.com/html5-qrcode"></script>

<script>

const searchInput = document.getElementById("search");
const searchBtn = document.getElementById("searchBtn");

searchInput.oninput = () => searchBtn.disabled = !searchInput.value;


/* =====================================================
   TOGGLE FIELDS
===================================================== */

function toggleFields() {

    let checked =
        document.getElementById("reason_enable").checked;

    let reason =
        document.getElementById("reason");

    let amount =
        document.getElementById("amount");

    reason.disabled = !checked;
    amount.disabled = !checked;

    if (!checked) {

        reason.value = "";

        setAmount();

    }

}


/* =====================================================
   AUTO AMOUNT
===================================================== */

function setAmount() {

    let select =
        document.getElementById("class_id");

    if (!select) {
        return;
    }

    let fee =
        select.options[select.selectedIndex]
        .getAttribute("data-fee");

    let amountField =
        document.getElementById("amount");

    let isManual =
        document.getElementById("reason_enable").checked;

    if (!isManual) {

        amountField.value =
            fee ? fee : "";

    }

}


/* =====================================================
   QR SCANNER
   MOBILE BACK CAMERA + COMPUTER WEBCAM
===================================================== */

let html5QrCode = null;
let scannerRunning = false;


/* =====================================================
   START SCANNER
===================================================== */

function startScanner() {

    if (scannerRunning) {
        return;
    }

    const reader =
        document.getElementById("reader");

    const stopButton =
        document.getElementById("stopCameraBtn");

    reader.style.display = "block";

    stopButton.style.display = "inline-block";


    /*
     * First try the environment camera.
     *
     * On mobile:
     * environment = BACK CAMERA
     *
     * On computer:
     * if environment is not available,
     * fallback will select the webcam.
     */

    html5QrCode =
        new Html5Qrcode("reader");


    html5QrCode.start(

        {
            facingMode: {
                ideal: "environment"
            }
        },

        {
            fps: 10,

            qrbox: {
                width: 250,
                height: 250
            },

            aspectRatio: 1.0

        },

        function(decodedText, decodedResult) {

            /*
             * QR CODE FOUND
             */

            document.getElementById("search").value =
                decodedText;

            searchBtn.disabled = false;


            /*
             * Stop camera first
             */

            stopScanner().then(function() {

                /*
                 * Automatically perform search
                 */

                document
                    .getElementById("searchBtn")
                    .click();

            });

        },

        function(errorMessage) {

            /*
             * Ignore normal scanning errors.
             */

        }

    )

    .then(function() {

        scannerRunning = true;

        stopButton.style.display = "inline-block";

    })

    .catch(function(error) {

        console.log(
            "Environment camera failed:",
            error
        );

        /*
         * If environment camera fails,
         * use getCameras() to find an available
         * webcam/back camera.
         */

        startCameraFallback();

    });

}


/* =====================================================
   CAMERA FALLBACK
   MOBILE + COMPUTER
===================================================== */

function startCameraFallback() {

    /*
     * Clear previous scanner
     */

    if (html5QrCode) {

        try {
            html5QrCode.clear();
        } catch(e) {}

    }


    html5QrCode =
        new Html5Qrcode("reader");


    Html5Qrcode.getCameras()

    .then(function(devices) {

        if (!devices || devices.length === 0) {

            alert("No camera found!");

            hideCamera();

            return;

        }


        /*
         * Find BACK/REAR camera.
         *
         * This is useful after the browser
         * has given camera labels.
         */

        let backCamera = devices.find(function(device) {

            let label =
                (device.label || "").toLowerCase();

            return (
                label.includes("back") ||
                label.includes("rear") ||
                label.includes("environment")
            );

        });


        let cameraId;


        if (backCamera) {

            /*
             * MOBILE:
             * Use the back camera.
             */

            cameraId =
                backCamera.id;

        } else {

            /*
             * COMPUTER:
             * Use the first available webcam.
             *
             * Also works on computers with
             * only one camera.
             */

            cameraId =
                devices[0].id;

        }


        html5QrCode.start(

            cameraId,

            {
                fps: 10,

                qrbox: {
                    width: 250,
                    height: 250
                },

                aspectRatio: 1.0

            },

            function(decodedText, decodedResult) {

                /*
                 * QR FOUND
                 */

                document
                    .getElementById("search")
                    .value = decodedText;

                searchBtn.disabled = false;


                /*
                 * Stop camera
                 */

                stopScanner().then(function() {

                    /*
                     * Search student
                     */

                    document
                        .getElementById("searchBtn")
                        .click();

                });

            },

            function(errorMessage) {

                /*
                 * Normal QR scanning errors.
                 * Do nothing.
                 */

            }

        )

        .then(function() {

            scannerRunning = true;

            document
                .getElementById("stopCameraBtn")
                .style.display = "inline-block";

        })

        .catch(function(error) {

            console.log(
                "Camera start error:",
                error
            );

            alert(
                "Unable to start camera. " +
                "Please allow camera permission " +
                "and try again."
            );

            hideCamera();

        });

    })

    .catch(function(error) {

        console.log(
            "Camera access error:",
            error
        );

        alert(
            "Unable to access camera. " +
            "Please allow camera permission " +
            "and try again."
        );

        hideCamera();

    });

}


/* =====================================================
   STOP CAMERA
===================================================== */

function stopScanner() {

    return new Promise(function(resolve) {

        if (!html5QrCode || !scannerRunning) {

            hideCamera();

            resolve();

            return;

        }


        html5QrCode.stop()

        .then(function() {

            scannerRunning = false;


            try {

                html5QrCode.clear();

            } catch(e) {}


            html5QrCode = null;

            hideCamera();

            resolve();

        })

        .catch(function(error) {

            console.log(
                "Camera stop error:",
                error
            );

            scannerRunning = false;

            html5QrCode = null;

            hideCamera();

            resolve();

        });

    });

}


/* =====================================================
   HIDE CAMERA UI
===================================================== */

function hideCamera() {

    document
        .getElementById("reader")
        .style.display = "none";

    document
        .getElementById("stopCameraBtn")
        .style.display = "none";

}


/* =====================================================
   STOP CAMERA WHEN LEAVING PAGE
===================================================== */

window.addEventListener(
    "beforeunload",
    function() {

        if (
            html5QrCode &&
            scannerRunning
        ) {

            html5QrCode
                .stop()
                .catch(function() {});

        }

    }
);

</script>

<?php include('allfoot.php'); ?>

