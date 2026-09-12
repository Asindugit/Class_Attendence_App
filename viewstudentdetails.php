<?php
session_start();

if ($_SESSION["uidx"] == "" || $_SESSION["uidx"] == NULL) {
	header('Location:userLogin');
}

$userid = $_SESSION["uidx"];
$fname = $_SESSION["uname"];
?>

<?php include('allhead.php'); ?>
<?php include('connection.php'); ?>

<div class="container">

	<div class="row">

		<div class="col-md-12">

			<h3>
				Welcome :
				<a href="welcomeuser.php" style="text-decoration:none;">
					<span style="color:#FF0004"><?php echo $fname; ?></span>
				</a>
			</h3>

			<form method="GET">

				<div class="row">

					<!-- Grade -->
					<div class="col-md-6">

						<div class="form-group" style="padding-top:0.5rem;">

							<label><strong>Grade :</strong></label>

							<select name="grade" class="form-control">

								<option value="">--Select Grade--</option>

								<?php
								$gq = mysqli_query($link, "SELECT DISTINCT grade FROM classess");
								while ($g = mysqli_fetch_array($gq)) {
									$sel = (isset($_GET['grade']) && $_GET['grade'] == $g['grade']) ? "selected" : "";
									echo "<option value='{$g['grade']}' $sel>{$g['grade']}</option>";
								}
								?>

							</select>

						</div>

					</div>

					<!-- Subject -->
					<div class="col-md-6">

						<div class="form-group" style="padding-top:0.5rem;">

							<label><strong>Subject :</strong></label>

							<select name="subject" class="form-control">

								<option value="">--Select Subject--</option>

								<?php
								$sq = mysqli_query($link, "SELECT DISTINCT subject FROM classess");
								while ($s = mysqli_fetch_array($sq)) {
									$sel = (isset($_GET['subject']) && $_GET['subject'] == $s['subject']) ? "selected" : "";
									echo "<option value='{$s['subject']}' $sel>{$s['subject']}</option>";
								}
								?>

							</select>

						</div>

					</div>

				</div>

				<br>
				<button type="submit" class="btn btn-primary">
					Search
				</button>

			</form>

			<br>

			<?php

			if (!empty($_GET['grade']) && !empty($_GET['subject'])) {

				$grade = $_GET['grade'];
				$subject = $_GET['subject'];

				$sql = "SELECT 
							s.s_id,
							s.SName,
							s.PName,
							s.School,
							s.Address,
							s.WNumber,
							s.VNumber,
							s.NNumber,
							s.Register_date,
							c.grade,
							c.subject
						FROM student s
						INNER JOIN student_classes sc ON s.s_id = sc.s_id
						INNER JOIN classess c ON sc.class_id = c.class_id
						WHERE c.grade = '$grade'
						AND c.subject = '$subject'";

				$result = mysqli_query($link, $sql);

				echo "<h2 class='page-header'>Student Details</h2>";

				echo "
				<div class='table-responsive'>
				<table class='table table-striped table-hover table-bordered'>
				<tr class='table-dark'>
					<th>ID</th>
					<th>Name</th>
					<th>Parent</th>
					<th>Grade</th>
					<th>School</th>
					<th>Address</th>
					<th>Subject</th>
					<th>WhatsApp</th>
					<th>Voice</th>
					<th>Neighbor</th>
					<th>Reg.Date</th>
				</tr>";

				while ($row = mysqli_fetch_array($result)) {
					?>
					<tr>
						<td><?php echo $row['s_id']; ?></td>
						<td><?php echo $row['SName']; ?></td>
						<td><?php echo $row['PName']; ?></td>
						<td><?php echo $row['grade']; ?></td>
						<td><?php echo $row['School']; ?></td>
						<td><?php echo $row['Address']; ?></td>
						<td><?php echo $row['subject']; ?></td>
						<td><?php echo $row['WNumber']; ?></td>
						<td><?php echo $row['VNumber']; ?></td>
						<td><?php echo $row['NNumber']; ?></td>
						<td><?php echo $row['Register_date']; ?></td>
					</tr>
					<?php
				}

				echo "</table></div>";
			}
			?>

			<a href="welcomeuser.php">
				<button class="btn btn-warning">Back</button>
			</a>

		</div>

	</div>

</div>

<?php include('allfoot.php'); ?>