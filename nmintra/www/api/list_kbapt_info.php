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

if($danzi!='') {
	$where[] = " danzi like '%$danzi%' ";
}

if($region!='') {
	$where[] = " rcode like '$region%' ";
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
	if($sortName == 'no') $orderby = " order by idx ";
	else if($sortName == 'dong') $orderby = " order by b.dong ";
	else $orderby = " order by ".$sortName." ";
	
	if($sortASC == 'false') {
		$orderby .= " desc ";
	} else {
		$orderby .= " asc ";
	}
	
} else {
	$orderby = " order by yyyy desc, mm desc, load_no desc ";
}

$sql = " select count(*) as cnt, sum(ho_cnt) as sum_ho from {$jsb['kbapt_info_table']} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;
$total_ho = ($row['sum_ho'])?$row['sum_ho']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select a.*, b.region, b.dong from {$jsb['kbapt_info_table']} as a 
				left join  {$jsb['regioncode_table']} as b on a.rcode = b.code and b.use_yn='존재'
				{$where_sql} {$orderby} limit {$start}, {$length} ";
//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['data_no'] = $row['idx'];
	$row['no'] = $no;
	$row['rcode'] = $row['rcode'];
	$row['sigungu'] = "";
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
$res['total']['ho_cnt'] = number_format($total_ho);

echo json_encode($res);

exit;
?>