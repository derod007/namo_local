<?php
namespace UnitTest\KR\GO\IROS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Iros-REVTWelcomeEvtC

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 인터넷등기소의 등기신청사건 처리현황 조회 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/iros/revtwelcomeevtc");

    // Body 추가
    $Rest->AddBody("IrosID", "", true);          // [암호화] iros.go.kr 로그인 ID(Base64 인코딩)
    $Rest->AddBody("IrosPwd", "", true);         // [암호화] iros.go.kr 로그인 패스워드(Base64 인코딩)
    $Rest->AddBody("UniqueNo", "", false);       // 부동산 고유번호('-'을 제외한 14자리)
    $Rest->AddBody("InsRealClsCd", "", false);   // 구분(공백시 건물) 토지 : 0 / 건물 : 1 / 집합건물 : 2
    $Rest->AddBody("A103Name", "", false);       // 소유자명

    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
