<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

$ap_no   = trim($_POST['ap_no']);
$kbapt_id 	= trim($_POST['kbapt_id']);
$memo 	= trim(addslashes($_POST['memo']));
$w 	= trim($_POST['w']);


if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

if(!$ap_no || !$kbapt_id) {
    alert('잘못된 접근입니다.');
}

$sql = "SELECT * FROM `{$jsb['actualprice_table']}` WHERE no='{$ap_no}' limit 1";
$ap = sql_fetch($sql);
//print_r2($ap);

$sql = "SELECT a.*, b.dong as sigundong FROM `{$jsb['kbapt_info_table']}` a
				left join  {$jsb['regioncode_table']} as b on a.rcode = b.code and b.use_yn='존재'
			WHERE idx='{$kbapt_id}' limit 1";
$kb = sql_fetch($sql);
//print_r2($kb);

// KB 아파트 테이블 생성
// 기존 apt_py 테이블에서 단지목록으로 재구성 + 실거래가 연동용 추가필드
// 등록은 실거래가 데이터 목록에서
// 수정은 KB 단지목록 데이터 에서

if(!$w) { 
	
	if($kb['sigundong']) {
		$sgd = explode(" ", $kb['sigundong']);
		$kb['si'] = $sgd[0];
		$kb['gun'] = $sgd[1];
		$kb['dong'] = $sgd[2];
		
	}
	
	$sql = " insert into {$jsb['kbapt_link_table']}
				set si   = '{$kb['si']}',
					gun   = '{$kb['gun']}',
					dong   = '{$kb['dong']}',
					danzi   = '{$kb['danzi']}',
					kbcode   = '{$kb['kbcode']}',
					juso   = '{$kb['juso']}',
					ap_region   = '{$ap['region_code']}',
					ap_danzi   = '{$ap['danzi']}',
					ap_zibun   = '{$ap['zibun']}',
					memo = '{$memo}',
					wdate = NOW()
					";
	//echo "<pre>".$sql."</pre>";
	$result = sql_query($sql, FALSE);
	
} else if($w=='u') {
	$kblink_id 	= trim($_POST['kblink_id']);
	if($kblink_id) {
	$sql = " update {$jsb['kbapt_link_table']}
				set ap_region   = '{$ap['region_code']}',
					ap_danzi   = '{$ap['danzi']}',
					ap_zibun   = '{$ap['zibun']}',
					memo = '{$memo}',
					wdate = NOW()
			  where no   = '{$kblink_id}' ";
	sql_query($sql);
	
	} else {
		alert('수정할 내용이 선택되지 않았습니다.');
	}
	
} else if($w=='d') {
	$kblink_id 	= trim($_POST['kblink_id']);
	if($kblink_id) {
		
		$sql = " delete from {$jsb['kbapt_link_table']} where no='{$kblink_id}' limit 1 ";
		sql_query($sql);
	}
} else {
	alert('잘못된 접근입니다.');
}

alert('저장되었습니다.', './realprice_list.php');
//goto_url('./realprice_list.php');
