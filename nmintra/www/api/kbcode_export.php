<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
// http://apitest.ddiablo.net/api/kbcode_export.php?rcode=36

header("Content-Type: plain/text; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//print_r($_POST);

// search
$where = array();

if($rcode!='') {
	$where_sql = " where rcode like '$rcode%' ";
} else {
	alert('잘못된 접근입니다.');
	die();
}

$orderby = " order by idx ";

$sql = " select kbcode from {$jsb['kbapt_info_table']} {$where_sql} {$orderby} ";
//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0;
while($row=sql_fetch_array($result)){
	echo $row['kbcode'].PHP_EOL;
}
exit;
?>