<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-NhisSimpleAuth-JpZaa00110

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 국민건강보험공단의 직장보험료 조회 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/nhissimpleauth/jpzaa00110");
    
    // Body 추가
    $Rest->AddBody("Year", "", false);                    // 조회년도(yyyy)
    $Rest->AddBody("CxId", "", false);                    // CxId
    $Rest->AddBody("PrivateAuthType", "", false);         // 인증종류 KakaoTalk / Payco / KbMobile / SamsungPass / TelecomPass
    $Rest->AddBody("ReqTxId", "", false);                 // ReqTxId
    $Rest->AddBody("Token", "", false);                   // Token
    $Rest->AddBody("TxId", "", false);                    // TxId
    $Rest->AddBody("UserName", "", true);                 // [암호화] 이용자명
    $Rest->AddBody("BirthDate", "", true);                // [암호화] 생년월일(yyyyMMdd)
    $Rest->AddBody("UserCellphoneNumber", "", true);      // [암호화] 휴대폰번호
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
