<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

/**
print_r2($_POST);

Array
(
    [w] => 
    [wr_id] => 3
    [wr_live] => T
    [wr_deposit] => 5000
    [wr_agree] => Y
    [mor_no] => Array
        (
            [0] => 1
            [1] => 2
        )

    [mor_amount] => Array
        (
            [0] => 5000
            [1] => 2000
        )

    [mor_bank] => Array
        (
            [0] => 신한은행
            [1] => 홍길동
        )

)
**/

$w 	= trim($_POST['w']);
$wr_id 	= trim($_POST['wr_id']);
$wr_live 	= trim($_POST['wr_live']);
$wr_deposit   = safe_request_string(trim($_POST['wr_deposit']));
$wr_agree 	= trim($_POST['wr_agree']);

if(!$wr_id) {
	alert("잘못된 접근입니다.");
}

$mor_no = $_POST['mor_no'];
$mor_amount = $_POST['mor_amount'];
$mor_bank = $_POST['mor_bank'];
$mor_same = $_POST['mor_same'];

$mor_cnt = count($mor_no);

$wr_status = '82';		// 81 ~ 89 번대는 자동승인 상태로 사용

$report = array();

$mortgage = array();
$total_amt = 0;
$same_amt =  0;
$differ_amt = 0;

if(count($mor_no) > 0) {
	foreach($mor_no as $k => $v) {
		
		$mor_same[$k] = ($mor_same[$k] == "1")?"1":"0";
		
		$dd = array(
			"no" => $k,
			"amt" => $mor_amount[$k],
			"bank" => $mor_bank[$k],
			"same" => $mor_same[$k],
		);
		
		// 설정금액이 있는 경우만 저장
		if($mor_amount[$k]) {
			$mortgage[] = $dd;
			$total_amt += $mor_amount[$k];
			if($mor_same[$k]) {	// 선순위 동일차주
				$same_amt += $mor_amount[$k];
			} else {		// 선순위 상이차주
				$differ_amt += $mor_amount[$k];
			}
		}
	}
}
$mortgage['total'] = $total_amt;
$mortgage['same'] = $same_amt;
$mortgage['differ'] = $differ_amt;
$report['mortgage'] = $mortgage;

//print_r2($mortgage);
//echo "<h4>선순위 Total : ".number_format($total_amt)." 만원</h4>";

$wr_deposit = (int)$wr_deposit;
$wr_mortgage = sql_real_escape_string(json_encode($mortgage, JSON_UNESCAPED_UNICODE));
$wr_mortgage_total = (int)$total_amt;

$sql = " update `loan_apt_tmp`
			set wr_live   = '{$wr_live}',
				wr_deposit   = '{$wr_deposit}',
				wr_tnagree   = '{$wr_agree}',
				wr_mortgage   = '{$wr_mortgage}',
				wr_mortgage_total   = '{$wr_mortgage_total}',
				wr_status = '{$wr_status}'
			where wr_id='{$wr_id}'
				";
//echo "<pre>".$sql."</pre>";
$result = sql_query($sql, FALSE);

@log_write($wr_id, $pt_idx, $member['mb_id'], "81", $wr_status, $sql );	// 이전상태/현재상태

//echo "<h4>보증금 : ".number_format($wr_deposit)." 만원</h4>";
$report['deposit'] = $wr_deposit;

// 로그인 세션(등록자 아이디 등)과 wr_id 를 확인해봐야함.
$sql = "select * from `loan_apt_tmp` where wr_id = '{$wr_id}' limit 1";
$row = sql_fetch($sql);

$wr_region = substr($row['wr_region'],0,2);
$wr_addr1 = $row['wr_addr1'];
$wr_m2 = $row['wr_m2'];
$wr_part = $row['wr_part'];
$wr_part_percent = $row['wr_part_percent'];

$report['apt'] = array(
	"region" => $wr_region,
	"addr1" => $wr_addr1,
	"m2" => $wr_m2,
	"part" => $wr_part,
	"part_percent" => $wr_part_percent,
);


