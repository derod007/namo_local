<?php
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

// search
$where = array();

if($searchtxt!='') {
	$where[] = " ( BudongsanSojaejibeon like '%{$searchtxt}%' or  NM_pname  like '%{$searchtxt}%' or NM_borrower  like '{$searchtxt}%' ) ";
}

if(!isset($_POST['page'])) {
	$page = 1;	
}

if($where) {
	$where_sql = " where ".implode(" and ",$where)."";
} else {
	$where_sql = " where delchk != '1' ";
}

if($sortName) {
	$orderby = " order by ".$sortName." ";
	if($sortASC == 'false') {
		$orderby .= " desc ";
	} else {
		$orderby .= " asc ";
	}
	
} else {
	$orderby = " order by idx desc ";
}

$sql = " select count(*) as cnt from tilko_managed_data {$where_sql} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select * from tilko_managed_data {$where_sql} {$orderby} limit {$start}, {$length} ";
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
	
	if($row['autocheck'] == '1') $row['auto'] = 'V';
	else if($row['autocheck'] == '0') $row['auto'] = '';
	else $row['auto'] = '';
	
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