<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
ini_set("display_errors", 0);

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

$w 	= trim($_POST['w']);
$wr_id 	= trim($_POST['wr_id']);
$prev_status 	= trim($_POST['prev_status']);
$next_status 	= trim($_POST['next_status']);
$jd_autoid 	= trim($_POST['jd_autoid']);
$maximum	= trim($_POST['maximum']);
if(!$jd_autoid) $jd_autoid = 0;

$wr_ca   = safe_request_string(trim($_POST['wr_ca']));
$wr_part   = safe_request_string(trim($_POST['wr_part']));
$wr_part_percent   = safe_request_string(trim($_POST['wr_part_percent']));
if(!$wr_part_percent) $wr_part_percent = 0;
$wr_type = safe_request_string(trim($_POST['wr_type']));

$wr_subject 	= safe_request_string(trim($_POST['wr_subject']));
$wr_cont1 	= safe_request_string(trim($_POST['wr_cont1']));
$wr_addr1 	= safe_request_string(trim($_POST['address1']));
$wr_addr2 	= safe_request_string(trim($_POST['address2']));
$wr_addr3 	= safe_request_string(trim($_POST['address3']));
$wr_addr_ext1 	= safe_request_string(trim($_POST['address_ext']));
$wr_m2 	= safe_request_string(trim($_POST['wr_m2']));
$wr_cont2 	= safe_request_string(trim($_POST['wr_cont2']));
$wr_cont3	= safe_request_string(trim($_POST['wr_cont3']));
$wr_cont4	= safe_request_string(trim($_POST['wr_cont4']));
$wr_rental_deposit	= safe_request_string(trim($_POST['wr_rental_deposit']));
$wr_amount 	= safe_request_string(trim($_POST['wr_amount']));
$wr_link1 	= safe_request_string(trim($_POST['wr_link1']));
$wr_link1_subj 	= safe_request_string(trim($_POST['wr_link1_subj']));
$wr_link2 	= safe_request_string(trim($_POST['wr_link2']));
$wr_link2_subj	= safe_request_string(trim($_POST['wr_link2_subj']));

// 자동 한도
$auto_real_price = safe_request_string(trim($_POST['auto_real_price'])) ?: 0;
$auto_ltv = safe_request_string(trim($_POST['auto_ltv'])) ?: 0;
$auto_small_deposit = safe_request_string(trim($_POST['auto_small_deposit'])) ?: 0;
$auto_senior_loan = safe_request_string(trim($_POST['auto_senior_loan'])) ?: 0;

if(!$wr_rental_deposit) $wr_rental_deposit='0';
$auto_deposit = max($auto_small_deposit, $wr_rental_deposit);
$auto_price = 0;
$auto_price = (($auto_real_price * ($wr_part_percent/100)) * ($auto_ltv/100)) - ($auto_deposit * ($wr_part_percent/100)) - ($auto_senior_loan * ($wr_part_percent/100));

if($maximum){
	// if($auto_price>0) $wr_amount = "최대요청 : ".$auto_price;
	// else $wr_amount = "최대요청";
	$wr_amount = "최대요청";
}

if($wr_type == "B"){
	$wr_cont3 = NULL;
	$wr_cont4 = NULL;
}

if($wr_part == "A") {
	$wr_part_percent = "100";
} else if($wr_part == "P") {
	$wr_part_percent = "50";
}

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//priwnt_r2($_POST);
//die();

$wr_ip = $_SERVER['REMOTE_ADDR'];
$wr_agent = $_SERVER['HTTP_USER_AGENT'];

if($member['is_sub']) {
	$pt_idx = $member['parent_id'];
} else {
	$pt_idx = $member['idx'];
} 

	$src_arr = array("서울시 ", "서울특별시 ", "경기도 ", "인천광역시 ", "인천시 ", "제주특별자치도 ", "강원특별자치도 ", "전북특별자치도 ", "부산광역시 ", "대구광역시 ", "대전광역시 " );
	$dst_arr = array("서울 ", "서울 ", "경기 ", "인천 ", "인천 ", "제주 ", "강원 ", "전북 ", "부산 ", "대구 ", "대전 " );
	$wr_addr1 = str_replace($src_arr, $dst_arr, trim($wr_addr1));

	$wr_addr1 = str_replace("  ", " ", $wr_addr1);
	$wr_addr2 = str_replace("  ", " ", $wr_addr2);

