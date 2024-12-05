<?php

$status_arr = array(
	'1' => "접수",
	'9' => "접수취소",
	'10' => "가승인",
	'20' => "부결",
	'30' => "진행요청",
	'60' => "대출실행",
	'90' => "중복",
	'99' => "진행취소"
);

/**
	'2' => "심사중",
**/

$use_status_arr = array(
	'0' => "중지",
	'1' => "사용",
);

// 자동승인 LTV 기준코드
$setcode_arr = array(
	"realave2020" => "2020년 실거래평균가",
	"realave2019" => "2019년 실거래평균가",
	"realave3y" => "3년 실거래평균가",
	"realave2y" => "2년 실거래평균가",
	"realave1y" => "1년 실거래평균가",
);

// 자동승인 LTV 지분여부
$ltv_part_arr = array(
	'A' => "전체지분",
	'H' => "50% 지분",
	'P' => "기타 지분",
);

// 자동승인 LTV 선순위여부
$ltv_priority_arr = array(
	'F' => "선순위",
	'A' => "후순위",
);

// 파트너목록 
$PT_LIST = get_partnerlist();

// array 를 SELECT 형식으로 얻음
function get_array_select($arr, $name, $selected='', $event='')
{
    $str = "<select id=\"$name\" name=\"$name\" $event>\n";
	$i = 0;
	foreach($arr as $k => $v) {
        if ($i == 0) $str .= "<option value=\"\">선택</option>";
        $str .= option_selected($k, $selected, $v);
		$i++;
    }
    $str .= "</select>";
    return $str;
}

// 계정 상태명을 SELECT 형식으로 얻음
function get_use_select($name, $selected='', $event='')
{
    $str = "<select id=\"$name\" name=\"$name\" $event class=\"form-control\">\n";
	$str .= option_selected("1", $selected, "사용");
	$str .= option_selected("0", $selected, "중지");
    $str .= "</select>";
    return $str;
}

// 파트너명을 SELECT 형식으로 얻음
function get_partner_select($name, $selected='', $event='')
{
	//global $jsb;
    $sql = " select * from partner_member where 1 order by mb_bizname asc ";
    $result = sql_query($sql);
    $str = "<select id=\"$name\" name=\"$name\" $event class=\"form-control\">\n";
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        if ($i == 0) $str .= "<option value=\"\">전체</option>";
		$subname = "";
		if($row['is_sub'] == '1') $subname = "(".$row['mb_name'].")";
		$row['mb_use_txt'] = ($row['mb_use']!='1')?"(중지)":"";
        $str .= option_selected($row['idx'], $selected, $row['mb_bizname'].$subname.$row['mb_use_txt']);
    }
    $str .= "</select>";
    return $str;
}

