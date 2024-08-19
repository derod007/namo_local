<?php

/*************************************************************************
**
**  일반 함수 모음
**
*************************************************************************/

// 마이크로 타임을 얻어 계산 형식으로 만듦
function get_microtime()
{
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

// 변수 또는 배열의 이름과 값을 얻어냄. print_r() 함수의 변형
function print_r2($var)
{
    ob_start();
    print_r($var);
    $str = ob_get_contents();
    ob_end_clean();
    $str = str_replace(" ", "&nbsp;", $str);
    echo nl2br("<span style='font-family:Tahoma, 굴림; font-size:9pt;'>$str</span>");
}


// 메타태그를 이용한 URL 이동
// header("location:URL") 을 대체
function goto_url($url)
{
    $url = str_replace("&amp;", "&", $url);
    //echo "<script> location.replace('$url'); </script>";

    if (!headers_sent())
        header('Location: '.$url);
    else {
        echo '<script>';
        echo 'location.replace("'.$url.'");';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
    }
    exit;
}


// 세션변수 생성
function set_session($session_name, $value)
{
    if (PHP_VERSION < '5.3.0')
        session_register($session_name);
    // PHP 버전별 차이를 없애기 위한 방법
    $$session_name = $_SESSION[$session_name] = $value;
}


// 세션변수값 얻음
function get_session($session_name)
{
    return isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : '';
}


// 쿠키변수 생성
function set_cookie($cookie_name, $value, $expire)
{
    setcookie(md5($cookie_name), base64_encode($value), SERVER_TIME + $expire, '/', COOKIE_DOMAIN);
}


// 쿠키변수값 얻음
function get_cookie($cookie_name)
{
    $cookie = md5($cookie_name);
    if (array_key_exists($cookie, $_COOKIE))
        return base64_decode($_COOKIE[$cookie]);
    else
        return "";
}


// 경고메세지를 경고창으로
function alert($msg='', $url='')
{
    if (!$msg) $msg = '올바른 방법으로 이용해 주십시오.';

	//header("Content-Type: text/html; charset=$g4[charset]");
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf8\">";
	echo "<script type='text/javascript'>alert('$msg');";
    if (!$url)
        echo "history.go(-1);";
    echo "</script>";
    if ($url) {
        echo '<script>';
        echo 'location.replace("'.$url.'");';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
	}
    exit;
}


// 경고메세지 출력후 창을 닫음
function alert_close($msg)
{
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf8\">";
    echo "<script type='text/javascript'> alert('$msg'); window.close(); </script>";
    exit;
}


function cut_str($str, $len, $suffix="…")
{
    $arr_str = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    $str_len = count($arr_str);

    if ($str_len >= $len) {
        $slice_str = array_slice($arr_str, 0, $len);
        $str = join("", $slice_str);

        return $str . ($str_len > $len ? $suffix : '');
    } else {
        $str = join("", $arr_str);
        return $str;
    }
}


// TEXT 형식으로 변환
function get_text($str, $html=0, $restore=false)
{
    $source[] = "<";
    $target[] = "&lt;";
    $source[] = ">";
    $target[] = "&gt;";
    $source[] = "\"";
    $target[] = "&#034;";
    $source[] = "\'";
    $target[] = "&#039;";

    if($restore)
        $str = str_replace($target, $source, $str);

    // TEXT 출력일 경우 &amp; &nbsp; 등의 코드를 정상으로 출력해 주기 위함
    if ($html == 0) {
        $str = html_symbol($str);
    }

    if ($html) {
        $source[] = "\n";
        $target[] = "<br/>";
    }

    return str_replace($source, $target, $str);
}


// HTML SYMBOL 변환
// &nbsp; &amp; &middot; 등을 정상으로 출력
function html_symbol($str)
{
    return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}

/*************************************************************************
**
**  SQL 관련 함수 모음
**
*************************************************************************/

// DB 연결
function sql_connect($host, $user, $pass, $db=MYSQL_DB)
{

    if(function_exists('mysqli_connect')) {
        $link = mysqli_connect($host, $user, $pass, $db);

        // 연결 오류 발생 시 스크립트 종료
        if (mysqli_connect_errno()) {
            die('Connect Error: '.mysqli_connect_error());
        }
    } else {
        $link = mysql_connect($host, $user, $pass);
    }
	
    return $link;
}


// DB 선택
function sql_select_db($db, $connect)
{
    if(function_exists('mysqli_select_db'))
        return @mysqli_select_db($connect, $db);
    else
        return @mysql_select_db($db, $connect);
	
    return mysql_select_db($db, $connect);
}


// mysql_query 와 mysql_error 를 한꺼번에 처리
function sql_query($sql, $error=TRUE, $link=null)
{
	global $jsb;
	
    if(!$link)
        $link = $jsb['connect_db'];

    // Blind SQL Injection 취약점 해결
    $sql = trim($sql);
    // union의 사용을 허락하지 않습니다.
    //$sql = preg_replace("#^select.*from.*union.*#i", "select 1", $sql);
    $sql = preg_replace("#^select.*from.*[\s\(]+union[\s\)]+.*#i ", "select 1", $sql);
    // `information_schema` DB로의 접근을 허락하지 않습니다.
    $sql = preg_replace("#^select.*from.*where.*`?information_schema`?.*#i", "select 1", $sql);

    if(function_exists('mysqli_query')) {
        if ($error) {
            $result = @mysqli_query($link, $sql) or die("<p>$sql<p>" . mysqli_errno($link) . " : " .  mysqli_error($link) . "<p>error file : {$_SERVER['SCRIPT_NAME']}");
        } else {
            $result = @mysqli_query($link, $sql);
        }
    } else {
        if ($error) {
            $result = @mysql_query($sql, $link) or die("<p>$sql<p>" . mysql_errno() . " : " .  mysql_error() . "<p>error file : {$_SERVER['SCRIPT_NAME']}");
        } else {
            $result = @mysql_query($sql, $link);
        }
    }
	
    return $result;
}


// 쿼리를 실행한 후 결과값에서 한행을 얻는다.
function sql_fetch($sql, $error=TRUE, $link=null)
{
	global $jsb;
	
    if(!$link)
        $link = $jsb['connect_db'];
	
    $result = sql_query($sql, $error);
    $row = sql_fetch_array($result);
    return $row;
}


// 결과값에서 한행 연관배열(이름으로)로 얻는다.
function sql_fetch_array($result)
{
    if(function_exists('mysqli_fetch_assoc'))
        $row = @mysqli_fetch_assoc($result);
    else
        $row = @mysql_fetch_assoc($result);
	
    return $row;
}


// $result에 대한 메모리(memory)에 있는 내용을 모두 제거한다.
// sql_free_result()는 결과로부터 얻은 질의 값이 커서 많은 메모리를 사용할 염려가 있을 때 사용된다.
// 단, 결과 값은 스크립트(script) 실행부가 종료되면서 메모리에서 자동적으로 지워진다.
function sql_free_result($result)
{
    if(function_exists('mysqli_free_result'))
        return mysqli_free_result($result);
    else
        return mysql_free_result($result);
	
    //return mysql_free_result($result);
}

function sql_password($value)
{
    $row = sql_fetch(" select password('$value') as pass ");

    return $row['pass'];
}


function sql_num_rows($result)
{
    if(function_exists('mysqli_num_rows'))
        return mysqli_num_rows($result);
    else
        return mysql_num_rows($result);
	
    //return mysql_num_rows($result);
}

function sql_insert_id($link=null)
{
    global $jsb;

    if(!$link)
        $link = $jsb['connect_db'];

    if(function_exists('mysqli_insert_id'))
        return mysqli_insert_id($link);
    else
        return mysql_insert_id($link);
}


// 문자열이 한글, 영문, 숫자, 특수문자로 구성되어 있는지 검사
function check_string($str, $options)
{

    $s = '';
    for($i=0;$i<strlen($str);$i++) {
        $c = $str[$i];
        $oc = ord($c);

        // 한글
        if ($oc >= 0xA0 && $oc <= 0xFF) {
            if ($options & HANGUL) {
                $s .= $c . $str[$i+1] . $str[$i+2];
            }
            $i+=2;
        }
        // 숫자
        else if ($oc >= 0x30 && $oc <= 0x39) {
            if ($options & NUMERIC) {
                $s .= $c;
            }
        }
        // 영대문자
        else if ($oc >= 0x41 && $oc <= 0x5A) {
            if (($options & ALPHABETIC) || ($options & ALPHAUPPER)) {
                $s .= $c;
            }
        }
        // 영소문자
        else if ($oc >= 0x61 && $oc <= 0x7A) {
            if (($options & ALPHABETIC) || ($options & ALPHALOWER)) {
                $s .= $c;
            }
        }
        // 공백
        else if ($oc == 0x20) {
            if ($options & SPACE) {
                $s .= $c;
            }
        }
        else {
            if ($options & SPECIAL) {
                $s .= $c;
            }
        }
    }

    // 넘어온 값과 비교하여 같으면 참, 틀리면 거짓
    return ($str == $s);
}


// 한글(2bytes)에서 마지막 글자가 1byte로 끝나는 경우
// 출력시 깨지는 현상이 발생하므로 마지막 완전하지 않은 글자(1byte)를 하나 없앰
function cut_hangul_last($hangul)
{

    // 한글이 반쪽나면 ?로 표시되는 현상을 막음
    $cnt = 0;
    for($i=0;$i<strlen($hangul);$i++) {
        // 한글만 센다
        if (ord($hangul[$i]) >= 0xA0) {
            $cnt++;
        }
    }

    return $hangul;
}


// 문자열에 utf8 문자가 들어 있는지 검사하는 함수
// 코드 : http://in2.php.net/manual/en/function.mb-check-encoding.php#95289
function is_utf8($str)
{
    $len = strlen($str);
    for($i = 0; $i < $len; $i++) {
        $c = ord($str[$i]);
        if ($c > 128) {
            if (($c > 247)) return false;
            elseif ($c > 239) $bytes = 4;
            elseif ($c > 223) $bytes = 3;
            elseif ($c > 191) $bytes = 2;
            else return false;
            if (($i + $bytes) > $len) return false;
            while ($bytes > 1) {
                $i++;
                $b = ord($str[$i]);
                if ($b < 128 || $b > 191) return false;
                $bytes--;
            }
        }
    }
    return true;
}


// UTF-8 문자열 자르기
// 출처 : https://www.google.co.kr/search?q=utf8_strcut&aq=f&oq=utf8_strcut&aqs=chrome.0.57j0l3.826j0&sourceid=chrome&ie=UTF-8
function utf8_strcut( $str, $size, $suffix='...' )
{
        $substr = substr( $str, 0, $size * 2 );
        $multi_size = preg_match_all( '/[\x80-\xff]/', $substr, $multi_chars );

        if ( $multi_size > 0 )
            $size = $size + intval( $multi_size / 3 ) - 1;

        if ( strlen( $str ) > $size ) {
            $str = substr( $str, 0, $size );
            $str = preg_replace( '/(([\x80-\xff]{3})*?)([\x80-\xff]{0,2})$/', '$1', $str );
            $str .= $suffix;
        }

        return $str;
}


/*
-----------------------------------------------------------
    Charset 을 변환하는 함수
-----------------------------------------------------------
iconv 함수가 있으면 iconv 로 변환하고
없으면 mb_convert_encoding 함수를 사용한다.
둘다 없으면 사용할 수 없다.
*/
function convert_charset($from_charset, $to_charset, $str)
{

    if( function_exists('iconv') )
        return iconv($from_charset, $to_charset, $str);
    elseif( function_exists('mb_convert_encoding') )
        return mb_convert_encoding($str, $to_charset, $from_charset);
    else
        die("Not found 'iconv' or 'mbstring' library in server.");
}

// mysqli_real_escape_string 의 alias 기능을 한다.
function sql_real_escape_string($str, $link=null)
{
    global $jsb;

    if(!$link)
        $link = $jsb['connect_db'];

    return mysqli_real_escape_string($link, $str);
}

function escape_trim($field)
{
    $str = call_user_func(ESCAPE_FUNCTION, $field);
    return $str;
}

// XSS 관련 태그 제거
function clean_xss_tags($str)
{
    $str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);

    return $str;
}

