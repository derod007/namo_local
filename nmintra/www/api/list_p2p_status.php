<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

function price_vw($price, $c=0) {
	
	if($price == '-' || !$price) {
		return "-";
	}
	
	return number_format($price, $c);

}

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//print_r($_POST);

// search
$where = array();

if($base_date!='') {
	$where[] = " base_date like '{$base_date}' ";
}

if($com_name!='') {
	$where[] = " com_name like '%{$com_name}%' ";
}

/*
if($sido!='') {
	$where[] = " a.rcode like '$sido' ";
}
*/

if(!isset($_POST['page'])) {
	$page = 1;	
}

if($where) {
	$where_sql = " where ".implode(" and ",$where)."";
} else {
	$where_sql = "";
}

if($sortName) {
	/*
	if($sortName == 'no') $orderby = " order by idx ";
	else if($sortName == 'dong') $orderby = " order by b.dong ";
	else $orderby = " order by ".$sortName." ";
	*/
	$orderby = " order by ".$sortName." ";
	
	if($sortASC == 'false') {
		$orderby .= " desc ";
	} else {
		$orderby .= " asc ";
	}
	
} else {
	$orderby = " order by base_date desc, loan_integral desc, com_name asc ";
}

$sql = " select count(*) as cnt, count(distinct com_name) as dist_cnt from {$jsb['p2p_status_table']} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;
$total_dist = ($row['dist_cnt'])?$row['dist_cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select * from {$jsb['p2p_status_table']} {$where_sql} {$orderby} limit {$start}, {$length} ";

//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['data_no'] = $row['no'];
	$row['no'] = $no;
	
	$row['loan_integral'] = price_vw($row['loan_integral']);
	$row['loan_remain'] = price_vw($row['loan_remain']);
	$row['loan_return'] = price_vw($row['loan_return']);

	$row['rate_return'] = price_vw($row['rate_return'],2);
	$row['rate_late'] = price_vw($row['rate_late'],2);
	$row['rate_poor'] = price_vw($row['rate_poor'],2);
	$row['rate_earn'] = price_vw($row['rate_earn'],2);
  
	//$row['dong'] = "";
	
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
$res['total']['dist_cnt'] = number_format($total_dist);

echo json_encode($res);

exit;
?>