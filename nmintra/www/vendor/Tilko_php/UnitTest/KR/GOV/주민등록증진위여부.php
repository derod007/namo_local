<?php
namespace UnitTest\KR\GO\IROS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Gov-AA090UserJuminCheckResApp

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 정부24의 주민등록증진위여부 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/gov/aa090userjumincheckresapp");
    
    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";

    // Body 추가
    $Rest->AddBody("CertFile", "", true);             // [암호화] 조회하는 사람의 인증서 공개키(Base64 인코딩)
    $Rest->AddBody("KeyFile", "", true);              // [암호화] 조회하는 사람의 인증서 개인키(Base64 인코딩)
    $Rest->AddBody("CertPassword", "", true);         // [암호화] 조회하는 사람의 인증서 암호(Base64 인코딩)
    $Rest->AddBody("PersonName", "", false);          // 조회 대상의 이름
    $Rest->AddBody("IdentityNumber", "", true);       // [암호화] 조회 대상의 주민등록번호(8012151XXXXXX / Base64 인코딩)
    $Rest->AddBody("PublishDate", "", false);         // 신분증 발행일(주민등록증 하단 발급기관 위의 발행날짜 / yyyyMMdd)
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>
