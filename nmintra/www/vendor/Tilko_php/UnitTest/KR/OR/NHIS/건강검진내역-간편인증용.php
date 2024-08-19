<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-NhisSimpleAuth-Ggpab003M0105

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 국민건강보험공단의 건강검진내역 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/nhissimpleauth/ggpab003m0105");
    
    // Body 추가
    $Rest->AddBody("CxId", "", false);                // CxId
    $Rest->AddBody("PrivateAuthType", "", false);     // 인증종류 KakaoTalk / Payco / KbMobile / SamsungPass / TelecomPass
    $Rest->AddBody("ReqTxId", "", false);             // ReqTxId
    $Rest->AddBody("Token", "", false);               // Token
    $Rest->AddBody("TxId", "", false;                 // TxId
    $Rest->AddBody("UserName", "", true);             // [암호화] 이용자명
    $Rest->AddBody("BirthDate", "", true);            // [암호화] 생년월일(yyyyMMdd)
    $Rest->AddBody("UserCellphoneNumber", "", true);  // [암호화] 휴대폰번호
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{A
    print($e->getMessage());
}
?>
