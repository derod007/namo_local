<?php
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

// search
$where = array();

if($SearchTxt!='') {
	$where[] = " (a.UniqueNo = '{$SearchTxt}' OR b.BudongsanSojaejibeon like '%{$SearchTxt}%' OR b.Owner like '%{$SearchTxt}%') ";
}

if(!isset($_POST['page'])) {
	$page = 1;	
}

if($where) {
	$where_sql = " where a.UniqueNo != '' and ".implode(" and ",$where)."";
} else {
	$where_sql = " where a.UniqueNo != '' ";
}

if($sortName) {
	$orderby = " order by ".$sortName." ";
	if($sortASC == 'false') {
		$orderby .= " desc ";
	} else {
		$orderby .= " asc ";
	}
	
} else {
	$orderby = " order by a.idx desc ";
}

//$sql = " select count(*) as cnt from tilkoapi_revtwelcomeevtc as a {$where_sql} ";
$sql = " select count(a.idx) as cnt from tilkoapi_revtwelcomeevtc as a left join tilkoapi_risuconfirmsimplec b on b.UniqueNo=a.UniqueNo {$where_sql} ";

$row = sql_fetch($sql);
$total_count = $row['cnt'];

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

//$sql = " select * from tilkoapi_risuretrieve {$where_sql} {$orderby} limit {$start}, {$length} ";
//$sql = " select a.*, (select BudongsanSojaejibeon from tilkoapi_risuconfirmsimplec where UniqueNo=a.UniqueNo) as BudongsanSojaejibeon from tilkoapi_revtwelcomeevtc as a {$where_sql} {$orderby} limit {$start}, {$length} ";
$sql = " select a.*, b.BudongsanSojaejibeon, b.Owner from tilkoapi_revtwelcomeevtc as a 
			left join tilkoapi_risuconfirmsimplec b on b.UniqueNo=a.UniqueNo {$where_sql} {$orderby} limit {$start}, {$length} ";

//echo $sql;
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['no'] = $no;
	
	if($row['GubunCode'] == '1') $row['Gubun'] = '건물';
	else if($row['GubunCode'] == '2') $row['Gubun'] = '집합건물';
	else if($row['GubunCode'] == '0') $row['Gubun'] = '토지';
	else $row['Gubun'] = '';
	
	if(!$row['RDJeobsuBeonho']) {
		$row['RDJeobsuBeonho'] = $row['Status'];
		if($row['Status'] == 'Error') $row['RDJeobsuBeonho'] = "<font color='red'>Error</font>";
	}
	
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

echo json_encode($res);

exit;
?>