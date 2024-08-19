<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Nhis-JpAca00101-GugMinYeonGeum

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 국민건강보험공단의 국민연금 납부내역 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/nhis/jpaca00101/gugminyeongeum");
    
    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";

    // Body 추가
    $rest->AddBody("CertFile", file_get_contents($PublicPath), true);                   // [암호화] 인증서 공개키(Base64 인코딩)
    $rest->AddBody("KeyFile", file_get_contents($PrivatePath), true);                    // [암호화] 인증서 개인키(Base64 인코딩)
    $rest->AddBody("CertPassword", $Constant::CertPassword, true);     // [암호화] 인증서 암호(Base64 인코딩)
    $rest->AddBody("Year", "", false);                               // 검색년도(yyyy)
    $rest->AddBody("StartMonth", "", false);                         // 검색 시작 월(MM)
    $rest->AddBody("EndMonth", "", false);                           // 검색 종료 월(MM)
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
