<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Hira-SuspectedDiseasesGet

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 건강보험심사평가원의 11대 질병 의심 여부 조회내역 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/hira/suspecteddiseasesget");
    
    // Body 추가
    $Rest->AddBody("MedicineCodeList", "", false);      // 내가먹는약 서비스의 약품코드(예 : {"medicine_code_list" : ["660700010","643503630","645903041","648104500","649801381"]})
        
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
