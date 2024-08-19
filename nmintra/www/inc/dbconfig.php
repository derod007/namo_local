<?php

if (PHP_VERSION >= '5.1.0') {
    //if (function_exists("date_default_timezone_set")) date_default_timezone_set("Asia/Seoul");
    date_default_timezone_set("Asia/Seoul");
}
define('TIMEZONE', 'Asia/Seoul');

/********************
    DB 접속정보
********************/

define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'namo');
define('MYSQL_PASSWORD', 'namo12#$');
define('MYSQL_DB', 'namo');
define('MYSQL_SET_MODE', false);


// 이 상수가 정의되지 않으면 각각의 개별 페이지는 별도로 실행될 수 없음
define('_JSB_', true);

/********************
    경로 상수
********************/
define('COOKIE_DOMAIN',  '');

// URL 은 브라우저상에서의 경로 (도메인으로 부터의)
define('HOME_URL', 'http://'.$_SERVER['HTTP_HOST']);
define('JSB_DATA_DIR',       'data');
define('JSB_FILE_DIR',       'file');
define('JSB_SESSION_DIR',    'session');

define('JSB_URL', '');
define('JSB_DATA_URL',      JSB_URL.'/'.JSB_DATA_DIR);
define('JSB_FILE_URL',      JSB_DATA_URL.'/'.JSB_FILE_DIR);

// PATH 는 서버상에서의 절대경로
define('JSB_PATH', $_SERVER['DOCUMENT_ROOT']);
define('JSB_DATA_PATH',     JSB_PATH.'/'.JSB_DATA_DIR);
define('JSB_FILE_PATH',     JSB_DATA_PATH.'/'.JSB_FILE_DIR);
define('JSB_SESSION_PATH',  JSB_DATA_PATH.'/'.JSB_SESSION_DIR);

/********************
    시간 상수
********************/
// 서버의 시간과 실제 사용하는 시간이 틀린 경우 수정하세요.
// 하루는 86400 초입니다. 1시간은 3600초
// 6시간이 빠른 경우 time() + (3600 * 6);
// 6시간이 느린 경우 time() - (3600 * 6);
define('SERVER_TIME',    time());
define('TIME_YMDHIS',    date('Y-m-d H:i:s', SERVER_TIME));
define('TIME_YMD',       substr(TIME_YMDHIS, 0, 10));
define('TIME_HIS',       substr(TIME_YMDHIS, 11, 8));

// 입력값 검사 상수 (숫자를 변경하시면 안됩니다.)
define('ALPHAUPPER',      1); // 영대문자
define('ALPHALOWER',      2); // 영소문자
define('ALPHABETIC',      4); // 영대,소문자
define('NUMERIC',         8); // 숫자
define('HANGUL',         16); // 한글
define('SPACE',          32); // 공백
define('SPECIAL',        64); // 특수문자

// 퍼미션
define('DIR_PERMISSION',  0755); // 디렉토리 생성시 퍼미션
define('FILE_PERMISSION', 0644); // 파일 생성시 퍼미션

// SQL 에러를 표시할 것인지 지정
// 에러를 표시하려면 TRUE 로 변경
define('JSB_DISPLAY_SQL_ERROR', FALSE);

// escape string 처리 함수 지정
// addslashes 로 변경 가능
define('ESCAPE_FUNCTION', 'sql_escape_string');

/********************
  테이블 이름 설정
********************/

$jsb     = array();

$jsb['member_table'] = 'site_member';   // 관리자 회원정보
$jsb['actualprice_table'] = 'actual_price';   // 실거래가 데이터
$jsb['actualprice_history_table'] = 'actualprice_history';   // 실거래가 크롤링 기록
$jsb['actualprice_statics_table'] = 'actual_statics';   // 실거래가 데이터 집계

$jsb['regioncode_table'] = 'sigungu_code_ext';   // 법정 코드
$jsb['kbapt_info_table'] = 'kbapt_info';   // KB아파트 전국 기본정보(KB코드별)
$jsb['kbapt_link_table'] = 'kbapt_link';   // KB아파트-국토부아파트 연결정보
$jsb['kbapt_sise2_table'] = 'kbapt_sise_2020';   // 아파트 시세(2020년)

$jsb['kbapt_table'] = 'apt_py';   // 아파트 법정동/평형(구)
$jsb['kbapt_sise_table'] = 'apt_sise';   // 아파트 시세(구)

$jsb['p2p_status_table'] = 'p2p_status';   // P2P업체별 상태값

/********************
  config
********************/
$config['rows'] = 50; 			// 한페이지에 보여줄 rows 기본값
define('JS_VERSION', date("Ymd"));	// JS/CSS VERSION

$allow_ips = array();
$allow_ips[] = "175.121.155.210";	// 나모 사무실
$allow_ips[] = "121.133.86.80"; 	// 히어로
$allow_ips[] = "61.101.123.162";
$allow_ips[] = "127.0.0.1";  // 로컬
?>