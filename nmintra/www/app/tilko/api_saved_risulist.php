<?php
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

// search
$where = array();

if($Sangtae!='') {
	$where[] = " Sangtae = '$Sangtae' ";
}

if($KindClsFlag!='') {
	$where[] = " Gubun = '$KindClsFlag' ";
}

if($BudongsanSojaejibeon!='') {
	$where[] = " BudongsanSojaejibeon like '%{$BudongsanSojaejibeon}%' ";
}

if($searchtxt !='') {
	$where[] = " BudongsanSojaejibeon like '%{$searchtxt}%' ";
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
	$orderby = " order by UniqueNo desc ";
}

$sql = " select count(*) as cnt from tilkoapi_risuconfirmsimplec {$where_sql} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select * from tilkoapi_risuconfirmsimplec {$where_sql} {$orderby} limit {$start}, {$length} ";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['no'] = $no;
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

echo json_encode($res);

exit;
?>