<?php

if (!defined('_TKAPI_')) exit;

	$cfg['IrosID'] = "namo605";			// iros.go.kr 로그인 ID(Base64 인코딩)
	$cfg['IrosPwd'] = "~605namo!";		// iros.go.kr 로그인 패스워드(Base64 인코딩)
	$cfg['EmoneyNo1'] = "X8089225";	// 전자지불 선불카드 총 12자리 중 영문을 포함한 앞 8자리 입력(Base64 인코딩)
	$cfg['EmoneyNo2'] = "9809";	// 전자지불 선불카드 총 12자리 중 나머지 뒤 4자리 숫자 입력(Base64 인코딩)
	$cfg['EmoneyPwd'] = "nm8292!";	// 전자지불 선불카드 비밀번호(Base64 인코딩)
	// X8089225 9809
	
if(false) {	

	$ch = curl_init();
	$url = 'https://api.tilko.net/api/v1.0/iros/risuconfirmsimplec'; /*URL*/
	$queryParams = '?' . urlencode('ServiceKey') . '=PkmvtK%2BS63cjV8jQpYHUDoqVM2akCl%2FX4Z0iI7710fIB84CJy2HeRwxOIx%2FYtySzD5KspW3B10M7bUex9vjaKw%3D%3D'; /*Service Key*/
	//$queryParams .= '&' . urlencode('pageNo') . '=' . urlencode('1'); /* 페이징 */
	//$queryParams .= '&' . urlencode('numOfRows') . '=' . urlencode('100'); /* 요청갯수 */
	$queryParams .= '&' . urlencode('LAWD_CD') . '=' . urlencode($region); /* 각 지역별 코드 - 수지구 '41465' */	
	$queryParams .= '&' . urlencode('DEAL_YMD') . '=' . urlencode($yymm); /* 월 단위 신고자료 */

	curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	$response = curl_exec($ch);
	curl_close($ch);
	
}

?>