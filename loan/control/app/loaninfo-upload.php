<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

$w 	= trim($_POST['w']);
$wr_id 	= trim($_POST['wr_id']);

$upload_max_filesize = ini_get('upload_max_filesize');

//print_r2($_POST);
//print_r2($_FILES);

if (empty($_POST)) {
    alert("파일의 크기가 서버에서 설정한 값을 넘어 오류가 발생하였습니다.\\npost_max_size=".ini_get('post_max_size')." , upload_max_filesize=".$upload_max_filesize."\\n서버관리자에게 문의 바랍니다.");
}

if(!$wr_id) {
	alert("잘못된 접근입니다.");
}

if($w == 'file') {
	
	// 파일개수 체크
	$file_count   = 0;

	// 디렉토리가 없다면 생성합니다. (퍼미션도 변경하구요.)
	@mkdir(JSB_DATA_PATH.'/file', DIR_PERMISSION);
	@chmod(JSB_DATA_PATH.'/file', DIR_PERMISSION);

	$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));

	// 가변 파일 업로드
	$file_upload_msg = '';
	$upload = array();
	for ($i=0; $i<count($_FILES['uploadfile']['name']); $i++) {
		$upload[$i]['file']     = '';
		$upload[$i]['source']   = '';
		$upload[$i]['filesize'] = 0;
		$upload[$i]['image']    = array();
		$upload[$i]['image'][0] = '0';
		$upload[$i]['image'][1] = '0';
		$upload[$i]['image'][2] = '0';

		$tmp_file  = $_FILES['uploadfile']['tmp_name'][$i];
		$filesize  = $_FILES['uploadfile']['size'][$i];
		$filename  = $_FILES['uploadfile']['name'][$i];
		$filename  = get_safe_filename($filename);

		// 서버에 설정된 값보다 큰파일을 업로드 한다면
		if ($filename) {
			if ($_FILES['uploadfile']['error'][$i] == 1) {
				$file_upload_msg .= '\"'.$filename.'\" 파일의 용량이 서버에 설정('.$upload_max_filesize.')된 값보다 크므로 업로드 할 수 없습니다.\\n';
				continue;
			}
			else if ($_FILES['uploadfile']['error'][$i] != 0) {
				$file_upload_msg .= '\"'.$filename.'\" 파일이 정상적으로 업로드 되지 않았습니다.\\n';
				continue;
			}
		}

		if (is_uploaded_file($tmp_file)) {

			//=================================================================\
			// 090714
			// 이미지나 플래시 파일에 악성코드를 심어 업로드 하는 경우를 방지
			// 에러메세지는 출력하지 않는다.
			//-----------------------------------------------------------------
			$timg = getimagesize($tmp_file);
			$upload[$i]['image'] = $timg;
			print_r($timg);

			// 프로그램 원래 파일명
			$upload[$i]['source'] = $filename;
			$upload[$i]['filesize'] = $filesize;

			// 아래의 문자열이 들어간 파일은 -x 를 붙여서 웹경로를 알더라도 실행을 하지 못하도록 함
			$filename = preg_replace("/\.(php|pht|phtm|htm|cgi|pl|py|exe|jsp|asp|inc)/i", "$0-x", $filename);

			shuffle($chars_array);
			$shuffle = implode('', $chars_array);

			// 첨부파일 첨부시 첨부파일명에 공백이 포함되어 있으면 일부 PC에서 보이지 않거나 다운로드 되지 않는 현상이 있습니다. (길상여의 님 090925)
			$upload[$i]['file'] = date("YmdHi").'_'.substr($shuffle,0,8).'_'.replace_filename($filename);

			$dest_file = JSB_DATA_PATH.'/file/'.$upload[$i]['file'];

			// 업로드가 안된다면 에러메세지 출력하고 죽어버립니다.
			$error_code = move_uploaded_file($tmp_file, $dest_file) or die($_FILES['projectfile']['error'][$i]);

			// 올라간 파일의 퍼미션을 변경합니다.
			chmod($dest_file, FILE_PERMISSION);
		}
	}

	$row = sql_fetch(" select max(file_no) as max_file_no from file_loaninfo where wr_id = '{$wr_id}' ");
	$next_no = (int)($row['max_file_no']+1);

	for ($i=0; $i<count($upload); $i++)
	{
		if (!get_magic_quotes_gpc()) {
			$upload[$i]['source'] = addslashes($upload[$i]['source']);
		}
		if(!$memo[$i]) $memo[$i] = '';
		if(!$upload[$i]['image']['0']) $upload[$i]['image']['0'] = 0;
		if(!$upload[$i]['image']['1']) $upload[$i]['image']['1'] = 0;
		if(!$upload[$i]['image']['2']) $upload[$i]['image']['2'] = 0;
		
		$sql = " insert into file_loaninfo
					set wr_id = '{$wr_id}',
						 file_no = '{$next_no}',
						 file_source = '{$upload[$i]['source']}',
						 file_name = '{$upload[$i]['file']}',
						 file_category = '{$category[$i]}',
						 file_memo = '{$memo[$i]}',
						 file_download = 0,
						 file_size = '{$upload[$i]['filesize']}',
						 file_width = '{$upload[$i]['image']['0']}',
						 file_height = '{$upload[$i]['image']['1']}',
						 file_type = '{$upload[$i]['image']['2']}',
						 file_datetime = '".TIME_YMDHIS."' ";
		sql_query($sql);
		$next_no++;
	}
	//echo "<p>".$sql."</p>";
	//@log_write("WRITE", "PROJECT", "FILE");
	
	goto_url('./loaninfo-file.php?wr_id='.$wr_id);
	
} else if($w == 'filedel') {
	// 파일삭제
	
	$row = sql_fetch(" select file_id, file_name from file_loaninfo where file_no = '{$file_no}' and wr_id = '{$wr_id}' limit 1 ");
	$dest_file = JSB_DATA_PATH.'/file/'.$row['file_name'];
	$file_id = $row['file_id'];
	@unlink($dest_file);
	
	$sql = " delete from file_loaninfo where file_id = '{$file_id}' limit 1 ";
	sql_query($sql);
	
	goto_url('./loaninfo-file.php?wr_id='.$wr_id);
}
