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

// search
$where = array();

if($searchtxt != '') {
	$where[] = " ( la_addr like '%$searchtxt%' ) ";
}

if($la_name != '') {
	$where[] = " ( la_name like '%$la_name%' or la_partner like '%$la_name%' ) ";
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
	if($sortName == 'no') $orderby = " order by la_id ";
	else $orderby = " order by ".$sortName." ";
	
	if($sortASC == 'false') {
		$orderby .= " desc ";
	} else {
		$orderby .= " asc ";
	}
	
} else {
	$orderby = " order by la_id desc ";
}

$sql = " select count(*) as cnt from loanaddr_history {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select * from loanaddr_history {$where_sql} {$orderby} limit {$start}, {$length} ";
//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['data_no'] = $row['la_id'];
	$row['no'] = $no;
	$row['wdate'] = $row['la_date'];
	
	$row['la_guarantee'] = str_replace(",","",$row['la_guarantee']);
	$row['la_priority_amount'] = str_replace(",","",$row['la_priority_amount']);
	$row['la_maximum_credit'] = str_replace(",","",$row['la_maximum_credit']);
	$row['la_loan_amount'] = str_replace(",","",$row['la_loan_amount']);

	$row['la_guarantee'] = ($row['la_guarantee'])?number_format($row['la_guarantee']):"";
	$row['la_priority_amount'] = ($row['la_priority_amount'])?number_format($row['la_priority_amount']):"";
	$row['la_maximum_credit'] = ($row['la_maximum_credit'])?number_format($row['la_maximum_credit']):"";
	$row['la_loan_amount'] = ($row['la_loan_amount'])?number_format($row['la_loan_amount']):"";
	
	
	//$row['jd_condition'] = utf8_strcut($row['jd_condition'],40);
		
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