<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// 한눈에 보기 앱별 주문비율
$sdate = date("Y-m-d", strtotime($_POST['sdate']));
$edate = date("Y-m-d", strtotime($_POST['edate']));

$sql = " select * from partner_member where idx !='1' order by idx asc ";
$result = sql_query($sql);
$partner_arr = array();
for ($i=0; $row=sql_fetch_array($result); $i++) {
	$row['mb_use_txt'] = ($row['mb_use']!='1')?"(중지)":"";
	$partner_arr[$row['idx']] = $row['mb_bizname'].$row['mb_use_txt'];
}



$sql = "SELECT pt_idx, wr_status, count(*) as cnt
FROM (
	SELECT wr_id, pt_idx, wr_status FROM `write_loaninfo` WHERE wr_datetime between '{$sdate} 00:00:00' and '{$edate} 23:59:59'
) v
GROUP BY pt_idx, wr_status
order by pt_idx, wr_status
";
//echo $sql;
$result = sql_query($sql);
$i = 0; $total = 0;
$labels = array();
$datas = array();
//$rates = array();
while($row=sql_fetch_array($result)){
	$row['partner'] = ($partner_arr[$row['pt_idx']])?$partner_arr[$row['pt_idx']]:$row['pt_idx'];
	$row['label'] = ($status_arr[$row['wr_status']])?$status_arr[$row['wr_status']]:$row['wr_status'];
	$row['cnt'] = $row['cnt'];
	
	$data[] = $row;
	
	$total += $row['cnt'];
	$i++;
}

$total_count = count($data);

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