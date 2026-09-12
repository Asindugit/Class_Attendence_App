
<?php 

	$host="localhost";
	$dbName="class_mangement";
	$user="root";
	$pass="";
	

	$link=new mysqli($host,$user,$pass,$dbName);

	if($link){
		//echo "Connection establish successfully";
	}
	else{
		echo "error";
	}



 ?>