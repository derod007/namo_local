<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

$list_table = "region_ltvconf";

$w 	= trim($_POST['w']);
$ltv_id 	= trim($_POST['ltv_id']);
$ltv_rcode   = safe_request_string(trim($_POST['ltv_rcode']));
$ltv_rname  =  get_sidocode_name($ltv_rcode);
$ltv_part  	= safe_request_string(trim($_POST['ltv_part']));
$ltv_priority  	= safe_request_string(trim($_POST['ltv_priority']));
$ltv_val  	= safe_request_string(trim($_POST['ltv_val']));
$ltv_setcode  	= safe_request_string(trim($_POST['ltv_setcode']));
$ltv_interest  	= safe_request_string(trim($_POST['ltv_interest']));
$ltv_use	= safe_request_string(trim($_POST['ltv_use']));

if(!$w) { 

	$sql = "SELECT * FROM {$list_table} WHERE ltv_rcode='{$ltv_rcode}' and ltv_part='{$ltv_part}' and ltv_priority='{$ltv_priority}' limit 1";
	$row = sql_fetch($sql);
	
	if($row['ltv_id']) {
		alert('이미 존재하는 데이터 입니다. 등록된 데이터를 수정해주세요.');
		die();
	}
	
	$sql = " insert into {$list_table}
				set ltv_rcode   = '{$ltv_rcode}',
					ltv_rname   = '{$ltv_rname}',
					ltv_part   = '{$ltv_part}',
					ltv_priority   = '{$ltv_priority}',
					ltv_val    = '{$ltv_val}',
					ltv_setcode    = '{$ltv_setcode}',
					ltv_interest    = '{$ltv_interest}',
					ltv_use   = '{$ltv_use}',
					ltv_datetime = NOW() ";
	//echo "<pre>".$sql."</pre>";
	$result = sql_query($sql, FALSE);
	
} else if($w=='u') {

	$sql = "SELECT * FROM {$list_table} WHERE ltv_id='{$ltv_id}' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['ltv_id']) {
		alert('해당되는 데이터가 없습니다');
		die();
	}
	
	$sql = "SELECT * FROM {$list_table} WHERE ltv_rcode='{$ltv_rcode}' and ltv_part='{$ltv_part}' and ltv_priority='{$ltv_priority}' and ltv_id!={$row[ltv_id]} limit 1";
	$row2 = sql_fetch($sql);
	
	if($row2['ltv_id']) {
		alert('중복되는 데이터 입니다. 등록된 데이터를 확인하고 수정해주세요.');
		die();
	}
	
	//print_r2($ap);
	
	$sql = " update {$list_table}
				set ltv_rcode   = '{$ltv_rcode}',
					ltv_rname   = '{$ltv_rname}',
					ltv_part   = '{$ltv_part}',
					ltv_priority   = '{$ltv_priority}',
					ltv_val    = '{$ltv_val}',
					ltv_setcode    = '{$ltv_setcode}',
					ltv_interest    = '{$ltv_interest}',
					ltv_use   = '{$ltv_use}',
					ltv_datetime = NOW()
			  where ltv_id   = '{$ltv_id}' ";
	//echo "<pre>".$sql."</pre>";
	sql_query($sql);

} else {
	alert('잘못된 접근입니다.');
}

//die();
alert('저장되었습니다.', './autoltv-list.php');