if($w =='') {
	$wr_status = '1';
} else if($w=='u') {
	$sql = "SELECT * FROM `loan_write` WHERE wr_id='{$wr_id}' and pt_idx='".$pt_idx."' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['wr_id']) {
		alert('해당되는 데이터가 없습니다');
	}
	//print_r2($ap);
}

if(!$w) { 

	$sql_pt = " pt_idx = '{$pt_idx}', pt_name='{$member['mb_name']}', ";
	
	$sql = " insert into `loan_write`
				set {$sql_pt} wr_ca   = '{$wr_ca}',
					wr_part   = '{$wr_part}',
					wr_part_percent   = '{$wr_part_percent}',
					wr_type	   = '{$wr_type}',
					wr_subject = '{$wr_subject}',
					wr_cont1   = '{$wr_cont1}',
					wr_cont2   = '{$wr_cont2}',
					wr_cont3   = '{$wr_cont3}',
					wr_cont4   = '{$wr_cont4}',
					wr_rental_deposit   = '{$wr_rental_deposit}',
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
					wr_auto_price = '{$auto_price}',
					wr_status = '{$wr_status}',
					wr_datetime = NOW(),
					wr_ip   = '{$wr_ip}',
					wr_agent = '{$wr_agent}',
					jd_autoid = '{$jd_autoid}'
					";
	//echo "<pre>".$sql."</pre>";
	
	$log_sql = $sql;	// 로그기록용 sql
	$result = sql_query($sql, FALSE);
	$wr_id = sql_insert_id();
	$log_sql = "[".$wr_id."] : ".$log_sql;	// 로그기록용 sql
	
	// log_write($wr_id, $pt_idx, $member['mb_id'], "0", $wr_status );
	// @log_write_file($log_sql);
	
	// 자동심사 승인건인 경우 가승인 처리를 위한 로직을 돌린다. 24.03.13
	if($jd_autoid) {
		
		$sql = "select * from `loan_apt_tmp` where wr_id = '{$jd_autoid}' limit 1";
		$jd = sql_fetch($sql);
		
		if($jd['wr_id'] && $jd['wr_judge_code'] == '0') {
			
			$jd_data = json_decode($jd['wr_judge'], true);
			$jd_amount = $jd_data['judge']['last_judge'];
			$jd_interest = $jd_data['judge']['interest'];
			$jd_condition = '';
			$jd_memo = '자동 가승인';

			$sql = " update `loan_write`
						set  wr_status = '10',
							jd_amount  = '{$jd_amount}',
							jd_interest  = '{$jd_interest}',
							jd_condition  = '{$jd_condition}',
							jd_memo  = '{$jd_memo}'
					  where wr_id   = '{$wr_id}' ";
			//echo "<pre>".$sql."</pre>";
			sql_query($sql);

			log_write($wr_id, '', 'SYSTEM', '1', '10' );
			
			$row['wr_status'] = '10';
		}

		// 자동심사 승인건의 경우 진행요청 처리. 24.05.16
		if($next_status == '30') {
			
			$wr_name   = safe_request_string(trim($_POST['wr_name']));
			$wr_tel 	= safe_request_string(trim($_POST['wr_tel']));
			$wr_memo 	= safe_request_string(trim($_POST['wr_memo']));

			if(!$wr_tel) {
				alert('차주 연락처가 누락되었습니다.', './loan-write.php?w=u&wr_id='.$wr_id);
				die();
			}
			
			$sql = " update `loan_write` set wr_status = '30', wr_name='{$wr_name}', wr_tel='{$wr_tel}', wr_memo='{$wr_memo}' where wr_id = '{$wr_id}' ";
			//echo "<pre>".$sql."</pre>";
			sql_query($sql);
			
			log_write($wr_id, $member['mb_id'], '', $row['wr_status'], '30' );
			
			alert('진행요청이 접수되었습니다.', './loan-list.php');
			die();
		}
		
	}
	
	
	
	
	//alert('등록되었습니다.', './loan-write.php?w=u&wr_id='.$wr_id);
	alert('등록되었습니다.', './loan-list.php');
	die();
	
} else if($w=='u') {
	
	$sql = " update `loan_write`
				set wr_ca   = '{$wr_ca}',
					wr_part   = '{$wr_part}',
					wr_part_percent   = '{$wr_part_percent}',
					wr_type	   = '{$wr_type}',
					wr_subject   = '{$wr_subject}',
					wr_cont1   = '{$wr_cont1}',
					wr_cont2   = '{$wr_cont2}',
					wr_cont3   = '{$wr_cont3}',
					wr_cont4   = '{$wr_cont4}',
					wr_addr1   = '{$wr_addr1}',
					wr_addr2   = '{$wr_addr2}',
					wr_addr3   = '{$wr_addr3}',
					wr_rental_deposit   = '{$wr_rental_deposit}',
					wr_addr_ext1   = '{$wr_addr_ext1}',
					wr_m2   = '{$wr_m2}',
					wr_link1_subj = '{$wr_link1_subj}',
					wr_link1   = '{$wr_link1}',
					wr_link2_subj = '{$wr_link2_subj}',
					wr_link2   = '{$wr_link2}',
					wr_link3_subj = '{$wr_link3_subj}',
					wr_link3   = '{$wr_link3}',
					wr_amount   = '{$wr_amount}',
					wr_auto_price = '{$auto_price}'
			  where wr_id   = '{$wr_id}' ";
	sql_query($sql);
	
	log_write($wr_id, '', $member['mb_id'], $prev_status, $wr_status );
	// alert('수정되었습니다.', './loan-write.php?w=u&wr_id='.$wr_id);
	alert('등록되었습니다.', './loan-list.php');
	die();

} else if($w=='pr') {
	
	// 진행요청 체크
	$wr_name   = safe_request_string(trim($_POST['wr_name']));
	$wr_tel 	= safe_request_string(trim($_POST['wr_tel']));
	$wr_memo 	= safe_request_string(trim($_POST['wr_memo']));
	
	// 기존 상태값 체크 (넘어온 값과 비교)
	$sql = "SELECT * FROM `loan_write` WHERE wr_id='{$wr_id}' and pt_idx='".$pt_idx."'  limit 1";
	$row = sql_fetch($sql);
		
	if($row['wr_status'] != '10') {
		alert('진행요청이 가능한 상태가 아닙니다.', './loan-write.php?w=u&wr_id='.$wr_id);
		die();
	}
	if(!$wr_tel) {
		alert('차주 연락처가 누락되었습니다.', './loan-write.php?w=u&wr_id='.$wr_id);
		die();
	}
	
	$sql = " update `loan_write` set wr_status = '30', wr_name='{$wr_name}', wr_tel='{$wr_tel}', wr_memo='{$wr_memo}' where wr_id = '{$wr_id}' ";
	//echo "<pre>".$sql."</pre>";
	sql_query($sql);
	
	log_write($wr_id, $member['mb_id'], '', $row['wr_status'], '30' );
	
	alert('진행요청이 접수되었습니다.', './loan-list.php');
	
} else if($w=='pc') {
	
	// 기존 상태값 체크 (넘어온 값과 비교)
	$sql = "SELECT * FROM `loan_write` WHERE wr_id='{$wr_id}' and pt_idx='".$pt_idx."'  limit 1";
	$row = sql_fetch($sql);
		
	if($row['wr_status'] == '60' || $row['wr_status'] == '20' ) {
		alert('진행취소가 가능한 상태가 아닙니다.', './loan-write.php?w=u&wr_id='.$wr_id);
		die();
	}
	
	$sql = " update `loan_write` set wr_status = '99' where wr_id = '{$wr_id}' ";
	//echo "<pre>".$sql."</pre>";
	sql_query($sql);
	
	log_write($wr_id, $member['mb_id'], '', $row['wr_status'], '99' );
	
	alert('진행취소가 접수되었습니다.', './loan-list.php');
	
} else {
	alert('잘못된 접근입니다.');
}

//die();
alert('저장되었습니다.', './loan-list.php');
