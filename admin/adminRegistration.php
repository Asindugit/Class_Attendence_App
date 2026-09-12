<?php include('allhead.php'); ?>
<?php include_once("connection.php"); ?>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name      = mysqli_real_escape_string($link, $_POST['name']);
    $whatsapp  = mysqli_real_escape_string($link, $_POST['whatsapp']);
    $voice     = mysqli_real_escape_string($link, $_POST['voice']);
    $address   = mysqli_real_escape_string($link, $_POST['address']);
    $gender    = mysqli_real_escape_string($link, $_POST['gender']);
    $date      = mysqli_real_escape_string($link, $_POST['date']);
    $username     = mysqli_real_escape_string($link, $_POST['username']);
    $password  = $_POST['password'];

    if (
        $name == "" ||
        $whatsapp == "" ||
        $voice == "" ||
        $address == "" ||
        $gender == "" ||
        $date == "" ||
        $username == ""||
        $password == ""
    ) {
        echo "<div class='alert alert-danger'>
				All fields are required.
			  </div>";
    } else {
        /* Encrypt Password */
        $encrypt_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO admin
		(name, whatsapp, voice, address, gender, date,username, password)
		VALUES
		('$name','$whatsapp','$voice','$address','$gender','$date','$username','$encrypt_password')";

        if (mysqli_query($link, $query)) {
            echo "<div class='alert alert-success'>
					User registered successfully.
				  </div>";
        } else {
            echo "<div class='alert alert-danger'>
					Registration failed.
				  </div>";
        }
    }
}

?>

<style>
.password-box{
	position:relative;
}

.password-box input{
	padding-right:45px;
}

.eye-icon{
	position:absolute;
	right:15px;
	top:50%;
	transform:translateY(-50%);
	cursor:pointer;
	color:#666;
}
</style>

<div class="container">

    <div class="register-box">

        <h2 class="title">Admin Registration</h2>

        <form method="post">

            <div class="mb-3" style="margin-top: 1rem;">
                <label>Name</label>
                <input type="text"
                    name="name"
                    class="form-control"
                    placeholder="Enter User Name">
            </div>

            <div class="mb-3" style="margin-top: 1rem;">
                <label>WhatsApp Number</label>
                <input type="text"
                    name="whatsapp"
                    class="form-control"
                    placeholder="0770090000">
            </div>

            <div class="mb-3" style="margin-top: 1rem;">
                <label>Voice Number</label>
                <input type="text"
                    name="voice"
                    class="form-control"
                    placeholder="0770090000">
            </div>

            <div class="mb-3" style="margin-top: 1rem;">
                <label>Address</label>
                <textarea name="address"
                    class="form-control"
                    placeholder="Enter User Address"></textarea>
            </div>

            <div class="mb-3" style="margin-top: 1rem;">
                <label>Gender</label>

                <select name="gender" class="form-control">
                    <option value="">-- Select Gender --</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <div class="mb-3" style="margin-top: 1rem;">
                <label>Date</label>

                <input type="date"
                    name="date"
                    class="form-control"
                    value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="mb-3" style="margin-top: 1rem;">
                <label>Username</label>
                <textarea name="username"
                    class="form-control"
                    placeholder="Enter username"></textarea>
            </div>

            <div class="mb-3" style="margin-top: 1rem;">
                <label>Password</label>

                <div class="password-box">

                    <input type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Enter Password">

                    <span class="eye-icon" onclick="togglePassword()">
                        <i id="eyeIcon" class="fa-solid fa-eye"></i>
                    </span>

                </div>
            </div>

            <div style="margin-top: 1rem;">

                <button type="submit"
                    class="btn btn-primary w-100">
                    Register Admin
                </button>

            </div>

        </form>

    </div>

</div>

<script>

function togglePassword()
{
	var password = document.getElementById("password");
	var icon = document.getElementById("eyeIcon");

	if(password.type === "password")
	{
		password.type = "text";
		icon.classList.remove("fa-eye");
		icon.classList.add("fa-eye-slash");
	}
	else
	{
		password.type = "password";
		icon.classList.remove("fa-eye-slash");
		icon.classList.add("fa-eye");
	}
}

</script>

<?php include('allfoot.php'); ?>