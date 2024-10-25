<?php
// API 엔드포인트 URL
$url = 'https://datahub-dev.scraping.co.kr/scrap/common/kbland/preaptnameInquiry';

// 요청에 사용할 데이터
$data = array(
    'DONGRONM' => '신천동',
    'DONGROCODE' => '4139010200'
);

// cURL 초기화
$ch = curl_init($url);

// JSON 형식으로 데이터 인코딩
$jsonData = json_encode($data);

// cURL 옵션 설정
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    // 'Authorization: Token 1072f0e3eeeb4f4c88a368e49c4e72025a0c74d5',
    'Content-Type: application/json;charset=UTF-8',
    'Content-Length: ' . strlen($jsonData)
));

// API 요청 실행
$response = curl_exec($ch);

// cURL 오류 확인
if(curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
}

// cURL 세션 종료
curl_close($ch);

// 응답 데이터 출력
echo $response;
?>
