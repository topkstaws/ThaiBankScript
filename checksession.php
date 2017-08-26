<?php
session_start();
if($_SESSION['UserID'] == "")
{
	header("location:login.php");
	exit();
}
 ?>
