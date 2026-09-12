<?php include('allhead.php'); ?>
<?php include_once("../connection.php"); ?>

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

/* =========================================
   ADD CLASS
========================================= */

if($_SERVER['REQUEST_METHOD']=='POST')
{
	$teacher_id = $_POST['teacher_id'];
	$grade      = $_POST['grade'];
	$subject    = $_POST['subject'];
	$monthly_fee= $_POST['monthly_fee'];

	if(
		$teacher_id=="" ||
		$grade=="" ||
		$subject=="" ||
		$monthly_fee==""
	)
	{
		echo "<div class='alert alert-danger'>
				Fields must not be empty.
			  </div>";
	}
	else
	{
		$query = "INSERT INTO classess
		(
			teacher_id,
			grade,
			subject,
			monthly_fee
		)
		VALUES
		(
			'$teacher_id',
			'$grade',
			'$subject',
			'$monthly_fee'
		)";

		if(mysqli_query($link,$query))
		{
			echo "<div class='alert alert-success'>
					Class added successfully.
				  </div>";
		}
		else
		{
			echo "<div class='alert alert-danger'>
					Class add failed.
				  </div>";
		}
	}
}

?>

<style>

body{
	background:#f4f6f9;
}

</style>

<div class="container">

	<h2 style="text-align:center;">
		Add Class
	</h2>

	<form method="post">

	<label>Select Teacher</label>
		<select name="teacher_id"
				class="form-control">

			<option value="">
				-- Select Teacher --
			</option>

			<?php

			$teacher_query = mysqli_query(
				$link,
				"SELECT * FROM teachers
				 WHERE status='active'
				 ORDER BY teacher_id DESC"
			);

			while($teacher = mysqli_fetch_assoc($teacher_query))
			{
				$teacher_name = decryptData(
					$teacher['name'],
					$secret_key
				);
			?>

				<option value="<?php echo $teacher['teacher_id']; ?>">

					<?php echo $teacher_name; ?>

				</option>

			<?php } ?>

		</select>

		<br>

		<label>Enter Grade</label>

		<input type="text"
			   name="grade"
			   class="form-control"
			   placeholder="Grade 10">

		<br>

		<label>Enter Subject</label>
		<input type="text"
			   name="subject"
			   class="form-control"
			   placeholder="Subject">

		<br>

		<label>Enter Monthlt Fee</label>
		<input type="number"
			   name="monthly_fee"
			   class="form-control"
			   placeholder="Monthly Fee">

		<br>

		<button type="submit"
				class="btn btn-primary">

			Add Class
		</button>

		<a href="welcomeadmin.php" class="btn btn-warning">Back</a>

	</form>

</div>

<?php include('allfoot.php'); ?>