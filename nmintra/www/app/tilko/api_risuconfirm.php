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
// https://tilko.net/Help/Api/POST-api-apiVersion-Iros-RISUConfirmSimpleC
// https://api.tilko.net/api/v1.0/iros/risuconfirmsimplec

$Sangtae = $_POST['Sangtae'];
$KindClsFlag = $_POST['KindClsFlag'];
$address1 = $_POST['address1'];
$address2 = $_POST['address2'];
$address3 = $_POST['address3'];
$Address = trim($address1." ".$address3);
$curpage = $_POST['CurPage'];

$log_ip = $_SERVER['REMOTE_ADDR'];
$log_agent = $_SERVER['HTTP_USER_AGENT'];

$query = json_encode($_POST, JSON_UNESCAPED_UNICODE);

// 1. 요청데이터 로그기록
$sql = " insert into tilko_api_log
		  set api_url = '/api/v1.0/iros/risuconfirmsimplec',
			  query = '{$query}',
			  result = '',
			  data_cnt = '0',
			  UniqueNo = '',
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
    
    // 인터넷등기소의 등기물건 주소검색 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/iros/risuconfirmsimplec");

    // Body 추가
    $Rest->AddBody("Address", "{$Address}", false);         // 주소 (빠른 조회를 원할 시 정확한 주소 입력 필요)
    $Rest->AddBody("Sangtae", "{$Sangtae}", false);         // 상태(공백 시 현행폐쇄) 현행:0/폐쇄:1/ 현행폐쇄:2
    $Rest->AddBody("KindClsFlag", "{$KindClsFlag}", false);     // 부동산구분(공백 시 전체) 전체:0/집합건물:1/건물:2/토지:3
	if($curpage > 1) {
    $Rest->AddBody("Page", "{$curpage}", false);     // 부동산구분(공백 시 전체) 전체:0/집합건물:1/건물:2/토지:3
	}
    $Rest->AddBody("Region", "", false);          // 도시(공백 시 전체) 전체:0/서울특별시:1/부산광역시:2/대구광역시:3/인천광역시:4/광주광역시:5/대전광역시:6/울산광역시:7/세종특별자치시:8/경기도:9/강원도:10/충청북도:11/충청남도:12/전라북도:13/전라남도:14/경상북도:15/경상남도:16/제주특별자치도:17
    
    // API 호출
	$response = $Rest->Call();
    define("Response", $response);
    //print("Response: " . Response);
	//$response = '{"Status":"OK","StatusSeq":0,"Message":"성공","TotalCount":1,"TotalPages":1,"CurrentPages":1,"ResultList":[{"UniqueNo":"11621996066630","Gubun":"집합건물","BudongsanSojaejibeon":"서울특별시 송파구 올림픽로35길 104 장미아파트 제7동 제8층 제807호 [신천동 7]","Sangtae":"현행"}]}';
	
	print($response);
	
	$res = json_decode($response, true);
	
	$list = $res['ResultList'];
	
	// 2. 요청결과 기록
	$sql = " update tilko_api_log	set result = '".addslashes($response)."', data_cnt = '".$res['TotalCount']."' where log_id = '{$log_id}' ";
	sql_query($sql);
	
	// 3. 개별 결과를 DB에 저장
	if(count($list) > 0 ) {
		foreach($list as $item) {
			
			// 기존 저장된 데이터가 있는지 조회.
			$sql1 = "select * from tilkoapi_risuconfirmsimplec where UniqueNo = '".$item['UniqueNo']."' limit 1";
			$row = sql_fetch($sql1);
			if($row['UniqueNo']) {
				$sql2 = "update tilkoapi_risuconfirmsimplec 
								set  Gubun = '".$item['Gubun']."',
									BudongsanSojaejibeon = '".$item['BudongsanSojaejibeon']."',
									Sangtae = '".$item['Sangtae']."',
									wdatetime = '".TIME_YMDHIS."'									
								where UniqueNo = '".$item['UniqueNo']."'
							";
				sql_query($sql2);
			} else {
				$sql2 = "insert into tilkoapi_risuconfirmsimplec 
								set  UniqueNo = '".$item['UniqueNo']."',
									Gubun = '".$item['Gubun']."',
									BudongsanSojaejibeon = '".$item['BudongsanSojaejibeon']."',
									Sangtae = '".$item['Sangtae']."',
									Owner = '',
									wdatetime = '".TIME_YMDHIS."'
							";
				sql_query($sql2);
			}
		}
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