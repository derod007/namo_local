<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

function price_vw($price) {
	
	if($price == '-' || !$price) {
		return "-";
	}
	
	return number_format($price);

}

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//print_r($_POST);

// search
$where = array();

if($danzi!='') {
	$where[] = " b.danzi like '%$danzi%' ";
}

if($kbcode!='') {
	$where[] = " b.kbcode like '$kbcode' ";
}

if($sido!='') {
	$where[] = " a.rcode like '$sido' ";
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

$sql = " select count(*) as cnt, count(distinct a.kbcode) as dist_cnt from {$jsb['kbapt_sise2_table']} as a 
				left join  {$jsb['kbapt_info_table']} as b on a.kbcode = b.kbcode
			{$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;
$total_dist = ($row['dist_cnt'])?$row['dist_cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select b.rcode as rcode2, r.dong, b.py_info, b.ho_cnt, b.danzi, b.rate, a.* from {$jsb['kbapt_sise2_table']} as a 
				left join  {$jsb['kbapt_info_table']} as b on a.kbcode = b.kbcode
				left join  {$jsb['regioncode_table']} as r on b.rcode = r.code and r.use_yn='존재' and r.step='0'
				{$where_sql} {$orderby} limit {$start}, {$length} ";

//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['data_no'] = $row['idx'];
	$row['no'] = $no;
	$row['rcode'] = $row['rcode2'];
	$row['py-view'] = $row['py']."/".$row['py2'];
	if($row['py-view'] == "-/-") $row['py-view'] = "-";
	
	$row['price'] = price_vw($row['price_low'])." / ".price_vw($row['price_mid'])." / ".price_vw($row['price_high']);
	$row['junse'] = price_vw($row['junse_low'])." / ".price_vw($row['junse_mid'])." / ".price_vw($row['junse_high']);
	
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