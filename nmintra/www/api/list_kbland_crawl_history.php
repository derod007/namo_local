<?php
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

// search
$where = array();

$term = trim($q['term']);	// 자동완성 검색어

$keyName = trim($_POST['keyName']);	
$region = trim($_POST['region']);	
$danzi = trim($_POST['danzi']);	

if($keyName)
{
	$where[] = " keyName like '{$keyName}' ";
} 

if($region)
{
	$where[] = " regioncode like '{$region}%' ";
} 

if(!empty($danzi)) 
{
	$where[] = " danzi like '%{$danzi}%' ";
}

if(!empty($term)) 
{
	$where[] = " danzi like '%{$term}%' ";
}

if(!isset($_POST['page'])) {
	$page = 1;	
}

if($where) {
	$where_sql = " where ".implode(" and ",$where)." "; //  and right(code,6) != '000000'
} else {
	$where_sql = " where 1=1 ";
}

$orderby = " order by log_id desc ";

$sql = " select count(*) as cnt from kbland_api_log {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];


$sql = " select a.*, (select dong from {$jsb['regioncode_table']} where code=keyNo limit 1) as dong, 
				(select danzi from kbland_danzi where kbno=keyNo) as danzi
				from kbland_api_log as a {$where_sql} {$orderby} limit {$start}, {$length} ";
//echo $sql;
$result = sql_query($sql);
$data = array();

$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$i++;
	$row['no'] = $no;
	$row['api_url'] = str_replace("https://api.kbland.kr/land-complex", "", $row['api_url']);
	$row['api_url'] = str_replace("https://api.kbland.kr/land-price", "", $row['api_url']);
	$data[] = $row;
	$no--;
}

$res['draw'] = intval($draw);
//$res['success'] = true;
$res['recordsTotal'] = intval($total_count);
$res['recordsFiltered'] = intval($total_count);
//$res['page'] = $page;
$res['length'] = $_POST['length'];
$res['data'] = $data;
$res['total'] = number_format($total_count);

echo json_encode($res);

exit;
?>