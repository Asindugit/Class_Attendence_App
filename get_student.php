<?php
include("connection.php");

$id = $_GET['id'];

$response = [];

$sql = "SELECT * FROM student WHERE s_id='$id'";
$result = mysqli_query($link, $sql);

if(mysqli_num_rows($result) > 0)
{
	$row = mysqli_fetch_assoc($result);

	$response['success'] = true;

	$response['s_id']   = $row['s_id'];
	$response['SName']  = $row['SName'];
	$response['PName']  = $row['PName'];
	$response['School'] = $row['School'];

	$class_sql = "
	SELECT 
		classess.class_id,
		classess.grade,
		classess.subject,
		classess.monthly_fee
	FROM student_classes
	INNER JOIN classess
	ON student_classes.class_id = classess.class_id
	WHERE student_classes.s_id='$id'
	";

	$class_result = mysqli_query($link, $class_sql);

	$classes = [];

	while($class_row = mysqli_fetch_assoc($class_result))
	{
		$classes[] = $class_row;
	}

	$response['classes'] = $classes;
}
else
{
	$response['success'] = false;
}

echo json_encode($response);
?>