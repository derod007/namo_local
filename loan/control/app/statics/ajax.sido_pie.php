<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

error_reporting(E_ALL);
ini_set("display_errors", 1);

// 한눈에 보기 앱별 주문비율
$sdate = date("Y-m-d", strtotime($_POST['sdate']));
$edate = date("Y-m-d", strtotime($_POST['edate']));

$sql = "SELECT sido, count(*) as cnt
FROM (
	SELECT wr_id, pt_idx, wr_status, LEFT(wr_addr1, 2) AS sido FROM `write_loaninfo` WHERE wr_datetime between '{$sdate} 00:00:00' and '{$edate} 23:59:59'
) v
GROUP BY sido
";
//echo $sql;
$result = sql_query($sql);
$i = 0; $total = 0;
$labels = array();
$datas = array();
//$rates = array();
while($row=sql_fetch_array($result)){
	$labels[] = $row['sido'];
	$datas[] = $row['cnt'];
	$total += $row['cnt'];
	$i++;
}

for($i=0; $i < count($datas); $i++) {
	$rates[$i] = number_format(($datas[$i]/$total) * 100, 1);
}

$total_count = count($labels);

$res = array();
if(count($labels)) {
	$res['data']['labels'] = $labels;
	$res['data']['rates'] = $rates;
	$res['data']['cnts'] = $datas;
} else {
	$res['data']['labels'] = array('None');
	$res['data']['rates'] = array(100);
	$res['data']['cnts'] = array(0);
}
die(json_encode($res));