function safe_request_string($str)
{
	$str = clean_xss_tags($str);
	$str = sql_real_escape_string($str);
	return $str;
}


// unescape nl 얻기
function conv_unescape_nl($str)
{
    $search = array('\\r', '\r', '\\n', '\n');
    $replace = array('', '', "\n", "\n");

    return str_replace($search, $replace, $str);
}

// API Return Error
function return_error($reason, $exit)
{

	$error_res['result'] = 'fail';
	$error_res['success'] = false;

	if($reason == '1000')
	{
		$error_res['reason'] = '이용권한이 없습니다. 다시 로그인해 주세요.';
		$error_res['reason_code'] = $reason;
	}	
	else
	{
		$error_res['reason'] = $reason;
	}

	echo json_encode($error_res);

	if($exit == 1)
		exit;
}


/*************************************************************************
**
**  FILE UPLOAD 관련 함수 모음
**
*************************************************************************/

// 파일의 용량을 구한다.
function get_filesize($size)
{
    //$size = @filesize(addslashes($file));
    if ($size >= 1048576) {
        $size = number_format($size/1048576, 1) . "M";
    } else if ($size >= 1024) {
        $size = number_format($size/1024, 1) . "K";
    } else {
        $size = number_format($size, 0) . "byte";
    }
    return $size;
}

