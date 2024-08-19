<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

$mortext = trim($_POST['mortext']);

//var_dump($mortext);

function extractInformation($inputString) {
    // 정규표현식을 사용하여 정보 추출
    preg_match('/▶\s*(선순위)?(?:\s*\/\/\s*(\w+)\s+\(최고액:\s*([\d.]+)%,\s*원금:\s*([\d.]+)%\))/u', $inputString, $matches);
	
    // 선순위 정보 추출
    $priority = isset($matches[1]) ? $matches[1] : '선순위';
    $ltv = isset($matches[2]) ? $matches[2] : '';
    $maxAmount = isset($matches[3]) ? $matches[3] : '';
    $principal = isset($matches[4]) ? $matches[4] : '';
    
    // 대출 기관 정보 추출
    //preg_match_all('/(\d+)\.\s+([\p{L}\d\s]+)\s+(\d+,\d+)만(?:\s+\(잔액\s+(\d+,\d+)만\))?(?:\s+\/\s+([\d.]+)%)?/u', $inputString, $matches, PREG_SET_ORDER);
	preg_match_all('/(\d+)\.\s+([\p{L}\d\s]+)\s+(\d+,\d+)만?(\s*\(\s*(?:잔액)?\s*([\d,]+)만\s*\))?(?:\s+\/\s+([\d.]+)%)?/u', $inputString, $matches, PREG_SET_ORDER);
    //print_r($matches);
	
    $loanProviders = array();
    foreach ($matches as $match) {
        $provider = array(
           'rank' => $match[1],		
            'name' => $match[2],
            'totalAmount' => str_replace(',', '', $match[3]) // 쉼표 제거
        );

        // '잔액' 정보가 있는 경우
		/*
        if (isset($match[4])) {
            $provider['principalAmount'] = str_replace(',', '', $match[4]); // 쉼표 제거
        } else
		*/
		if (isset($match[5])) {
            $provider['principalAmount'] = str_replace(',', '', $match[5]); // 쉼표 제거
        } else {
            // 괄호 안에 principalAmount가 없는 경우
            preg_match('/\(([\d,]+)만\)/', $match[0], $principalMatch);
            if (isset($principalMatch[1])) {
                $provider['principalAmount'] = str_replace(',', '', $principalMatch[1]); // 쉼표 제거
            }
        }
		

        // LTV 정보가 있는 경우
        if (isset($match[6])) {
            $provider['ltv'] = $match[6];
        }

        $loanProviders[] = $provider;
    }
        
    // 합계 정보 추출
    preg_match('/총\s+합계\s+(\d+,\d+)\s+\((\d+,\d+)\)/u', $inputString, $matches);
    $totalAmount = str_replace(',', '', $matches[1]); // 쉼표 제거
    $totalPrincipal = str_replace(',', '', $matches[2]); // 쉼표 제거
    
    // 추출한 정보 반환
    $ret = array(
        'priority' => $priority,
        'ltv' => $ltv,
        'maxAmount' => $maxAmount,
        'principal' => $principal,
        'loanProviders' => $loanProviders,
        'totalAmount' => $totalAmount,
        'totalPrincipal' => $totalPrincipal
    );
	
	return json_encode($ret, JSON_UNESCAPED_UNICODE);
}

// 정보 추출
echo extractInformation($mortext);


