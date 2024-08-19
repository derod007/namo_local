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

$cdate_ymd = $_POST['cdate_ymd'];

if(!$cdate_ymd) {
	alert("데이터 기준일자가 입력되지 않았습니다.");
	die();
}

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
	
	$dest_file = $dest_path.date("YmdHis")."_namomember_".$cdate_ymd.".xls";
	
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
  echo "</pre>";
  
  // 업체명	 누적대출액 	 대출잔액 	 상환원금 	상환률	연체율	부실률	수익률	사이트주소
  // 1. 타이틀 비교해서 파일이 맞는지 확인
  if($titlerow[2] == "회원번호" && $titlerow[3] == "회원명") {
    
    //if($numrows < 4) {
     //   $file_upload_msg .= '\"'.$filename.'\" 파일에 추가할 목록이 없습니다.\\n';
     //   alert($file_upload_msg, $returnurl);
    //}
    
	$sql = "TRUNCATE TABLE `namo_member`";
	sql_query($sql);
	
	
    for($i=4; $i<=10000;$i++) {
      $row = $datalist[$i];
      
	  $base_date = $cdate_ymd;		// 데이터 기준일자
	  
      $nm_type = trim($row[1]);
      $nm_mbnum = trim($row[2]);
      $nm_mbname = trim($row[3]);
	  
	  if(!$nm_mbname) continue;

      $nm_email = trim($row[4]);
      $nm_phone = trim($row[5]);
      $nm_joindate = trim($row[8]);
	  
	  $nm_invest = str_replace(",","",trim($row[11]));
	  $nm_deposit = str_replace(",","",trim($row[12]));
	  $nm_loan = '0';

      $nm_birth = trim($row[18]);
	  $nm_sex = trim($row[19]);
	  $nm_sex_num = '';
	  if($nm_sex != '') {
		  if($nm_sex == '남성') {
			  $nm_sex_num = '1';
		  } else if($nm_sex == '여성') {
			  $nm_sex_num = '2';
		  }
		  if($nm_birth != '') {
			$nm_birth = $nm_birth.$nm_sex_num;
		  }
	  }
	  
	  if($nm_mbnum) {
	  
      // 3. 각각의 데이터를 insert
      $sql = " insert into namo_member
                  set nm_type = '{$nm_type}',
                      nm_mbnum = '{$nm_mbnum}',
                      nm_mbname = '{$nm_mbname}',
                      nm_email = '{$nm_email}',
                      nm_phone = '{$nm_phone}',
                      nm_joindate = '{$nm_joindate}',
                      nm_invest = '{$nm_invest}',
                      nm_deposit = '{$nm_deposit}',
					  nm_loan = '{$nm_loan}',
                      nm_birth = '{$nm_birth}',
                      reg_date = '".TIME_YMDHIS."'
                  ";
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
