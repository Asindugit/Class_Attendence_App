<?php include('allhead.php'); ?>
<?php include_once("connection.php"); ?>

<?php

/* =========================================
   SECRET KEY
========================================= */

$secret_key = "my_secret_key_12345";

/* =========================================
   ENCRYPT FUNCTION
========================================= */

function encryptData($data, $key)
{
	$iv = openssl_random_pseudo_bytes(16);

	$encrypted = openssl_encrypt(
		$data,
		'AES-256-CBC',
		$key,
		0,
		$iv
	);

	return base64_encode($encrypted . '::' . $iv);
}

/* =========================================
   REGISTER TEACHER
========================================= */

if($_SERVER['REQUEST_METHOD']=='POST')
{
	$name      = trim($_POST['name']);
	$whatsapp  = trim($_POST['whatsapp']);
	$voice     = trim($_POST['voice']);
	$email     = trim($_POST['email']);
	$username  = trim($_POST['username']);
	$password  = trim($_POST['password']);
	$status    = trim($_POST['status']);

	if (
		$name=="" ||
		$whatsapp=="" ||
		$voice=="" ||
		$email=="" ||
		$username=="" ||
		$password=="" ||
		$status==""
	)
	{
		echo "<div class='alert alert-danger'>
				Fields must not be empty.
			  </div>";
	}
	else
	{
		/* =========================================
		   CHECK USERNAME
		========================================= */

		$check = mysqli_query(
			$link,
			"SELECT * FROM teachers
			 WHERE username='$username'"
		);

		if(mysqli_num_rows($check) > 0)
		{
			echo "<div class='alert alert-danger'>
					Username already exists.
				  </div>";
		}
		else
		{
			/* =========================================
			   ENCRYPT DATA
			========================================= */

			$enc_name      = encryptData($name, $secret_key);
			$enc_whatsapp  = encryptData($whatsapp, $secret_key);
			$enc_voice     = encryptData($voice, $secret_key);
			$enc_email     = encryptData($email, $secret_key);

			/* =========================================
			   PASSWORD HASH
			========================================= */

			$hashed_password = password_hash(
				$password,
				PASSWORD_DEFAULT
			);

			/* =========================================
			   INSERT QUERY
			========================================= */

			$query = "INSERT INTO teachers
			(
				name,
				whatsapp,
				voice,
				email,
				username,
				password,
				status,
				create_at
			)
			VALUES
			(
				'$enc_name',
				'$enc_whatsapp',
				'$enc_voice',
				'$enc_email',
				'$username',
				'$hashed_password',
				'$status',
				NOW()
			)";

			if(mysqli_query($link,$query))
			{
				echo "<div class='alert alert-success'>
						Teacher registered successfully.
					  </div>";
			}
			else
			{
				echo "<div class='alert alert-danger'>
						Teacher registration failed.
					  </div>";
			}
		}
	}
}

?>


<div class="container">

	<h2 style="text-align:center;">
		Teacher Registration
	</h2>

	<form method="post">

		<input type="text"
			   name="name"
			   class="form-control"
			   placeholder="Teacher Name"><br>

		<input type="text"
			   name="whatsapp"
			   class="form-control"
			   placeholder="WhatsApp Number"><br>

		<input type="text"
			   name="voice"
			   class="form-control"
			   placeholder="Voice Number"><br>

		<input type="email"
			   name="email"
			   class="form-control"
			   placeholder="Email Address"><br>

		<input type="text"
			   name="username"
			   class="form-control"
			   placeholder="Username"><br>

		<input type="password"
			   name="password"
			   class="form-control"
			   placeholder="Password"><br>

		<select name="status"
				class="form-control">

			<option value="">
				-- Select Status --
			</option>

			<option value="active">
				Active
			</option>

			<option value="inactive">
				Inactive
			</option>

		</select>

		<br>

		<button type="submit"
				class="btn btn-primary">

			Register Teacher

		</button>

	</form>

</div>

<?php include('allfoot.php'); ?>