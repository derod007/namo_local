# PHP
PHP 프로젝트의 소스코드입니다.

## 데이터 형태별 샘플 코드
|파일명|설명|API 예시|
|---|---|---|
|UnitTest/TestCase1.php|인증서 필요 없음, 파라미터 암호화 필요 없음|인터넷등기소 등기물건 주소검색|
|UnitTest/TestCase2.php|인증서 필요 없음, 파라미터 암호화 필요함|한국신용정보원 가입여부 확인|
|UnitTest/TestCase3.php|인증서 필요함|정부24 주민등록진위여부|
|UnitTest/TestCase4-1.php|간편인증 요청|국민건강보험공단 간편인증 요청|
|UnitTest/TestCase4-2.php|간편인증용 API 호출|국민건강보험공단 건강검진내역|
|UnitTest/TestCase5.php|바이너리 데이터를 파일로 저장|인터넷등기소 등기부등본 PDF 발급|

## 샘플 코드 (API 호출)
```php
<?php
namespace UnitTest\KR\OR\NHIS;

$BasePath = realpath("../../../../Tilko.API/../");
require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/REST.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");

use Tilko\API;
use UnitTest;

// API 상세설명 URL
// https://tilko.net/Help/Api/POST-api-apiVersion-Nhis-Ggpab003M0105

try {
    $Constant = new \UnitTest\Constant;
    
    $Rest = new \Tilko\API\REST($Constant::ApiKey);
    $Rest->Init();
    
    // 국민건강보험공단의 건강검진내역 endPoint 설정
    $Rest->SetEndPointUrl($Constant::ApiHost . "api/v1.0/nhis/ggpab003m0105");
    
    // 공동인증서 경로 설정
    $PublicPath = $Constant::CertPath . "/signCert.der";
    $PrivatePath = $Constant::CertPath . "/signPri.key";
    
    // Body 추가
    $Rest->AddBody("CertFile", file_get_contents($PublicPath), true);   // [암호화] 인증서 공개키
    $Rest->AddBody("KeyFile", file_get_contents($PrivatePath), true);   // [암호화] 인증서 개인키
    $Rest->AddBody("CertPassword", $Constant::CertPassword, true);      // [암호화] 인증서 암호
    
    // API 호출
    define("Response", $Rest->Call());
    print("Response: " . Response);
}
catch (\Exception $e)
{
    print($e->getMessage());
}
?>

```