// 폴더의 용량 ($dir는 / 없이 넘기세요)
function get_dirsize($dir)
{
    $size = 0;
    $d = dir($dir);
    while ($entry = $d->read()) {
        if ($entry != '.' && $entry != '..') {
            $size += filesize($dir.'/'.$entry);
        }
    }
    $d->close();
    return $size;
}

// 파일명에서 특수문자 제거
function get_safe_filename($name)
{
    //$pattern = '/["\'<>=#&!%\\\\(\)\*\+\?]/';
	$pattern = '/["\'<>=#&!%\\\\*\+\?]/';
    $name = preg_replace($pattern, '', $name);

    return $name;
}

// 파일명 치환
function replace_filename($name)
{
    @session_start();
    $ss_id = session_id();
    $usec = get_microtime();
	$ext = explode('.', $name);
    $ext = array_pop($ext);

    return substr(sha1($ss_id.$_SERVER['REMOTE_ADDR'].$usec),0,20).'.'.$ext;
}

/*************************************************************************
**
**  DATA LOAD 관련 함수 모음
**
*************************************************************************/

// SELECT 형식으로 출력시 선택값
function option_selected($value, $selected, $text='')
{
    if (!$text) $text = $value;
    if ($value == $selected)
        return "<option value=\"$value\" selected=\"selected\">$text</option>\n";
    else
        return "<option value=\"$value\">$text</option>\n";
}

