<?php
session_start();
if($_SESSION['Roles'] != "ADMIN") {
  echo "<script>
  alert('คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้เนื่องจากคุณไม่ใช่ผู้ดูแลระบบ');
  window.location.href='index.php';
  </script>";
}

require_once '../config.php';
$strUsername = mysqli_real_escape_string($dbconnect,$_POST['txtUsername']);
$strPassword = mysqli_real_escape_string($dbconnect,md5($_POST['txtPassword']));
$strRoles = mysqli_real_escape_string($dbconnect,($_POST['txtRoles']));

$strSQL = "INSERT INTO LoginUser (Username,Password,Roles) VALUES ('".$strUsername."','".$strPassword."','".$strRoles."')";
$objQuery = mysqli_query($dbconnect,$strSQL);
$objResult = mysqli_fetch_array($objQuery);
if ($objResult["Username"] == $_POST['txtUsername']) {
  echo "<script>
  alert(' เพิ่มผู้ใช้สำเร็จ ');
  </script>";
}

?>
<head>
  <script type="text/javascript">
    window.location.href = "../adduser.php"
</script>
</head>
