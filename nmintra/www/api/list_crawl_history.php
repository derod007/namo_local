<?php
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

$region_arr = array();
$sql = " select * from {$jsb['regioncode_table']} where step='2' order by code desc";
$result = sql_query($sql);
while($row=sql_fetch_array($result)){
	$region_arr[$row['region']] = $row['dong'];
}

// search
$where = array();

if($yymm!='') {
	$where[] = " rdate = '$yymm' ";
}

if($region!='') {
	$where[] = " region_code = '$region' ";
}

if($status!='') {
	$where[] = " status = '$status' ";
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
	$orderby = " order by no desc ";
}

$sql = " select count(*) as cnt, sum(data_cnt) as sum_data from {$jsb['actualprice_history_table']} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_datacnt = $row['sum_data'];

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select * from {$jsb['actualprice_history_table']} {$where_sql} {$orderby} limit {$start}, {$length} ";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['no'] = $no;
	$row['rdate'] = $row['rdate'];
	$row['region'] = $row['region_code']." ".$region_arr[$row['region_code']];
	$row['data_cnt'] = number_format($row['data_cnt']);
	$row['status'] = $row['status'];
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
$res['total']['datacnt'] = number_format($total_datacnt);

echo json_encode($res);

exit;
?>