<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Nhis-RetrieveCareDescList

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 국민건강보험공단의 병원/약국 이용 이력 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/nhis/retrievecaredesclist");

    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";
    
    // Body 추가
    $rest->AddBody("CertFile", file_get_contents($PublicPath), true);                   // [암호화] 인증서 공개키(Base64 인코딩)
    $rest->AddBody("KeyFile", file_get_contents($PrivatePath), true);                    // [암호화] 인증서 개인키(Base64 인코딩)
    $rest->AddBody("CertPassword", $Constant::CertPassword, true);     // [암호화] 인증서 암호(Base64 인코딩)
    $Rest->AddBody("IdentityNumber", "", true);                      // [암호화]유저 주민등록번호 앞자리(yyMMdd / Base64 인코딩)
    $Rest->AddBody("StartDate", "", false);                          // 검색시작일(yyyyMMdd) 오늘부터 14개월 전부터 조회 가능
    $Rest->AddBody("EndDate", "", false);                            // 검색종료일(yyyyMMdd) 오늘부터 2개월 전까지 조회 가능
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
