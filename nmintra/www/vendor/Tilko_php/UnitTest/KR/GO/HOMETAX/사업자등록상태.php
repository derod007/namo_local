<?php
namespace UnitTest\KR\GO\IROS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Hometax-UTEABAAA13-SaEobJaSangTae

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 홈택스의 사업자등록상태 조회 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/hometax/uteabaaa13/saeobjasangtae");

    // Body 추가
    $Rest->AddBody("BusinessNumber", "", true);       // [암호화] 검색 할 사업자등록번호 (Base64 인코딩)")
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
