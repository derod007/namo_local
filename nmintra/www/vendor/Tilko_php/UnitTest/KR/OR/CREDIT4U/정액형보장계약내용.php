<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Credit4u-ContractData

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 한국신용정보원의 정액형 보장 계약내용 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/credit4u/contractdata");
    
    // Body 추가
    $Rest->AddBody("UserID", "", true);          // [암호화] 로그인 아이디(Base64 인코딩)
    $Rest->AddBody("UserPassword", "", true);    // [암호화] 로그인 비밀번호(Base64 인코딩)   
    $Rest->AddBody("ContractStatus", "", false); // 계약상태(A:전체/S:정상 공백 시 정상 데이터만 조회합니다.)

    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
