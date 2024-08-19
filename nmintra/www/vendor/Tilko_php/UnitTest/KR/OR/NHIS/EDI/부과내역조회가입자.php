<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-NhisEdi-BMBB_020

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 국민건강보험공단 EDI의 부과내역조회(가입자) endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/nhisedi/bmbb_020");
    
    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";

    // Body 추가
    $Rest->AddBody("CertFile", file_get_contents($PublicPath), true);                    // [암호화] 인증서 공개키(Base64 인코딩)
    $Rest->AddBody("KeyFile", file_get_contents($PrivatePath), true);                     // [암호화] 인증서 개인키(Base64 인코딩)
    $Rest->AddBody("CertPassword", $Constant::CertPassword, true);      // [암호화] 인증서 암호(Base64 인코딩)
    $Rest->AddBody("IdentityNumber", "", true);                      // [암호화] 검색 할 위임 사업자등록번호(xxxxxxxxxx / Base64 인코딩)
    $Rest->AddBody("FirmSym", "", false);                            // 사업장기호
    $Rest->AddBody("UnitFirmSym", "", false);                        // 단위사업장기호
    $Rest->AddBody("FirmMgmtNo", "", false);                         // 사업장관리번호
    $Rest->AddBody("WrtChasu", "", false);                           // 작성일차수
    $Rest->AddBody("WrtDupSeq", "", false);                          // 작성일차수Seq
    $Rest->AddBody("GojiYyyymm", "", false);                         // 고지년월
    $Rest->AddBody("GojiChasu", "", false);                          // 고지차수
    $Rest->AddBody("DocId", "", false);                              // DocId
    $Rest->AddBody("NationFinanceCd", "", false);                    // NationFinanceCd
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
