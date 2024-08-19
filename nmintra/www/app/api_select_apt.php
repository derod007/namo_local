<?php
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

//error_reporting(E_ALL);
//ini_set("display_errors", 1);


include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

// search
$where = array();

$term = trim($q['term']);	// 자동완성 검색어

$dongcode = trim($_POST['dongcode']);

if($dongcode)
{
	//$where[] = " no = '{$dongcode}' ";
	$dong = sql_fetch(" select * from {$jsb['kbapt_table']} where no = '{$dongcode}' ");
	$where[] = " si = '{$dong['si']}' and gun = '{$dong['gun']}' and dong = '{$dong['dong']}' ";
	
} 

if(!empty($term)) 
{
	$where[] = " dong like '%{$term}%' ";
}

if(!empty($danzi)) 
{
	$where[] = " danzi like '%{$danzi}%' ";
}

if(!isset($_POST['page'])) {
	$page = 1;	
}

$groupby_sql = "";
if($where) {
	$where_sql = " where ".implode(" and ",$where)." ";
	$groupby_sql = " group by kbcode ";
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

$sql = " select si,gun,dong,danzi,kbcode from {$jsb['kbapt_table']} {$where_sql} {$groupby_sql} {$orderby} ";
//echo $sql;
$result = sql_query($sql);
$data = array();
$data2 = array();
$i = 0;
while($row=sql_fetch_array($result)){
	$data[] = $row;
	$data2[] = array('id'=>$row['kbcode'], 'text'=>"(".$row['dong'].") ".$row['danzi']);	// 자동완성용 
}

//if($flag =='auto') {
	$res['results'] = $data2;
//} else {
//	$res['results'] = $data;
//}

echo json_encode($res);

exit;
?>