<?php

$targeturl = "http://nmapi.xfund.co.kr/get_realprice2.php";

$dst_arr = array("서울 ", "경기 ", "인천 ", "제주특별자치도 ", "강원특별자치도 " );
$src_arr = array("서울특별시 ", "경기도 ", "인천시 ", "제주", "강원도 ");
$addr1 = str_replace($dst_arr, $src_arr, trim($param['addr1']));

//print_r($_POST);
//die();
$param_list = array(
	"addr1" => $addr1,
	"py" => $param['py'],
	"year" => $param['year'],
);


//$params_json = json_encode($param_list);
$post_field_string = http_build_query($param_list, '', '&');

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $targeturl);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field_string);
//curl_setopt($ch, CURLOPT_HEADER, TRUE);
//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);


$response = curl_exec($ch);
$curlinfo = curl_getinfo($ch);
curl_close($ch);

//$res = array();
//$res['search'] = $search;
//$res['data'] = json_decode($response);
//print_r2($res['data']);
//die();
$jsondata = json_decode($response, true);
//print_r2($jsondata);

$danzi = "";
$trade_cnt = 0;
$total_price = $ave_price = $total_price2 = $ave_price = 0;
$datasets = array();

foreach($jsondata['data'] as $kk => $vv) {
	$danzi = $vv['danzi'];
	$datasets[] = array(
		"ymd" => $vv['yyyy'].".".$vv['mm'].".".$vv['dd'],
		"py" => $vv['py'],
		"floor" => $vv['floor'],
		"year" => $param['year'],
		"price" => $vv['price'],
	);
	$trade_cnt++;
	$total_price += $vv['price'];
	$total_price2 += $vv['price'] / $vv['py'];
}

if($trade_cnt) {
	$ave_price = round($total_price / $trade_cnt, 0);
	$ave_price2 = round((($total_price2 / $trade_cnt ) * $param['py']) , 0);
}


$res = array();
if(count($datasets)) {
	$res['data']['danzi'] = $danzi;
	$res['data']['year'] = $param['year'];
	$res['data']['trade_cnt'] = $trade_cnt;
	$res['data']['aveprice'] = $ave_price;
	$res['data']['aveprice2'] = $ave_price2;
	//$res['data']['datas'] = $datasets;
} else {
	$res['data']['danzi'] = "";
	$res['data']['year'] = $param['year'];
	$res['data']['trade_cnt'] = 0;
	$res['data']['aveprice'] = 0;
	//$res['data']['datas'] = [];
}

