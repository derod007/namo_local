<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

ini_set("display_errors", 0);
$targeturl = "http://nmapi.xfund.co.kr/get_realprice4.php";

$search = serialize($_POST);

// $dst_arr = array("서울 ", "경기 ", "인천 ", "제주특별자치도 ", "강원특별자치도 ", "대전", "부산", "대구");
// $src_arr = array("서울특별시 ", "경기도 ", "인천광역시 ", "제주", "강원도 ", "대전광역시", "부산광역시", "대구광역시");
// $addr1 = str_replace($dst_arr, $src_arr, trim($_POST['addr1']));
$dst_arr = array("/\b서울\b/u", "/\b경기\b/u", "/\b인천\b/u", "/\b제주\b/u", "/\b강원\b/u", "/\b대전\b/u", "/\b부산\b/u", "/\b대구\b/u");
$src_arr = array("서울특별시", "경기도", "인천광역시", "제주특별자치도", "강원특별자치도", "대전광역시", "부산광역시", "대구광역시");
$addr1 = preg_replace($dst_arr, $src_arr, trim($_POST['addr1']));

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
	// $total_price += $vv['price'];
}

// 최근 5건 추출
$recent_trades = array_slice($datasets, -5); // 마지막 5건
foreach ($recent_trades as $trade) {
    $total_price += $trade['y'];
}

if($trade_cnt > 5) {
	$ave_price = round($total_price / 5, 0);
}else if ($tradfe_cnt <= 5){
	$ave_price = round($total_price / $trade_cnt, 0);
}else{
	$ave_price = 0;
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
