<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

session_unset(); // 모든 세션변수를 언레지스터 시켜줌
session_destroy(); // 세션해제함

$link = HOME_URL;
alert('로그아웃 되었습니다.',$link);
//goto_url($link);
?>
