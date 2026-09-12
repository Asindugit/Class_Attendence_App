<?php
include("connection.php");

$id = $_GET['id'];

$currentMonth = date("F");
$currentYear = date("Y");

$sql = "SELECT * FROM payment WHERE s_id='$id'";
$result = mysqli_query($link, $sql);

$payments = [];
$paidThisMonth = false;

while($row = mysqli_fetch_assoc($result))
{
    $payments[] = $row;

    if($row['month'] == $currentMonth &&
       $row['year'] == $currentYear &&
       $row['status'] == "Paid")
    {
        $paidThisMonth = true;
    }
}

echo json_encode([
    "success" => true,
    "payments" => $payments,
    "paid_this_month" => $paidThisMonth,
    "current_month" => $currentMonth,
    "current_year" => $currentYear
]);
?>