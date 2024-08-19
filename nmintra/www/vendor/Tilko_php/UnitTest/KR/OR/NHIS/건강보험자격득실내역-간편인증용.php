<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-NhisSimpleAuth-JpAea00401

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 국민건강보험공단의 건강보험자격득실내역 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/nhissimpleauth/jpaea00401");
     
    // Body 추가
    $Rest->AddBody("NhisQuery", "", false);                    // 검색조건 전체 : 0 / 직장가입자 : 1 / 지역가입자 : 2 / 가입자 전체 : 3
    $Rest->AddBody("CxId", "", false);                         // CxId
    $Rest->AddBody("PrivateAuthType", "", false);              // 인증종류 KakaoTalk / Payco / KbMobile / SamsungPass / TelecomPass
    $rest->AddBody("ReqTxId", "", false);                     // ReqTxId
    $rest->AddBody("Token", "", false);                       // Token
    $rest->AddBody("TxId", "", false);                        // TxId
    $rest->AddBody("UserName", "", true);                     // [암호화] 이용자명
    $rest->AddBody("BirthDate", "", true);                    // [암호화] 생년월일(yyyyMMdd)
    $rest->AddBody("UserCellphoneNumber", "", true);          // [암호화] 휴대폰번호
   
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
