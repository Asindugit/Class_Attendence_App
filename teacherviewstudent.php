<?php
session_start();

if (!isset($_SESSION["uidx"]) || $_SESSION["uidx"] == "") {
    header('Location:userLogin');
    exit();
}

include('allhead.php');
include('connection.php');

$teacher_id = $_SESSION["uidx"];
$fname = $_SESSION["uname"];
?>

<div class="container">

    <div class="row">
        <div class="col-md-12">

            <h3>
                Welcome :
                <span style="color:#FF0004"><?php echo $fname; ?></span>
            </h3>

            <!-- GRADE ONLY FORM -->
            <form method="GET">

                <div class="row">

                    <div class="col-md-6">
                        <label><strong>Grade :</strong></label>
                        <select name="grade" class="form-control" required>
                            <option value="">--Select Grade--</option>

                            <?php
                            $gq = mysqli_query($link, "
                                SELECT DISTINCT grade 
                                FROM classess 
                                WHERE teacher_id = '$teacher_id'
                            ");

                            while ($g = mysqli_fetch_array($gq)) {
                                $sel = (isset($_GET['grade']) && $_GET['grade'] == $g['grade']) ? "selected" : "";
                                echo "<option value='{$g['grade']}' $sel>{$g['grade']}</option>";
                            }
                            ?>

                        </select>
                    </div>

                </div>

                <br>
                <button type="submit" class="btn btn-primary">View Students</button>

            </form>

            <br>

            <?php
            if (!empty($_GET['grade'])) {

                $grade = $_GET['grade'];

                $sql = "SELECT 
                            s.SName,
                            c.grade,
                            s.School,
                            s.Address,
                            s.WNumber,
                            s.VNumber
                        FROM student s
                        INNER JOIN student_classes sc ON s.s_id = sc.s_id
                        INNER JOIN classess c ON sc.class_id = c.class_id
                        WHERE c.teacher_id = '$teacher_id'
                        AND c.grade = '$grade'";

                $result = mysqli_query($link, $sql);

                echo "<h2 class='page-header'>Student Details</h2>";

                echo "
                <div class='table-responsive'>
                <table class='table table-striped table-hover table-bordered'>
                <tr class='table-dark'>
                    <th>Name</th>
                    <th>Grade</th>
                    <th>School</th>
                    <th>Address</th>
                    <th>WhatsApp</th>
                    <th>Voice</th>
                </tr>";

                while ($row = mysqli_fetch_array($result)) {
                    echo "<tr>
                        <td>{$row['SName']}</td>
                        <td>{$row['grade']}</td>
                        <td>{$row['School']}</td>
                        <td>{$row['Address']}</td>
                        <td>{$row['WNumber']}</td>
                        <td>{$row['VNumber']}</td>
                    </tr>";
                }

                echo "</table></div>";
            }
            ?>

            <a href="welcometeacher.php">
                <button class="btn btn-warning">Back</button>
            </a>

        </div>
    </div>

</div>

<?php include('allfoot.php'); ?>