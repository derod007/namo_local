<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

$w 	= trim($_POST['w']);
$wr_id 	= trim($_POST['wr_id']);

$wr_ca   = safe_request_string(trim($_POST['wr_ca']));
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

$wr_addr1 = str_replace("  ", " ", $wr_addr1);
$wr_addr2 = str_replace("  ", " ", $wr_addr2);

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
	$sql = "SELECT * FROM `write_loaninfo` WHERE wr_id='{$wr_id}' and pt_idx='".$member['idx']."'  limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['wr_id']) {
		alert('해당되는 데이터가 없습니다');
	}
	//print_r2($ap);
}

if(!$w) { 
	
	$sql = " insert into `write_loaninfo`
				set pt_idx = '{$member[idx]}',
					wr_ca   = '{$wr_ca}',
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
	log_write($wr_id, $member['mb_id'], '', "0", $wr_status );

	
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
	
	// 정보수정 저장
	log_write($wr_id, $member['mb_id'], '', $row['wr_status'], $row['wr_status'] );
	
} else if($w=='pr') {
	
	// 진행요청 체크
	$wr_name   = safe_request_string(trim($_POST['wr_name']));
	$wr_tel 	= safe_request_string(trim($_POST['wr_tel']));
	$wr_memo 	= safe_request_string(trim($_POST['wr_memo']));
	
	// 기존 상태값 체크 (넘어온 값과 비교)
	$sql = "SELECT * FROM `write_loaninfo` WHERE wr_id='{$wr_id}' and pt_idx='".$member['idx']."'  limit 1";
	$row = sql_fetch($sql);
		
	if($row['wr_status'] != '10') {
		alert('진행요청이 가능한 상태가 아닙니다.', './loaninfo-write.php?w=u&wr_id='.$wr_id);
		die();
	}
	if(!$wr_tel) {
		alert('차주 연락처가 누락되었습니다.', './loaninfo-write.php?w=u&wr_id='.$wr_id);
		die();
	}
	
	$sql = " update `write_loaninfo` set wr_status = '30', wr_name='{$wr_name}', wr_tel='{$wr_tel}', wr_memo='{$wr_memo}' where wr_id = '{$wr_id}' ";
	//echo "<pre>".$sql."</pre>";
	sql_query($sql);
	
	log_write($wr_id, $member['mb_id'], '', $row['wr_status'], '30' );
	
	alert('진행요청이 접수되었습니다.', './loaninfo-list.php');

} else if($w=='pc') {
	
	// 기존 상태값 체크 (넘어온 값과 비교)
	$sql = "SELECT * FROM `write_loaninfo` WHERE wr_id='{$wr_id}' and pt_idx='".$member['idx']."'  limit 1";
	$row = sql_fetch($sql);
		
	if($row['wr_status'] == '60' || $row['wr_status'] == '20' ) {
		alert('진행취소가 가능한 상태가 아닙니다.', './loaninfo-write.php?w=u&wr_id='.$wr_id);
		die();
	}
	
	$sql = " update `write_loaninfo` set wr_status = '99' where wr_id = '{$wr_id}' ";
	//echo "<pre>".$sql."</pre>";
	sql_query($sql);
	
	log_write($wr_id, $member['mb_id'], '', $row['wr_status'], '99' );
	
	alert('진행취소가 접수되었습니다.', './loaninfo-list.php');
	
} else {
	alert('잘못된 접근입니다.');
}

//die();
alert('저장되었습니다.', './loaninfo-list.php');
