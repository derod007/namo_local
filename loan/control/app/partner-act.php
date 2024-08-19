<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );
//print_r2($_POST);
//die();

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

$w 	= trim($_POST['w']);
$idx 	= trim($_POST['idx']);

$mb_id   = safe_request_string(trim($_POST['mb_id']));
$mb_id   = strtolower($mb_id);
$mb_pw 	= safe_request_string(trim($_POST['mb_pw']));
$mb_name	= safe_request_string(trim($_POST['mb_name']));
$mb_bizname 	= safe_request_string(trim($_POST['mb_bizname']));
$mb_use 	= safe_request_string(trim($_POST['mb_use']));
$mb_display 	= safe_request_string(trim($_POST['mb_display']));

$mb_level	= safe_request_string(trim($_POST['mb_level']));
if(!$mb_level) $mb_level = '2';

if(strlen($mb_id) < 4 ) {
	alert('아이디는 4자 이상으로 등록해주세요'.strlen($mb_id));
	die();
}

if($mb_pw && strlen($mb_pw) < 4 ) {
	alert('패스워드는 4자 이상으로 등록해주세요');
	die();
}

$sql = "SELECT * FROM `partner_member` WHERE mb_id='{$mb_id}' limit 1";
$row = sql_fetch($sql);

if($row['idx'] && $row['idx'] != $idx ) {
	alert('이미 사용중인 아이디 입니다.');
	die();
}

if($w =='') {
	;
} else if($w=='u' || $w == 'su') {
	$sql = "SELECT * FROM `partner_member` WHERE idx='{$idx}' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['idx']) {
		alert('해당되는 데이터가 없습니다');
	}
	//print_r2($ap);
}

if(!$w) { 
	$mb_pw = sql_password($mb_pw);
	
	$sql = " insert into `partner_member`
				set mb_id   = '{$mb_id}',
					mb_pw   = '{$mb_pw}',
					mb_name   = '{$mb_name}',
					mb_bizname   = '{$mb_bizname}',
					mb_level   = '{$mb_level}',
					mb_joindate = NOW(),
					mb_lastlogin = NOW(),
					mb_use   = '{$mb_use}',
					mb_display   = '{$mb_display}',
					is_sub = '0',
					parent_id = ''
					";
	//echo "<pre>".$sql."</pre>";
	$result = sql_query($sql, FALSE);
	
	@log_partnerid($sql);
	alert('저장되었습니다.', './partner-list.php');
	
} else if($w=='u') {
	
	if($mb_pw) {
		$mb_pw = sql_password($mb_pw);
		$sql_pw = " mb_pw   = '{$mb_pw}', ";
	}
	
	$sql = " update `partner_member`
				set mb_id   = '{$mb_id}', {$sql_pw}
					mb_name   = '{$mb_name}',
					mb_bizname   = '{$mb_bizname}',
					mb_level   = '{$mb_level}',
					mb_use   = '{$mb_use}',
					mb_display   = '{$mb_display}'
			  where idx   = '{$idx}' ";
	//echo "<pre>".$sql."</pre>";
	sql_query($sql);
	
	@log_partnerid($sql);
	alert('저장되었습니다.', './partner-list.php');

} else if($w=='sw') {

	$pt_idx 	= trim($_POST['pt_idx']);
	
	$mb_pw = sql_password($mb_pw);
	
	$sql = " insert into `partner_member`
				set mb_id   = '{$mb_id}',
					mb_pw   = '{$mb_pw}',
					mb_name   = '{$mb_name}',
					mb_bizname   = '{$mb_bizname}',
					mb_level   = '{$mb_level}',
					mb_joindate = NOW(),
					mb_lastlogin = NOW(),
					mb_use   = '{$mb_use}',
					mb_display   = '{$mb_display}',
					is_sub = '1',
					parent_id = '{$pt_idx}'
					";
	//echo "<pre>".$sql."</pre>";
	$result = sql_query($sql, FALSE);
	
	@log_partnerid($sql);
	alert('저장되었습니다.', './partner-sub-list.php?idx='.$pt_idx);
	
} else if($w=='su') {
	
	if($mb_pw) {
		$mb_pw = sql_password($mb_pw);
		$sql_pw = " mb_pw   = '{$mb_pw}', ";
	}
	
	$sql = " update `partner_member`
				set mb_id   = '{$mb_id}', {$sql_pw}
					mb_name   = '{$mb_name}',
					mb_bizname   = '{$mb_bizname}',
					mb_level   = '{$mb_level}',
					mb_use   = '{$mb_use}',
					mb_display   = '{$mb_display}'
			  where idx   = '{$idx}' ";
	//echo "<pre>".$sql."</pre>";
	sql_query($sql);
	
	@log_partnerid($sql);
	alert('저장되었습니다.', './partner-sub-list.php?idx='.$pt_idx);

} else {
	alert('잘못된 접근입니다.');
}

//die();
