<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Credit4u-CheckedSelf

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 한국신용정보원의 본인인증 서비스 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/credit4u/checkedself");
    
    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";

    // Body 추가
    $Rest->AddBody("CertFile", file_get_contents($PublicPath), true);                    // [암호화] 인증서 공개키(Base64 인코딩)
    $reset->AddBody("KeyFile", file_get_contents($PrivatePath), true);                    // [암호화] 인증서 개인키(Base64 인코딩)
    $rest->AddBody("CertPassword", $Constant::CertPassword, true);      // [암호화] 인증서 암호(Base64 인코딩)
    $Rest->AddBody("IdentityNumber", "", true);                       // [암호화] 주민등록번호(8012151XXXXXX / Base64 인코딩)
    $Rest->AddBody("UserID", "", true);                               // [암호화] 로그인 아이디(Base64 인코딩)
    $Rest->AddBody("UserPassword	", "", true);                     // [암호화] 로그인 비밀번호(Base64 인코딩)
    $Rest->AddBody("UserName", "", true);                             // [암호화] 가입자 명(Base64 인코딩)

    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
