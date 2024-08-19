<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Kcomwel-SelectSdgSaeopjangInfoNaeyeok

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 고용산재토탈의 사무수임사업장 내역 조회 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/kcomwel/selectsdgsaeopjanginfonaeyeok");
    
    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";
    
    // Body 추가
    $Rest->AddBody("CertFile", file_get_contents($PublicPath), true);   // [암호화] 인증서 공개키(Base64 인코딩)
    $Rest->AddBody("KeyFile", file_get_contents($PrivatePath), true);   // [암호화] 인증서 개인키(Base64 인코딩)
    $Rest->AddBody("CertPassword", $Constant::CertPassword, true);      // [암호화] 인증서 암호(Base64 인코딩)
    $Rest->AddBody("BusinessNumber", "", true);                         // [암호화] 검색 할 사업자등록번호 또는 주민등록번호(xxxxxxxxxx 또는 xxxxxxxxxxxxx / Base64 인코딩)
    $Rest->AddBody("UserGroupFlag", "", false);                         // 인증서 - 사업장(0)/사무대행(1) 구분
    $Rest->AddBody("IndividualFlag", "", false);                        // 인증서 - 개인(0)/법인(1) 구분
    $Rest->AddBody("BoheomFg", "", false);                              // 보험구분 - 산재(0)/고용(1)/전체(2)
    $Rest->AddBody("BugwaGojiYn", "", false);                           // 부과고지구분 - 부과고지(0)/자진신고(1)/전체(2)
    $Rest->AddBody("SjSaeopFg", "", false);                             // 산재사업구분 - 계속(0)/유기(1)/일괄계속(2)/일괄유기(3)/해외사업장(4)/중소기업사업주(5)/자영업자(6)/전체(7)
    $Rest->AddBody("GySaeopFg", "", false);                             // 고용사업구분 - 계속(0)/유기(1)/일괄계속(2)/일괄유기(3)/해외사업장(4)/중소기업사업주(5)/자영업자(6)/전체(7)
    $Rest->AddBody("GySaeopjangStatusCd", "", false);                   // 고용사업장상태 - 정상(0)/소멸(1)/해지(2)/전체(3)
    $Rest->AddBody("SjSaeopjangStatusCd", "", false);                   // 산재사업장상태 - 정상(0)/소멸(1)/해지(2)/전체(3)
    $Rest->AddBody("GwanriNo", "", false);                              // 관리번호
    $Rest->AddBody("GwanriJisaCd", "", false);                          // 관리지사 - 별도제공
    $Rest->AddBody("JeopsuInfoJoheoYN", "", false);                     // 접수내역표시 - Y/N
    $Rest->AddBody("JeopsuDtFrom", "", false);                          // 접수일(시작 - yyyyMMdd)
    $Rest->AddBody("JeopsuDtTo", "", false);                            // 접수일(종료 - yyyyMMdd)
   
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
