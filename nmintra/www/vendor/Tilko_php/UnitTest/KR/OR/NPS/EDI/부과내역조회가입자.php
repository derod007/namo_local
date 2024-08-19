<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-NpsEdi-U040205M01

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 국민연금 EDI 의 부과내역조회 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/npsedi/u040205m01");
    
    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";
    
    // Body 추가
    $Rest->AddBody("CertFile", file_get_contents($PublicPath), true);   // [암호화] 인증서 공개키
    $Rest->AddBody("KeyFile", file_get_contents($PrivatePath), true);   // [암호화] 인증서 개인키
    $Rest->AddBody("CertPassword", $Constant::CertPassword, true);      // [암호화] 인증서 암호
    $Rest->AddBody("BusinessNumber", "", true);                         // [암호화] 검색 할 사업자등록번호 또는 주민등록번호(xxxxxxxxxx 또는 xxxxxxxxxxxxx / Base64 인코딩)
    $Rest->AddBody("DocNo", "", false);                                 // 문서번호(국민연금보험료 결정내역 조회 후 받은 DocNo 값)
    $Rest->AddBody("ConfirmDt", "", false);                             // 해당년월(국민연금보험료 결정내역 조회 후 받은 ConfirmDt 값)
    $Rest->AddBody("FmCd", "", false);                                  // 국민연금보험료 결정내역 조회 후 받은 FmCd 값
    $Rest->AddBody("RgstChrgpId", "", false);                           // 사업장관리번호(국민연금보험료 결정내역 조회 후 받은 RgstChrgpId 값)
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
