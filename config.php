<?php
	date_default_timezone_set('Asia/Bangkok');
	error_reporting(0);
	require_once 'simple_html_dom.php';
	require_once 'function.php';
	require_once 'key.php';

//Primary Encrypt key
		$xkey = "uXqVidqJy7Rn";
		$dbhost = "localhost";
		$dbusername = "topwebpl_bank";
		$dbpassword = "016523568";
		$dbname = "topwebpl_bank";
		$dbconnect = mysqli_connect($dbhost,$dbusername,$dbpassword,$dbname);
		if (mysqli_connect_errno())
		{
			echo "การเชื่อมต่อไปยังฐานข้อมูลล้มเหลว : " . mysqli_connect_error();
			exit();
		}

		//*** Reject user not online
	$intRejectTime = 1; // Minute
	$sql = "UPDATE LoginUser SET Status = '0', LastUpdate = '0000-00-00 00:00:00'  WHERE 1 AND DATE_ADD(LastUpdate, INTERVAL $intRejectTime MINUTE) <= NOW() ";
	$query = mysqli_query($dbconnect,$sql);
?>
