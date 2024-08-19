<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

$w 	= trim($_POST['w']);
$wr_id 	= trim($_POST['wr_id']);
$prev_status 	= trim($_POST['prev_status']);

$wr_ca   = safe_request_string(trim($_POST['wr_ca']));
$wr_part   = safe_request_string(trim($_POST['wr_part']));

$wr_subject 	= safe_request_string(trim($_POST['wr_subject']));
$wr_cont1 	= safe_request_string(trim($_POST['wr_cont1']));
$wr_addr1 	= safe_request_string(trim($_POST['address1']));
$wr_addr2 	= safe_request_string(trim($_POST['address2']));
$wr_addr3 	= safe_request_string(trim($_POST['address3']));
$wr_addr_ext1 	= safe_request_string(trim($_POST['address_ext']));
$wr_m2 	= safe_request_string(trim($_POST['wr_m2']));
$wr_cont2 	= safe_request_string(trim($_POST['wr_cont2']));
$wr_amount 	= safe_request_string(trim($_POST['wr_amount']));
$wr_link1 	= safe_request_string(trim($_POST['wr_link1']));
$wr_link1_subj 	= safe_request_string(trim($_POST['wr_link1_subj']));
$wr_link2 	= safe_request_string(trim($_POST['wr_link2']));
$wr_link2_subj	= safe_request_string(trim($_POST['wr_link2_subj']));

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//print_r2($_POST);
//die();

$wr_ip = $_SERVER['REMOTE_ADDR'];
$wr_agent = $_SERVER['HTTP_USER_AGENT'];

if($w =='') {
	$wr_status = '1';
} else if($w=='u') {
	$sql = "SELECT * FROM `write_loaninfo` WHERE wr_id='{$wr_id}' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['wr_id']) {
		alert('해당되는 데이터가 없습니다');
	}
	//print_r2($ap);
}

if(!$w) { 
	$pt_idx   = safe_request_string(trim($_POST['pt_idx']));
	$sql_pt = "";
	if($pt_idx) {
		$sql_pt = " pt_idx = '{$pt_idx}', ";
	}
	
	$sql = " insert into `write_loaninfo`
				set {$sql_pt} wr_ca   = '{$wr_ca}',
					wr_subject   = '{$wr_subject}',
					wr_cont1   = '{$wr_cont1}',
					wr_cont2   = '{$wr_cont2}',
					wr_addr1   = '{$wr_addr1}',
					wr_addr2   = '{$wr_addr2}',
					wr_addr3   = '{$wr_addr3}',
					wr_addr_ext1   = '{$wr_addr_ext1}',
					wr_m2   = '{$wr_m2}',
					wr_link1_subj = '{$wr_link1_subj}',
					wr_link1   = '{$wr_link1}',
					wr_link2_subj = '{$wr_link2_subj}',
					wr_link2   = '{$wr_link2}',
					wr_link3_subj = '{$wr_link3_subj}',
					wr_link3   = '{$wr_link3}',
					wr_amount   = '{$wr_amount}',
					wr_status = '{$wr_status}',
					wr_datetime = NOW(),
					wr_ip   = '{$wr_ip}',
					wr_agent = '{$wr_agent}'
					";
	//echo "<pre>".$sql."</pre>";
	$result = sql_query($sql, FALSE);
	$wr_id = sql_insert_id();
	
	log_write($wr_id, $pt_idx, $member['mb_id'], "0", $wr_status );
	alert('등록되었습니다.', './loaninfo-write.php?w=u&wr_id='.$wr_id);
	die();
	
} else if($w=='u') {
	
	$sql = " update `write_loaninfo`
				set wr_ca   = '{$wr_ca}',
					wr_subject   = '{$wr_subject}',
					wr_cont1   = '{$wr_cont1}',
					wr_cont2   = '{$wr_cont2}',
					wr_addr1   = '{$wr_addr1}',
					wr_addr2   = '{$wr_addr2}',
					wr_addr3   = '{$wr_addr3}',
					wr_addr_ext1   = '{$wr_addr_ext1}',
					wr_m2   = '{$wr_m2}',
					wr_link1_subj = '{$wr_link1_subj}',
					wr_link1   = '{$wr_link1}',
					wr_link2_subj = '{$wr_link2_subj}',
					wr_link2   = '{$wr_link2}',
					wr_link3_subj = '{$wr_link3_subj}',
					wr_link3   = '{$wr_link3}',
					wr_amount   = '{$wr_amount}'
			  where wr_id   = '{$wr_id}' ";
	//echo "<pre>".$sql."</pre>";
	sql_query($sql);
	
	log_write($wr_id, '', $member['mb_id'], $prev_status, $wr_status );
	alert('수정되었습니다.', './loaninfo-write.php?w=u&wr_id='.$wr_id);
	die();
} else if($w=='pr') {
	
	$next_status   = safe_request_string(trim($_POST['next_status']));
	$status_sql = "";
	if(!empty($next_status)) {
		$status_sql = " wr_status = '{$next_status}',";
	}
	
	$sql = " update `write_loaninfo`
				set {$status_sql}
					jd_amount  = '{$jd_amount}',
					jd_interest  = '{$jd_interest}',
					jd_condition  = '{$jd_condition}',
					jd_memo  = '{$jd_memo}',
					rf_first1 = '{$rf_first1}',
					rf_first2 = '{$rf_first2}',
					rf_first3 = '{$rf_first3}'
			  where wr_id   = '{$wr_id}' ";
	//echo "<pre>".$sql."</pre>";
	sql_query($sql);

	log_write($wr_id, '', $member['mb_id'], $prev_status, $next_status );
	jdlog_write($wr_id, $member['mb_id'], $_POST );
	
	alert('저장되었습니다.', './loaninfo-list.php');
	
} else {
	alert('잘못된 접근입니다.');
}

//die();
alert('저장되었습니다.', './loaninfo-list.php');