// SELECT 형식으로 출력시 선택값, 엄격한 비교 적용
function option_selected_exact($value, $selected, $text='')
{
    if (!$text) $text = $value;
    if ($value === $selected)
        return "<option value=\"$value\" selected=\"selected\">$text</option>\n";
    else
        return "<option value=\"$value\">$text</option>\n";
}

// 지역코드 정보를 얻는다.
function get_regioncode_select($name, $selected='', $event='')
{
    global $jsb;
	
    $sql = " select * from sigungu_code_ext where step='2' ";
    $result = sql_query($sql);
    $str = "<select id=\"$name\" name=\"$name\" $event class=\"form-control\">\n";
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        if ($i == 0) $str .= "<option value=\"\">선택</option>";
        $str .= option_selected($row['region'], $selected, "(".$row['region'].") ".$row['dong']);
    }
    $str .= "</select>";
    return $str;
	
}

// 시도코드 정보를 얻는다.
function get_sidocode_select($name, $selected='', $event='')
{
    global $jsb;
	
    $sql = " select * from sigungu_code_ext where step='1' ";
    $result = sql_query($sql);
    $str = "<select id=\"$name\" name=\"$name\" $event class=\"form-control\">\n";
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        if ($i == 0) $str .= "<option value=\"\">선택</option>";
		//$row['sido'] = substr($row['region'],0,2);
        $str .= option_selected($row['region'], $selected, "(".$row['region'].") ".$row['dong']);
    }
    $str .= "</select>";
    return $str;
	
}

