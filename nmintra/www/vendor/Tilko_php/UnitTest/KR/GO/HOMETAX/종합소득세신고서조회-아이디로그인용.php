<?php
namespace UnitTest\KR\GO\IROS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-HometaxIdLogin-UTERNAAZ110-JongHabSoDeugSe-SinGo

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 홈택스의 종합소득세 신고서 조회 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/hometaxidlogin/uternaaz110/jonghabsodeugse/singo");

    // Body 추가
    $Rest->AddBody("UserId", "", true);                // [암호화] 홈택스 ID(Base64 인코딩)
    $Rest->AddBody("UserPassword", "", true);          // [암호화] 홈택스 암호(Base64 인코딩)
    $Rest->AddBody("BusinessNumber", "", true);        // [암호화] 검색 할 사업자등록번호 또는 주민등록번호(xxxxxxxxxx 또는 xxxxxxxxxxxxx / Base64 인코딩) 공백일 검색기간은 30일, 아닐경우 검색기간은 365일
    $Rest->AddBody("StartDate", "", false);            // 검색시작일(yyyyMMdd) 공백일 경우 기본값을 API에서 셋팅
    $Rest->AddBody("EndDate", "", false);              // 검색종료일(yyyyMMdd) 공백일 경우 기본값을 API에서 셋팅
     
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
