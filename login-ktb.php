<?php
require_once 'config.php';
require_once 'deathbycaptcha.php';
require_once 'checksession.php';

$captcha_username = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($xkey), base64_decode($C_USERNAME), MCRYPT_MODE_CBC, md5(md5($xkey))), "\0");
$captcha_password = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($xkey), base64_decode($C_PASSWORD), MCRYPT_MODE_CBC, md5(md5($xkey))), "\0");
$client = new DeathByCaptcha_SocketClient($captcha_username, $captcha_password);

$balance = (int) $client->balance;
if (empty($balance) || $balance <= 0) {
	echo '<div class="alert">Death By Captcha ยอดเงินหมด หรือตั้งค่าไม่ถูกต้อง</div>';
	exit();
}

$PATH = dirname(__FILE__).'/';
$COOKIEFILE = $PATH.'protect/ktb-cookies';

$USERNAME =  rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($xkey), base64_decode($T_USERNAME), MCRYPT_MODE_CBC, md5(md5($xkey))), "\0");
$PASSWORD = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($xkey), base64_decode($T_PASSWORD), MCRYPT_MODE_CBC, md5(md5($xkey))), "\0");
$ACCOUNT_NAME = $T_ACCOUNT_NAME;

$MAIN_URL = 'https://www.ktbnetbank.com';
$ch = curl_init();
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.6 (KHTML, like Gecko) Chrome/16.0.897.0 Safari/535.6");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT);
curl_setopt($ch, CURLOPT_CAINFO, $PATH."cacert.pem");
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, $COOKIEFILE);
curl_setopt($ch, CURLOPT_COOKIEFILE, $COOKIEFILE);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);

curl_setopt($ch, CURLOPT_URL, 'https://www.ktbnetbank.com/consumer/');
$data = curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, 'https://www.ktbnetbank.com/consumer/captcha/verifyImg');
$captcha_data = curl_exec($ch);
$fp = fopen($PATH.'cap.png', 'w');
fwrite($fp, $captcha_data);
fclose($fp);

$html = str_get_html($data);
$form_field = array();
foreach ($html->find('form input') as $element) {
	$form_field[$element->name] = $element->value;
}

$img = $PATH.'cap.png';
if ($captcha = $client->decode($img, DeathByCaptcha_Client::DEFAULT_TIMEOUT)) {
	//echo "CAPTCHA {$captcha['captcha']} solved: {$captcha['text']}\n";
}
$form_field['imageCode'] = $captcha['text'];
$form_field['userId'] = $USERNAME;
$form_field['password'] = $PASSWORD;
$form_field['cmd'] = 'login';

$post_string = '';
foreach ($form_field as $key => $value) {
	$post_string .= $key . '=' . urlencode($value) . '&';
}
$post_string = substr($post_string, 0, -1);


// do login
curl_setopt($ch, CURLOPT_REFERER, $MAIN_URL.'/consumer/');
curl_setopt($ch, CURLOPT_URL, $MAIN_URL.'/consumer/Login.do');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
$data = curl_exec($ch);

// find sessionKey = 'xxxx';
preg_match("/sessionKey = '(.*)'/", $data, $output_array);
$session_key = $output_array[1];

$ACCOUNT_NAME = str_replace('-', '', $ACCOUNT_NAME);
$date_period = date('d-m-Y');
$form_field = array();
$form_field['acctNo'] = $ACCOUNT_NAME;
$form_field['fromDate'] = $date_period;
$form_field['radio'] = '1';
$form_field['sessId'] = $session_key;
$form_field['specificAmtFrom'] = '';
$form_field['specificAmtTo'] = '';
$form_field['toDate'] = $date_period;
$form_field['txnRefNoFrom	'] = '';
$form_field['txnRefNoTo'] = '';

$r = microtime(true);
curl_setopt($ch, CURLOPT_REFERER, $MAIN_URL.'/consumer/main.jsp');
curl_setopt($ch, CURLOPT_URL, $MAIN_URL.'/consumer/SearchSpecific.do?cmd=search&r='.$r);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $form_field);
$data = curl_exec($ch);

$html = str_get_html($data);
$table = $html->find('table.subcontenttable', 0);
$s = 'วันที่';
$total = array();
if (!(empty($table))) {
	foreach($table->find('tr') as $tr) {
		$td1 = clean($tr->find('td',0)->plaintext);
		$pos = strpos($td1, $s);
		if ($pos !== false) continue;

		$list = array();
		$list['date'] = substr($td1, 0, -3);
		$list['in'] = (float) str_replace(',','', clean($tr->find('td',3)->plaintext));
		$list['info'] = clean($tr->find('td',1)->plaintext).' '.clean($tr->find('td',6)->plaintext);

		if (empty($list['in']) || $list['in'] < 0) continue;
		$total[] = $list;
	}
}


?>

<?php if (empty($total)) { ?>
	<div class="alert">ไม่มีรายการของวันนี้</div>
<?php } else { ?>
	<table class="table table-striped">
		<tr>
			<th>วันที่</th>
			<th>รายละเอียด</th>
			<th>ฝาก</th>
		</tr>
		<?php foreach ($total as $val) { ?>
			<tr>
				<td><?php echo  $val['date'] ?></td>
				<td><?php echo  $val['info'] ?></td>
				<td class="r"><?php echo  number_format($val['in'], 2) ?></td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>

<?php echo '<p class="text-info">&nbsp;&nbsp;&nbsp;ยอดเงินคงเหลือ Death By Captcha: $'.number_format($client->balance/100, 2).'</p>'?>
