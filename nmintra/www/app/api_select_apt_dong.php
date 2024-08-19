<?php
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

// search
$where = array();

$term = trim($q['term']);	// 자동완성 검색어

if($term)
{
	$where[] = " dong like '{$term}%' ";
} 

if(!isset($_POST['page'])) {
	$page = 1;	
}

$groupby_sql = "";
if($where) {
	$where_sql = " where ".implode(" and ",$where)." ";
	$groupby_sql = " group by si,gun,dong ";
} else {
	$where_sql = " where 1=0 ";
	
	print_r($_GET);
	$res = array("result_code"=>"9999");
	echo json_encode($res);
	exit;
}

$orderby = " order by kbcode asc ";

/*
$sql = " select count(*) as cnt from {$jsb['regioncode_table']} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
*/

$sql = " select no, si,gun,dong from {$jsb['kbapt_table']} {$where_sql} {$groupby_sql} {$orderby} ";
//echo $sql;
$result = sql_query($sql);
$data = array();
$data2 = array();
$i = 0;
while($row=sql_fetch_array($result)){
	$data[] = $row;
	$data2[] = array('id'=>$row['no'], 'text'=>$row['si']." ".$row['gun']." ".$row['dong']);	// 자동완성용 
}

//$res['draw'] = intval($draw);
//$res['success'] = true;
//$res['recordsTotal'] = intval($total_count);
//$res['recordsFiltered'] = intval($total_count);
//$res['page'] = $page;
//$res['search'] = $search;
if($flag =='auto') {
	$res['results'] = $data2;
} else {
	$res['results'] = $data;
}

echo json_encode($res);

exit;
?>