// 기 접수내역 조회확인
function check_duplicate($addr, $addr2, $wr_id='') 
{
	global $PT_LIST, $status_arr;
	//$src_arr = array("서울 ", "경기 ", "인천 ");
	$src_arr = array("서울시 ", "서울특별시 ", "경기도 ", "인천시 ", "제주특별자치도 ", "강원특별자치도 ", "전북특별자치도 ");
	$dst_arr = array("서울 ", "서울 ", "경기 ", "인천 ", "제주 ", "강원 ", "전북 ");
	$addr = str_replace($src_arr, $dst_arr, trim($addr));
	$addr_s = trim(str_replace($dst_arr, "", $addr));
		
	$winsn = $wr_id;
	$cnt = 0;
	if(!$wr_id) {
		$winsn = mt_rand(100000, 999999);
	} else {
		$sql = " select * from loan_write where wr_id = '{$wr_id}' limit 1 ";
		$wr = sql_fetch($sql);
		$wr_subject = $wr['wr_subject'];
	}
	
	//global $jsb;
    $str = "<div class='info_window' id='win-".$winsn."'>";
	$str .= "<p><b>".$addr." ".$addr2." :: ".$wr_subject."</b></p><hr/>";
    // $sql = " select * from loan_write where wr_addr1 like '%{$addr_s}%' and wr_id != '{$wr_id}' and wr_status!='90' order by wr_id desc limit 6 ";	// 중복체크된건 제외
	//park 띄어쓰기 제외 검색
	$sql = " SELECT * FROM loan_write WHERE REPLACE(wr_addr1, ' ', '') LIKE REPLACE('%{$addr_s}%', ' ', '') 
      			AND wr_id != '{$wr_id}' AND wr_status != '90' ORDER BY wr_id DESC LIMIT 6;
			";

    $result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        if ($wr_id == $row['wr_id']) continue;
		if($i > 0) $str .= "<hr/>";
		$str .= "<p>".substr($row['wr_datetime'], 0, 11)."</p>";
		
		switch($row['wr_ca']) {
			case "B" : $row['wr_ca'] = " - 일반담보"; break;
			case "B1" : $row['wr_ca'] = " - 지분담보"; break;
			default : $row['wr_ca'] = " - 기타"; 
		}
		
		if(empty($row['wr_m2'])) $row['wr_m2'] = "-";
		if(empty($row['rf_first3'])) $row['rf_first3'] = "-";
		
		$str .= "<p>[".$PT_LIST[$row['pt_idx']]['mb_bizname']."]  {$row['wr_ca']}</p>";
		$str .= "<p>{$row['wr_subject']}</p>";
		$str .= "<p>{$row['wr_addr1']} {$row['wr_addr3']} {$row['wr_addr2']}</p>";
		$str .= "<p>전용면적(㎡) : {$row['wr_m2']} / 담보가산정 : {$row['rf_first3']}</p>";
		$str .= "<p>승인한도/금리 : {$row['jd_amount']} / {$row['jd_interest']}</p>";
		$str .= "<p>부대조건 : {$row['jd_condition']}</p>";
		$str .= "<p class='red'>".$status_arr[$row['wr_status']]."</p>";
		$str .= "<p><a href='./loan-write.php?w=u&wr_id={$row['wr_id']}' target='_blank'>자세히보기</a></p>";
		$str .= "<p>
					<button class='duplicate-data-btn' 
						data-rf-first3='{$row['rf_first3']}'
						data-jd-amount='{$row['jd_amount']}' 
						data-jd-interest='{$row['jd_interest']}'
						data-jd-condition='{$row['jd_condition']}'>
						선택
					</button>
				</p>";
		$cnt++;
    }
	//$str .= "<p>{$sql}</p>";
	
	/*
	$src_arr = array("서울 ", "경기 ", "인천 ");
	$dst_arr = array("서울시 ", "경기도 ", "인천시 ");
	$addr = str_replace($src_arr, $dst_arr, $addr);
	
    $sql = " select * from loanaddr_history where la_addr like '{$addr}%'  order by la_id desc limit 3 ";
    $result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
		if($i > 0) $str .= "<hr/>";
		$str .= "<p>".$row['la_date']."</p>";
		$str .= "<p>[{$row['la_partner']}] {$row['la_name']}</p>";
		$str .= "<p>{$row['la_addr']}</p>";
		$str .= "<p>담보가/한도 : {$row['la_guarantee']} / {$row['la_loan_amount']}</p>";
		$str .= "<p><a href='./history-list.php?searchtxt=".urlencode($addr)."' target='_blank'>자세히보기</a></p>";
		$cnt++;
    }
	*/
	
    $str .= "</div>";
	
	// 중복자료가 6건 이상인경우 검색링크 추가
	$link_str = "";
	if($cnt >= 6) {
		$link_str = "&nbsp;<a href='/app/new/loan-list.php?searchtxt=".urlencode($addr_s)."' class='btn_infowin' target='_blank'><i class='fas fa-search'></i></a>";
	}
	
	if($cnt) {
		$str = "<span class='btn_infowin' data-winsn='win-".$winsn."'>{$cnt}</span>".$link_str.$str;
	}
    return $str;
}
