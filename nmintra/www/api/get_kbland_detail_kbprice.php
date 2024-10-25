<?php
//error_reporting(E_ALL);
ini_set("display_errors", 0);

header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//print_r($_POST);

// search
$where = array();
/*
if($danzi!='') {
	$where[] = " danzi like '%$danzi%' ";
}

if($region!='') {
	$where[] = " rcode like '$region%' ";
}
*/
if($where) {
	$where_sql = " where ".implode(" and ",$where)."";
} else {
	$where_sql = "";
}

$sql = " select max(kbprice_date) as lastdate from kbland_kbprice where kbno='{$kbno}' ";
$chk = sql_fetch($sql);
$lastdate = $chk['lastdate'];

$sql = " select * from kbland_kbprice where kbno='{$kbno}' and kbprice_date = '{$lastdate}' order by area_cr asc";

//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0; $total_count = 0;
$pricedata = array();
//$lastdate = "";
while($row=sql_fetch_array($result)){
	$row['data_no'] = $row['idx'];
	if(!$pricedata["{$row['pyno']}"]['kbprice_date'] || $pricedata["{$row['pyno']}"]['kbprice_date'] < $row['kbprice_date']) {
		$pricedata["{$row['pyno']}"]['kbprice_date'] = $row['kbprice_date'];
		$pricedata["{$row['pyno']}"]['kbprice_basic'] = number_format($row['kbprice_basic']);
		$pricedata["{$row['pyno']}"]['kbprice_high'] = number_format($row['kbprice_high']);
		$pricedata["{$row['pyno']}"]['kbprice_low'] = number_format($row['kbprice_low']);
		$pricedata["{$row['pyno']}"]['is_kbprice'] = $row['is_kbprice'];
		//if($lastdate < $row['kbprice_date']) {
		//	$lastdate = $row['kbprice_date'];
		//}
	}
	//$row['dong'] = "";
	
	$data[] = $row;
	$total_count++;
}
if($lastdate) {
	$lastdate = substr($lastdate,0,4).".".substr($lastdate,4,2).".".substr($lastdate,6,2);
}

$res['draw'] = intval($draw);
//$res['success'] = true;
//$res['recordsTotal'] = intval($total_count);
//$res['recordsFiltered'] = intval($total_count);
//$res['page'] = $page;
//$res['search'] = $search;
$res['data'] = $pricedata;
$res['kbpricedate'] = $lastdate;
$res['total']['cnt'] = number_format($total_count);

echo json_encode($res);

exit;
?>