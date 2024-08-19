<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Kcomwel-SelectGeunrojaGyIryeok

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 고용산재토탈의 근로자 고용정보 현황 조회 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/kcomwel/selectgeunrojagyiryeok");
    
    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";
    
    // Body 추가
    $Rest->AddBody("CertFile", file_get_contents($PublicPath), true);   // [암호화] 인증서 공개키
    $Rest->AddBody("KeyFile", file_get_contents($PrivatePath), true);   // [암호화] 인증서 개인키
    $Rest->AddBody("CertPassword", $Constant::CertPassword, true);      // [암호화] 인증서 암호
    $Rest->AddBody("BusinessNumber", "", true);                         // [암호화] 검색 할 사업자등록번호 또는 주민등록번호(xxxxxxxxxx 또는 xxxxxxxxxxxxx / Base64 인코딩)
    $Rest->AddBody("UserGroupFlag", "", false);                         // 인증서 - 사업장(0)/사무대행(1) 구분
    $Rest->AddBody("IndividualFlag", "", false);                        // 인증서 - 개인(0)/법인(1) 구분
    $Rest->AddBody("GwanriNo", "", false);                              // 관리번호
    $Rest->AddBody("BoheomFg", "", false);                              // 보험구분 - 산재(0)/고용(1)/전체(2)
    $Rest->AddBody("GyStatusCd", "", false);                            // 고용상태 - 고용(0)/고용종료(1)/고용휴직(2)/전체(3)
    $Rest->AddBody("GeunrojaNm", "", false);                            // 근로자 성명
    $Rest->AddBody("GeunrojaRgNo", "", true);                           // [암호화] 근로자 주민등록번호(13자리)
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
