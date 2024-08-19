<?php
namespace UnitTest\KR\GO\IROS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-HiKorea-InfoFrnRegIdChkRsltR-kr

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 하이코리아의 외국인등록진위여부 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/hikorea/infofrnregidchkrsltr/kr");

    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";

    // Body 추가
    $Rest->AddBody("CertFile", file_get_contents($PublicPath), true);                   // [암호화] 인증서 공개키(Base64 인코딩)
    $Rest->AddBody("KeyFile", file_get_contents($PrivatePath), true);                    // [암호화] 인증서 개인키(Base64 인코딩)
    $Rest->AddBody("CertPassword", $Constant::CertPassword, true);     // [암호화] 인증서 암호(Base64 인코딩)
    $Rest->AddBody("IdentityNumber	", "", true);                    // [암호화] 조회하는 사람의 주민등록번호(8012151XXXXXX / Base64 인코딩)
    $Rest->AddBody("TargetIdentityNumber", "", true);                // [암호화] 조회 대상의 외국인등록번호(9012156XXXXXX / Base64 인코딩)
    $Rest->AddBody("TargetPublishDate", "", false);                  // 조회 대상의 외국인등록증 발급일자(yyyyMMdd)
        
     // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
