<?php
require_once 'config.php';
require_once 'checksession.php';

$PATH = dirname(__FILE__).'/';
$COOKIEFILE = $PATH.'protect/bbl-cookies';
$USERNAME = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($xkey), base64_decode($B_USERNAME), MCRYPT_MODE_CBC, md5(md5($xkey))), "\0");
$PASSWORD = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($xkey), base64_decode($B_PASSWORD), MCRYPT_MODE_CBC, md5(md5($xkey))), "\0");
$ACCOUNT_NAME = str_replace("-", "", $B_ACCOUNT_NAME);

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

curl_setopt($ch, CURLOPT_URL, 'https://ibanking.bangkokbank.com/SignOn.aspx');
$data = curl_exec($ch);

$html = str_get_html($data);
$form_field = array();
foreach ($html->find('form input') as $element) {
	$form_field[$element->name] = $element->value;
}
$form_field['txtID'] = $USERNAME;
$form_field['txtPwd'] = $PASSWORD;
$form_field['btnLogOn'] = '	Log On';
$form_field['VAM_Group'] = 'GROUPMAIN';
unset($form_field['btnLogOff']);
$post_string = '';
foreach ($form_field as $key => $value) {
	$post_string .= $key . '=' . urlencode($value) . '&';
}
$post_string = substr($post_string, 0, -1);

// login
curl_setopt($ch, CURLOPT_URL, 'https://ibanking.bangkokbank.com/SignOn.aspx');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
$data = curl_exec($ch);

curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, null);
curl_setopt($ch, CURLOPT_URL, 'https://ibanking.bangkokbank.com/workspace/16AccountActivity/wsp_AccountSummary_AccountSummaryPage.aspx');
$data = curl_exec($ch);

$html = str_get_html($data);
$form_field = array();
foreach ($html->find('form input') as $element) {
	$form_field[$element->name] = $element->value;
}

// find AccIndex
// javascript:dataPostPage('wsp_AccountActivity_SavingCurrent.aspx', '../navigator/nav_AccountActivity.aspx', '3', '1234567890')
$acc_target = $html->find("a[href*=$ACCOUNT_NAME]", 0)->outertext;
$acc_target = html_entity_decode($acc_target, ENT_QUOTES, 'UTF-8');
$acc_target = explode(',', $acc_target);
$acc_index = str_replace("'", '', $acc_target[2]);

$form_field['AcctID'] = $ACCOUNT_NAME;
$form_field['AcctIndex'] = (int) $acc_index;
$post_string = '';
foreach ($form_field as $key => $value) {
	$post_string .= $key . '=' . urlencode($value) . '&';
}
$post_string = substr($post_string, 0, -1);

// last statement page
curl_setopt($ch, CURLOPT_URL, 'https://ibanking.bangkokbank.com/workspace/16AccountActivity/wsp_AccountActivity_SavingCurrent.aspx');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
$data = curl_exec($ch);

//$data = iconv("windows-874", "utf-8", $data);
$total = array();
$s = iconv("windows-874", "utf-8", 'วันที่');
$html = str_get_html($data);
$table = $html->find('table#ctl00_ctl00_C_CW_gvAccountTrans', 0);
if (!(empty($table))) {
	foreach ($table->find('tr') as $tr) {
		$td1 = clean($tr->find('td', 1)->plaintext);

		$pos = strpos($td1, $s);
		if ($pos !== false) continue;
		if (empty($td1)) continue;

		$list = array();
		$list['date'] = $td1;
		$list['in'] = (float) str_replace(',','', clean($tr->find('td',4)->plaintext));
		$list['out'] = (float) str_replace(',','', clean($tr->find('td',3)->plaintext));
		$list['info'] = clean($tr->find('td', 6)->plaintext) . ' ' . clean($tr->find('td', 2)->plaintext);
		list($d,$m,$y,$h) = explode(' ', $list['date']);
		$month_thai = array("ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
		$month_num = array('01','02','03','04','05','06','07','08','09','10','11','12');
		$m = str_replace($month_thai, $month_num, $m);
		$y = (int) $y;
		$y = $y - 543;
		$list['date'] = $d.'/'.$m.'/'.$y.' '.$h;
		$check_date = $d.'-'.$m.'-'.$y.' '.$h;
		if (date('Ymd') != date('Ymd', strtotime($check_date))) continue;
		if (empty($list['in'])) continue;
		// change date format

		$total[] = $list;
	}
}

curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, null);
curl_setopt($ch, CURLOPT_URL, 'https://ibanking.bangkokbank.com/LogOut.aspx');
$data = curl_exec($ch);

?>

<?php if (empty($total)) { ?>
	<div class="alert">ไม่มีรายการของวันนี้</div>
<?php } else {
	$total = array_reverse($total);
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
