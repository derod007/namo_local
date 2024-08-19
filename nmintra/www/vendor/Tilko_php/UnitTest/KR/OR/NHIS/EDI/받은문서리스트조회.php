<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-NhisEdi-RetrieveDocList

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 국민건강보험공단 EDI의 받은문서 리스트 조회 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/nhisedi/retrievedoclist");
    
    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";

    // Body 추가
    $Rest->AddBody("CertFile", file_get_contents($PublicPath), true);                   // [암호화] 인증서 공개키(Base64 인코딩)
    $Rest->AddBody("KeyFile", file_get_contents($PrivatePath), true);                    // [암호화] 인증서 개인키(Base64 인코딩)
    $Rest->AddBody("CertPassword", $Constant::CertPassword, true);     // [암호화] 인증서 암호(Base64 인코딩)
    $Rest->AddBody("IdentityNumber", "", true);                     // [암호화] 검색 할 위임 사업자등록번호(xxxxxxxxxx / Base64 인코딩)
    $Rest->AddBody("FirmSym", "", false);                           // 사업장기호
    $Rest->AddBody("UnitFirmSym", "", false);                       // 단위사업장기호
    $Rest->AddBody("FirmName", "", false);                          // 사업장명
    $Rest->AddBody("FirmMgmtNo	", "", false);                      // 사업장관리번호
    $Rest->AddBody("FromDt", "", false);                            // 조회기간(시작)
    $Rest->AddBody("ToDt", "", false);                              // 조회기간(종료)
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
