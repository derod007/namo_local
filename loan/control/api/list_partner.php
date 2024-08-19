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

if($searchtxt != '') {
	$where[] = " ( mb_name like '%$searchtxt%' or  mb_bizname like '%$searchtxt%' ) ";
}

if(!isset($in_sub)) {
	$where[] = " ( is_sub = 0 )";
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
	if($sortName == 'no') $orderby = " order by idx ";
	else $orderby = " order by ".$sortName." ";
	
	if($sortASC == 'false') {
		$orderby .= " desc ";
	} else {
		$orderby .= " asc ";
	}
	
} else {
	$orderby = " order by idx desc ";
}

$sql = " select count(*) as cnt from partner_member {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select *, (select count(*) from partner_member where is_sub=1 and parent_id=a.idx ) as sub_cnt from partner_member a {$where_sql} {$orderby} limit {$start}, {$length} ";
//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['data_no'] = $row['idx'];
	$row['no'] = $no;
	$row['wdate'] = substr($row['mb_joindate'],0,10);
	$row['status'] = $use_status_arr[$row['mb_use']];
	$row['display'] = $use_status_arr[$row['mb_display']];
	//$row['is_sub'] = $row['is_sub'];
	//$row['parent_id'] = $row['parent_id'];
	$row['sub_cnt'] = $row['sub_cnt'];	// 서브아이디 갯수
	
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