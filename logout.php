<?php
  require_once('config.php');
	session_start();
	//*** Update Status
	$sql = "UPDATE LoginUser SET Status = '0', LastUpdate = '0000-00-00 00:00:00' WHERE UserID = '".$_SESSION["UserID"]."' ";
	$query = mysqli_query($dbconnect,$sql);

	session_destroy();
	header("location:login.php");
?>
