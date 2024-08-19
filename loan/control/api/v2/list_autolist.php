<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/data.inc.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//print_r($_POST);

$loan_table = "loan_apt_tmp";		// 자동심사 신청

$partners = get_partnerlist();

// search
$where = array();
$where[] = " wr_status !='81' ";	// 등록(81) 단계는 보여지지 않게

// pt_idx = [4,5,8];
if(isset($_POST["pt_idx"]) && count($_POST["pt_idx"])>0) {
	
	$pt_idx = $_POST['pt_idx'];
	$chk_pt_idx = array();
	if(count($pt_idx) > 0) {
		foreach($pt_idx as $v) {
			$chk_pt_idx[] = $v; 
		}
		//print_r($chk_pt_idx);
		$wq = implode(",", $chk_pt_idx);
		$where[] = " pt_idx in ({$wq}) ";
	}
		
} else if($pt_idx != '') {
	$where[] = " pt_idx = '{$pt_idx}' ";
}
$searchtxt = trim($searchtxt);
if($searchtxt != '') {
	$where[] = " ( wr_addr1 like '%$searchtxt%' or  wr_addr2 like '%$searchtxt%'  ) ";
}

$searchmemo = trim($searchmemo);
if($searchmemo != '') {
	$where[] = " ( jd_memo like '%$searchmemo%' ) ";
}

if($regdate != '') {
	$where[] = " wr_datetime like '{$regdate}%' ";
}

if(!isset($_POST['page'])) {
	$page = 1;	
}

if(count($where) > 0) {
	$where_sql = " where ".implode(" and ",$where)."";
} else {
	$where_sql = "";
}

if($sortName) {
	if($sortName == 'no') $orderby = " order by wr_id ";
	else $orderby = " order by ".$sortName." ";
	
	if($sortASC == 'false') {
		$orderby .= " desc ";
	} else {
		$orderby .= " asc ";
	}
	
} else {
	$orderby = " order by wr_id desc ";
}

$sql = " select count(*) as cnt from {$loan_table} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select * from {$loan_table} {$where_sql} {$orderby} limit {$start}, {$length} ";
//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['data_no'] = $row['wr_id'];
	$row['no'] = $no;
	switch($row['wr_ca']) {
		case "A" : $row['wr_ca'] = "아파트"; break;
		case "B" : $row['wr_ca'] = "빌라"; break;
		case "E" : $row['wr_ca'] = "기타"; break;
		default : $row['wr_ca'] = "기타"; 
	}
	$row['address'] = $row['wr_addr1'];
	$row['address2'] = $row['wr_addr3']." ".$row['wr_addr2'];
	$row['wdate'] = substr($row['wr_datetime'],5,11);
	$row['status'] = $status_arr[$row['wr_status']];
	$row['mb_bizname'] = $partners[$row['pt_idx']]['mb_bizname'];
	$row['mb_name'] = $partners[$row['pt_idx']]['mb_name'];
	
	switch($row['wr_judge_code']) {
		case "80" : $row['status'] = "기준가격실패"; break;
		case "90" : $row['status'] = "한도부족"; break;
		default : $row['status'] = ""; 
	}
	if(!$row['status']) {
		switch($row['wr_status']) {
			case "81" : $row['status'] = "등록"; break;
			case "82" : $row['status'] = "선순위입력"; break;
			case "83" : $row['status'] = "한도계산"; break;
			default : $row['status'] = " "; 
		}
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
$res['sql'] = $sql;

echo json_encode($res);

exit;
?>