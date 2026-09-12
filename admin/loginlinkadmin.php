<?php
session_start();
include('../connection.php');

$x = $_POST["uid"];
$y = $_POST["pass"];

/* Step 1: Get user by ID only */
$sql = "SELECT * FROM admin WHERE admin_id='$x' LIMIT 1";
$result = mysqli_query($link, $sql);

if($result && mysqli_num_rows($result) == 1)
{
	$row = mysqli_fetch_assoc($result);

	$hashed_password = $row["password"];

	/* Step 2: verify password */
	if(password_verify($y, $hashed_password))
	{
		$_SESSION["uidx"] = $row["admin_id"];
		$_SESSION["uname"] = $row["name"];

		header("Location: welcomeadmin.php");
		exit();
	}
	else
	{
		echo "<script>
			alert('Invalid Password!');
			window.location.href='adminLogin.php';
		</script>";
	}
}
else
{
	echo "<script>
		alert('User ID not found!');
		window.location.href='adminLogin.php';
	</script>";
}

$link->close();
?>