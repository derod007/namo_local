<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Credit4u-CheckedSelfCaptcha

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 한국신용정보원의 SMS 본인인증 서비스 1단계 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/credit4u/checkedselfcaptcha");
    
    // Body 추가
   
    $Rest->AddBody("UserID", "", true);            // [암호화] 로그인 아이디(Base64 인코딩)
    $Rest->AddBody("UserPassword", "", true);      // [암호화] 로그인 비밀번호(Base64 인코딩)
    $Rest->AddBody("UserName", "", true);          // [암호화] 가입자 명(Base64 인코딩)
    $Rest->AddBody("IdentityNumber", "", true);    // [암호화] 주민등록번호(8012151XXXXXX / Base64 인코딩)
    $Rest->AddBody("NiceMobileCorp", "", false);   // 통신사코드SKT : 0 / KT : 1 / LGT : 2 / SKM(알뜰폰) : 3 / KTM(알뜰폰) : 4 / LGM(알뜰폰) : 5
   
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
