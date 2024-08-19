<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

if (!empty($member['member_id'])) {
    goto_url('/app');
}

$w 		=  $_POST['w'];
$grcode   = trim($_POST['grcode']);

 if($w=='d') {
	
	if(!$grcode) {
		alert('필수입력값이 누락되었습니다.');
	}
	
	$sql = " delete from p2p_publicofficial where grcode = '{$grcode}'";
	$result = sql_query($sql, TRUE);
	
	goto_url('./publicofficial.php');
	die();
	
}

