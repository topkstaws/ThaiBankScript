<?php
session_start();
if($_SESSION['Roles'] != "ADMIN") {
  echo "<script>
  alert('คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้เนื่องจากคุณไม่ใช่ผู้ดูแลระบบ');
  window.location.href='index.php';
  </script>";
}
?>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex" />

  <meta http-equiv="cache-control" content="no-cache">
  <meta http-equiv="expires" content="0">
  <meta http-equiv="pragma" content="no-cache">
<link href="https://fonts.googleapis.com/css?family=Kanit" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
<title>เพิ่มผู้ใช้ - ระบบเช็คยอดเงินธนาคาร</title>
</head>
<body>
  <font face="Kanit">
  <nav class="navbar navbar-expand-md navbar-dark bg-primary">
  <a class="navbar-brand" href="index.php">ระบบเช็คยอดเงินธนาคาร</a>
	<div class="collapse navbar-collapse" id="navbarNav">
	<ul class="navbar-nav">
	<li class="nav-item active">
	<a class="nav-link" href="adduser.php">เพิ่มผู้ใช้งาน <span class="sr-only">(current)</span></a>
	</li>
	<li class="nav-item">
	<a class="nav-link" href="deleteuser.php">ลบผู้ใช้งาน</a>
	</li>
	<li class="nav-item">
	<a class="nav-link" href="modifyuser.php">แก้ไขผู้ใช้งาน</a>
	</li>
	</ul>
  </div>
    <span class="navbar-text">
      <font color="white">ชื่อผู้ใช้งาน <?php echo $_SESSION["Username"]; ?> </font>
			<font color="white"><a href="logout.php">ออกจากระบบ</a></font>
    </span>
</nav>
<br>
<center><h2>เพิ่มผู้ใช้ในระบบ</h2></center>
<form method="post" name="adduserform" action="admin/adduser-post.php">
  <center><h3>กรุณาตรวจสอบข้อมูลต่างๆให้ครบก่อนที่จะทำการส่งข้อมูล</h3></center><br>
  <div class="container">
  <div class="row">
    <div class="col">
    </div>
    <div class="col-6"><br><br>
        <div class="form-group">
        <label for="InputUsername">ชื่อผู้ใช้</label>
        <input type="text" class="form-control" id="txtUsername" name="txtUsername" placeholder="กรุณากรอกชื่อผู้ใช้" required>
      </div>
      <div class="form-group">
        <label for="InputPassword1">รหัสผ่าน</label>
        <input type="password" class="form-control" id="txtPassword" name="txtPassword" placeholder="กรุณากรอกรหัสผ่าน" required>
      </div>
      <div class="form-group">
      <label for="InputRoles">สิทธิ์ของบัญชี</label>
      <select class="form-control" name="txtRoles" id="txtRoles" required>
        <option>USER</option>
        <option>ADMIN</option>
      </select>
      </div>
        <br>
        <center><button type="submit" class="btn btn-primary">ลงทะเบียน</button></center>
      </form>
    </div>
    <div class="col">
    </div>
  </div>
</body>
