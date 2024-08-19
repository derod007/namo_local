<?php
namespace UnitTest\KR\GO\IROS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Iros-GetPdfFile

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 인터넷등기소의 등기부등본 PDF 발급 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/iros/getpdffile");

    // Body 추가
    $Rest->AddBody("TransactionKey", "", false);    // 등본발급 시 리턴받은 트랜잭션 키 (GUID)
    $Rest->AddBody("IsSummary", "Y", false);        // 요약 데이터 포함여부(Y/N 공백 또는 다른 문자열일 경우 기본값 Y)

    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);

    // PDF 파일 저장
    file_put_contents("D:/Temp/test_등기부등본PDF발급.pdf", base64_decode(json_decode(Response)->Message));
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
