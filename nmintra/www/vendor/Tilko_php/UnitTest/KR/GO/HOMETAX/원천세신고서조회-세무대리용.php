v<?php
namespace UnitTest\KR\GO\IROS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-HometaxAgent-UTERNAAZ110-WonCheonSe-SinGo

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 홈택스의 원천세 신고서 조회 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/hometaxagent/uternaaz110/woncheonse/singo");

    // 공동인증서 경로 설정 
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";

     // Body 추가
     $Rest->AddBody("CertFile", file_get_contents($PublicPath), true);                   // [암호화] 인증서 공개키(Base64 인코딩)
     $Rest->AddBody("KeyFile", file_get_contents($PrivatePath), true);                    // [암호화] 인증서 개인키(Base64 인코딩)
     $Rest->AddBody("CertPassword", $Constant::CertPassword, true);     // [암호화] 인증서 암호(Base64 인코딩)
     $Rest->AddBody("AgentId", "", true);                             // [암호화] 세무대리인 ID(세무대리 관리번호가 있는 경우 / Base64 인코딩)
     $Rest->AddBody("AgentPassword", "", true);                       // [암호화] 세무대리인 암호(세무대리 관리번호가 있는 경우 / Base64 인코딩)
     $Rest->AddBody("BusinessNumber", "", true);                      // [암호화] 검색 할 사업자등록번호 또는 주민등록번호(xxxxxxxxxx 또는 xxxxxxxxxxxxx / Base64 인코딩) 공백일 검색기간은 30일, 아닐경우 검색기간은 365일
     $Rest->AddBody("StartDate", "", false);                          // 검색시작일(yyyyMMdd) 공백일 경우 기본값을 API에서 셋팅 
     $Rest->AddBody("EndDate", "", false);                            // 검색종료일(yyyyMMdd) 공백일 경우 기본값을 API에서 셋팅
    
     // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