$calc_part_percent = 0;

if($wr_part == 'A') {	// 단독소유
	$calc_part_percent = 1;
	$order_sql = " order by FIELD(`ltv_part`, 'A', 'P', 'PE') ";
} else if($wr_part == 'P') {		// 지분소유(50%)
	$calc_part_percent = 0.5;
	$order_sql = " order by FIELD(`ltv_part`, 'P', 'PE', 'A') ";
} else if($wr_part == 'PE' && $wr_part_percent > 0 ) {		// 지분소유(기타)
	$calc_part_percent = $wr_part_percent * 0.01 ;
	$order_sql = " order by FIELD(`ltv_part`, 'PE', 'A', 'P') ";
}

// 선순위/후순위
if($wr_mortgage_total == 0) {	// 선순위
	$order_sql .= ", FIELD(`ltv_priority`, 'F', 'A') ";
} else {
	$order_sql .= ", FIELD(`ltv_priority`, 'A', 'F') ";
}


$sql = "select * from `region_ltvconf` where ltv_use='1' and ltv_rcode like '{$wr_region}%' {$order_sql} limit 1 ";
$row = sql_fetch($sql);

//echo "<h4>LTV 기준자료</h4>";
//print_r2($row);

if($row['ltv_id']) {
	$ltv_id = $row['ltv_id'];
	$ltv_setcode = $row['ltv_setcode'];
	$ltv_val = $row['ltv_val'];
	$ltv_interest = $row['ltv_interest'];
} else {
	$ltv_id = '0';
	$ltv_setcode = "realave2019";
	$ltv_val = "75";
	
	$sql = "select * from `site_conf` where 1 order by idx desc limit 1";
	$siteconf = sql_fetch($sql);
	$ltv_interest = $siteconf['auto_interest'];
	
}

$report['ltv'] = array(
	"ltv_id" => $ltv_id,
	"ltv_setcode" => $ltv_setcode,
	"ltv_val" => $ltv_val,
	"ltv_interest" => $ltv_interest
);

$sql = "select * from `region_preferential` where rp_use='1' and rp_rcode like '{$wr_region}%'";
$row = sql_fetch($sql);

//echo "<h4>최우선변제금</h4>";
//print_r2($row);

if($row['rp_id']) {
	$rp_repay_amt = $row['rp_repay_amt'];	// 소액 임차보증금
	$rp_deposit_amt = $row['rp_deposit_amt'];	// 보증금 기준금액
} else {
	$rp_repay_amt = "4800";
	$rp_deposit_amt = "14500";
}

$report['rp'] = array(
	"rp_repay_amt" => $rp_repay_amt,
	"rp_deposit_amt" => $rp_deposit_amt,
);

$judge_price = 0;

// 실거래 기준 조회인경우
if(substr($ltv_setcode,0,4) == "real") {
	
	//echo "<h4>실거래가 조회</h4>";
	
	// 실거래 자료 조회
	$param = array();
	$param['addr1'] = $wr_addr1;
	$param['py'] = $wr_m2;
	$param['year'] = (int)substr($ltv_setcode,7,4);
	if(!$param['year']) $param['year'] = 2019;

	//print_r2($param);
	/*
	$param_list = array(
		"search" => $search,
		"addr1" => $param['addr1'],
		"danzi" => $_POST['danzi'],
		"py" => $_POST['py'],
		"year" => $_POST['year'],
	);
	*/
	include('./inc_real_price.php');

	//print_r2($res);
	/*
	Array
	(
		[data] => Array
			(
				[danzi] => 장미1
				[year] => 2019
				[trade_cnt] => 34
				[aveprice] => 156132		// 평형과 관계없이 평균가액
				[aveprice2] => 160791		// 개별 실거래가를 평형대비로 계산하여 요청한 평형을 적용한 평균가액 (평형 검색은 0.9 ~ 1.1배율)
			)

	)
	*/
	
	$judge_price = $res['data']['aveprice2'];
	
}
$report['realprice'] = array(
	"param" => $param,
	"result" => $res,
);


