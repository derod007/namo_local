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
// https://tilko.net/Help/Api/POST-api-apiVersion-Iros-REVTWelcomeEvtC
// https://api.tilko.net/api/v1.0/iros/revtwelcomeevtc

$UniqueNo = $_POST['UniqueNo'];
$InsRealCls = $_POST['InsRealCls'];	// 구분코드
$A103Name = $_POST['A103Name'];				// 소유자명
$log_ip = $_SERVER['REMOTE_ADDR'];
$log_agent = $_SERVER['HTTP_USER_AGENT'];

// 구분(공백시 건물) 토지 : 0 / 건물 : 1 / 집합건물 : 2
if($InsRealCls == "집합건물") {
	$InsRealClsCd = "2";
} else if($InsRealCls == "건물") {
	$InsRealClsCd = "1";
} else if($InsRealCls == "토지") {
	$InsRealClsCd = "0";
} else {
	alert('물건 구분정보가 없습니다.');
	die();
}

if(!$A103Name) {
	alert('소유자 정보를 저장후 요청해주세요.');
	die();
}

$query = json_encode($_POST, JSON_UNESCAPED_UNICODE);

// 1. 요청데이터 로그기록
$sql = " insert into tilko_api_log
		  set api_url = '/api/v1.0/iros/revtwelcomeevtc',
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
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/iros/revtwelcomeevtc");

    // Body 추가
    $Rest->AddBody("IrosID", "{$cfg['IrosID']}", true);            // [암호화] iros.go.kr 로그인 ID(Base64 인코딩)
    $Rest->AddBody("IrosPwd", "{$cfg['IrosPwd']}", true);           // [암호화] iros.go.kr 로그인 패스워드(Base64 인코딩)
    $Rest->AddBody("UniqueNo", "{$UniqueNo}", false);         // 부동산 고유번호('-'을 제외한 14자리)
    $Rest->AddBody("InsRealClsCd", "{$InsRealClsCd}", false);           // 구분(공백시 건물) 토지 : 0 / 건물 : 1 / 집합건물 : 2
    $Rest->AddBody("A103Name", "{$A103Name}", false);          // 소유자명

    
    // API 호출
	$response = $Rest->Call();
    define("Response", $response);
    //print("Response: " . Response);
	//$response = '';
	
	//print($response);
	
	$res = json_decode($response, true);
	
	// 조회된 등기사건조회 결과
	$Status = $res['Status'];
	$StatusSeq = $res['StatusSeq'];
	//$Message = addslashes($res['Message']);	
	// 리턴메세지 규격변경TargetMessage
	$Message = addslashes($res['TargetMessage']);	
	
	$ResultList = $res['ResultList'];
	if(is_array($res['ResultList'])) {
		$data_cnt = count($res['ResultList']);
	} else {
		$data_cnt = 0;
	}
	
	// 2. 요청결과 기록
	$sql = " update tilko_api_log	set result = '".addslashes($response)."', data_cnt = '".$data_cnt."', UniqueNo='".$UniqueNo."'  where log_id = '{$log_id}' ";
	sql_query($sql);
	
	if($data_cnt >0) {
		$k = 0;
		foreach($res['ResultList'] as $rdata) {
			
			$sql2 = "insert into tilkoapi_revtwelcomeevtc 
							set  UniqueNo = '".$UniqueNo."',
								GubunCode = '".$InsRealClsCd."',
								Status = '".$Status."',
								StatusSeq = '".$StatusSeq."',
								Message = '".$Message."',
								ResultSeq = '".$k."',
								RDselectIndex = '".$rdata['selectIndex']."',
								RDJeobsuIlja = '".$rdata['JeobsuIlja']."',
								RDJeobsuBeonho = '".$rdata['JeobsuBeonho']."',
								RDGwanhalDeunggiso = '".$rdata['GwanhalDeunggiso']."',
								RDDeunggiMogjeog = '".$rdata['DeunggiMogjeog']."',
								RDCheoliSangtae = '".$rdata['CheoliSangtae']."',
								ResultData = '".addslashes(json_encode($rdata, JSON_UNESCAPED_UNICODE))."',
								wdatetime = '".TIME_YMDHIS."'
						";
			sql_query($sql2);
			//$ret_id = sql_insert_id();
			$k++;
		}
	} else {
		
			$sql2 = "insert into tilkoapi_revtwelcomeevtc 
							set  UniqueNo = '".$UniqueNo."',
								GubunCode = '".$InsRealClsCd."',
								Status = '".$Status."',
								StatusSeq = '".$StatusSeq."',
								Message = '".$Message."',
								ResultSeq = '0',
								RDselectIndex = '',
								RDJeobsuIlja = '',
								RDJeobsuBeonho = '',
								RDGwanhalDeunggiso = '',
								RDDeunggiMogjeog = '',
								RDCheoliSangtae = '',
								ResultData = '',
								wdatetime = '".TIME_YMDHIS."'
						";
			sql_query($sql2);
		
	}
	
	if($res['Status'] == "OK") {
		print('{"Status":"OK","StatusSeq":"'.$StatusSeq.'","Message":"'.$Message.'","Result_cnt":"'.$data_cnt.'"}');
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