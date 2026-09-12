<?php
session_start();

/* =========================
   CHECK USER LOGIN
========================= */

if (!isset($_SESSION["uidx"]) || $_SESSION["uidx"] == "" || $_SESSION["uidx"] == NULL) {
    header('Location:userLogin');
    exit();
}

include('allhead.php');
include('connection.php');


/* =========================
   UPDATE PASSWORD
========================= */

if (isset($_POST['update_password'])) {

    $user_id = $_POST['user_id'];
    $new_pass = $_POST['password'];

    if ($new_pass == "") {

        echo "<script>
                alert('Please enter a new password');
              </script>";

    } else {

        $hash = password_hash($new_pass, PASSWORD_DEFAULT);

        $update_sql = "UPDATE users 
                       SET password='$hash' 
                       WHERE user_id='$user_id'";

        if (mysqli_query($link, $update_sql)) {

            echo "<script>
                    alert('Password updated successfully');
                  </script>";

        } else {

            echo "<script>
                    alert('Failed to update password');
                  </script>";
        }
    }
}


/* =========================
   SESSION DETAILS
========================= */

$userid = $_SESSION["uidx"];
$uname = $_SESSION["uname"];


/* =========================
   GET LOGGED-IN USER DETAILS
========================= */

$varid = $userid;

$sql = "SELECT * FROM users WHERE user_id='$varid' LIMIT 1";

$result = mysqli_query($link, $sql);

?>

<div class="container">

    <div class="row">

        <div class="col-md-2"></div>

        <div class="col-md-8">

            <h3>
                Welcome :
                <span style="color:red">
                    <?php echo htmlspecialchars($uname); ?>
                </span>
            </h3>


            <?php

            if ($result && mysqli_num_rows($result) > 0) {

                $row = mysqli_fetch_assoc($result);

            ?>

            <fieldset>

                <legend>My Details</legend>

                <table class="table table-hover">

                    <!-- ID -->
                    <tr>
                        <td><b>ID</b></td>
                        <td>
                            <?php echo htmlspecialchars($row['user_id']); ?>
                        </td>
                    </tr>


                    <!-- NAME -->
                    <tr>
                        <td><b>Name</b></td>
                        <td>
                            <?php echo htmlspecialchars($row['name']); ?>
                        </td>
                    </tr>


                    <!-- WHATSAPP -->
                    <tr>
                        <td><b>WhatsApp</b></td>
                        <td>
                            <?php echo htmlspecialchars($row['whatsapp']); ?>
                        </td>
                    </tr>


                    <!-- VOICE -->
                    <tr>
                        <td><b>Voice</b></td>
                        <td>
                            <?php echo htmlspecialchars($row['voice']); ?>
                        </td>
                    </tr>


                    <!-- ADDRESS -->
                    <tr>
                        <td><b>Address</b></td>
                        <td>
                            <?php echo htmlspecialchars($row['address']); ?>
                        </td>
                    </tr>


                    <!-- GENDER -->
                    <tr>
                        <td><b>Gender</b></td>
                        <td>
                            <?php echo htmlspecialchars($row['gender']); ?>
                        </td>
                    </tr>


                    <!-- DATE -->
                    <tr>
                        <td><b>Date</b></td>
                        <td>
                            <?php echo htmlspecialchars($row['date']); ?>
                        </td>
                    </tr>


                    <!-- PASSWORD -->
                    <tr>

                        <td>
                            <b>Password</b>
                        </td>

                        <td>

                            <span id="pwText">
                                ********
                            </span>


                            <!-- PASSWORD EDIT FORM -->

                            <form method="post"
                                  id="pwForm"
                                  style="display:none; margin-top:10px;">

                                <input type="hidden"
                                       name="user_id"
                                       value="<?php echo htmlspecialchars($row['user_id']); ?>">


                                <input type="password"
                                       name="password"
                                       id="pwInput"
                                       class="form-control"
                                       placeholder="New Password">


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


                    <!-- BUTTONS -->
                    <tr>

                        <td>

                            <button type="button"
                                    id="editBtn"
                                    class="btn btn-info"
                                    onclick="enableEdit()">

                                Edit

                            </button>

                        </td>


                        <td>

                            <a href="welcomeuser.php"
                               class="btn btn-warning">

                                Back

                            </a>

                        </td>

                    </tr>

                </table>

            </fieldset>


            <?php

            } else {

                echo '<div class="alert alert-danger">
                        User details not found.
                      </div>';
            }

            ?>

        </div>

        <div class="col-md-2"></div>

    </div>

</div>


<script>

/* =========================
   ENABLE PASSWORD EDIT
========================= */

function enableEdit()
{
    document.getElementById("pwText").style.display = "none";

    document.getElementById("pwForm").style.display = "block";

    document.getElementById("editBtn").style.display = "none";

    document.getElementById("pwInput").focus();
}


/* =========================
   CANCEL PASSWORD EDIT
========================= */

function cancelEdit()
{
    document.getElementById("pwText").style.display = "inline";

    document.getElementById("pwForm").style.display = "none";

    document.getElementById("editBtn").style.display = "inline";

    document.getElementById("pwInput").value = "";
}

</script>


<?php

mysqli_close($link);

include('allfoot.php');

?>