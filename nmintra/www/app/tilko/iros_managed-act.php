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
$delchk 	= trim($_POST['delchk']);
$memo 	= trim(addslashes($_POST['memo']));
$w 	= trim($_POST['w']);

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

if(!$autocheck) $autocheck = '0';
if(!$delchk) $delchk = '0';

//print_r2($_POST);
//die();


if($w=='u') {
	$sql = "SELECT * FROM `tilko_managed_data` WHERE idx='{$data_no}' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['idx']) {
		alert('해당되는 데이터가 없습니다');
	}
	//print_r2($ap);
}


if(!$w) { 
	
	$sql = " insert into `tilko_managed_data`
				set UniqueNo   = '{$UniqueNo}',
					GubunCode   = '{$GubunCode}',
					BudongsanSojaejibeon   = '{$BudongsanSojaejibeon}',
					Owner   = '{$Owner}',
					NM_pname   = '{$NM_pname}',
					NM_ncode   = '{$NM_ncode}',
					NM_borrower   = '{$NM_borrower}',
					autocheck   = '{$autocheck}',
					delchk   = '{$delchk}',
					memo = '{$memo}',
					wdatetime = NOW(),
					udatetime = NOW()
					";
	//echo "<pre>".$sql."</pre>";
	$result = sql_query($sql, FALSE);
	
} else if($w=='u') {
	
	$sql = " update `tilko_managed_data`
				set UniqueNo   = '{$UniqueNo}',
					GubunCode   = '{$GubunCode}',
					BudongsanSojaejibeon   = '{$BudongsanSojaejibeon}',
					Owner   = '{$Owner}',
					NM_pname   = '{$NM_pname}',
					NM_ncode   = '{$NM_ncode}',
					NM_borrower   = '{$NM_borrower}',
					autocheck   = '{$autocheck}',
					delchk   = '{$delchk}',
					memo = '{$memo}',
					udatetime = NOW()
			  where idx   = '{$data_no}' ";
	sql_query($sql);
	
} else if($w=='d') {
	$sql = " delete from `tilko_managed_data` where idx  = '{$data_no}' limit 1 ";
	sql_query($sql);
} else {
	alert('잘못된 접근입니다.');
}

//die();
alert('저장되었습니다.', './iros_managed_list.php');
