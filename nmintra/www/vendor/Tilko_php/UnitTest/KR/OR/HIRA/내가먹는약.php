<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Hira-HIRAA050300000100

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 건강보험심사평가원의 내가 먹는 약 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/hira/hiraa050300000100");
    
    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";

    // Body 추가
    $rest->AddBody("CertFile", file_get_contents($PublicPath), true);                   // [암호화] 인증서 공개키(Base64 인코딩)
    $rest->AddBody("KeyFile", file_get_contents($PrivatePath), true);                    // [암호화] 인증서 개인키(Base64 인코딩)
    $rest->AddBody("CertPassword", $Constant::CertPassword, true);     // [암호화] 인증서 암호(Base64 인코딩)
    $Rest->AddBody("IdentityNumber", "", true);                      // [암호화] 주민등록번호(8012151234567 / Base64 인코딩)
    $Rest->AddBody("TelecomCompany", "", falsee);                    // 통신사 SKT : 0 / KT : 1 / LGT : 2 / SKT알뜰폰 : 3 / KT알뜰폰 : 4 / LGT알뜰폰 : 5 / NA : 6
    $Rest->AddBody("CellphoneNumber", "", true);                     // [암호화] 연락처(010XXXXXXXX / Base64 인코딩)
        
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
