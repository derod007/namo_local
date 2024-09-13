<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

ini_set("display_errors", 0);
$targeturl = "http://nmapi.xfund.co.kr/get_realprice4.php";

$search = serialize($_POST);

$dst_arr = array("서울 ", "경기 ", "인천 ", "제주특별자치도 ", "강원특별자치도 " );
$src_arr = array("서울특별시 ", "경기도 ", "인천시 ", "제주", "강원도 ");
$addr1 = str_replace($dst_arr, $src_arr, trim($_POST['addr1']));

$param_list = array(
	"search" => $search,
	"addr1" => $addr1,
	"danzi" => $_POST['danzi'],
	"py" => $_POST['py'],
	"year" => $_POST['year'],
);


$post_field_string = http_build_query($param_list, '', '&');

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $targeturl);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);


$response = curl_exec($ch);
$curlinfo = curl_getinfo($ch);
curl_close($ch);

$jsondata = json_decode($response, true);

for($mm = 1; $mm <= 12; $mm++) {
	$tdays[] = $mm;
}

$danzi = "";
$trade_cnt = 0;
$total_price = $ave_price = 0;
$datasets = array();
foreach($jsondata['data'] as $kk => $vv) {
	$danzi = $vv['danzi'];
	$datasets[] = array(
		"floor" => $vv['floor']."F",
		"year" => $_POST['year'],
		"x" => intval($vv['mm']),
		"y" => $vv['price'],
	);
	$trade_cnt++;
	$total_price += $vv['price'];
}
if($trade_cnt) {
	$ave_price = round($total_price / $trade_cnt, 0);
}

$res = array();
if(count($datasets)) {
	$res['data']['days'] = $tdays;
	// $res['data']['year'] = $_POST['year'];
	$res['data']['datas'] = $datasets;
	$res['data']['danzi'] = $danzi;
	$res['data']['htmltable'] = $htmltable;
	$res['data']['ave_price'] = $ave_price;
} else {
	$res['data']['danzi'] = "데이터없음";
	$res['data']['htmltable'] = "<h2>조회된 데이터 없음</h2>";
}

die(json_encode($res));