// 시도코드 이름을 얻는다. 2023-09-04
function get_sidocode_name($rcode)
{
    global $jsb;
	
    $sql = " select * from sigungu_code_ext where region='{$rcode}' limit 1 ";
    $row = sql_fetch($sql);
	
    return $row['dong'];
}


// 게시글에 첨부된 파일을 얻는다. (배열로 반환)
function get_writefile($wr_id)
{
    global $jsb;

    $file['count'] = 0;
    $sql = " select * from file_loaninfo where wr_id = '$wr_id' order by file_no desc ";
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result))
    {
        $no = $row['file_no'];
        $file[$no]['href'] = JSB_URL."/app/module/download.php?wr_id=$wr_id&amp;no=$no";
        $file[$no]['download'] = $row['file_download'];
        $file[$no]['path'] = JSB_DATA_URL.'/file';
        $file[$no]['size'] = get_filesize($row['file_size']);
        $file[$no]['datetime'] = $row['file_datetime'];
        $file[$no]['source'] = addslashes($row['file_source']);
		$file[$no]['name'] = addslashes($row['file_name']);
        $file[$no]['memo'] = $row['file_memo'];
        $file[$no]['file'] = $row['file_name'];
        //$file[$no]['image_width'] = $row['file_width'] ? $row['file_width'] : 640;
        //$file[$no]['image_height'] = $row['file_height'] ? $row['file_height'] : 480;
        $file[$no]['image_type'] = $row['file_type'];
		
		$file[$no]['category'] = ($row['file_category'])?$row['file_category']:'일반';
		
        $file['count']++;
    }

    return $file;
}

// 파트너 목록정보를 읽어온다
function get_partnerlist()
{
    global $jsb;

    $file['count'] = 0;
    $sql = " select * from partner_member where mb_use = '1' order by idx asc ";
    $result = sql_query($sql);
	$ret = array();
    while ($row = sql_fetch_array($result))
    {
		$k = $row['idx'];
		$ret[$k]['idx'] = $row['idx'];
		$ret[$k]['mb_id'] = $row['mb_id'];
		$ret[$k]['mb_name'] = $row['mb_name'];
		$ret[$k]['mb_bizname'] = $row['mb_bizname'];
		$ret[$k]['mb_level'] = $row['mb_level'];
		$ret[$k]['parent_id'] = $row['parent_id'];
    }

    return $ret;
}

// 파트너 목록정보를 읽어온다
function get_partnerdata($pt_idx)
{
    global $jsb;

    $file['count'] = 0;
    $sql = " select * from partner_member where idx='{$pt_idx}' and mb_use = '1' limit 1 ";
    $row = sql_fetch($sql);
	
	$ret = array();
	if($row['idx']) {
		$ret['idx'] = $row['idx'];
		$ret['mb_id'] = $row['mb_id'];
		$ret['mb_name'] = $row['mb_name'];
		$ret['mb_bizname'] = $row['mb_bizname'];
		$ret['mb_level'] = $row['mb_level'];
		if($row['is_sub']) {
			$ret['parent_id'] = $row['parent_id'];
		} else {
			$ret['parent_id'] = $row['idx'];
		}
    }

    return $ret;
}


