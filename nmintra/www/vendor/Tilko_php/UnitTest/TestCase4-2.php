<?php

set_include_path(get_include_path() . PATH_SEPARATOR . '../phpseclib1.0.19');
require_once('Crypt/RSA.php');

$apiHost   = "https://api.tilko.net/";
$apiKey    = "";


// AES 암호화 함수
function aesEncrypt($aesKey, $aesIv, $plainText) {
    $ret = openssl_encrypt($plainText, 'AES-128-CBC', $aesKey, OPENSSL_RAW_DATA, $aesIv);	//default padding은 PKCS7 padding
    return base64_encode($ret);
}


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
print("rsaPublicKey:" . $rsaPublicKey);


// AES Secret Key 및 IV 생성
$aesKey     = random_bytes(16);
$aesIv      = str_repeat(chr(0), 16);


// AES Key를 RSA Public Key로 암호화
$rsa            = new Crypt_RSA();
$rsa->loadKey($rsaPublicKey);
$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

$aesCipheredKey = $rsa->encrypt($aesKey);


// API URL 설정(정부24 간편인증 주민등록증진위여부 조회: https://tilko.net/Help/Api/POST-api-apiVersion-GovSimpleAuth-AA090UserJuminCheckResApp)
$url        = $apiHost . "api/v1.0/GovSimpleAuth/AA090UserJuminCheckResApp";


// 간편인증 요청 후 받은 값 정리
$reqData = array(
    "CxId"                      => "",
    "PrivateAuthType"           => "",
    "ReqTxId"                   => "",
    "Token"                     => "",
    "TxId"                      => "",
    "UserName"                  => "",
    "BirthDate"                 => "",
    "UserCellphoneNumber"       => "",
);


// API 요청 파라미터 설정
$headers    = array(
    "Content-Type:"             . "application/json",
    "API-Key:"                  . $apiKey,
    "ENC-Key:"                  . base64_encode($aesCipheredKey),
);

$bodies     = array(
    "PersonName"                => aesEncrypt($aesKey, $aesIv, "홍길동"),
    "IdentityNumber"            => aesEncrypt($aesKey, $aesIv, "8801011234567"),
    "PublishDate"               => "20200101",
    "CxId"                      => $reqData["CxId"],
    "PrivateAuthType"           => $reqData["PrivateAuthType"],
    "ReqTxId"                   => $reqData["ReqTxId"],
    "Token"                     => $reqData["Token"],
    "TxId"                      => $reqData["TxId"],
    "UserName"                  => aesEncrypt($aesKey, $aesIv, $reqData["UserName"]),
    "BirthDate"                 => aesEncrypt($aesKey, $aesIv, $reqData["BirthDate"]),
    "UserCellphoneNumber"       => aesEncrypt($aesKey, $aesIv, $reqData["UserCellphoneNumber"]),
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
