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

if($sido!='') {
	$where[] = " rcode like '$sido' ";
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
	if($sortName == 'no') $orderby = " order by v.no ";
	else if($sortName == 'wdate') $orderby = " order by v.wdate ";
	else if($sortName == 'rcode') $orderby = " order by v.rcode ";
	else $orderby = " order by ".$sortName." ";
	
	if($sortASC == 'false') {
		$orderby .= " desc ";
	} else {
		$orderby .= " asc ";
	}
	
	if($sortName == 'wdate') $orderby .= " , v.rcode asc ";	
	
} else {
	$orderby = " order by v.wdate desc, v.rcode asc ";
}
if($sortName == 'no') $orderby = "";


$sql = " select count(*) as cnt, count(distinct kbcode) as dist_cnt from {$jsb['kbapt_sise2_table']} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;
$total_dc = ($row['dist_cnt'])?$row['dist_cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select v.rcode, v.dong, v.wdate, count(*) as cnt, count(distinct v.kbcode) as dist_cnt
			from 
				(select a.*, b.region, b.dong from {$jsb['kbapt_sise2_table']} as a 
				left join  {$jsb['regioncode_table']} as b on a.rcode = b.region and b.use_yn='존재' and b.step='1'
				{$where_sql} ) as v
			group by v.rcode, v.wdate
			{$orderby}
			limit {$start}, {$length} ";
//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$no = 1;
//$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['no'] = $no;
	
	$data[] = $row;
	$no++;
}

$res['draw'] = intval($draw);
//$res['success'] = true;
$res['recordsTotal'] = intval($no-1);
$res['recordsFiltered'] = intval($no-1);
//$res['page'] = $page;
$res['search'] = $search;
$res['data'] = $data;
$res['total']['cnt'] = number_format($total_count);
$res['total']['dist_cnt'] = number_format($total_dc);

echo json_encode($res);

exit;
?>