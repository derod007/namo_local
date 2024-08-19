<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/data.inc.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//print_r($_POST);

$write_table = "bbs_write";		// 게시판

//$partners = get_partnerlist();

$bo_table = $_POST['bo_table'];
if(!$bo_table) {
	alert('잘못된 접근입니다.');
}

// search
$where = array();

$where[] = " bo_table = '{$bo_table}' ";

$searchtxt = trim($searchtxt);
if($searchtxt != '') {
	$where[] = " wr_subject like '%$searchtxt%' ";
}

if($regdate != '') {
	$where[] = " wr_datetime like '{$regdate}%' ";
}

if(!isset($_POST['page'])) {
	$page = 1;	
}

if(count($where) > 0) {
	$where_sql = " where ".implode(" and ",$where)."";
} else {
	$where_sql = "";
}

if($sortName) {
	if($sortName == 'no') $orderby = " order by wr_id ";
	else $orderby = " order by ".$sortName." ";
	
	if($sortASC == 'false') {
		$orderby .= " desc ";
	} else {
		$orderby .= " asc ";
	}
	
} else {
	$orderby = " order by wr_id desc ";
}

$sql = " select count(*) as cnt from {$write_table} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select * from {$write_table} {$where_sql} {$orderby} limit {$start}, {$length} ";
//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['data_no'] = $row['wr_id'];
	$row['no'] = $no;
	$row['wdate'] = substr($row['wr_datetime'],5,11);
	$row['open'] = ($row['wr_open'])?"공개":"비공개";

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
$res['sql'] = $sql;

echo json_encode($res);

exit;
?>