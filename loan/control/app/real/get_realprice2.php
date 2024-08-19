<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting(E_ALL);
//ini_set("display_errors", 1);
$targeturl = "http://nmapi.xfund.co.kr/get_realprice2.php";

$search = serialize($_POST);

$dst_arr = array("서울 ", "경기 ", "인천 ", "제주특별자치도 ", "강원특별자치도 " );
$src_arr = array("서울특별시 ", "경기도 ", "인천시 ", "제주", "강원도 ");
$addr1 = str_replace($dst_arr, $src_arr, trim($_POST['addr1']));

//print_r($_POST);
//die();
$param_list = array(
	"search" => $search,
	"addr1" => $addr1,
	"danzi" => $_POST['danzi'],
	"py" => $_POST['py'],
	"year" => $_POST['year'],
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
//echo $response;
//print_r2($res['data']);
//die();
$jsondata = json_decode($response, true);
//print_r2($jsondata);
//die();

for($mm = 1; $mm <= 12; $mm++) {
	$tdays[] = $mm;
}

$danzi = "";
$trade_cnt = 0;
$total_price = $ave_price = 0;
$datasets = array();
foreach($jsondata['data'] as $kk => $vv) {
	//print_r2($vv);
	//$label = $vv['mm']."/".$vv['dd']." ".($vv['floor'])?$vv['floor']."F":"";
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

$htmltable = "
		<div class='realprice_datatable'>
			<div class=''>
				<label>총 거래건수</label>
				<span>{$trade_cnt} 건</span>
			</div>
			<div class=''>
				<label>평균 거래금액</label>
				<span>".number_format($ave_price)." 만원</span>
			</div>
		</div>

		<table class='realprice_datatable'>
		<caption>실거래 내역</caption>
        <thead>
        <tr>
            <th scope='col'>거래일자</th>
            <th scope='col'>전용면적</th>
            <th scope='col'>층</th>
            <th scope='col'>거래금액</th>
        </tr>
        </thead>
        <tbody>
";

foreach($jsondata['data'] as $kk => $vv) {
		
		$bg = 'bg'.($i%2);
$htmltable .= "
			<tr class='".$bg."'>
				<td class='td_center'>".$vv['yyyy'].".".$vv['mm'].".".$vv['dd']."</td>
				<td class='td_center'>".$vv['py']."</td>
				<td class='td_center'>".$vv['floor']."</td>
				<td class='td_center'>".number_format($vv['price'])."</td>
			</tr>
";
	}
$htmltable .= "			
		</table>
		<div>&nbsp;</div>
";


$res = array();
if(count($datasets)) {
	$res['data']['days'] = $tdays;
	$res['data']['year'] = $_POST['year'];
	$res['data']['datas'] = $datasets;
	$res['data']['danzi'] = $danzi;
	$res['data']['htmltable'] = $htmltable;
} else {
	$res['data']['danzi'] = "데이터없음";
	$res['data']['year'] = $_POST['year'];
	$res['data']['htmltable'] = "<h2>조회된 데이터 없음</h2>";
}

//print_r2($tdays);
//print_r2($res);

die(json_encode($res));
