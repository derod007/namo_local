#!/usr/bin/php -q
<?php
################################################################################
# 실행방법    : /usr/bin/php /home/namo/loan/control/cron_script/cancel_processing_loan_cron.php
# 단지정보를 가져오기
################################################################################

//die();
// DBCONFIG 파일을 인클루드 하기 위해 선언
$_SERVER['HTTP_HOST'] = "manage.xfund.co.kr";	// PHP Warning  용
$HOME_DIR = "/home/namo/loan/control";
include_once ($HOME_DIR."/inc/dbconfig.php");
include_once ($HOME_DIR."/inc/common.lib.php");    // 공통 라이브러리
//include_once ($HOME_DIR."/cronscript/mailer.lib.php");   // 메일 라이브러리

    $connect_db = sql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD) or die('MySQL Connect Error!!!');
    $select_db  = sql_select_db(MYSQL_DB, $connect_db) or die('MySQL DB Error!!!');

    // mysql connect resource $ti 배열에 저장 - 명랑폐인님 제안
    $jsb['connect_db'] = $connect_db;

    sql_query(" set names utf8 ");
    if(defined('MYSQL_SET_MODE') && MYSQL_SET_MODE) sql_query("SET SESSION sql_mode = ''");
    if (defined(TIMEZONE)) sql_query(" set time_zone = '".TIMEZONE."'");


define('LIMIT_YMD',    date("Y-m-d", strtotime(TIME_YMD." -121days")));

//$sql = "SELECT * FROM `write_loaninfo` WHERE `wr_status` = '30' and wr_datetime <= '".LIMIT_YMD." 00:00:00' order by wr_id asc limit 10 ";
$sql = " select *, (select reg_date from log_action where wr_id = write_loaninfo.wr_id and next_status='30' limit 1 ) as procdate from write_loaninfo where wr_status = '30' and wr_datetime <= '".LIMIT_YMD." 00:00:00' order by procdate asc limit 10 ";

echo $sql.PHP_EOL;

$result = sql_query($sql);
while($row=sql_fetch_array($result)){
	echo $row['wr_id']." / ".$row['wr_status']." / ".$row['wr_datetime']." / ".PHP_EOL;
	
	// 해당 건 취소처리 wr_status = '99'	// 진행취소
	$sql_up = "update `write_loaninfo` set  `wr_status` = '99' where wr_id='".$row['wr_id']."' limit 1 ";
	echo $sql_up.PHP_EOL;
	sql_query($sql_up);
	log_write($row['wr_id'], '', 'system', $row['wr_status'], '99' );
	
}

