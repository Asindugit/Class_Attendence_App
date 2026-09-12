<?php
session_start();
include("connection.php");

/* =========================================
   SECRET KEY
========================================= */
$secret_key = "my_secret_key_12345";

/* =========================================
   DECRYPT FUNCTION
========================================= */
function decryptData($data, $key)
{
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);

    return openssl_decrypt(
        $encrypted_data,
        'AES-256-CBC',
        $key,
        0,
        $iv
    );
}

/* =========================================
   LOGIN
========================================= */

if(isset($_POST['uid']) && isset($_POST['pass']))
{
    $username = mysqli_real_escape_string($link, $_POST['uid']);
    $password = $_POST['pass'];

    $sql = "SELECT * FROM teachers WHERE username='$username' LIMIT 1";
    $result = mysqli_query($link, $sql);

    if(mysqli_num_rows($result) > 0)
    {
        $row = mysqli_fetch_assoc($result);

        /* CHECK STATUS */
        if($row['status'] != 'active')
        {
            echo "<script>
                    alert('Teacher account is inactive');
                    window.location='teacherlogin.php';
                  </script>";
            exit();
        }

        /* VERIFY PASSWORD */
        if(password_verify($password, $row['password']))
        {
            /* DECRYPT NAME */
            $teacher_name = decryptData($row['name'], $secret_key);

            $_SESSION["uidx"]  = $row['teacher_id'];
            $_SESSION["uname"] = $teacher_name;

            echo "<script>
                    window.location='welcometeacher.php';
                  </script>";
        }
        else
        {
            echo "<script>
                    alert('Wrong Password');
                    window.location='teacherlogin.php';
                  </script>";
        }
    }
    else
    {
        echo "<script>
                alert('Username Not Found');
                window.location='teacherlogin.php';
              </script>";
    }
}
else
{
    header("Location: teacherlogin.php");
}
?>