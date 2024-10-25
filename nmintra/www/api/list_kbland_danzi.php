<?php
ini_set("display_errors", 0);
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

// search
$where = array();

$term = trim($q['term']);	// 자동완성 검색어

$code = trim($_POST['code']);	
if($_GET['code']) {
	$code = trim($_GET['code']);	
}
$region = trim($_POST['region']);	
$danzi = trim($_POST['danzi']);	

if($code)
{
	$where[] = " regioncode like '{$code}%' ";
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

$orderby = " order by regioncode asc ";

$sql = " select count(*) as cnt from kbland_danzi {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];


$sql = " select a.*, (select dong from {$jsb['regioncode_table']} where code=regioncode limit 1) as dong, 
				(select count(*) from kbland_danzi_py where kbno=a.kbno) as pycnt 
				from kbland_danzi as a {$where_sql} {$orderby} limit {$start}, {$length} ";
// echo $sql;
$result = sql_query($sql);
$data = array();

$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$i++;
	$row['no'] = $no;
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