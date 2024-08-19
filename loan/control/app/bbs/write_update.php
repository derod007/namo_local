<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );
//print_r2($_POST);
//die();

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

$write_table = "bbs_write";

$bo_table = isset($_POST['bo_table']) ? $_POST['bo_table'] : '';

$ca_name = '';
if (isset($_POST['wr_ca'])) {
    $ca_name = substr(trim($_POST['wr_ca']),0,150);
    $ca_name = preg_replace("#[\\\]+$#", "", $ca_name);
}

$wr_subject = '';
if (isset($_POST['wr_subject'])) {
    $wr_subject = substr(trim($_POST['wr_subject']),0,255);
    $wr_subject = preg_replace("#[\\\]+$#", "", $wr_subject);
}
if ($wr_subject == '') {
    $msg[] = '<strong>제목</strong>을 입력하세요.';
}

$wr_content = '';
if (isset($_POST['wr_content'])) {
    $wr_content = substr(trim($_POST['wr_content']),0,65536);
    $wr_content = preg_replace("#[\\\]+$#", "", $wr_content);
}
if ($wr_content == '') {
    $msg[] = '<strong>내용</strong>을 입력하세요.';
}

$wr_open = isset($_POST['wr_open']) ? clean_xss_tags($_POST['wr_open'], 1, 1) : 0;

if ($w == 'u' || $w == 'r') {
	
	$sql = " select * from {$write_table} where wr_id='{$wr_id}' limit 1";
	$wr = sql_fetch($sql);
	
    if (!$wr['wr_id']) {
        alert("글이 존재하지 않습니다.\\n글이 삭제되었거나 이동하였을 수 있습니다.");
    }
}

if ($w == '' || $w == 'u') {
	

} else if ($w == 'r') {

    // 게시글 배열 참조
    $reply_array = &$wr;

    // 최대 답변은 테이블에 잡아놓은 wr_reply 사이즈만큼만 가능합니다.
    if (strlen($reply_array['wr_reply']) == 10) {
        alert("더 이상 답변하실 수 없습니다.\\n답변은 10단계 까지만 가능합니다.");
    }

    $reply_len = strlen($reply_array['wr_reply']) + 1;
	$begin_reply_char = 'A';
	$end_reply_char = 'Z';
	$reply_number = +1;
	$sql = " select MAX(SUBSTRING(wr_reply, $reply_len, 1)) as reply from {$write_table} where wr_parent = '{$reply_array['wr_parent']}' and SUBSTRING(wr_reply, {$reply_len}, 1) <> '' ";
    if ($reply_array['wr_reply']) $sql .= " and wr_reply like '{$reply_array['wr_reply']}%' ";
    $row = sql_fetch($sql);

    if (!$row['reply']) {
        $reply_char = $begin_reply_char;
    } else if ($row['reply'] == $end_reply_char) { // A~Z은 26 입니다.
        alert("더 이상 답변하실 수 없습니다.\\n답변은 26개 까지만 가능합니다.");
    } else {
        $reply_char = chr(ord($row['reply']) + $reply_number);
    }

    $reply = $reply_array['wr_reply'] . $reply_char;

} else {
    alert('w 값이 제대로 넘어오지 않았습니다.');
}


if (!isset($_POST['wr_subject']) || !trim($_POST['wr_subject']))
    alert('제목을 입력하여 주십시오.');

if ($w == '' || $w == 'r') {

    if ($member['mb_id']) {
        $mb_id = $member['mb_id'];
        $wr_name = addslashes(clean_xss_tags($board['bo_use_name'] ? $member['mb_name'] : $member['mb_nick']));
        $wr_password = '';
    } else {
        $mb_id = '';
        // 비회원의 경우 이름이 누락되는 경우가 있음
        $wr_name = clean_xss_tags(trim($_POST['wr_name']));
        if (!$wr_name)
            alert('이름은 필히 입력하셔야 합니다.');
        $wr_password = get_encrypt_string($wr_password);
    }

    if ($w == 'r') {
        // 답변의 원글이 비밀글이라면 비밀번호는 원글과 동일하게 넣는다.
        if ($secret)
            $wr_password = $wr['wr_password'];

        $wr_id = $wr_id . $reply;
		$wr_parent = $wr['wr_parent'];
        $wr_reply = $reply;
    } else {
        $wr_reply = '';
    }

    $sql = " insert into $write_table
                set bo_table = '$bo_table',
                     wr_reply = '$wr_reply',
					 wr_parent = '0',
					 wr_is_comment = 0,
                     wr_comment = 0,
					 wr_comment_reply = '',
                     wr_ca = '$ca_name',
                     wr_subject = '$wr_subject',
                     wr_content = '$wr_content',
                     wr_hit = 0,
                     mb_id = '{$member['mb_id']}',
                     wr_name = '$wr_name',
					 wr_open = '$wr_open',
                     wr_datetime = '".TIME_YMDHIS."',
					 wr_file = 0,
                     wr_last = '".TIME_YMDHIS."',
                     wr_ip = '{$_SERVER['REMOTE_ADDR']}' ";
	//echo "<pre>".$sql."</pre>";
	
    sql_query($sql);

    $wr_id = sql_insert_id();
	
	if($w == '') {
		sql_query("update {$write_table} set wr_parent='{$wr_id}' where wr_id='{$wr_id}' limit 1");
	}

}  else if ($w == 'u') {

    if ($member['mb_id']) {
        // 자신의 글이라면
        $wr_name = addslashes(clean_xss_tags($member['mb_name']));
    } else {
        $wr_name = clean_xss_tags(trim($_POST['wr_name']));
    }

    $sql = " update {$write_table}
                set wr_ca = '$ca_name',
                     wr_subject = '$wr_subject',
                     wr_content = '$wr_content',
                     wr_name = '$wr_name',
					 wr_open = '$wr_open',
                     wr_last = '".TIME_YMDHIS."'
              where wr_id = '{$wr['wr_id']}' ";
	//echo "<pre>".$sql."</pre>";
	
    sql_query($sql);
}

alert("등록되었습니다.", "./notice-list.php");