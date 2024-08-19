<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

$member = sql_fetch("select * from {$jsb['member_table']} where mb_id='{$_SESSION['ss_login_id']}'");

$old_password   = sql_password(trim($_POST['old_password']));
$new_password 	= trim($_POST['new_password']);
$confirm_password	= trim($_POST['confirm_password']);

if($member['mb_pw']==$old_password) {
	if($new_password === $confirm_password) {
		
		$new_password = sql_password($new_password);
		
		$sql = " update {$jsb['member_table']} 
					set mb_pw = '{$new_password}' 
				where idx = '{$member['idx']}' ";
		$res = sql_query($sql);
		if($res) {
			alert('비밀번호가 변경되었습니다.', './myinfo.php');
		}
	} else {
		alert('비밀번호 확인이 일치하지 않습니다.');
	}
} else {
	alert('기존 비밀번호가 일치하지 않습니다.');
}

goto_url('./myinfo.php');
