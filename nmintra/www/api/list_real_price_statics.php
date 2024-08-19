<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//print_r($_POST);

// search
$where = array();

if($region!='') {
	$where[] = " region_code = '$region' ";
}

if($yyyy) {
	$where[] = " yyyy = '$yyyy' ";
}

if($mm) {
	$where[] = " mm = '$mm' ";
}

if(!isset($_POST['page'])) {
	$page = 1;	
}

if($where) {
	$where_sql = " where ".implode(" and ",$where)."";
} else {
	$where_sql = "";
}

if($sortName) {
	$orderby = " order by ".$sortName." ";
	if($sortASC == 'false') {
		$orderby .= " desc ";
	} else {
		$orderby .= " asc ";
	}
	
} else {
	$orderby = " order by yyyy desc, mm desc, load_no desc ";
}

$sql = " select count(*) as cnt, avg(price_avg) as avg_price, avg(price_pyavg) as pyavg_price from {$jsb['actualprice_statics_table']} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;
$total_avg = ($row['avg_price'])?$row['avg_price']:0;
$total_pyavg = ($row['pyavg_price'])?$row['pyavg_price']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select * from {$jsb['actualprice_statics_table']} {$where_sql} {$orderby} limit {$start}, {$length} ";
//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['no'] = $no;
	$row['rdate'] = $row['yyyy']."-".$row['mm'];
	$row['region'] = $row['region_code']." ".$row['sigungu'];
	$row['price_cnt'] = number_format($row['price_cnt']);
	$row['price_total'] = number_format($row['price_total']);
	$row['price_avg'] = number_format($row['price_avg']);
	$row['price_pyavg'] = number_format($row['price_pyavg']);
	$row['wdate'] = substr($row['wdate'], 0, 16);	
	
	$data[] = $row;
	$no--;
}

$res['draw'] = intval($draw);
//$res['success'] = true;
$res['recordsTotal'] = intval($total_count);
$res['recordsFiltered'] = intval($total_count);
//$res['page'] = $page;
$res['search'] = $search;
$res['data'] = $data;
$res['total']['cnt'] = number_format($total_count);
$res['total']['price'] = number_format($total_avg);
$res['total']['pyprice'] = number_format($total_pyavg);

echo json_encode($res);

exit;
?>