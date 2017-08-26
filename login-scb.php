<?php
require_once 'config.php';
require_once 'checksession.php';

$PATH = dirname(__FILE__).'/';
$COOKIEFILE = $PATH.'protect/scb-cookies';
$USERNAME = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($xkey), base64_decode($S_USERNAME), MCRYPT_MODE_CBC, md5(md5($xkey))), "\0");
$PASSWORD = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($xkey), base64_decode($S_PASSWORD), MCRYPT_MODE_CBC, md5(md5($xkey))), "\0");
$ACCOUNT_NAME = str_replace("-", "", $S_ACCOUNT_NAME);

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

$acc_id = 0;

$form_field = array();
$form_field['LOGIN']  = $USERNAME;
$form_field['PASSWD'] = $PASSWORD;
$form_field['LANG'] = 'T';
$post_string = '';
foreach($form_field as $key => $value) {
    $post_string .= $key . '=' . urlencode($value) . '&';
}
$post_string = substr($post_string, 0, -1);

// login
curl_setopt($ch, CURLOPT_URL, 'https://www.scbeasy.com/online/easynet/page/lgn/login.aspx');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
$data = curl_exec($ch);


$html = str_get_html($data);
$SESSIONEASY = $html->find('input[name="SESSIONEASY"]', 0)->value;

$form_field = array();
$form_field['SESSIONEASY']  = $SESSIONEASY;
$post_string = '';
foreach($form_field as $key => $value) {
    $post_string .= $key . '=' . urlencode($value) . '&';
}
$post_string = substr($post_string, 0, -1);
curl_setopt($ch, CURLOPT_URL, 'https://www.scbeasy.com/online/easynet/page/acc/acc_mpg.aspx');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
$data = curl_exec($ch);

// get AccBal ID
$html = str_get_html($data);
foreach($html->find('a[id*="DataProcess_SaCaGridView_SaCa_LinkButton_"]') as $a) {
	$text = $a->outertext;
	$s = substr($ACCOUNT_NAME, 4);
	$pos = strpos($text, $s);
	if ($pos !== false) {
		// javascript:__doPostBack('ctl00$DataProcess$SaCaGridView$ctl02$SaCa_LinkButton','')
		//javascript:__doPostBack(&#39;ctl00$DataProcess$SaCaGridView$ctl02$SaCa_LinkButton&#39;,&#39;&#39;)
		$href =  htmlspecialchars_decode($a->href, ENT_QUOTES);
		$href = str_replace("javascript:__doPostBack('", '', $href);
		$href = str_replace("','')", '', $href);
		$acc_href = $href;
		break;
	}
}

$html = str_get_html($data);
$form_field = array();
foreach($html->find('form input') as $element) {
	$form_field[$element->name] = $element->value;
}
$form_field['__EVENTTARGET']  = $acc_href;
$post_string = '';
foreach($form_field as $key => $value) {
    $post_string .= $key . '=' . urlencode($value) . '&';
}
$post_string = substr($post_string, 0, -1);
curl_setopt($ch, CURLOPT_URL, 'https://www.scbeasy.com/online/easynet/page/acc/acc_mpg.aspx');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
$data = curl_exec($ch);


// #f1 form redirect
$html = str_get_html($data);
$form_field = array();
foreach($html->find('form#f1 input') as $element) {
	$form_field[$element->name] = $element->value;
}
$post_string = '';
foreach($form_field as $key => $value) {
	$post_string .= $key . '=' . urlencode($value) . '&';
}
$post_string = substr($post_string, 0, -1);
curl_setopt($ch, CURLOPT_URL, 'https://www.scbeasy.com/online/easynet/page/acc/acc_bnk_tst.aspx');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
$data = curl_exec($ch);

// filter table
$html = str_get_html($data);
$table = $html->find('table#DataProcess_GridView', 0);
$s = 'วันที่';//iconv("windows-874", "utf-8", 'วันที่');
$s2 = 'รวม';//iconv("windows-874", "utf-8", 'รวม');
$total = array();
if (!(empty($table))) {
	foreach($table->find('tr') as $tr) {
		$td1 = clean($tr->find('td',0)->plaintext);
		$pos = strpos($td1, $s);
		if ($pos !== false) continue;
		$pos = strpos($td1, $s2);
		if ($pos !== false) continue;

		$list = array();
		$list['date'] = $td1.' '.clean($tr->find('td',1)->plaintext);
		$list['in'] = (float) str_replace(',','', clean($tr->find('td',5)->plaintext));
		$list['out'] = (float) str_replace(',','', clean($tr->find('td',4)->plaintext));
		$list['info'] = clean($tr->find('td',3)->plaintext).' '.clean($tr->find('td',6)->plaintext);

		if (empty($list['in'])) continue;
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
