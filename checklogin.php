<?php
	require_once("config.php");
	session_start();
	$strUsername = mysqli_real_escape_string($dbconnect,$_POST['txtUsername']);
	$strPassword = mysqli_real_escape_string($dbconnect,md5($_POST['txtPassword']));

	$strSQL = "SELECT * FROM LoginUser WHERE Username = '".$strUsername."'
	and Password = '".$strPassword."'";
	$objQuery = mysqli_query($dbconnect,$strSQL);
	$objResult = mysqli_fetch_array($objQuery);
	if(!$objResult)
	{
    echo "<script>
    alert(' ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้องโปรดลองใหม่อีกครั้ง ');
    window.location.href='login.php';
    </script>";
	}
	else
	{
		if($objResult["Status"] == "1")
		{
      echo "<script>
      alert(' ".$objResult["Username"]." ได้เข้าสู่ระบบอยู่แล้ว');
      window.location.href='login.php';
      </script>";
			exit();
		}
		else
		{
			//*** Update Status Login
			$sql = "UPDATE LoginUser SET Status = '1' , LastUpdate = NOW() WHERE UserID = '".$objResult["UserID"]."' ";
			$query = mysqli_query($dbconnect,$sql);

			//*** Session
			$_SESSION["UserID"] = $objResult["UserID"];
			$_SESSION["Username"] = $objResult["Username"];
			$_SESSION["Roles"] = $objResult["Roles"];
			session_write_close();

			//*** Go to Main page
			header("location:index.php");
      exit();
		}

	}
	mysqli_close($dbconnect);
?>
