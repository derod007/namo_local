<?php
namespace UnitTest\KR\GO\IROS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Efine-LicenTruth

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 경찰청교통민원24의 운전면허진위여부 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/efine/licentruth");

    // Body 추가
    $Rest->AddBody("BirthDate", "", false);       // 대상자 생년월일(yyyyMMdd)
    $Rest->AddBody("Name", "", false);            // 대상자 성명
    $Rest->AddBody("LicNumber", "", true);        // [암호화] 운전면허번호(예: 서울-XX-XXXXXX-XX / Base64 인코딩)
    $Rest->AddBody("SpecialNumber", "", true);    // [암호화] 식별번호(면허증 우측 하단 작은 사진 밑에 있는 일련번호 / Base64 인코딩)
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