$last_judge = 0;
$fail_code = 0;

if($judge_price) {

	/**
	2024-05-22 변경되는 계산방식

	((시세*지분율)*LTV)-(소액보증금/임대차보증금*지분율)-(선순위 최고액*지분율) -> 선순위 차주 상이물건
	((시세*지분율)*LTV)-(소액보증금/임대차보증금*지분율)-(선순위 최고액) -> 선순위 차주 동일물건

	**/
	
	//echo "<h4>평균 실거래가 : ".number_format($judge_price)."만원</h4>";
	$judge_price2 = $judge_price*$calc_part_percent*($ltv_val/100);
	//echo "<h4>LTV 적용 평가액(지분율) : ".number_format($judge_price2)."만원</h4>";
	
	if($wr_deposit > 0 && $rp_deposit_amt > $wr_deposit) {
		$judge_price3 = $judge_price2 - ($rp_repay_amt * $calc_part_percent);
		//echo "<h4>소액임차보증금 제외금액 : ".number_format($judge_price3)."만원</h4>";
	} else if($wr_deposit > 0 && $rp_deposit_amt <= $wr_deposit) {
		$judge_price3 = $judge_price2 - ($wr_deposit * $calc_part_percent);
		//echo "<h4>임차보증금 제외금액 : ".number_format($judge_price3)."만원</h4>";
	} else {
		$judge_price3 = $judge_price2;
	}
	
	$title_mort = "";
	if($same_amt > 0) {
		$title_mort = "(전체)";
		$judge_price4 = $judge_price3 - $wr_mortgage_total;
	} else {
		$title_mort = "(지분)";
		$judge_price4 = $judge_price3 - ($wr_mortgage_total * $calc_part_percent);
	}
	//echo "<h4>선순위 제외금액 : ".number_format($judge_price4)."만원</h4>";
	
	$last_judge = $judge_price4;
	
	$last_judge = (int)$last_judge / 1000;
	$last_judge = (int)$last_judge * 1000;

	if($last_judge > 0) {
		//echo "<h4>최종한도 : ".number_format($last_judge)."만원</h4>";
	} else {
		// alert("한도가 부족해서 자동부결됩니다. 수동심사를 등록하시겠습니까?");
		$fail_code = 90;
	}
	
} else {
	// alert("기준시세 조회가 실패했습니다. 수동심사를 등록하시겠습니까?");
	$fail_code = 80;
}

$report['judge'] = array(
	"judge1" => $judge_price,
	"judge2" => $judge_price2,
	"judge3" => $judge_price3,
	"judge4" => $judge_price4,
	"last_judge" => $last_judge,
	"fail_code" => $fail_code,
	"interest" => $ltv_interest,
);

$report['result'] = array(
	"judge1" => "평균 실거래가 : ".$judge_price,
	"judge2" => "LTV 적용(지분율) : ".$judge_price2,
	"judge3" => "소액/임차보증금 : ".$judge_price3,
	"judge4" => "선순위 제외".$title_mort." : ".$judge_price4,
	"judge5" => "최종한도(절사) : ".$last_judge,
	"fail_code" => $fail_code,
	"interest" => $ltv_interest,
);

// 조회결과 내역을 json 으로 팩킹해서 DB에 저장

$wr_status = 83;
$wr_judge = sql_real_escape_string(json_encode($report, JSON_UNESCAPED_UNICODE));

$sql = " update `loan_apt_tmp`
			set wr_judge   = '{$wr_judge}',
				wr_judge_code = '{$fail_code}',
				wr_status = '{$wr_status}'
			where wr_id='{$wr_id}'
				";
//echo "<pre>".$sql."</pre>";
$result = sql_query($sql, FALSE);

@log_write($wr_id, $pt_idx, $member['mb_id'], "82", $wr_status, '' );	// 이전상태/현재상태

echo json_encode($report, JSON_UNESCAPED_UNICODE);
