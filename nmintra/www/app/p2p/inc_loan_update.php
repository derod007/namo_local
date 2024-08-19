<?php
if (!defined('_VAPI_')) exit;

require_once '../../inc/Excel/reader.php';

// ExcelFile($filename, $encoding);
$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
//$data->setOutputEncoding('CP1251'); // default
//$data->setOutputEncoding('CP949');
$data->setOutputEncoding('UTF-8');

//print_r2($_POST);
print_r2($_FILES);
//die();

$cdate_ymd = TIME_YMD;

$tmp_file  = $_FILES['p2pdata_xls']['tmp_name'];
$filesize  = $_FILES['p2pdata_xls']['size'];
$filename  = $_FILES['p2pdata_xls']['name'];
//$filename  = get_safe_filename($filename);

// 서버에 설정된 값보다 큰파일을 업로드 한다면
if ($filename) {
    if ($_FILES['p2pdata_xls']['error'] == 1) {
        $file_upload_msg .= '\"'.$filename.'\" 파일의 용량이 서버에 설정('.$upload_max_filesize.')된 값보다 크므로 업로드 할 수 없습니다.\\n';
        alert($file_upload_msg, $returnurl);
    }
    else if ($_FILES['p2pdata_xls']['error'] != 0) {
        $file_upload_msg .= '\"'.$filename.'\" 파일이 정상적으로 업로드 되지 않았습니다.\\n';
        alert($file_upload_msg, $returnurl);
    }
}

$dest_path = "/home/user/hosting/namo/intra/data/file/";	// 파일 저장경로


if (is_uploaded_file($tmp_file)) {
	
	$dest_file = $dest_path.date("YmdHis")."_namoloan_".$cdate_ymd.".xls";
	
	// 업로드가 안된다면 에러메세지 출력하고 죽어버립니다.
	$error_code = move_uploaded_file($tmp_file, $dest_file) or die($_FILES['p2pdata_xls']['error']);

	// 올라간 파일의 퍼미션을 변경합니다.
	chmod($dest_file, 0644);

  // 엑셀파일 read
  $data->read($dest_file);
  
  /*** 데이터 READ ***/
  //echo "<pre>";
  //print_r($data->sheets[0]);
  //$numrows = $data->sheets[0]['numRows'];
  //$numcols = $data->sheets[0]['numCols'];
  $datalist = $data->sheets[0]['cells'];
  $titlerow = $data->sheets[0]['cells'][3];
  /***
  for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
  	for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
  		echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
  	}
  	echo "\n";
  
  }
  ***/
  //echo "</pre>";
	
	// 0. 대출금액 초기화
	$sql = " update namo_member set nm_loan = '0' where 1=1 ";
	sql_query($sql);
  
  // 상태	회원번호	차주명	차주구분	관리번호	상품번호	상품명	상품분류	최초대출금액	상환액	현재대출잔액	대출일
  // 1. 타이틀 비교해서 파일이 맞는지 확인
  if($titlerow[2] == "회원번호" && $titlerow[3] == "차주명") {
    
	// 최대 10000행 추가
    for($i=4; $i<=10000;$i++) {
      $row = $datalist[$i];
	  
      $nm_type = trim($row[1]);			// 상태
      $nm_mbnum = trim($row[2]);		// 회원번호
      $nm_mbname = trim($row[3]);	// 차주명
	  
	  if(!$nm_type || ($nm_type != "상환중" && $nm_type != "연체중"))  continue;
	  if(!$nm_mbname) continue;

      $nm_loan = str_replace(",","",trim($row[11]));
	  
	  if($nm_mbnum && $nm_loan > 0) {
		
		$sql = " select nm_loan from namo_member where nm_mbnum = '{$nm_mbnum}' limit 1";
		$row1 = sql_fetch($sql);
		$new_loan = $row1['nm_loan'] + $nm_loan;
		
		// 3. 각각의 데이터를 update
		$sql = " update namo_member set nm_loan = '{$new_loan}' where nm_mbnum = '{$nm_mbnum}' ";
		//echo "<hr/><pre>".$sql."</pre>";
		sql_query($sql);
	  }
    }
  }
}

/* 
echo "<div>".$sql."</div>";
print_r2($_POST);
die();
 */
