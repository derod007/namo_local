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

if($reg_date!='') {
	$where[] = " reg_date like '{$reg_date}%' ";
}

if($filename!='') {
	$where[] = " filename like '%{$filename}%' ";
}

if(!isset($_POST['page'])) {
	$page = 1;	
}

if($where) {
	$where_sql = " where ".implode(" and ",$where)."";
} else {
	$where_sql = " where 1=1 ";
}

if($sortName) {
	
	$orderby = " order by ".$sortName." ";
	
	if($sortASC == 'false') {
		$orderby .= " desc ";
	} else {
		$orderby .= " asc ";
	}
	
} else {
	$orderby = " order by grcode desc ";
}

$sql = " select count(distinct grcode) cnt, count(*) as total_cnt from p2p_publicofficial {$where_sql}";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_dist = $row['total_cnt'];

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

//$sql = " select * from p2p_publicofficial {$where_sql} {$orderby} limit {$start}, {$length} ";
$sql = " select grcode, filename, reg_date, count(*) as cnt from p2p_publicofficial {$where_sql} group by grcode {$orderby} limit {$start}, {$length} ";

//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['no'] = $no;
	
	//$row['grcode'] = $row['grcode'];
	//$row['filename'] = $row['filename'];
	$row['reg_date'] = substr($row['reg_date'],0,16);
	//$row['cnt'] = $row['cnt'];
  
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