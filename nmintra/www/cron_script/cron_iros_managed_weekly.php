#!/usr/bin/php -q
<?php
################################################################################
# 실행방법    : /usr/bin/php /home/namo/nmintra/www/cron_script/cron_iros_managed_weekly.php
# 등기부 관리목록 조회
################################################################################

// DBCONFIG 파일을 인클루드 하기 위해 선언
$_SERVER['HTTP_HOST'] = "nmintra.xfund.co.kr";	// PHP Warning  용 nmintra.event-on.kr
$HOME_DIR = "/home/namo/nmintra/www";

include_once($HOME_DIR.'/inc/dbconfig.php');   // 설정 파일

//==============================================================================
// 공통
//------------------------------------------------------------------------------
    include_once($HOME_DIR.'/inc/common.lib.php');    // 공통 라이브러리

    $connect_db = sql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD) or die('MySQL Connect Error!!!');
    $select_db  = sql_select_db(MYSQL_DB, $connect_db) or die('MySQL DB Error!!!');

    // mysql connect resource $ti 배열에 저장 - 명랑폐인님 제안
    $jsb['connect_db'] = $connect_db;

    sql_query(" set names utf8 ");
    if(defined('MYSQL_SET_MODE') && MYSQL_SET_MODE) sql_query("SET SESSION sql_mode = ''");
    if (defined(TIMEZONE)) sql_query(" set time_zone = '".TIMEZONE."'");
//==============================================================================

error_reporting(E_ALL);
ini_set("display_errors", 1);



function curl_request($data) {
	
	if(!$data['UniqueNo'] || !$data['InsRealCls'] || !$data['A103Name']) {
		echo "Incorrect Request";
	}
	
	$ch = curl_init();
	$url = 'http://nmintra.xfund.co.kr/cron_script/api_revtwelcomeevtc.php';	// URL
	/*
	$queryParams = '?' . urlencode('UniqueNo') . '=' . urlencode($data['UniqueNo']); // 물건고유번호
	$queryParams .= '&' . urlencode('InsRealCls') . '=' . urlencode($data['InsRealCls']); // 구분코드
	$queryParams .= '&' . urlencode('A103Name') . '=' . urlencode($data['A103Name']); // 소유자명
	
	//echo $queryParams;
	*/
	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	$response = curl_exec($ch);
	curl_close($ch);
	
	print($response.PHP_EOL);
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 1. 조회대상 관리목록을 DB에서 불러오기

$sql = " select * from tilko_managed_data where UniqueNo != '' and autocheck='1' order by idx desc";
$result = sql_query($sql);
while($row=sql_fetch_array($result)){
	echo $row['UniqueNo'].PHP_EOL;
	
	// curl 로 POST로 데이터 전송해서 요청.
	// http://nmintra.event-on.kr/cron_script/api_revtwelcomeevtc.php
	
	$data = array();
	$data['UniqueNo'] = $row['UniqueNo'];
	$data['InsRealCls'] = $row['GubunCode'];
	$data['A103Name'] = $row['Owner'];
	
	curl_request($data);
	unset($data);
	
	sleep(10);
	
}	

print "".PHP_EOL;
//die();


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

die();

