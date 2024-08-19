<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Credit4u-SendAuthCode

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 한국신용정보원의 인증코드 발송 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/credit4u/sendauthcode");
    
    // Body 추가
    $Rest->AddBody("UserEmail", "", true);          // [암호화] 사이트 가입용 이메일(예: 이메일@도메인 / Base64 인코딩)
  
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
