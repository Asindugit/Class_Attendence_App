<?php include('allhead.php'); ?>
<?php include_once("connection.php"); ?>

<?php

$generatedQR = "";
$studentName = "";
$currentDate = "";

if($_SERVER['REQUEST_METHOD']=='POST')
{
	$name      = $_POST['name'];
	$pname     = $_POST['pname'];
	$school    = $_POST['school'];
	$address   = $_POST['address'];
	$whatsapp  = $_POST['whatsapp'];
	$voice     = $_POST['voice'];
	$neigbor   = $_POST['neigbor'];
	$date      = $_POST['date'];

	$classes = isset($_POST['classes']) ? $_POST['classes'] : [];

	if (
		$name=="" ||
		$pname=="" ||
		$school=="" ||
		$address=="" ||
		$whatsapp=="" ||
		$voice=="" ||
		$neigbor=="" ||
		$date==""
	)
	{
		echo "<div class='alert alert-danger'>Fields must not be empty.</div>";
	}
	else if(count($classes) == 0)
	{
		echo "<div class='alert alert-danger'>Please select at least one class.</div>";
	}
	else
	{
		mysqli_begin_transaction($link);

		try {

			$query = "INSERT INTO student
			(SName,PName,School,Address,WNumber,VNumber,NNumber,Register_Date)
			VALUES
			('$name','$pname','$school','$address','$whatsapp','$voice','$neigbor','$date')";

			if(!mysqli_query($link,$query)){
				throw new Exception("Student insert failed");
			}

			$last_id = mysqli_insert_id($link);
			$qr_value = $last_id;

			mysqli_query($link,"
				UPDATE student SET qr_code='$qr_value'
				WHERE s_id='$last_id'
			");

			foreach($classes as $class_id)
			{
				$class_sql = "INSERT INTO student_classes
				(s_id,class_id,join_date)
				VALUES
				('$last_id','$class_id',NOW())";

				if(!mysqli_query($link,$class_sql)){
					throw new Exception("Class insert failed");
				}
			}

			mysqli_commit($link);

			$generatedQR = $qr_value;
			$studentName = $name;
			$currentDate = date("Y-m-d");

			echo "<div class='alert alert-success'>
					Student registered successfully.
				  </div>";

		} catch(Exception $e) {

			mysqli_rollback($link);

			echo "<div class='alert alert-danger'>
					".$e->getMessage()."
				  </div>";
		}
	}
}

?>

<style>

#printArea{
	width:78mm;
	padding:5mm;
	margin:auto;
	text-align:center;
	font-family:Arial;
	border:1px solid #ddd;
}

.print-title{font-size:20px;font-weight:bold;}
.print-text{font-size:16px;}
.print-date{font-size:14px;}

@media print{
	body *{visibility:hidden;}
	#printArea, #printArea *{visibility:visible;}
	#printArea{
		position:absolute;
		left:0;
		top:0;
		width:80mm;
		border:none;
	}
	.no-print{display:none;}
}

.class-box{
    border:1px solid #ddd;
    border-radius:8px;
    padding:12px;
    margin-bottom:8px;
    background:#f8f9fa;
}

.class-item{
    display:flex;
    align-items:center;
    justify-content:space-between;
    width:100%;
}

.class-left{
    display:flex;
    align-items:center;
    flex:1;
}

.class-left label{
    margin-left:10px;
    margin-bottom:0;
    cursor:pointer;
    width:100%;
}

.class-fee{
    font-weight:bold;
    color:#198754;
    white-space:nowrap;
}

.form-check-input{
    width:20px;
    height:20px;
}

@media(max-width:576px){

    .class-item{
        font-size:14px;
    }

    .class-fee{
        font-size:13px;
    }
}

</style>

<div class="container">

	<h2 style="text-align:center;">Student Registration</h2>

	<form method="post">
		<label>Student Name :</label>
		<input type="text" name="name" class="form-control" placeholder="Student Name"><br>
		<label>Parent Name :</label>
		<input type="text" name="pname" class="form-control" placeholder="Parent Name"><br>
		<label>School :</label>
		<input type="text" name="school" class="form-control" placeholder="School"><br>
		<label>Address :</label>
		<input type="text" name="address" class="form-control" placeholder="Address"><br>
		<label>Whatsapp Number :</label>
		<input type="text" name="whatsapp" class="form-control" placeholder="WhatsApp"><br>
		<label>Contact Number :</label>
		<input type="text" name="voice" class="form-control" placeholder="Contact"><br>
		<label>Neighbor Contact :</label>
		<input type="text" name="neigbor" class="form-control" placeholder="Neighbor Contact"><br>
		<input type="date" name="date" class="form-control"><br>

		<h4>Select Classes</h4>

<?php
$class_query = mysqli_query(
    $link,
    "SELECT * FROM classess ORDER BY grade, subject"
);

while($class = mysqli_fetch_assoc($class_query))
{
?>

<div class="class-box">

    <div class="class-item">

        <div class="class-left">

            <input
                type="checkbox"
                name="classes[]"
                value="<?php echo $class['class_id']; ?>"
                class="form-check-input"
                id="c<?php echo $class['class_id']; ?>">

            <label for="c<?php echo $class['class_id']; ?>">

                <strong>
                    Grade <?php echo $class['grade']; ?>
                </strong>

                |
                <?php echo $class['subject']; ?>

            </label>

        </div>

        <div class="class-fee">
            Rs. <?php echo number_format($class['monthly_fee']); ?>
        </div>

    </div>

</div>

<?php
}
?>

		<br>

		<button type="submit" class="btn btn-primary">
			Register Student
		</button>

	</form>

	<?php if($generatedQR!=""){ ?>

	<hr>

	<div id="printArea">

		<h3 class="print-title">Student QR Card</h3>

		<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo $generatedQR; ?>">

		<h4 class="print-text">Student ID: <?php echo $generatedQR; ?></h4>

		<p class="print-text">Name: <?php echo $studentName; ?></p>

		<p class="print-date">Date: <?php echo $currentDate; ?></p>

	</div>

	<br>

	<button class="btn btn-success no-print" onclick="printAndRedirect()">
		Print QR
	</button>

	<?php } ?>

</div>

<script>
function printAndRedirect() {
	window.print();
}

window.onafterprint = function () {
	window.location.href = "welcomeuser.php";
};
</script>

<?php include('allfoot.php'); ?>