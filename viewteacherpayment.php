<?php
session_start();

if ($_SESSION["uidx"] == "" || $_SESSION["uidx"] == NULL) {
    header('Location:teacherlogin');
}

include("connection.php");

$teacher_id = $_SESSION["uidx"];
$fname      = $_SESSION["uname"];
?>

<?php include('allhead.php'); ?>

<div class="container">

    <div class="row">

        <div class="col-md-12">

            <h3>
                Welcome :
                <a href="teacherwelcome.php" style="text-decoration:none;">
                    <span style="color:#FF0004">
                        <?php echo $fname; ?>
                    </span>
                </a>
            </h3>

            <!-- SEARCH FORM -->

            <form method="GET">

                <div class="row">

                    <!-- GRADE -->

                    <div class="col-md-4">

                        <div class="form-group" style="padding-top:0.5rem;">

                            <label><strong>Grade :</strong></label>

                            <select name="grade" class="form-control" required>

                                <option value="">--Select Grade--</option>

                                <?php

                                $grade_query = mysqli_query(
                                    $link,
                                    "SELECT DISTINCT grade
                                     FROM classess
                                     WHERE teacher_id = '$teacher_id'
                                     ORDER BY grade ASC"
                                );

                                while($g = mysqli_fetch_assoc($grade_query))
                                {
                                ?>

                                    <option
                                        value="<?php echo $g['grade']; ?>"

                                        <?php
                                        if(isset($_GET['grade']) &&
                                           $_GET['grade'] == $g['grade'])
                                        {
                                            echo "selected";
                                        }
                                        ?>

                                    >

                                        <?php echo $g['grade']; ?>

                                    </option>

                                <?php
                                }
                                ?>

                            </select>

                        </div>

                    </div>

                    <!-- DATE -->

                    <div class="col-md-4">

                        <div class="form-group" style="padding-top:0.5rem;">

                            <label><strong>Payment Date :</strong></label>

                            <input type="date"
                                   name="date"
                                   class="form-control"
                                   value="<?php if(isset($_GET['date'])){ echo $_GET['date']; } ?>"
                                   required>

                        </div>

                    </div>

                    <!-- BUTTON -->

                    <div class="col-md-4">

                        <div class="form-group" style="padding-top:2.3rem;">

                            <button type="submit" class="btn btn-primary">

                                <i class="fa fa-search"></i>
                                Search

                            </button>

                        </div>

                    </div>

                </div>

            </form>

            <br>

            <?php

            if(isset($_GET['grade']) && isset($_GET['date']))
            {

                $grade = mysqli_real_escape_string($link, $_GET['grade']);
                $date  = mysqli_real_escape_string($link, $_GET['date']);

                $sql = "

                SELECT

                    payment.payment_id,
                    payment.s_id,
                    payment.date,
                    payment.amount,
                    payment.reason,
                    payment.receipt_no,

                    student.SName,

                    classess.grade,
                    classess.subject

                FROM payment

                INNER JOIN student
                ON payment.s_id = student.s_id

                INNER JOIN classess
                ON payment.class_id = classess.class_id

                WHERE classess.teacher_id = '$teacher_id'

                AND classess.grade = '$grade'

                AND payment.date = '$date'

                ORDER BY payment.payment_id DESC

                ";

                $result = mysqli_query($link, $sql);

                // TOTAL PAYMENT

                $total_sql = "

                SELECT SUM(payment.amount) AS total_amount

                FROM payment

                INNER JOIN classess
                ON payment.class_id = classess.class_id

                WHERE classess.teacher_id = '$teacher_id'

                AND classess.grade = '$grade'

                AND payment.date = '$date'

                ";

                $total_result = mysqli_query($link, $total_sql);

                $total_row = mysqli_fetch_assoc($total_result);

                $total_payment = $total_row['total_amount'];

                ?>

                <h2 class='page-header'>
                    Payment Details
                </h2>

                <div class='table-responsive'>

                <table class='table table-striped table-hover table-bordered'>

                    <tr class='table-dark'>

                        <th>Payment ID</th>
                        <th>Receipt No</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Grade</th>
                        <th>Subject</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Reason</th>

                    </tr>

                <?php

                if(mysqli_num_rows($result) > 0)
                {

                    while($row = mysqli_fetch_assoc($result))
                    {

                        // REASON CHECK

                        $reason = trim($row['reason']);

                        if($reason == "" || $reason == NULL)
                        {
                            $reason = "-";
                        }

                ?>

                        <tr>

                            <td>
                                <?php echo $row['payment_id']; ?>
                            </td>

                            <td>
                                <?php echo $row['receipt_no']; ?>
                            </td>

                            <td>
                                <?php echo $row['s_id']; ?>
                            </td>

                            <td>
                                <?php echo $row['SName']; ?>
                            </td>

                            <td>
                                <?php echo $row['grade']; ?>
                            </td>

                            <td>
                                <?php echo $row['subject']; ?>
                            </td>

                            <td>
                                <?php echo $row['date']; ?>
                            </td>

                            <td style="color:green;font-weight:bold;">

                                Rs.
                                <?php echo number_format($row['amount'], 2); ?>

                            </td>

                            <td>

                                <?php echo $reason; ?>

                            </td>

                        </tr>

                <?php
                    }
                ?>

                    <!-- TOTAL PAYMENT ROW -->

                    <tr style="background:#ffeeba;font-weight:bold;">

                        <td colspan="7" style="text-align:right;">

                            Total Payment :

                        </td>

                        <td colspan="2" style="color:blue;">

                            Rs.
                            <?php echo number_format($total_payment, 2); ?>

                        </td>

                    </tr>

                <?php

                }
                else
                {
                    ?>

                    <tr>

                        <td colspan='9'
                            style='text-align:center;color:red;'>

                            No Payment Records Found

                        </td>

                    </tr>

                    <?php
                }

                ?>

                </table>

                </div>

                <?php
            }

            ?>

            <br>

            <a href="welcometeacher.php">

                <input type="button"
                       value="Back"
                       class="btn btn-warning"
                       style="border-radius:0%">

            </a>

        </div>

    </div>

</div>

<?php include('allfoot.php'); ?>