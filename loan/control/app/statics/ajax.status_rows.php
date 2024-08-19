<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// 한눈에 보기 앱별 주문비율
$sdate = date("Y-m-d", strtotime($_POST['sdate']));
$edate = date("Y-m-d", strtotime($_POST['edate']));

$sql = "select wr_status, count(*) as cnt from (
	select wr_id, wr_status, wr_datetime from `write_loaninfo` where wr_datetime between '{$sdate} 00:00:00' and '{$edate} 23:59:59'
) as v group by wr_status";
//echo $sql;
$result = sql_query($sql);
$i = 0; $total = 0;
$labels = array();
$datas = array();
$rates = array();
while($row=sql_fetch_array($result)){
	$row['label'] = ($status_arr[$row['wr_status']])?$status_arr[$row['wr_status']]:$row['wr_status'];
	$row['cnt'] = $row['cnt'];
	
	$data[] = $row;
	
	$total += $row['cnt'];
	$i++;
}

$total_count = count($data);

for($i=0; $i < count($data); $i++) {
	$data[$i]['rate'] = number_format(($data[$i]['cnt']/$total) * 100, 1);
}

$res['draw'] = intval($draw);
//$res['success'] = true;
$res['recordsTotal'] = intval($total_count);
$res['recordsFiltered'] = intval($total_count);
//$res['page'] = $page;
//$res['search'] = $search;
$res['data'] = $data;
$res['total']['cnt'] = number_format($total_count);
//$res['sql'] = $sql;

echo json_encode($res);

exit;
?>