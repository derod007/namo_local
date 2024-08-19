<?php
//error_reporting(E_ALL);
ini_set("display_errors", 0);

header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/data.inc.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//print_r($_POST);

$loan_table = "loan_write";

// search
$where = array();

if($status != '') {
	$where[] = " wr_status = '{$status}' ";
}

$searchtxt = trim($searchtxt);
if($searchtxt != '') {
	$where[] = " ( wr_subject like '%$searchtxt%' or  wr_addr1 like '%$searchtxt%' or  wr_addr2 like '%$searchtxt%'  ) ";
}

if(!isset($_POST['page'])) {
	$page = 1;	
}


if($member['is_sub']) {
	$where_sql = " where pt_idx = '".$member['parent_id']."' and wr_datetime >= '".LIMIT_YMD."' ";
} else {
	$where_sql = " where pt_idx = '".$member['idx']."' and wr_datetime >= '".LIMIT_YMD."' ";
}

if(count($where) > 0) {
	$where_sql .= " and ".implode(" and ",$where)."";
}

if($sortName ?? '') {
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

$sql = " select count(*) as cnt from {$loan_table} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select * from {$loan_table} {$where_sql} {$orderby} limit {$start}, {$length} ";
//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['data_no'] = $row['wr_id'];
	$row['no'] = $no;
	switch($row['wr_ca']) {
		case "A" : $row['wr_ca'] = "아파트"; break;
		case "B" : $row['wr_ca'] = "빌라"; break;
		case "E" : $row['wr_ca'] = "기타"; break;
		default : $row['wr_ca'] = "기타"; 
	}
	$row['address'] = $row['wr_addr1'];
	$row['address2'] = $row['wr_addr3']." ".$row['wr_addr2'];
	$row['wdate'] = substr($row['wr_datetime'],5,11);
	$row['status'] = $status_arr[$row['wr_status']];
	$row['mb_bizname'] = $partners[$row['pt_idx']]['mb_bizname'];
	$row['mb_name'] = $partners[$row['pt_idx']]['mb_name'];
	$row['jd_condition'] = utf8_strcut($row['jd_condition'],40);
	$row['wr_subject'] = utf8_strcut($row['wr_subject'],25);
	
	$pjfile = get_writefile($row['wr_id']);
	$row['filecnt'] = number_format($pjfile['count']);
	
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