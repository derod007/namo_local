<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-NhisSimpleAuth-SimpleAuthRequest

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 국민건강보험공단의 간편인증 요청 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/nhissimpleauth/simpleauthrequest");
    
    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";

    // Body 추가
    $Rest->AddBody("PrivateAuthType", "", false);            // 인증종류 0: 카카오톡 / 1: 페이코 / 2: 국민은행모바일 / 3: 삼성패스 / 4: Pass
    $Rest->AddBody("UserName", "", true);                    // [암호화] 이용자명
    $Rest->AddBody("BirthDate", "", true);                  // [암호화] 생년월일(yyyyMMdd)
    $Rest->AddBody("UserCellphoneNumber", "", true);        // [암호화] 휴대폰번호
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
