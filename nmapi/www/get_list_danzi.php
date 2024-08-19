<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
include_once('./inc_auth.php');

$region_code = $_POST['region_code'];

$sigungu = $_POST['sigungu'];
$dong = $_POST['dong'];
$zibun = $_POST['zibun'];
$danzi = $_POST['danzi'];
$page = $_POST['page'];

// search
$where = array();

if($danzi!='') {
	$where[] = " danzi like '%$danzi%' ";
}

if($region_code!='') {
	$where[] = " region_code = '$region_code' ";
}

if(!isset($_POST['page'])) {
	$page = 1;	
}

if($where) {
	$where_sql = " where ".implode(" and ",$where)."";
} else {
	$where_sql = "";
}

// 실거래가 단지정보
$sql = "SELECT count(*) as cnt FROM actual_danzi {$where_sql}";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;


if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = "SELECT * FROM actual_danzi {$where_sql} order by dong asc, zibun asc, danzi asc limit {$start}, {$length} ";
$result = sql_query($sql);
$danzi = array();
$i=1;
while($row = sql_fetch_array($result)) {
	// 아파트 오픈년월보다 이후 데이터만 출력
	$ap_opendate = $row['yyyy'].str_pad($row['mm'],2,'0',STR_PAD_LEFT);
	//if($ap_opendate >= $apt_info['opendate']) {
		$row['num'] = $i;
		$danzi[] = $row;
		$i++;
	//}
}

$data['danzi'] = $danzi;
//print_r2($danzi);

//die();
//$res['search'] = $search;
$res['draw'] = intval($draw);
//$res['success'] = true;
$res['recordsTotal'] = intval($total_count);
$res['recordsFiltered'] = intval($total_count);
$res['total'] = intval($total_count);
$res['data'] = $danzi;
$res['sql'] = addslashes($sql);



if(isset ($_GET['callback']))
{
    //header("Content-Type: application/json");
    echo $_GET['callback']."(".json_encode($res).")";

} else {
	echo json_encode($res);
}
exit;