<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-NhisSimpleAuth-JpAca00101-GeonGangBoHeom

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 국민건강보험공단의 건강보험료 납부내역 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/nhissimpleauth/jpaca00101/geongangboheom");
     
    // Body 추가
    $Rest->AddBody("Year", "", false);                     // 검색년도(yyyy)
    $Rest->AddBody("StartMonth", "", false);               // 검색 시작 월(MM)
    $Rest->AddBody("EndMonth", "", false);                 // 검색 종료 월(MM)
    $rest->AddBody("CxId", "", false);                    // CxId
    $rest->AddBody("PrivateAuthType", "", false);         // 인증종류 KakaoTalk / Payco / KbMobile / SamsungPass / TelecomPass
    $rest->AddBody("ReqTxId", "", false);                 // ReqTxId
    $rest->AddBody("Token", "", false);                   // Token
    $rest->AddBody("TxId", "", false);                    // TxId
    $rest->AddBody("UserName", "", true);                 // [암호화] 이용자명
    $rest->AddBody("BirthDate", "", true);                // [암호화] 생년월일(yyyyMMdd)
    $rest->AddBody("UserCellphoneNumber", "", true);      // [암호화] 휴대폰번호
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
