<?php
session_start();

if ($_SESSION["uidx"] == "" || $_SESSION["uidx"] == NULL) {
    header('Location:teacherLogin.php');
    exit();
}

include('allhead.php');
include('connection.php');

$secret_key = "my_secret_key_12345";

/* =========================
   DECRYPT FUNCTION
========================= */
function decryptData($data, $key)
{
    $parts = explode('::', base64_decode($data), 2);

    if (count($parts) !== 2) return $data;

    list($encrypted_data, $iv) = $parts;

    return openssl_decrypt(
        $encrypted_data,
        'AES-256-CBC',
        $key,
        0,
        $iv
    );
}

/* =========================
   UPDATE PASSWORD ONLY
========================= */
if (isset($_POST['update_password'])) {

    $teacher_id = $_POST['teacher_id'];
    $new_pass = $_POST['password'];

    $hash = password_hash($new_pass, PASSWORD_DEFAULT);

    mysqli_query($link, "
        UPDATE teachers 
        SET password='$hash' 
        WHERE teacher_id='$teacher_id'
    ");

    echo "<script>alert('Password updated successfully');</script>";
}

$teacher_id = $_SESSION["uidx"];

/* =========================
   GET TEACHER DATA
========================= */
$sql = "SELECT * FROM teachers WHERE teacher_id='$teacher_id'";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result);
?>

<div class="container">
<div class="row">
<div class="col-md-2"></div>

<div class="col-md-8">

<h3>
    Welcome :
    <span style="color:red">
        <?php echo decryptData($row['name'], $secret_key); ?>
    </span>
</h3>

<fieldset>
<legend>Teacher Details</legend>

<div class="table-responsive">
<table class="table table-hover ">

<tr>
    <td><b>Teacher ID</b></td>
    <td><?php echo $row['teacher_id']; ?></td>
</tr>

<tr>
    <td><b>Name</b></td>
    <td><?php echo decryptData($row['name'], $secret_key); ?></td>
</tr>

<tr>
    <td><b>WhatsApp</b></td>
    <td><?php echo decryptData($row['whatsapp'], $secret_key); ?></td>
</tr>

<tr>
    <td><b>Voice</b></td>
    <td><?php echo decryptData($row['voice'], $secret_key); ?></td>
</tr>

<tr>
    <td><b>Email</b></td>
    <td><?php echo decryptData($row['email'], $secret_key); ?></td>
</tr>

<tr>
    <td><b>Username</b></td>
    <td><?php echo decryptData($row['username'], $secret_key); ?></td>
</tr>

<tr>
    <td><b>Register Date</b></td>
    <td><?php echo $row['create_at']; ?></td>
</tr>

<!-- PASSWORD SECTION -->
<tr>
    <td><b>Password</b></td>
    <td>

        <span id="pwText">********</span>

        <form method="post" id="pwForm" style="display:none; margin-top:10px;">

            <input type="hidden" name="teacher_id" value="<?php echo $row['teacher_id']; ?>">

            <input type="password"
                   name="password"
                   class="form-control"
                   placeholder="New Password"
                   required>

            <br>

            <button type="submit"
                    name="update_password"
                    class="btn btn-success btn-sm">
                Save
            </button>

            <button type="button"
                    class="btn btn-danger btn-sm"
                    onclick="cancelEdit()">
                Cancel
            </button>

        </form>

    </td>
</tr>

<tr>
    <td>
        <button type="button"
                id="editBtn"
                class="btn btn-info"
                onclick="enableEdit()">
            Change Password
        </button>
    </td>

    <td>
        <a href="welcometeacher.php" class="btn btn-warning">
            Back
        </a>
    </td>
</tr>

</table>
</div>

</fieldset>

</div>
<div class="col-md-2"></div>
</div>
</div>

<script>

function enableEdit()
{
    document.getElementById("pwText").style.display = "none";
    document.getElementById("pwForm").style.display = "block";
    document.getElementById("editBtn").style.display = "none";
}

function cancelEdit()
{
    document.getElementById("pwText").style.display = "inline";
    document.getElementById("pwForm").style.display = "none";
    document.getElementById("editBtn").style.display = "inline";
}

</script>

<?php include('allfoot.php'); ?>