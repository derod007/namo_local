<?php
// http://managedev.xfund.co.kr/app/new/autoltv-input.php

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

die();

function ltvconf_input ($sido, $part, $priority, $ltv ) {
    global $jsb;
	
	$write_table = "region_ltvconf";
	
	$ltv_rcode = $sido['region'];
	$ltv_rname = trim($sido['dong']);
	$ltv_setcode = "realave2019";
	$ltv_interest = "13.5";
	$ltv_use = "1";
	


	$sql = " insert into {$write_table}
				set ltv_rcode   = '{$ltv_rcode}',
					ltv_rname   = '{$ltv_rname}',
					ltv_part   = '{$part}',
					ltv_priority   = '{$priority}',
					ltv_val    = '{$ltv}',
					ltv_setcode    = '{$ltv_setcode}',
					ltv_interest    = '{$ltv_interest}',
					ltv_use   = '{$ltv_use}',
					ltv_datetime = NOW(); ";
	return "<pre>".$sql."</pre>";
	//$result = sql_query($sql, FALSE);
	//return;
}


    $sql = " select * from sigungu_code_ext where step='1' and region != '42000' ";
    $result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
		
		echo ltvconf_input ($row, 'A', 'F', '75' );
		echo ltvconf_input ($row, 'A', 'A', '75' );
		echo ltvconf_input ($row, 'H', 'F', '80' );
		echo ltvconf_input ($row, 'H', 'A', '80' );
    }

