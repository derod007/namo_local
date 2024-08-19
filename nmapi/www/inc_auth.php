<?php
// 서버IP 와 인증 요청 Key 를 체크한다.

$ALLOW_IP = array(
	"58.229.176.15", // ITeasy 서버
	"116.125.140.62", // FORIZ2020 서버
	"175.121.155.210", // 나모펀딩 사무실
);
if(!in_array($_SERVER['REMOTE_ADDR'],$ALLOW_IP)) {
	die(header('HTTP/1.0 403 Forbidden'));
}


function sido_text($title) {
	
	if($title == '제주특별자치도') $title = '제주도';
	else if($title == '강원특별자치도') $title = '강원도';
	else if($title == '세종특별자치시') $title = '세종시';
	else if($title == '세종특별시') $title = '세종시';
	
	return $title;
}

