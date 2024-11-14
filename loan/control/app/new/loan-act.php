<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

$w 	= trim($_POST['w']);
$wr_id 	= trim($_POST['wr_id']);
$prev_status 	= trim($_POST['prev_status']);
$jd_autoid 	= trim($_POST['jd_autoid']);
if(!$jd_autoid) $jd_autoid = 0;

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
$wr_rental_deposit	= safe_request_string(trim($_POST['wr_rental_deposit']));
$wr_amount 	= safe_request_string(trim($_POST['wr_amount']));
$wr_link1 	= safe_request_string(trim($_POST['wr_link1']));
$wr_link1_subj 	= safe_request_string(trim($_POST['wr_link1_subj']));
$wr_link2 	= safe_request_string(trim($_POST['wr_link2']));
$wr_link2_subj	= safe_request_string(trim($_POST['wr_link2_subj']));

$calculate_log_data = safe_request_string(trim($_POST['calculate_log_data']));

$wr_part_percent = (int)$wr_part_percent;
if(!$wr_part_percent) $wr_part_percent = 0;

if($wr_part == "A") {
	$wr_part_percent = "100";
} else if($wr_part == "P") {
	$wr_part_percent = "50";
}

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
	$sql = "SELECT * FROM `loan_write` WHERE wr_id='{$wr_id}' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['wr_id']) {
		alert('해당되는 데이터가 없습니다');
	}
	//print_r2($ap);
}

if(!$w) { 
	$pt_idx   = safe_request_string(trim($_POST['pt_idx']));
	
	$src_arr = array("서울시 ", "서울특별시 ", "경기도 ", "인천시 ", "제주특별자치도 ", "강원특별자치도 ", "전북특별자치도 ");
	$dst_arr = array("서울 ", "서울 ", "경기 ", "인천 ", "제주 ", "강원 ", "전북 ");
	$wr_addr1 = str_replace($src_arr, $dst_arr, trim($wr_addr1));
	
	$sql_pt = "";
	if($pt_idx) {
		$pt_mem = get_partnerdata($pt_idx);
		$sql_pt = " pt_idx = '".$pt_mem['parent_id']."', pt_name = '".$pt_mem['mb_bizname']."',";
	}
	
	$sql = " insert into `loan_write`
				set {$sql_pt} wr_ca   = '{$wr_ca}',
					wr_part   = '{$wr_part}',
					wr_part_percent   = '{$wr_part_percent}',
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
					wr_agent = '{$wr_agent}',
					jd_autoid = '{$jd_autoid}'
					";
	echo "<pre>".$sql."</pre>";
	$result = sql_query($sql, FALSE);
	$wr_id = sql_insert_id();
	
	log_write($wr_id, $pt_idx, $member['mb_id'], "0", $wr_status );
	alert('등록되었습니다.', './loan-write.php?w=u&wr_id='.$wr_id);
	die();
	
} else if($w=='u') {
	
	$sql = " update `loan_write`
				set wr_ca   = '{$wr_ca}',
					wr_part   = '{$wr_part}',
					wr_part_percent   = '{$wr_part_percent}',
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
					wr_rental_deposit	= '{$wr_rental_deposit}'
			  where wr_id   = '{$wr_id}' ";
	//echo "<pre>".$sql."</pre>";
	sql_query($sql);
	
	log_write($wr_id, '', $member['mb_id'], $prev_status, $wr_status );
	alert('수정되었습니다.', './loan-write.php?w=u&wr_id='.$wr_id);
	die();
} else if($w=='pr') {
	
	$next_status   = safe_request_string(trim($_POST['next_status']));
	$status_sql = "";
	if(!empty($next_status)) {
		$status_sql = " wr_status = '{$next_status}',";
	}

	$sql = " update `loan_write`
				set {$status_sql}
					wr_price_log = '{$calculate_log_data}',
					jd_amount  = '{$jd_amount}',
					jd_interest  = '{$jd_interest}',
					jd_condition  = '{$jd_condition}',
					jd_memo  = '{$jd_memo}',
					rf_first1 = '{$rf_first1}',
					rf_first2 = '{$rf_first2}',
					rf_first3 = '{$rf_first3}'
			where wr_id   = '{$wr_id}' ";
	sql_query($sql);

	log_write($wr_id, '', $member['mb_id'], $prev_status, $next_status );
    jdlog_write($wr_id, $member['mb_id'], $_POST );
	// // 기존 값 가져오기
	// $existing_data = sql_fetch("SELECT jd_amount, jd_interest, jd_condition, jd_memo, rf_first1, rf_first2, rf_first3 FROM loan_write WHERE wr_id = '{$wr_id}'");

	// // 값 비교
	// $memo_changed = ($existing_data['jd_memo'] != $jd_memo);
	// $other_fields_changed = "0";
	// $other_fields_changed = (
	// 	$existing_data['jd_amount'] != $jd_amount ||
	// 	$existing_data['jd_interest'] != $jd_interest ||
	// 	$existing_data['jd_condition'] != $jd_condition ||
	// 	$existing_data['rf_first1'] != $rf_first1 ||
	// 	$existing_data['rf_first2'] != $rf_first2 ||
	// 	$existing_data['rf_first3'] != $rf_first3
	// );

	// // 업데이트 쿼리 실행
	// $sql = "UPDATE `loan_write`
	// 		SET {$status_sql}
	// 			jd_amount = '{$jd_amount}',
	// 			jd_interest = '{$jd_interest}',
	// 			jd_condition = '{$jd_condition}',
	// 			jd_memo = '{$jd_memo}',
	// 			rf_first1 = '{$rf_first1}',
	// 			rf_first2 = '{$rf_first2}',
	// 			rf_first3 = '{$rf_first3}'
	// 		WHERE wr_id = '{$wr_id}'";

	// sql_query($sql);

	// // 로그 기록
	// log_write($wr_id, '', $member['mb_id'], $prev_status, $next_status);

	// // jd_memo가 변경되었고 다른 필드도 변경된 경우에만 jdlog_write 호출
	// if ($other_fields_changed || ($memo_changed && $other_fields_changed)) {
	// 	jdlog_write($wr_id, $member['mb_id'], $_POST);
	// }

	// echo "<script>alert('저장되었습니다.');history.go(-2);</script>";
	// die();
	alert('저장되었습니다.', './loan-list.php');
	
} else {
	alert('잘못된 접근입니다.');
}

//die();
alert('저장되었습니다.', './loan-list.php');
