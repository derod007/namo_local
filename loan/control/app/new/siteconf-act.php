<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

$list_table = "site_conf";

$idx   = safe_request_string(trim($_POST['idx']));
$auto_interest   = safe_request_string(trim($_POST['auto_interest']));

$sql = "SELECT * FROM {$list_table} WHERE idx='{$idx}' limit 1";
$row = sql_fetch($sql);

if(!$row['idx']) {
	alert('해당되는 데이터가 없습니다');
	die();
}
//print_r2($ap);

$sql = " update {$list_table} set auto_interest   = '{$auto_interest}' where idx   = '{$idx}' ";
//echo "<pre>".$sql."</pre>";
sql_query($sql);

//die();
alert('저장되었습니다.', './siteconf-write.php');
