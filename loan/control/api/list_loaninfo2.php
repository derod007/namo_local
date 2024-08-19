<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/data.inc.php');



// 기 접수내역 조회확인
function check_duplicate2($addr, $addr2, $wr_id='') 
{
	global $PT_LIST, $status_arr;
	//$src_arr = array("서울 ", "경기 ", "인천 ");
	$src_arr = array("서울시 ", "서울특별시 ", "경기도 ", "인천시 ");
	$dst_arr = array("서울 ", "서울 ", "경기 ", "인천 ");
	$addr = str_replace($src_arr, $dst_arr, trim($addr));
	$addr_s = trim(str_replace($dst_arr, "", $addr));
	
	$winsn = $wr_id;
	$cnt = 0;
	if(!$wr_id) {
		$winsn = mt_rand(100000, 999999);
	} else {
		$sql = " select * from write_loaninfo where wr_id = '{$wr_id}' limit 1 ";
		$wr = sql_fetch($sql);
		$wr_subject = $wr['wr_subject'];
	}
	
	//global $jsb;
    $str = "<div class='info_window' id='win-".$winsn."'>";
	$str .= "<p><b>".$addr." ".$addr2." :: ".$wr_subject."</b></p><hr/>";
    $sql = " select * from write_loaninfo where wr_addr1 like '%{$addr_s}%' and wr_id != '{$wr_id}'  order by wr_id desc limit 3 ";
	$str .= "<p>".$sql."</p>";
    $result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        if ($wr_id == $row['wr_id']) continue;
		if($i > 0) $str .= "<hr/>";
		$str .= "<p>".substr($row['wr_datetime'], 0, 11)."</p>";
		$str .= "<p>[".$PT_LIST[$row['pt_idx']]['mb_bizname']."]</p>";
		$str .= "<p>{$row['wr_subject']}</p>";
		$str .= "<p>{$row['wr_addr1']} {$row['wr_addr3']} {$row['wr_addr2']}</p>";
		$str .= "<p>승인한도/금리 : {$row['jd_amount']} / {$row['jd_interest']}</p>";
		$str .= "<p class='red'>".$status_arr[$row['wr_status']]."</p>";
		$str .= "<p><a href='./loaninfo-write.php?w=u&wr_id={$row['wr_id']}' target='_blank'>자세히보기</a></p>";
		$cnt++;
    }

	$src_arr = array("서울 ", "경기 ", "인천 ");
	$dst_arr = array("서울시 ", "경기도 ", "인천시 ");
	$addr = str_replace($src_arr, $dst_arr, $addr);
	
    $sql = " select * from loanaddr_history where la_addr like '{$addr}%'  order by la_id desc limit 3 ";
    $result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
		if($i > 0) $str .= "<hr/>";
		$str .= "<p>".$row['la_date']."</p>";
		$str .= "<p>[{$row['la_partner']}] {$row['la_name']}</p>";
		$str .= "<p>{$row['la_addr']}</p>";
		$str .= "<p>담보가/한도 : {$row['la_guarantee']} / {$row['la_loan_amount']}</p>";
		$str .= "<p><a href='./history-list.php?searchtxt=".urlencode($addr)."' target='_blank'>자세히보기</a></p>";
		$cnt++;
    }
	
    $str .= "</div>";
	$cnt++;		// 디버그용 (상시표시)
	if($cnt) {
		$str = "<span class='btn_infowin' data-winsn='win-".$winsn."'>{$cnt}</span>".$str;
	}
    return $str;
}

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//print_r($_POST);

$partners = get_partnerlist();

// search
$where = array();

if($status != '') {
	$where[] = " wr_status = '{$status}' ";
}

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

$sql = " select count(*) as cnt from write_loaninfo {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select * from write_loaninfo {$where_sql} {$orderby} limit {$start}, {$length} ";
//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['data_no'] = $row['wr_id'];
	$row['no'] = $no;
	$row['wr_ca'] = ($row['wr_ca']=='B')?"일반담보":"지분담보";
	$row['address'] = $row['wr_addr1'];
	$row['address2'] = $row['wr_addr3']." ".$row['wr_addr2'];
	$row['wdate'] = substr($row['wr_datetime'],5,11);
	$row['status'] = $status_arr[$row['wr_status']];
	$row['mb_bizname'] = $partners[$row['pt_idx']]['mb_bizname'];
	$row['mb_name'] = $partners[$row['pt_idx']]['mb_name'];
	$row['jd_condition'] = utf8_strcut($row['jd_condition'],60);
	$row['wr_subject'] = utf8_strcut($row['wr_subject'],25);
	
	
	$pjfile = get_writefile($row['wr_id']);
	$row['filecnt'] = number_format($pjfile['count']);
	
	$row['duppop'] = check_duplicate2($row['wr_addr1'], $row['address2'], $row['wr_id']);
	
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