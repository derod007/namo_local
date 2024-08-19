<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

$login_id   = trim($_POST['login_id']);
$login_pw 	= trim($_POST['login_pw']);
$pw = sql_password($login_pw);

if (!$login_id || !$login_pw)
    alert('회원아이디나 비밀번호가 공백이면 안됩니다.');

$sql = " select * from {$jsb['member_table']} where mb_id='{$login_id}' and mb_use='1'";
$mb = sql_fetch($sql);

if (!$mb['mb_id'] || $pw != $mb['mb_pw']) {
    alert('등록된 아이디가 아니거나 비밀번호가 틀립니다.\\n비밀번호는 대소문자를 구분합니다.');
}

// 회원아이디 세션 생성
set_session('ss_login_id', $mb['mb_id']);
if($mb['is_sub']) {
	set_session('ss_partner_id', $mb['parent_id']);
} else {
	set_session('ss_partner_id', $mb['idx']);
}

// 회원이름 쿠키 생성
set_cookie('member_name', $mb['mb_name'], 60*60*24*30);

// FLASH XSS 공격에 대응하기 위하여 회원의 고유키를 생성해 놓는다.
set_session('ss_member_key', md5($mb['mb_id'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));

sql_query("update {$jsb['member_table']} set mb_lastlogin = NOW() where idx='".$mb['idx']."' ");

$member['member_id'] = $mb['mb_id'];

$link = "/app/main.php";

goto_url($link);

