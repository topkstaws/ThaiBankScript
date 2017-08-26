<?php
error_reporting(0);
require_once 'config.php';
require_once 'checksession.php';
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8 ie7" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9 ie8" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex" />

	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="pragma" content="no-cache">

	<title>ระบบเช็คยอดเงินธนาคาร</title>
	<link href="https://fonts.googleapis.com/css?family=Kanit" rel="stylesheet">
	<link rel="apple-touch-icon" href="apple-touch-icon.png"/>
	<link rel="apple-touch-icon-precomposed" href="apple-touch-icon.png"/>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="css/style.css" type="text/css" media="all"/>
	<script type="text/javascript" src="js/site.js"></script>
	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>

</head>
<body>
<font face="Kanit">
	<nav class="navbar navbar-expand-md navbar-dark bg-primary">
  <a class="navbar-brand" href="#">ระบบเช็คยอดเงินธนาคาร</a>
	<?php
	session_start();
	if($_SESSION['Roles'] == "ADMIN") {
	echo '<div class="collapse navbar-collapse" id="navbarNav">';
	echo '<ul class="navbar-nav">';
	echo	 '<li class="nav-item">';
	echo		'<a class="nav-link" href="adduser.php">เพิ่มผู้ใช้งาน </a>';
	echo	 '</li>';
	echo	 '<li class="nav-item">';
	echo		 '<a class="nav-link" href="deleteuser.php">ลบผู้ใช้งาน</a>';
	echo	 '</li>';
	echo	 '<li class="nav-item">';
	echo		 '<a class="nav-link" href="modifyuser.php">แก้ไขผู้ใช้งาน</a>';
	echo	 '</li>';
	echo '</ul>';
  echo '</div>';
} ?>
    <span class="navbar-text">
      <font color="white">ชื่อผู้ใช้งาน <?php echo $_SESSION["Username"]; ?> </font>
			<font color="white"><a href="logout.php">ออกจากระบบ</a></font>
    </span>

</nav>
	<div class="container-fluid bank-container">
		<div class="row-fluid">
			<div class="span12">
				<div class="box-wrap">
					<?php if (!(empty($K_USERNAME) || empty($K_PASSWORD) || empty($K_ACCOUNT_NAME))) { ?>
					<div class="box">
						<div class="bank-detail">
							<div class="clearfix head">
								<img src="images/kbank.png" width="80" class="bank-logo"/>
								<div class="bank-name">ธนาคารกสิกรไทย</div>
							</div>
							<div class="content">
								<div class="load do-load" data-url="login-kbank.php?key=<?php echo $xkey?>">ตรวจสอบรายการ</div>
							</div>
						</div>
					</div>
					<?php } ?>

					<?php if (!(empty($S_USERNAME) || empty($S_PASSWORD) || empty($S_ACCOUNT_NAME))) { ?>
					<div class="box">
						<div class="bank-detail">
							<div class="clearfix head">
								<img src="images/scb.png" width="80" class="bank-logo"/>
								<div class="bank-name">ธนาคารไทยพาณิชย์</div>
							</div>
							<div class="content">
								<div class="load do-load" data-url="login-scb.php?key=<?php echo $xkey?>">ตรวจสอบรายการ</div>
							</div>
						</div>
					</div>
					<?php } ?>

					<?php if (!(empty($B_USERNAME) || empty($B_PASSWORD) || empty($B_ACCOUNT_NAME))) { ?>
					<div class="box">
						<div class="bank-detail">
							<div class="clearfix head">
								<img src="images/bbl.png" width="80" class="bank-logo"/>
								<div class="bank-name">ธนาคารกรุงเทพ</div>
							</div>
							<div class="content">
								<div class="load do-load" data-url="login-bbl.php?key=<?php echo $xkey?>">ตรวจสอบรายการ</div>
							</div>
						</div>
					</div>
					<?php } ?>

					<?php if (!empty($T_USERNAME) && !empty($T_PASSWORD) && !empty($T_ACCOUNT_NAME) && !empty($C_USERNAME) && !empty($C_PASSWORD)) { ?>
					<div class="box">
						<div class="bank-detail">
							<div class="clearfix head">
								<img src="images/ktb.png" width="80" class="bank-logo"/>
								<div class="bank-name">ธนาคารกรุงไทย</div>
							</div>
							<div class="content">
								<div class="load do-load" data-url="login-ktb.php?key=<?php echo $xkey?>">ตรวจสอบรายการ</div>
							</div>
						</div>
					</div>
					<?php } elseif (!empty($T_USERNAME) && !empty($T_PASSWORD) && !empty($T_ACCOUNT_NAME)) { ?>
					<div class="box">
						<div class="bank-detail">
							<div class="clearfix head">
								<img src="images/ktb.png" width="80" class="bank-logo"/>
								<div class="bank-name">ธนาคารกรุงไทย</div>
							</div>
							<div class="content">
								<div class="load do-load" data-url="login-ktb-captcha.php?key=<?php echo $xkey?>">กดเพื่อโหลด CAPTCHA</div>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</font>
</body>
</html>
