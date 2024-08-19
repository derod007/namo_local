<?php
namespace UnitTest;

class Constant
{
    const ApiHost = "https://api.tilko.net/";

    /*
     * API 키
     * API 키는 틸코 API 홈페이지에서 발급 받으세요.
     * https://tilko.net
    */
    const ApiKey = "dbed1e37b2c2426ea31a260513a0ac42";

    /*
     * 공동인증서 경로 설정
     * 공동인증서는 "C:\Users\[사용자계정]\AppData\LocalLow\NPKI\yessign\USER\[인증서DN명]"에 존재합니다.
    */
    const CertPath = "C:\Users\[사용자계정]\AppData\LocalLow\NPKI\yessign\USER\[인증서DN명]";

    /*
     * 공동인증서 비밀번호
    */
    const CertPassword = "";
}
?>
