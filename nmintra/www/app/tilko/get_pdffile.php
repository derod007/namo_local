<?php
namespace UnitTest\KR\GO\IROS;

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

$BasePath = realpath("../../vendor/Tilko_php/Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Iros-GetPdfFile
// https://api.tilko.net/api/v1.0/iros/getpdffile

//print_r2($_REQUEST);

$TransactionKey = $_REQUEST['TransactionKey'];
$log_ip = $_SERVER['REMOTE_ADDR'];
$log_agent = $_SERVER['HTTP_USER_AGENT'];

$query = json_encode($_REQUEST, JSON_UNESCAPED_UNICODE);

// 1. 요청데이터 로그기록
$sql = " insert into tilko_api_log
		  set api_url = '/api/v1.0/iros/getpdffile',
			  query = '{$query}',
			  result = '',
			  data_cnt = '0',
			  log_datetime = '".TIME_YMDHIS."',
			  log_ip = '{$log_ip}',
			  log_agent = '{$log_agent}'
		  ";
sql_query($sql);
$log_id = sql_insert_id();

$sql = "select * from tilkoapi_risuretrieve where TransactionKey = '".$TransactionKey."' limit 1";
//echo $sql;
$row = sql_fetch($sql);

if(!$row['UniqueNo']) {
	alert("등기부등본 조회정보가 없습니다.".$row['UniqueNo']);
}
$UniqueNo = $row['UniqueNo'];	// 고유번호
$ret_id = $row['idx'];	// PK

try {
	
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 인터넷등기소의 등기부등본 PDF 발급 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/iros/getpdffile");

    // Body 추가
    $Rest->AddBody("TransactionKey", "{$TransactionKey}", false);    // 등본발급 시 리턴받은 트랜잭션 키 (GUID)
    $Rest->AddBody("IsSummary", "Y", false);        // 요약 데이터 포함여부(Y/N 공백 또는 다른 문자열일 경우 기본값 Y)
    
    // API 호출
	$response = $Rest->Call();
    define("Response", $response);
    //print("Response: " . Response);
	//$response = '';
	
	print($response);
	
    // PDF 파일 저장
	$save_path = "/home/namo/nmintra/www/data/tilko_pdf/";
	$save_filename = date("YmdHis")."_retrieve_".$UniqueNo.".pdf";
    file_put_contents($save_path.$save_filename, base64_decode(json_decode(Response)->Message));
	
	$res = json_decode($response, true);
	
	// 조회된 등기부등본 결과
	//$TransactionKey = $res['TransactionKey'];
	
	$Result = addslashes($res['Message']);
	$Status = $res['Status'];
	$StatusSeq = $res['StatusSeq'];
	
	if($Status == "OK") {
		// 2. 요청결과 기록
		$sql = " update tilko_api_log	set result = '".$Status."', UniqueNo='".$UniqueNo."' where log_id = '{$log_id}' ";
		sql_query($sql);
		
		$sql2 = "update tilkoapi_risuretrieve set  pdf_filename = '".$save_filename."' where idx = '".$ret_id."' limit 1";
		sql_query($sql2);
	} else {
		//alert($res['Message']);
	}
	
}
catch (\Exception $e)
{
	// 2-1. 요청결과 기록
	$sql = " update tilko_api_log	set result = '".addslashes($e->getMessage())."', data_cnt = '0' where log_id = '{$log_id}' ";
	sql_query($sql);
	
	//alert($e->getMessage());
    //print($e->getMessage());
	
}

?>