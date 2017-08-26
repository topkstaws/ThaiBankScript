<?php
require_once 'function.php';
require_once 'checksession-admin.php';
error_reporting(0);
if (!empty($_GET['key'])) {
	$random_key = $_GET['key'];
} else {
	$random_key = random_string(12);
}

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
	<title>เข้ารหัส และสร้าง key.php</title>

	<link rel="apple-touch-icon" href="apple-touch-icon.png"/>
	<link rel="apple-touch-icon-precomposed" href="apple-touch-icon.png"/>

	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" media="all"/>
	<link rel="stylesheet" href="css/style.css" type="text/css" media="all"/>

	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="js/site.js"></script>

</head>
<body>

	<div class="container-fluid bank-container">
		<div class="row-fluid">
			<div class="span12">
				<div class="box-wrap">
					<div class="box">
						<div class="bank-detail">
							<div class="encode-wrap">
								<div class="title"><img src="images/lock.png" /> เข้ารหัส Username + Password ของธนาคาร</div>
								<?php if ($_POST['action'] != 'encode') { ?>
								<form action="" method="post">
									<input type="hidden" name="action" value="encode" />
									<div>
										<label><strong>ข้อความที่ใช้สำหรับเข้ารหัส และเข้าหน้าเช็คยอด</strong></label>
										<input type="text" name="encode_key" value="<?php echo $random_key; ?>" class="span4">
										<div class="muted">(ระบบจะสุ่มมาให้ สามารถเปลี่ยนได้ตามต้องการ)</div>
									</div>

									<hr />
									<div class="title"><img src="images/kbank.png" width="40" /> ธนาคารกสิกรไทย</div>
									<div>
										<label>Username</label>
										<input type="text" name="k_username" class="span4">
									</div>
									<div>
										<label>Password</label>
										<input type="password" name="k_password" class="span4">
									</div>
									<div>
										<label>เลขที่บัญชีธนาคาร (ใส่ตามรูปแบบ 123-4-56789-0 เท่านั้น)</label>
										<input type="text" name="k_account_name" placeholder="123-4-56789-0" class="span4">
									</div>

									<hr />
									<div class="title"><img src="images/scb.png" width="40" /> ธนาคารไทยพาณิชย์</div>
									<div>
										<label>Username</label>
										<input type="text" name="s_username" class="span4">
									</div>
									<div>
										<label>Password</label>
										<input type="password" name="s_password" class="span4">
									</div>
									<div>
										<label>เลขที่บัญชีธนาคาร (ใส่ตามรูปแบบ 123-4-56789-0 เท่านั้น)</label>
										<input type="text" name="s_account_name" placeholder="123-4-56789-0" class="span4">
									</div>

									<hr />
									<div class="title"><img src="images/bbl.png" width="40" /> ธนาคารกรุงเทพ</div>
									<div>
										<label>Username</label>
										<input type="text" name="b_username" class="span4">
									</div>
									<div>
										<label>Password</label>
										<input type="password" name="b_password" class="span4">
									</div>
									<div>
										<label>เลขที่บัญชีธนาคาร (ใส่ตามรูปแบบ 123-4-56789-0 เท่านั้น)</label>
										<input type="text" name="b_account_name" placeholder="123-4-56789-0" class="span4">
									</div>

									<hr />
									<div class="title"><img src="images/ktb.png" width="40" /> ธนาคารกรุงไทย</div>
									<div>
										<label>Username</label>
										<input type="text" name="t_username" class="span4">
									</div>
									<div>
										<label>Password</label>
										<input type="password" name="t_password" class="span4">
									</div>
									<div>
										<label>เลขที่บัญชีธนาคาร (ใส่ตามรูปแบบ 123-4-56789-0 เท่านั้น)</label>
										<input type="text" name="t_account_name" placeholder="123-4-56789-0" class="span4">
									</div>
									<hr />
									<h3>Death By Captcha (<a href="http://deathbycaptcha.com" target="_blank">สมัครที่นี่</a>)</h3>
									<p>สำหรับถอดรหัส CAPTCHA ของธนาคารกรุงไทย (เว้นว่างไว้ถ้าต้องการกรอก CAPTCHA เอง)</p>
									<div>
										<label>Username</label>
										<input type="text" name="c_username" class="span4">
									</div>
									<div>
										<label>Password</label>
										<input type="password" name="c_password" class="span4">
									</div>


									<br />
									<button type="submit"  class="btn btn-primary btn-large">ตกลง</button>
								</form>
								<?php } else { ?>

								<?php

								$text = '';
								if (!empty($_POST['encode_key'])) {
									$key = $_POST['encode_key'];

									if (!(empty($_POST['k_username']) || empty($_POST['k_password']) || empty($_POST['k_account_name']))) {
										$string = $_POST['k_username'];
										$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
										$text .= '$K_USERNAME = \''.$encrypted.'\';'.PHP_EOL;

										$string = $_POST['k_password'];
										$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
										$text .= '$K_PASSWORD = \''.$encrypted.'\';'.PHP_EOL;

										$text .= '$K_ACCOUNT_NAME = \''.$_POST['k_account_name'].'\';'.PHP_EOL.PHP_EOL;
									}

									if (!(empty($_POST['s_username']) || empty($_POST['s_password']) || empty($_POST['s_account_name']))) {
										$string = $_POST['s_username'];
										$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
										$text .= '$S_USERNAME = \''.$encrypted.'\';'.PHP_EOL;

										$string = $_POST['s_password'];
										$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
										$text .= '$S_PASSWORD = \''.$encrypted.'\';'.PHP_EOL;

										$text .= '$S_ACCOUNT_NAME = \''.$_POST['s_account_name'].'\';'.PHP_EOL.PHP_EOL;
									}

									if (!(empty($_POST['b_username']) || empty($_POST['b_password']) || empty($_POST['b_account_name']))) {
										$string = $_POST['b_username'];
										$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
										$text .= '$B_USERNAME = \''.$encrypted.'\';'.PHP_EOL;

										$string = $_POST['b_password'];
										$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
										$text .= '$B_PASSWORD = \''.$encrypted.'\';'.PHP_EOL;

										$text .= '$B_ACCOUNT_NAME = \''.$_POST['b_account_name'].'\';'.PHP_EOL.PHP_EOL;
									}

									if (!(empty($_POST['t_username']) || empty($_POST['t_password']) || empty($_POST['t_account_name']))) {
										$string = $_POST['t_username'];
										$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
										$text .= '$T_USERNAME = \''.$encrypted.'\';'.PHP_EOL;

										$string = $_POST['t_password'];
										$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
										$text .= '$T_PASSWORD = \''.$encrypted.'\';'.PHP_EOL;

										$text .= '$T_ACCOUNT_NAME = \''.$_POST['t_account_name'].'\';'.PHP_EOL.PHP_EOL;
									}

									if (!(empty($_POST['c_username']) || empty($_POST['c_password']))) {
										$string = $_POST['c_username'];
										$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
										$text .= '$C_USERNAME = \''.$encrypted.'\';'.PHP_EOL;

										$string = $_POST['c_password'];
										$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
										$text .= '$C_PASSWORD = \''.$encrypted.'\';'.PHP_EOL;
									}

									$text = '<?php'.PHP_EOL.$text.'?>';

									$page = getcurrentpath().'?key='.$key;
								?>
								<h3>1. Copy ข้อความในกล่องข้อความไปวางที่ไฟล์ key.php</h3>
								<textarea class="span12" rows="18"><?php echo htmlspecialchars($text); ?></textarea>

								<h3>2. สามารถเข้าใช้งานได้ที่</h3>
								<p class="text-error">(ให้จดบันทึก URL นี้ไว้ ห้ามทำหาย ถ้าไม่งั้นต้องสร้าง <strong>key.php</strong> ใหม่)</p>
								<div class="alert alert-info"><a href="<?php echo $page ?>" target="_blank"><?php echo htmlspecialchars($page) ?></a></div>
								<img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?php echo htmlspecialchars($page)?>&choe=UTF-8" />
								<?php
								} else {
									echo 'ERROR!!';
								}
								?>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
