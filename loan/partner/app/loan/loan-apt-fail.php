<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

// 관리자건 파트너건 아이디로 저장되면 구분이 됨.
$pt_id = $member['mb_id'];


// 아파트 검색실패 로그 기록

$sql = " insert into `log_aptsearch`
			set pt_id='{$pt_id}',
				search_region   = '{$s_region}',
				search_keyword   = '{$s_danzi}',
				reg_date = NOW()
				";
//echo "<pre>".$sql."</pre>";
@sql_query($sql, FALSE);

goto_url('./loan-write.php');
