<?php
if (!defined('_VAPI_')) exit;

//print_r2($_POST);
print_r2($_FILES);
//die();

//$cdate_ymd = $_POST['cdate_ymd'];
$cdate_ymd = TIME_YMD;

if(!$cdate_ymd) {
	alert("데이터 기준일자가 입력되지 않았습니다.");
	die();
}

$tmp_file  = $_FILES['p2ppublic_txt']['tmp_name'];
$filesize  = $_FILES['p2ppublic_txt']['size'];
$filename  = $_FILES['p2ppublic_txt']['name'];
//$filename  = get_safe_filename($filename);

// 서버에 설정된 값보다 큰파일을 업로드 한다면
if ($filename) {
    if ($_FILES['p2ppublic_txt']['error'] == 1) {
        $file_upload_msg .= '\"'.$filename.'\" 파일의 용량이 서버에 설정('.$upload_max_filesize.')된 값보다 크므로 업로드 할 수 없습니다.\\n';
        alert($file_upload_msg, $returnurl);
    }
    else if ($_FILES['p2ppublic_txt']['error'] != 0) {
        $file_upload_msg .= '\"'.$filename.'\" 파일이 정상적으로 업로드 되지 않았습니다.\\n';
        alert($file_upload_msg, $returnurl);
    }
}

$dest_path = "/home/user/hosting/namo/intra/data/file/";	// 파일 저장경로

if (is_uploaded_file($tmp_file)) {
	
	$dest_file = $dest_path.date("YmdHis")."_publicofficial_".$cdate_ymd.".txt";
	
	// 업로드가 안된다면 에러메세지 출력하고 죽어버립니다.
	$error_code = move_uploaded_file($tmp_file, $dest_file) or die($_FILES['p2ppublic_txt']['error']);

	// 올라간 파일의 퍼미션을 변경합니다.
	chmod($dest_file, 0644);

  /*** 데이터 READ ***/
  
	// 파일 열기
	$fp = fopen($dest_file, "r") or die("파일을 열 수 없습니다！");

	// 그룹코드 NEXT VAL 가져오기
	$sql = "select max(grcode) as nextval from p2p_publicofficial where 1";
	$row = sql_fetch($sql);
	$grcode = $row['nextval']+1;
	
	// 파일 내용 출력
	echo "<pre>";
	$i = 0;
	$encode = "UTF-8";
	while( !feof($fp) ) {
		$str = fgets($fp);
		$str = trim($str);
		if(!$str) {
			continue;
		}
		if($i == 0) {
			$encoding =  mb_detect_encoding($str, ['EUC-KR', 'UTF-8', 'ISO-8859-1'], false);
		}
		if($encoding == "EUC-KR") {
			$str = iconv('EUC-KR', 'UTF-8', $str);
			echo $str.PHP_EOL;
		}
		
		// 구분자로 문자열 분리
		$tmp = array();
		$tmp = explode("|", $str);
		
		// DB INSERT
		
		$sql = " insert into p2p_publicofficial
				  set grcode = '{$grcode}',
					  filename = '{$filename}',
					  d_num = '".trim($tmp[0])."',
					  d_jumin = '".trim($tmp[1])."',
					  d_name = '".trim($tmp[2])."',
					  d_rqdate = '".trim($tmp[3])."',
					  d_rqdept = '".trim($tmp[4])."',
					  d_rqname = '".trim($tmp[5])."',
					  d_rqtel = '".trim($tmp[6])."',
					  reg_date = '".TIME_YMDHIS."'
				  ";
		//echo "<hr/><pre>".$sql."</pre>";
		sql_query($sql);
		
		$i++;
	}

	// 파일 닫기
	fclose($fp);  
	echo "</pre>";
  
}

/* 
echo "<div>".$sql."</div>";
print_r2($_POST);
die();
 */

