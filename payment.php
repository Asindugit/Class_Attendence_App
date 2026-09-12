<?php
session_start();

if ( $_SESSION[ "uidx" ] == "" || $_SESSION[ "uidx" ] == NULL ) {
	header( 'Location:fuserLogin' );
}

$userid = $_SESSION[ "uidx" ];
$name = $_SESSION[ "uname" ];

?>




<?php include('allhead.php'); ?> 
 </nav>

  <style>

html,
body{
    height:100%;
    margin:0;
    padding:0;
}

.bg{
    background-image:url('img/login.jpg');
    background-size:cover;
    background-position:center;
    background-repeat:no-repeat;
    min-height:100vh;
    padding-top:100px;
}

</style>

 <div class="bg">

 <div class="container">
	<div class="row">

		<div class="col-md-12 text-center">
			<!--Welcome page for faculty-->
			<h3> Welcome <a href="welcomeuser.php" style=" text-decoration: none"><span style="color:#FF0004"> <?php echo $name; ?></span></h3>
			</a> 
			<a href="makepayment.php"><button  href="" type="submit" class="btn btn-primary" style="border-radius:0%"><i class="fa-solid fa-marker"></i> Make Payment</button></a>
			<a href="viewpayment.php"><button  href="" type="submit" class="btn btn-primary" style="border-radius:0%"><i class="fa fa-clipboard-list"></i> View Payments</button></a>
            <a href="paymenthistory.php"><button  href="" type="submit" class="btn btn-primary" style="border-radius:0%"><i class="fa-solid fa-clock-rotate-left"></i> Payment History</button></a>
            <!-- <a href="viewpayment.php"><button  href="" type="submit" class="btn btn-primary" style="border-radius:0%"><i class="fa-solid fa-receipt"></i> Print Receipt</button></a> -->
            <td><a href="welcomeuser.php?>"><input type="button" Value="Back" style="border-radius:0%" class="btn btn-warning"></a></td>
			

		</div>



	</div>
 </div>
 	<?php include('allfoot.php');  ?>