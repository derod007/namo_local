<?php

set_include_path(get_include_path() . PATH_SEPARATOR . '../phpseclib1.0.19');
require_once('Crypt/RSA.php');

$apiHost   = "https://api.tilko.net/";
$apiKey    = "dbed1e37b2c2426ea31a260513a0ac42";		// Tilko API-KEY


// RSA 공개키(Public Key) 조회 함수
function getPublicKey($apiKey) {
    global $apiHost;

    $url        = $apiHost . "api/Auth/GetPublicKey?APIkey=" . $apiKey;

    $curl       = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL             => $url,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_CUSTOMREQUEST   => "GET",
        CURLOPT_SSL_VERIFYHOST  => 0,
        CURLOPT_SSL_VERIFYPEER  => 0
    ));

    $response   = curl_exec($curl);

    curl_close($curl);

    return json_decode($response, true)["PublicKey"];
}


// RSA Public Key 조회
$rsaPublicKey   = getPublicKey($apiKey);
//print("rsaPublicKey:" . $rsaPublicKey);


// AES Secret Key 및 IV 생성
$aesKey     = random_bytes(16);
$aesIv      = array(0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00);


// AES Key를 RSA Public Key로 암호화
$rsa            = new Crypt_RSA();
$rsa->loadKey($rsaPublicKey);
$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

$aesCipheredKey = $rsa->encrypt($aesKey);


// API URL 설정(인터넷 등기소 등기물건 주소검색: https://tilko.net/Help/Api/POST-api-apiVersion-Iros-RISUConfirmSimpleC)
$url        = $apiHost . "api/v1.0/Iros/RISUConfirmSimpleC";


// API 요청 파라미터 설정
$headers    = array(
    "Content-Type:"             . "application/json",
    "API-Key:"                  . $apiKey,
    "ENC-Key:"                  . base64_encode($aesCipheredKey),
);

$bodies     = array(
    "Address"                   => "서울특별시 중구 무교동 11",		// 주소
    "Sangtae"                   => "2",		// 상태(공백 시 현행폐쇄) 현행:0/폐쇄:1/ 현행폐쇄:2
    "KindClsFlag"               => "0",	// 부동산구분(공백 시 전체) 전체:0/집합건물:1/건물:2/토지:3
    "Region"                    => "0",		// 도시(공백 시 전체) 전체:0/서울특별시:1/부산광역시:2/ ...
    "Page"                      => "1",		// 조회할 페이지 번호(기본값 1)
);


// API 호출
$curl   = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL             => $url,
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_CUSTOMREQUEST   => "POST",
    CURLOPT_POSTFIELDS      => json_encode($bodies),
    CURLOPT_HTTPHEADER      => $headers,
    CURLOPT_VERBOSE         => false,
    CURLOPT_SSL_VERIFYHOST  => 0,
    CURLOPT_SSL_VERIFYPEER  => 0
));

$response   = curl_exec($curl);

curl_close($curl);

print($response);

?>
