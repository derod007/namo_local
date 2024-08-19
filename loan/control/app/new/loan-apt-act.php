<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

print_r2($_POST);


$w 	= trim($_POST['w']);
$wr_id 	= trim($_POST['wr_id']);
$prev_status 	= trim($_POST['prev_status']);

$wr_ca   = safe_request_string(trim($_POST['wr_ca']));
$wr_part   = safe_request_string(trim($_POST['wr_part']));
$wr_part_percent   = safe_request_string(trim($_POST['wr_part_percent']));
$wr_part_percent   = ((int)$wr_part_percent)?(int)$wr_part_percent:0;

$wr_subject 	= safe_request_string(trim($_POST['wr_subject']));
$wr_addr1 	= safe_request_string(trim($_POST['wr_addr1']));		// 주소
$wr_addr2 	= safe_request_string(trim($_POST['wr_addr2']));		// 단지명
$wr_addr3 	= safe_request_string(trim($_POST['wr_addr3']));		// 동/호수
$wr_addr_ext1 	= safe_request_string(trim($_POST['wr_addr_ext1']));	// 층/세대수
$wr_m2 	= safe_request_string(trim($_POST['wr_m2']));				// 전용면적
$wr_region = trim($_POST['s_region']);		// 지역코드
$wr_danzi = trim($_POST['s_danzi']);		// 지역코드

$wr_status = '81';		// 81 ~ 89 번대는 자동승인 상태로 사용

$wr_ip = $_SERVER['REMOTE_ADDR'];
$wr_agent = $_SERVER['HTTP_USER_AGENT'];

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}


$pt_idx   = safe_request_string(trim($_POST['pt_idx']));
$sql_pt = "";
if($pt_idx) {
	$sql_pt = " pt_idx = '{$pt_idx}', ";
}

$sql = " insert into `loan_apt_tmp`
			set {$sql_pt} wr_ca   = '{$wr_ca}',
				wr_part   = '{$wr_part}',
				wr_part_percent   = '{$wr_part_percent}',
				wr_subject   = '{$wr_subject}',
				wr_addr1   = '{$wr_addr1}',
				wr_addr2   = '{$wr_addr2}',
				wr_addr3   = '{$wr_addr3}',
				wr_addr_ext1   = '{$wr_addr_ext1}',
				wr_m2   = '{$wr_m2}',
				wr_region = '{$wr_region}',
				wr_amount   = '{$wr_amount}',
				wr_status = '{$wr_status}',
				wr_datetime = NOW(),
				wr_ip   = '{$wr_ip}',
				wr_agent = '{$wr_agent}'
				";
//echo "<pre>".$sql."</pre>";
$result = sql_query($sql, FALSE);
$wr_id = sql_insert_id();

@log_write($wr_id, $pt_idx, $member['mb_id'], "0", $wr_status, $sql );

//alert('등록되었습니다.', './loan-apt-next.php?wr_id='.$wr_id);
goto_url('./loan-apt-next.php?wr_id='.$wr_id);

