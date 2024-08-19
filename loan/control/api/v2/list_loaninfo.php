<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

ob_start();

header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/data.inc.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

print_r($_POST);

$loan_table = "loan_write";

$partners = get_partnerlist();

// search
$where = array();

if($status != '') {
	$where[] = " wr_status = '{$status}' ";
} else {
	$where[] = " wr_status != '90' ";	// 중복 상태로 검색할때만 중복이 보이게
}

// 초기화 .park
$pt_idx='';
$draw='';
$search='';

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
	$where[] = " ( wr_subject like '%$searchtxt%' or  wr_addr1 like '%$searchtxt%' or  wr_addr2 like '%$searchtxt%'  ) ";
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

if($sortName ?? '') {
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
	if($row['wr_part'] == 'P' || $row['wr_part'] == 'PE') {
		$row['wr_ca'] = $row['wr_ca']."[지분]";
	}
	$row['address'] = $row['wr_addr1'];
	$row['address2'] = $row['wr_addr3']." ".$row['wr_addr2'];
	$row['wdate'] = substr($row['wr_datetime'],5,11);
	$row['status'] = $status_arr[$row['wr_status']];
	if(!$row['pt_name']) {
		$row['mb_bizname'] = $partners[$row['pt_idx']]['mb_bizname'];
	} else {
		$row['mb_bizname'] = $partners[$row['pt_idx']]['mb_bizname']."(".$row['pt_name'].")";
	}
	$row['mb_name'] = $partners[$row['pt_idx']]['mb_name'];
	
	$row['jd_condition'] = utf8_strcut($row['jd_condition'],60);
	$row['wr_subject'] = utf8_strcut($row['wr_subject'],25);
	
	$pjfile = get_writefile($row['wr_id']);
	$row['filecnt'] = number_format($pjfile['count']);
	
	$row['duppop'] = check_duplicate($row['wr_addr1'], $row['address2'], $row['wr_id']);
	$row['jd_autoid'] = $row['jd_autoid'];
	
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
ob_clean();
echo json_encode($res);
ob_end_flush();
exit;
?>