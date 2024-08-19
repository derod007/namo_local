<?php
// 소유자명 DB 저장

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

$sql = "SELECT * FROM `tilkoapi_risuconfirmsimplec` WHERE UniqueNo='".$_POST['UniqueNo']."' limit 1";
$item = sql_fetch($sql);

if($item['UniqueNo']) {

	$sql2 = "update tilkoapi_risuconfirmsimplec 
					set  Owner = '".addslashes($_POST['Owner'])."'
					where UniqueNo = '".$_POST['UniqueNo']."'
				";
	//echo $sql2;
	sql_query($sql2);

	$response = '{"Status":"OK","Message":"성공","UniqueNo":"'.$_POST['UniqueNo'].'"}';
	print($response);

} else {
	$response = '{"Status":"Error","Message":"해당되는 데이터가 없습니다.","UniqueNo":"'.$_POST['UniqueNo'].'"}';
	print($response);
}

