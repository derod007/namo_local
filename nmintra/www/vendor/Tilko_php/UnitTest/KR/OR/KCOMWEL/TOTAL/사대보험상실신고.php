<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Kcomwel-SamuSangsilSingo

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 고용산재토탈의 4대보험 상실신고 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/kcomwel/samusangsilsingo");
    
    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";
    
    // Body 추가
    $Rest->AddBody("CertFile", file_get_contents($PublicPath), true);   // [암호화] 인증서 공개키
    $Rest->AddBody("KeyFile", file_get_contents($PrivatePath), true);   // [암호화] 인증서 개인키
    $Rest->AddBody("CertPassword", $Constant::CertPassword, true);      // [암호화] 인증서 암호
    $Rest->AddBody("BusinessNumber", "", true);                         // [암호화] 검색 할 사업자등록번호 또는 주민등록번호(xxxxxxxxxx 또는 xxxxxxxxxxxxx / Base64 인코딩)
    $Rest->AddBody("UserGroupFlag", "", false);                         // 인증서 - 사업장(0)/사무대행(1) 구분
    $Rest->AddBody("IndividualFlag", "", false);                        // 인증서 - 개인(0)/법인(1) 구분
    $Rest->AddBody("GwanriNo", "", false);                              // 관리번호
    $Rest->AddBody("GeunrojaRgNo", "", true);                           // [암호화]근로자 주민등록번호(xxxxxxxxxxxxx / Base64 인코딩)
    $Rest->AddBody("SangsilDt", "", false);                             // 상실일자(YYYYMMDD)
    $Rest->AddBody("DBosuChongak", "", false);                          // 당해년도 보수총액
    $Rest->AddBody("DSanjengMM", "", false);                            // 당해년도 근무개월수
    $Rest->AddBody("JBosuChongak", "", false);                          // 전년도 보수총액
    $Rest->AddBody("JSanjengMM", "", false);                            // 전년도 근무개월수
    $Rest->AddBody("SangsilSayu", "", false);                           // 상실사유 - 개인사정으로인한자진퇴사(0)/사업장이전근로조건변동임금체불등으로자진퇴사(1)/폐업도산(2)/경영상필요및회사불황으로인원감축등에의한퇴사해고권고사직명예퇴직포함(3)/예술인근로자의귀책사유에의한징계해고권고사직(4)/정년(5)/계약기간만료공사종료(6)/고용보험비적용(7)/이중고용(8)
    $Rest->AddBody("SangsilSayuDetail", "", false);                     // 구체적 사유
    $Rest->AddBody("NHICSangsilBuhoCd", "", false);                     // 건강보험 상실 부호 - 퇴직(0)/사망(1)/의료급여수급권자(2)/유공자등건강보험배제신청(3)/기타외국인당연적용제외등(4)
    $Rest->AddBody("NPSSangsilBuhoCd", "", false);                      // 국민연금 상실 부호 - 사망(0)/사용관계종료(1)/국적상실국외이주(2)/육십세도달(3)/다른공적연금가입(4)/전출통폐합(5)/국민기초생활보장법에따른수급자(6)/노령연금수급권취득자중특수직종60세미만(7)/협정국연금가입(8)/체류기간만료외국인(9)/적용제외체류자격외국인(10)
   
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
