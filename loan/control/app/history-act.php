<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

$w 	= trim($_POST['w']);
$la_id 	= trim($_POST['la_id']);

$la_date   = safe_request_string(trim($_POST['la_date']));
$la_partner 	= safe_request_string(trim($_POST['la_partner']));
$la_name 	= safe_request_string(trim($_POST['la_name']));
$la_addr 	= safe_request_string(trim($_POST['la_addr']));
$la_m2 	= safe_request_string(trim($_POST['la_m2']));
$la_guarantee 	= safe_request_string(trim($_POST['la_guarantee']));
$la_priority_amount 	= safe_request_string(trim($_POST['la_priority_amount']));
$la_maximum_credit 	= safe_request_string(trim($_POST['la_maximum_credit']));
$la_loan_amount 	= safe_request_string(trim($_POST['la_loan_amount']));
$la_category 	= safe_request_string(trim($_POST['la_category']));
$la_caloan 	= safe_request_string(trim($_POST['la_caloan']));
$la_remark 	= safe_request_string(trim($_POST['la_remark']));
$la_ref1 	= safe_request_string(trim($_POST['la_ref1']));
$la_ref2	= safe_request_string(trim($_POST['la_ref2']));

if($w =='') {
	
} else if($w=='u') {
	$sql = "SELECT * FROM `loanaddr_history` WHERE la_id='{$la_id}' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['la_id']) {
		alert('해당되는 데이터가 없습니다');
	}
	//print_r2($ap);
}

if(!$w) { 
	
	$sql = " insert into `loanaddr_history`
				set la_date   = '{$la_date}',
					la_partner   = '{$la_partner}',
					la_name   = '{$la_name}',
					la_addr   = '{$la_addr}',
					la_m2   = '{$la_m2}',
					la_guarantee   = '{$la_guarantee}',
					la_priority_amount   = '{$la_priority_amount}',
					la_maximum_credit   = '{$la_maximum_credit}',
					la_loan_amount   = '{$la_loan_amount}',
					la_category = '{$la_category}',
					la_caloan   = '{$la_caloan}',
					la_remark = '{$la_remark}',
					la_ref1   = '{$la_ref1}',
					la_ref2 = '{$la_ref2}',
					la_datetime = NOW()
					";
	//echo "<pre>".$sql."</pre>";
	$result = sql_query($sql, FALSE);
	
} else if($w=='u') {
	
	$sql = " update `loanaddr_history`
				set la_date   = '{$la_date}',
					la_partner   = '{$la_partner}',
					la_name   = '{$la_name}',
					la_addr   = '{$la_addr}',
					la_m2   = '{$la_m2}',
					la_guarantee   = '{$la_guarantee}',
					la_priority_amount   = '{$la_priority_amount}',
					la_maximum_credit   = '{$la_maximum_credit}',
					la_loan_amount   = '{$la_loan_amount}',
					la_category = '{$la_category}',
					la_caloan   = '{$la_caloan}',
					la_remark = '{$la_remark}',
					la_ref1   = '{$la_ref1}',
					la_ref2 = '{$la_ref2}'
			  where la_id   = '{$la_id}' ";
	//echo "<pre>".$sql."</pre>";
	sql_query($sql);

} else {
	alert('잘못된 접근입니다.');
}

//die();
alert('저장되었습니다.', './history-list.php');
