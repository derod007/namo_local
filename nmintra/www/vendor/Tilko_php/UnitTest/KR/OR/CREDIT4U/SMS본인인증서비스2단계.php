<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Credit4u-CheckedSelfAuthCode

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 한국신용정보원의 SMS 본인인증 서비스 2단계 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/credit4u/checkedselfauthcode");
    
    // Body 추가
   
    $Rest->AddBody("UserName", "", true);            // [암호화] 가입자 명(Base64 인코딩)
    $Rest->AddBody("IdentityNumber", "", true);      // [암호화] 주민등록번호(8012151XXXXXX / Base64 인코딩)
    $Rest->AddBody("UserCellphone", "", true);       // [암호화] 연락처(010XXXXXXXX / Base64 인코딩)
    $Rest->AddBody("CaptchaCode", "", false);        // 캡챠코드Tilko Session이 유지되는 180초 이내에 입력을 하셔야 합니다.
   
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
