<?php
ini_set("display_errors", 0);
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

$jd_id 	= trim($_REQUEST['jd_id']);

$sql = "select * from `loan_apt_tmp` where wr_id = '{$jd_id}' limit 1";
$row = sql_fetch($sql);

$result = array();

if($row['wr_id']) {
	$result['ret_code'] = '0000';
	$result['wr_id'] = $row['wr_id'];
	$result['wr_mortgage'] = $row['wr_mortgage'];
	$result['wr_mortgage_total'] = $row['wr_mortgage_total'];
	$result['wr_judge'] = $row['wr_judge'];
	$result['wr_judge_code'] = $row['wr_judge_code'];
	$result['wr_deposit'] = $row['wr_deposit'];
	$result['wr_part_percent'] = $row['wr_part_percent'];
	
	$jd_data = json_decode($result['wr_judge'], true);
	
	//print_r2($jd_data);
	
	$html = "<table class='table table-striped table-bordered'>";
	if($result['wr_part_percent'] < 100 && $result['wr_part_percent'] > 0 ) {
		$html .= "<tr><td>지분율</td><td>".number_format($result['wr_part_percent'])."%</td></tr>";
	}
	if($result['wr_deposit'] > 0) {
		$html .= "<tr><td>임차보증금</td><td>".number_format($result['wr_deposit'])."만원</td></tr>";
	}
	$html .= "<tr><td>선순위채권금액</td><td>".number_format($result['wr_mortgage_total'])."만원</td></tr>";
	$html .= "<tr><td>적용 평균 실거래가</td><td>".number_format($jd_data['realprice']['result']['data']['aveprice2'])."만원(".$jd_data['realprice']['result']['data']['year']."년 기준)</td></tr>";
	$html .= "<tr><td>자동심사 결과</td><td>".$jd_data['result']['judge1']."<br/>";
	$html .= $jd_data['result']['judge2']."<br/>";
	$html .= $jd_data['result']['judge3']."<br/>";
	$html .= $jd_data['result']['judge4']."<br/>";
	$html .= $jd_data['result']['judge5']."<br/></td></tr>";
	if($jd_data['judge']['last_judge'] < 0) {
		$html .= "<tr><td>최종 한도</td><td class='red'>".number_format($jd_data['judge']['last_judge'])."만원</td></tr>";
	} else {
		$html .= "<tr><td>최종 한도</td><td>".number_format($jd_data['judge']['last_judge'])."만원";
		if($jd_data['judge']['interest'] > 0) {
			$html .= " (".$jd_data['judge']['interest']."%)";
		}
		$html .= "</td></tr>";
	}
	if($jd_data['ltv']['ltv_id'] > 0) {
	$html .= "<tr><td>적용LTV</td><td>";
		$html .= " ".$jd_data['ltv']['ltv_val']." % ";
		$html .= " (ref : ".$jd_data['ltv']['ltv_id'].")<br/>";
		//foreach($jd_data['ltv'] as $k => $v) {
		//	$html .= $k ." : ".$v."<br/>";
		//}
	$html .= "</td></tr>";
	}
	
	
	$html .= "</table>";

} else {
	$result['ret_code'] = '9999';

	$html = "<table class='table table-striped table-bordered'>";
	$html .= "<tr><td class='empty'>자동심사 조회 결과가 없습니다.</td></tr>";
	$html .= "</table>";
}

echo $html;
