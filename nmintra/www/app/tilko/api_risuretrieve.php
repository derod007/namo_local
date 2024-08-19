<?php
namespace UnitTest\KR\GO\IROS;

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

define('_TKAPI_', true);

$BasePath = realpath("../../vendor/Tilko_php/Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");
require_once("./tilko_cfg.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Iros-RISURetrieve
// https://api.tilko.net/api/v1.0/iros/risuretrieve

$UniqueNo = $_POST['UniqueNo'];
$log_ip = $_SERVER['REMOTE_ADDR'];
$log_agent = $_SERVER['HTTP_USER_AGENT'];

$query = json_encode($_POST, JSON_UNESCAPED_UNICODE);

// 1. 요청데이터 로그기록
$sql = " insert into tilko_api_log
		  set api_url = '/api/v1.0/iros/risuretrieve',
			  query = '{$query}',
			  result = '',
			  data_cnt = '0',
			  log_datetime = '".TIME_YMDHIS."',
			  log_ip = '{$log_ip}',
			  log_agent = '{$log_agent}'
		  ";
sql_query($sql);
$log_id = sql_insert_id();

try {
	
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 인터넷등기소의 등기부등본 조회 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/iros/risuretrieve");

    // Body 추가
    $Rest->AddBody("IrosID", "{$cfg['IrosID']}", true);            // [암호화] iros.go.kr 로그인 ID(Base64 인코딩)
    $Rest->AddBody("IrosPwd", "{$cfg['IrosPwd']}", true);           // [암호화] iros.go.kr 로그인 패스워드(Base64 인코딩)
    $Rest->AddBody("EmoneyNo1", "{$cfg['EmoneyNo1']}", true);         // [암호화] 전자지불 선불카드 총 12자리 중 영문을 포함한 앞 8자리 입력(Base64 인코딩)
    $Rest->AddBody("EmoneyNo2", "{$cfg['EmoneyNo2']}", true);         // [암호화] 전자지불 선불카드 총 12자리 중 나머지 뒤 4자리 숫자 입력(Base64 인코딩)
    $Rest->AddBody("EmoneyPwd", "{$cfg['EmoneyPwd']}", true);         // [암호화] 전자지불 선불카드 비밀번호(Base64 인코딩)
    $Rest->AddBody("UniqueNo", "{$UniqueNo}", false);         // 부동산 고유번호('-'을 제외한 14자리)
    $Rest->AddBody("JoinYn", "Y", false);           // 공동담보/전세목록 추출여부(Y/N 공백 또는 다른 문자열일 경우 기본값 N)
    $Rest->AddBody("CostsYn", "Y", false);          // 매매목록추출여부(Y/N 공백 또는 다른 문자열일 경우 기본값 N)
    $Rest->AddBody("DataYn", "", false);           // 전산폐쇄추출여부(Y/N 공백 또는 다른 문자열일 경우 기본값 N)
    $Rest->AddBody("ValidYn", "", false);          // 유효사항만 포함여부(Y/N 공백 또는 다른 문자열일 경우 기본값 N)
    $Rest->AddBody("IsSummary", "", false);        // 요약데이터 표시여부(Y/N 공백 또는 다른 문자열일 경우 기본값 Y)

    
    // API 호출
	$response = $Rest->Call();
    define("Response", $response);
    //print("Response: " . Response);
	//$response = '';
	
	//print($response);
	
	$res = json_decode($response, true);
	
	// 조회된 등기부등본 결과
	$TransactionKey = $res['TransactionKey'];
	$Result = addslashes($res['Message']);
	$Status = $res['Status'];
	$StatusSeq = $res['StatusSeq'];
	
	// 2. 요청결과 기록
	$sql = " update tilko_api_log	set result = '".addslashes($response)."', data_cnt = '1', UniqueNo='".$UniqueNo."'  where log_id = '{$log_id}' ";
	sql_query($sql);
	
	$sql2 = "insert into tilkoapi_risuretrieve 
					set  UniqueNo = '".$UniqueNo."',
						TransactionKey = '".$TransactionKey."',
						Result = '".$Result."',
						Status = '".$Status."',
						StatusSeq = '".$StatusSeq."',		
						pdf_filename = '',
						wdatetime = '".TIME_YMDHIS."'
				";
	sql_query($sql2);
	$ret_id = sql_insert_id();
	
	if($res['Status'] == "OK" && $ret_id) {
		print('{"Status":"OK","StatusSeq":0,"Message":"조회가 완료되었습니다.","TransactionKey":"'.$TransactionKey.'","Data_id":'.$ret_id.'}');
	} else {
		print($response);
	}
	
	
}
catch (\Exception $e)
{
    print($e->getMessage());
	
	// 2-1. 요청결과 기록
	$sql = " update tilko_api_log	set result = '".addslashes($e->getMessage())."', data_cnt = '0' where log_id = '{$log_id}' ";
	sql_query($sql);
	
}

?>