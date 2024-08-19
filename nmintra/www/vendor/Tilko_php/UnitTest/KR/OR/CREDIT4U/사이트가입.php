<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Credit4u-JoinSite

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 한국신용정보원의 사이트 가입 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/credit4u/joinsite");
    
    // Body 추가
    $Rest->AddBody("EmailCode", "", true);            // [암호화] 이메일 인증코드(Base64 인코딩)
    $Rest->AddBody("UserEmail", "", true);            // [암호화] 사이트 가입용 이메일(예: 이메일@도메인 / Base64 인코딩)
    $Rest->AddBody("UserID", "", true);               // [암호화] 로그인 아이디(Base64 인코딩)
    $Rest->AddBody("UserPassword", "", true);         // [암호화] 로그인 비밀번호(Base64 인코딩)
    $Rest->AddBody("UserName", "", true);             // [암호화] 가입자 명(Base64 인코딩)
    $Rest->AddBody("DateOfBirth", "", false);         // 생년월일(yyyyMMdd)
    $Rest->AddBody("Sex", "", false);                 // 성별(남자 : m / 여자 : f)
   
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
