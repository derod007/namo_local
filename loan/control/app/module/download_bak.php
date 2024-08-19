<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

if(!$member['mb_id']) {
	alert('로그인후 이용해주세요.', '/');
}

if((empty($wr_id)) || empty($no)) { 
	alert('정상적인 접근이 아닙니다.');
}

$sql = "select * from write_loaninfo where wr_id = '{$wr_id}' limit 1";
$row = sql_fetch($sql);
if(!$row['wr_id']) {
	alert('접근권한이 없습니다.');
}

if($wr_id) {
	$file = get_writefile($wr_id);
}

$filepath = JSB_PATH.$file[$no]['path'].'/'.$file[$no]['name'];
$filepath = addslashes($filepath);
if (!is_file($filepath) || !file_exists($filepath))
    alert('파일이 존재하지 않습니다.'.$filepath);

$original = iconv('utf-8', 'euc-kr', $file[$no]['source']);

if(preg_match("/msie/i", $_SERVER['HTTP_USER_AGENT']) && preg_match("/5\.5/", $_SERVER['HTTP_USER_AGENT'])) {
    header("content-type: doesn/matter");
    header("content-length: ".filesize("$filepath"));
    header("content-disposition: attachment; filename=\"$original\"");
    header("content-transfer-encoding: binary");
} else {
    header("content-type: file/unknown");
    header("content-length: ".filesize("$filepath"));
    header("content-disposition: attachment; filename=\"$original\"");
    header("content-description: php generated data");
}
header("pragma: no-cache");
header("expires: 0");
flush();

$fp = fopen($filepath, 'rb');

$download_rate = 10;

while(!feof($fp)) {
    print fread($fp, round($download_rate * 1024));
    flush();
    usleep(1000);
}
fclose ($fp);
flush();
