<?php
require_once 'config.php';
require_once 'checksession.php';

$PATH = dirname(__FILE__).'/';
$COOKIEFILE = $PATH.'protect/kk-cookies';
$USERNAME = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($xkey), base64_decode($K_USERNAME), MCRYPT_MODE_CBC, md5(md5($xkey))), "\0");
$PASSWORD = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($xkey), base64_decode($K_PASSWORD), MCRYPT_MODE_CBC, md5(md5($xkey))), "\0");
$ACCOUNT_NAME = $K_ACCOUNT_NAME;

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

$form_field = array();
$form_field['isConfirm	']  = 'T';
$post_string = '';
foreach($form_field as $key => $value) {
    $post_string .= $key . '=' . urlencode($value) . '&';
}
$post_string = substr($post_string, 0, -1);

// pre login page
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
curl_setopt($ch, CURLOPT_URL, 'https://online.kasikornbankgroup.com/K-Online/preLogin/popupPreLogin.jsp?lang=th&isConfirm=T');
$data = curl_exec($ch);

// load login
curl_setopt($ch, CURLOPT_URL, 'https://online.kasikornbankgroup.com/K-Online/login.do');
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, null);
$data = curl_exec($ch);

$html = str_get_html($data);
$form_field = array();
foreach($html->find('form input') as $element) {
	$form_field[$element->name] = $element->value;
}
$form_field['userName']  = $USERNAME;
$form_field['password'] = $PASSWORD;
$post_string = '';
foreach($form_field as $key => $value) {
    $post_string .= $key . '=' . urlencode($value) . '&';
}
$post_string = substr($post_string, 0, -1);

// login
curl_setopt($ch, CURLOPT_REFERER, 'https://online.kasikornbankgroup.com/K-Online/login.do');
curl_setopt($ch, CURLOPT_URL, 'https://online.kasikornbankgroup.com/K-Online/login.do');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
$data = curl_exec($ch);

curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, null);
curl_setopt($ch, CURLOPT_URL, 'https://online.kasikornbankgroup.com/K-Online/indexHome.jsp');
$data = curl_exec($ch);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, null);
curl_setopt($ch, CURLOPT_REFERER, 'https://online.kasikornbankgroup.com/K-Online/indexHome.jsp');
curl_setopt($ch, CURLOPT_URL, 'https://online.kasikornbankgroup.com/K-Online/checkSession.jsp');
$data = curl_exec($ch);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, null);
curl_setopt($ch, CURLOPT_REFERER, 'https://online.kasikornbankgroup.com/K-Online/indexHome.jsp');
curl_setopt($ch, CURLOPT_URL, 'https://online.kasikornbankgroup.com/K-Online/clearSession.jsp');
$data = curl_exec($ch);


// redirect after login
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, null);
curl_setopt($ch, CURLOPT_URL, 'https://online.kasikornbankgroup.com/K-Online/ib/redirectToIB.jsp?r=7027');
$data = curl_exec($ch);

$html = str_get_html($data);
$form_field = array();
foreach($html->find('form input') as $element) {
	$form_field[$element->name] = $element->value;
}
$post_string = '';
foreach($form_field as $key => $value) {
    $post_string .= $key . '=' . urlencode($value) . '&';
}
$post_string = substr($post_string, 0, -1);

// welcom page
curl_setopt($ch, CURLOPT_URL, 'https://ebank.kasikornbankgroup.com/retail/security/Welcome.do');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
$data = curl_exec($ch);


// last statement page
curl_setopt($ch, CURLOPT_URL, 'https://ebank.kasikornbankgroup.com/retail/cashmanagement/TodayAccountStatementInquiry.do');
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, null);
$data = curl_exec($ch);

$data = iconv("windows-874", "utf-8", $data);

$html = str_get_html($data);
$form_field = array();
foreach($html->find('form[name="TodayStatementForm"] input') as $element) {
	$form_field[$element->name] = $element->value;
}
// select account
$s = $ACCOUNT_NAME;
foreach($html->find('select[name="acctId"] option') as $element) {
	$text = clean($element->plaintext);
	$pos = strpos($text, $s);
	if ($pos !== false) {
		$form_field['acctId'] = $element->value;
	}
}
$post_string = '';
foreach($form_field as $key => $value) {
    $post_string .= $key . '=' . urlencode($value) . '&';
}
$post_string = substr($post_string, 0, -1);

curl_setopt($ch, CURLOPT_URL, 'https://ebank.kasikornbankgroup.com/retail/cashmanagement/TodayAccountStatementInquiry.do');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
$data = curl_exec($ch);

$total = array();
$s = 'วันที่';
$html = str_get_html($data);
$table = $html->find('table[rules="rows"]', 0);
if (!(empty($table))) {
	foreach($table->find('tr') as $tr) {
		$td1 = clean($tr->find('td',0)->plaintext);
		$pos = strpos($td1, $s);
		if ($pos !== false) continue;

		$list = array();
		$list['date'] = substr($td1, 0, -3);
		$list['in'] = (float) str_replace(',','', clean($tr->find('td',4)->plaintext));
		$list['out'] = (float) str_replace(',','', clean($tr->find('td',3)->plaintext));
		$list['info'] = clean($tr->find('td',1)->plaintext).' '.clean($tr->find('td',6)->plaintext);

		if (empty($list['in'])) continue;
		$total[] = $list;
	}

	// check next page
	$next = $html->find("a[href*='action=detail']");
	$html->clear();
	unset($html);
	if (!(empty($next))) {
		foreach($next as $a) {
			$total_next = array();

			$_query = strstr($a->href, '?');

			curl_setopt($ch, CURLOPT_URL, 'https://ebank.kasikornbankgroup.com/retail/cashmanagement/TodayAccountStatementInquiry.do'.$_query);
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_POSTFIELDS, null);
			$data = curl_exec($ch);

			$html = str_get_html($data);
			$table = $html->find('table[rules="rows"]', 0);
			if (!(empty($table))) {
				foreach($table->find('tr') as $tr) {
					$td1 = clean($tr->find('td',0)->plaintext);
					$pos = strpos($td1, $s);
					if ($pos !== false) continue;

					$list = array();
					$list['date'] = substr($td1, 0, -3);
					$list['in'] = (float) str_replace(',','', clean($tr->find('td',4)->plaintext));
					$list['out'] = (float) str_replace(',','', clean($tr->find('td',3)->plaintext));
					$list['info'] = clean($tr->find('td',1)->plaintext).' '.clean($tr->find('td',6)->plaintext);

					if (empty($list['in'])) continue;
					$total_next[] = $list;
				}
				$total = array_merge($total, $total_next);
			}
			$html->clear();
			unset($html);
		}
	} // next
}

?>
<?php if (empty($total)) { ?>
	<div class="alert">ไม่มีรายการของวันนี้</div>
<?php } else {
	//$total = array_reverse($total);
	?>
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
