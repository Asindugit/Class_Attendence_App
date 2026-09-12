<?php
session_start();

if (!isset($_SESSION["uidx"]) || $_SESSION["uidx"] == "") {
	header('Location:userLogin');
	exit();
}

include('allhead.php');
include('connection.php');

/* =========================
   UPDATE ADMIN DETAILS
========================= */
if(isset($_POST['update_admin']))
{
	$admin_id = mysqli_real_escape_string($link,$_POST['admin_id']);
	$name     = mysqli_real_escape_string($link,$_POST['name']);
	$whatsapp = mysqli_real_escape_string($link,$_POST['whatsapp']);
	$voice    = mysqli_real_escape_string($link,$_POST['voice']);
	$address  = mysqli_real_escape_string($link,$_POST['address']);
	$gender   = mysqli_real_escape_string($link,$_POST['gender']);
	$password = $_POST['password'];

	if(!empty($password)){
		$hash = password_hash($password, PASSWORD_DEFAULT);

		$sql = "UPDATE admin SET 
				name='$name',
				whatsapp='$whatsapp',
				voice='$voice',
				address='$address',
				gender='$gender',
				password='$hash'
				WHERE admin_id='$admin_id'";
	} else {
		$sql = "UPDATE admin SET 
				name='$name',
				whatsapp='$whatsapp',
				voice='$voice',
				address='$address',
				gender='$gender'
				WHERE admin_id='$admin_id'";
	}

	if(mysqli_query($link,$sql)){
		echo "<script>alert('Updated Successfully');</script>";
	}else{
		echo "<script>alert('Update Failed');</script>";
	}
}

$userid = $_SESSION["uidx"];
$uname  = $_SESSION["uname"];

$varid = $_REQUEST['myid'];

$sql = "SELECT * FROM admin WHERE admin_id='$varid'";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result);
?>

<div class="container">
<div class="row">
<div class="col-md-2"></div>

<div class="col-md-8">

<h3>Welcome : <span style="color:red"><?php echo $uname; ?></span></h3>

<form method="post">

<input type="hidden" name="admin_id" value="<?php echo $row['admin_id']; ?>">

<fieldset>
<legend>My Details</legend>

<table class="table table-hover">

<tr>
	<td><b>ID</b></td>
	<td><?php echo $row['admin_id']; ?></td>
</tr>

<tr>
	<td><b>Name</b></td>
	<td>
	<input type="text" name="name" id="name"
	class="form-control"
	value="<?php echo $row['name']; ?>" readonly>
	</td>
</tr>

<tr>
	<td><b>WhatsApp</b></td>
	<td>
	<input type="text" name="whatsapp" id="whatsapp"
	class="form-control"
	value="<?php echo $row['whatsapp']; ?>" readonly>
	</td>
</tr>

<tr>
	<td><b>Voice</b></td>
	<td>
	<input type="text" name="voice" id="voice"
	class="form-control"
	value="<?php echo $row['voice']; ?>" readonly>
	</td>
</tr>

<tr>
	<td><b>Address</b></td>
	<td>
	<textarea name="address" id="address"
	class="form-control" readonly><?php echo $row['address']; ?></textarea>
	</td>
</tr>

<tr>
	<td><b>Gender</b></td>
	<td>
	<select name="gender" id="gender"
	class="form-control" disabled>
		<option <?php if($row['gender']=="Male") echo "selected"; ?>>Male</option>
		<option <?php if($row['gender']=="Female") echo "selected"; ?>>Female</option>
	</select>
	</td>
</tr>

<tr>
	<td><b>Password</b></td>
	<td>

	<div class="input-group">

		<input type="password" name="password" id="password"
		class="form-control"
		placeholder="New Password"
		readonly>

		<div class="input-group-append">
			<button type="button"
			class="btn btn-secondary"
			onclick="togglePassword()">👁</button>
		</div>

	</div>

	<small style="color:gray;">Leave empty if no change</small>

	</td>
</tr>

<tr>
	<td><b>Date</b></td>
	<td><?php echo $row['date']; ?></td>
</tr>

<tr>
	<td>
		<button type="button" id="editBtn" class="btn btn-info"
		onclick="enableEdit()">Edit</button>

		<button type="submit" name="update_admin" class="btn btn-success"
		style="display:none;">
		Save
		</button>
	</td>

	<td>
		<a href="welcomeadmin.php" class="btn btn-warning">Back</a>
	</td>
</tr>

</table>

</fieldset>

</form>

</div>
<div class="col-md-2"></div>
</div>
</div>

<script>

function enableEdit()
{
	document.getElementById("name").readOnly = false;
	document.getElementById("whatsapp").readOnly = false;
	document.getElementById("voice").readOnly = false;
	document.getElementById("address").readOnly = false;
	document.getElementById("password").readOnly = false;
	document.getElementById("gender").disabled = false;

	document.getElementById("editBtn").style.display = "none";
	document.getElementById("saveBtn").style.display = "inline-block";
}

function togglePassword()
{
	var x = document.getElementById("password");
	x.type = (x.type === "password") ? "text" : "password";
}

</script>

<?php include('allfoot.php'); ?>