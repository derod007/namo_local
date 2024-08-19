<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

$data_no   = trim($_POST['data_no']);
$UniqueNo 	= trim($_POST['UniqueNo']);
$GubunCode 	= trim($_POST['GubunCode']);
$BudongsanSojaejibeon 	= trim($_POST['BudongsanSojaejibeon']);
$Owner 	= trim($_POST['Owner']);
$NM_pname 	= trim($_POST['NM_pname']);
$NM_ncode 	= trim($_POST['NM_ncode']);
$NM_borrower 	= trim($_POST['NM_borrower']);
$autocheck 	= trim($_POST['autocheck']);
$memo 	= trim(addslashes($_POST['memo']));
$w 	= trim($_POST['w']);


if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//print_r2($_POST);
//die();


if(!$w) {
	$sql = "SELECT * FROM `tilko_managed_data` WHERE UniqueNo='{$UniqueNo}' limit 1";
	$row = sql_fetch($sql);
	
	if($row['idx']) {
		$Message = "이미 등록된 물건입니다.";
		print('{"Status":"Error","Message":"'.$Message.'","Result_cnt":"0"}');
		die();
	}
	//print_r2($ap);
	
	
	if(!$autocheck) $autocheck = '0';
	
	$sql = " insert into `tilko_managed_data`
				set UniqueNo   = '{$UniqueNo}',
					GubunCode   = '{$GubunCode}',
					BudongsanSojaejibeon   = '{$BudongsanSojaejibeon}',
					Owner   = '{$Owner}',
					NM_pname   = '{$NM_pname}',
					NM_ncode   = '{$NM_ncode}',
					NM_borrower   = '{$NM_borrower}',
					autocheck   = '{$autocheck}',
					memo = '{$memo}',
					wdatetime = NOW(),
					udatetime = NOW()
					";
	//echo "<pre>".$sql."</pre>";
	$result = sql_query($sql, FALSE);
	
	
	$Message = "정상적으로 등록되었습니다.";
	print('{"Status":"OK","Message":"'.$Message.'","Result_cnt":"1"}');

} else {
	$Message = "잘못된 접근입니다.";
	print('{"Status":"Error","Message":"'.$Message.'","Result_cnt":"0"}');
	
}
