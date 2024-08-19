<?php
namespace UnitTest\KR\GO\IROS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Iros-RISURetrieve

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 인터넷등기소의 등기부등본 조회 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/iros/risuretrieve");

    // Body 추가
    $Rest->AddBody("IrosID", "", true);            // [암호화] iros.go.kr 로그인 ID(Base64 인코딩)
    $Rest->AddBody("IrosPwd", "", true);           // [암호화] iros.go.kr 로그인 패스워드(Base64 인코딩)
    $Rest->AddBody("EmoneyNo1", "", true);         // [암호화] 전자지불 선불카드 총 12자리 중 영문을 포함한 앞 8자리 입력(Base64 인코딩)
    $Rest->AddBody("EmoneyNo2", "", true);         // [암호화] 전자지불 선불카드 총 12자리 중 나머지 뒤 4자리 숫자 입력(Base64 인코딩)
    $Rest->AddBody("EmoneyPwd", "", true);         // [암호화] 전자지불 선불카드 비밀번호(Base64 인코딩)
    $Rest->AddBody("UniqueNo", "", false);         // 부동산 고유번호('-'을 제외한 14자리)
    $Rest->AddBody("JoinYn", "", false);           // 공동담보/전세목록 추출여부(Y/N 공백 또는 다른 문자열일 경우 기본값 N)
    $Rest->AddBody("CostsYn", "", false);          // 매매목록추출여부(Y/N 공백 또는 다른 문자열일 경우 기본값 N)
    $Rest->AddBody("DataYn", "", false);           // 전산폐쇄추출여부(Y/N 공백 또는 다른 문자열일 경우 기본값 N)
    $Rest->AddBody("ValidYn", "", false);          // 유효사항만 포함여부(Y/N 공백 또는 다른 문자열일 경우 기본값 N)
    $Rest->AddBody("IsSummary", "", false);        // 요약데이터 표시여부(Y/N 공백 또는 다른 문자열일 경우 기본값 Y)

    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