// 로그파일 기록
function log_write_file($str=''){

	//오늘의 날짜를 변수로 저장 
	$logDate = date("Ymd");
	//로그파일 생성. 파일명은 오늘날짜. 
	$logfileName = "loan_".$logDate.".log";
	
    //디렉토리 경로
    $log_dir = $_SERVER["DOCUMENT_ROOT"].'/data/log/';
	
    $log_txt = PHP_EOL;
    $log_txt .= '[' . date("Y-m-d H:i:s") . ']' . PHP_EOL;
	$log_txt .= $_SERVER['REMOTE_ADDR'] . ' : ' . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL;
	$log_txt .= $_SERVER['SCRIPT_FILENAME'] . PHP_EOL;
	
	ob_start();
	
	echo $log_txt;
	//echo "[_SESSION] : ".PHP_EOL;
	//print_r($_SESSION);
	echo "[_POST] : ".PHP_EOL;
	print_r($_POST);
	echo "[MESSAGE] : ".PHP_EOL;
    print_r($str);
	
	$log_content = ob_get_clean();
	
    $log_file = @fopen($log_dir . $logfileName, 'a+');
    @fwrite($log_file, $log_content.PHP_EOL);
    @fclose($log_file);

}

// 실행 로그 기록
function log_write($wr_id, $pt_id, $manage_id, $prev_status="", $next_status="", $text="" )
{
	$sql = " insert into log_action
			set wr_id = '{$wr_id}',
				pt_id = '{$pt_id}',
				manage_id = '{$manage_id}',
				prev_status = '{$prev_status}',
				next_status = '{$next_status}',
				reg_date = '".TIME_YMDHIS."',
				reg_ip = '".$_SERVER['REMOTE_ADDR']."' ";
	@sql_query($sql);
	@log_write_file($text);
	return;
}

// 한도부여 기록
function jdlog_write($wr_id, $manage_id, $ipt = array() )
{
	$jd_amount = $ipt['jd_amount'];
	$jd_interest = $ipt['jd_interest'];
	$jd_condition = $ipt['jd_condition'];
	$jd_memo = $ipt['jd_memo'];
	$rf_first1 = $ipt['rf_first1'];
	$rf_first2 = $ipt['rf_first2'];
	$rf_first3 = $ipt['rf_first3'];
	
	$sql = " insert into log_judge
			set wr_id = '{$wr_id}',
				manage_id = '{$manage_id}',
				jd_amount = '{$jd_amount}',
				jd_interest = '{$jd_interest}',
				jd_condition = '{$jd_condition}',
				jd_memo = '{$jd_memo}',
				rf_first1 = '{$rf_first1}',
				rf_first2 = '{$rf_first2}',
				rf_first3 = '{$rf_first3}',
				reg_date = '".TIME_YMDHIS."',
				reg_ip = '".$_SERVER['REMOTE_ADDR']."' ";
	@sql_query($sql);
	return;
}

// 파트너 관리 로그파일 기록
function log_partnerid($str=''){

	//오늘의 날짜를 변수로 저장 
	$logDate = date("Ymd");
	//로그파일 생성. 파일명은 오늘날짜. 
	$logfileName = "partner_".$logDate.".log";
	
    //디렉토리 경로
    $log_dir = $_SERVER["DOCUMENT_ROOT"].'/data/log/';
	
    $log_txt = PHP_EOL;
    $log_txt .= '[' . date("Y-m-d H:i:s") . ']' . PHP_EOL;
	$log_txt .= $_SERVER['REMOTE_ADDR'] . ' : ' . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL;
	$log_txt .= $_SERVER['SCRIPT_FILENAME'] . PHP_EOL;
	
	ob_start();
	
	echo $log_txt;
	//echo "[_SESSION] : ".PHP_EOL;
	//print_r($_SESSION);
	echo "[_POST] : ".PHP_EOL;
	print_r($_POST);
	echo "[MESSAGE] : ".PHP_EOL;
    print_r($str);
	
	$log_content = ob_get_clean();
	
    $log_file = @fopen($log_dir . $logfileName, 'a+');
    @fwrite($log_file, $log_content.PHP_EOL);
    @fclose($log_file);

}
