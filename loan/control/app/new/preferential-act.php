<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

$list_table = "region_preferential";

$w 	= trim($_POST['w']);
$rp_id 	= trim($_POST['rp_id']);
$rp_rcode   = safe_request_string(trim($_POST['rp_rcode']));
$rp_rname  =  get_sidocode_name($rp_rcode);
$rp_deposit_amt 	= safe_request_string(trim($_POST['rp_deposit_amt']));
$rp_repay_amt 	= safe_request_string(trim($_POST['rp_repay_amt']));
$rp_use	= safe_request_string(trim($_POST['rp_use']));

if(!$w) { 

	$sql = "SELECT * FROM {$list_table} WHERE rp_rcode='{$rp_rcode}' limit 1";
	$row = sql_fetch($sql);
	
	if($row['rp_id']) {
		alert('이미 존재하는 지역코드 입니다. 등록된 데이터를 수정해주세요.');
		die();
	}
	
	$sql = " insert into {$list_table}
				set rp_rcode   = '{$rp_rcode}',
					rp_rname   = '{$rp_rname}',
					rp_deposit_amt   = '{$rp_deposit_amt}',
					rp_repay_amt   = '{$rp_repay_amt}',
					rp_use   = '{$rp_use}',
					rp_datetime = NOW() ";
	//echo "<pre>".$sql."</pre>";
	$result = sql_query($sql, FALSE);
	
} else if($w=='u') {

	$sql = "SELECT * FROM {$list_table} WHERE rp_id='{$rp_id}' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['rp_id']) {
		alert('해당되는 데이터가 없습니다');
		die();
	}
	//print_r2($ap);
	
	$sql = " update {$list_table}
				set rp_rcode   = '{$rp_rcode}',
					rp_rname   = '{$rp_rname}',
					rp_deposit_amt   = '{$rp_deposit_amt}',
					rp_repay_amt   = '{$rp_repay_amt}',
					rp_use   = '{$rp_use}',
					rp_datetime = NOW()
			  where rp_id   = '{$rp_id}' ";
	//echo "<pre>".$sql."</pre>";
	sql_query($sql);

} else {
	alert('잘못된 접근입니다.');
}

//die();
alert('저장되었습니다.', './preferential-list.php');
