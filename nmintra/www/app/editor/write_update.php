<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://nmintra.event-on.kr/app/p2p/data_txtimport.php
include_once '../../header.php';

$code = $_POST['code'];
$contents = $_POST['ir1'];

  $sql = " update namo_private
			  set pcode = '{$code}',
				  contents = '{$contents}',
				  wdate =  '".TIME_YMDHIS."'
			  where idx = '{$idx}'
			  ";
  //echo "<hr/><pre>".$sql."</pre>";
	sql_query($sql);

goto_url('./private.php?code='.$code);
die();

