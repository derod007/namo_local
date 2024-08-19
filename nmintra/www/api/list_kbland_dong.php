<?php
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

// search
$where = array();

$term = trim($q['term']);	// 자동완성 검색어
$sido = substr($sido,0,2);
if($sido)
{
	$where[] = " region like '{$sido}%' ";
} 

if($region)
{
	$where[] = " region like '{$region}%' ";
} 

if(!empty($term)) 
{
	$where[] = " dong like '%{$term}%' ";
}

if(!isset($_POST['page'])) {
	$page = 1;	
}

if($where) {
	$where_sql = " where ".implode(" and ",$where)." and use_yn='존재' and step='0' "; //  and right(code,6) != '000000'
} else {
	$where_sql = " where 1=0 ";
	
	$res = array("result_code"=>"9999");
	echo json_encode($res);
	exit;
}

$orderby = " order by code asc ";


$sql = " select count(*) as cnt from {$jsb['regioncode_table']} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$url = 'https://api.kbland.kr/land-complex/complexComm/hscmList';	// API URL
$sql = " select *, (select count(*) from kbland_danzi where regioncode=code ) as scnt,
				(select log_datetime from kbland_api_log where keyNo=code and api_url='{$url}' order by log_id desc ) as lastdate
			from {$jsb['regioncode_table']} {$where_sql} {$orderby} limit {$start}, {$length} ";
//echo $sql;
$result = sql_query($sql);
$data = array();
$data2 = array();

$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$i++;
	$row['no'] = $no;
	$data[] = $row;
	$data2[] = array('id'=>$row['code'], 'text'=>$row['dong']);	// 자동완성용 
	$no--;
}

$res['draw'] = intval($draw);
//$res['success'] = true;
$res['recordsTotal'] = intval($total_count);
$res['recordsFiltered'] = intval($total_count);
//$res['page'] = $page;
$res['length'] = $_POST['length'];
if($flag =='auto') {
	$res['data'] = $data2;
} else {
	$res['data'] = $data;
}
$res['total']['cnt'] = number_format($total_count);

echo json_encode($res);

exit;
?>