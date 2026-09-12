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
			<a href="studentRegistration.php"><button  href="" type="submit" class="btn btn-primary" style="border-radius:0%"><i class="fa fa-address-book"></i> Add Student</button></a>
			<a href="viewstudentdetails.php"><button  href="" type="submit" class="btn btn-primary" style="border-radius:0%"><i class="fa fa-graduation-cap"></i> Student Details</button></a>
            <a href="printqrcode.php"><button  href="" type="submit" class="btn btn-primary" style="border-radius:0%"><i class="fa-solid fa-qrcode"></i> Print QR Code</button></a>
			
			  
			<a href="welcomeuser.php"><input type="button" value="Back" class="btn btn-warning" style="border-radius:0%">

            </a>

		</div>



	</div>
 </div>
 	<?php include('allfoot.php');  ?>