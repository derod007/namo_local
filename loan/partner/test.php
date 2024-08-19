<?php 

$ch = curl_init();
$url = 'http://openapi.molit.go.kr:8081/OpenAPI_ToolInstallPackage/service/rest/RTMSOBJSvc/getRTMSDataSvcAptTrade'; /*URL*/
$queryParams = '?' . urlencode('ServiceKey') . '=PkmvtK%2BS63cjV8jQpYHUDoqVM2akCl%2FX4Z0iI7710fIB84CJy2HeRwxOIx%2FYtySzD5KspW3B10M7bUex9vjaKw%3D%3D'; /*Service Key*/
//$queryParams .= '&' . urlencode('pageNo') . '=' . urlencode('1'); /* 페이징 */
//$queryParams .= '&' . urlencode('numOfRows') . '=' . urlencode('100'); /* 요청갯수 */
$queryParams .= '&' . urlencode('LAWD_CD') . '=' . urlencode('41465'); /* 각 지역별 코드 - 송파구 */	
$queryParams .= '&' . urlencode('DEAL_YMD') . '=' . urlencode('201908'); /* 월 단위 신고자료 */

curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
$response = curl_exec($ch);
curl_close($ch);

//var_dump($response);
/* 
header('Content-type: text/xml');
echo $response;
die();
 */

$xml=simplexml_load_string($response) or die("Error: Cannot create object");

$result_code = $xml->header[0]->resultCode;
$result_msg = $xml->header[0]->resultMsg;
$total_cnt = $xml->body[0]->totalCount;
$page = $xml->body[0]->pageNo;
$page_rows = $xml->body[0]->numOfRows;

if($result_code == "00") {
	echo "<H3>성공</H3>";
	echo "<div>전체 ".$total_cnt ."건</div>";
	//echo "<div>전체 ".$total_cnt ."건 중 ".$page_rows ."건, ".$page." page </div>";
}

$obj_addr = $xml->body[0]->items[0]; // ->item[0]


echo "
<table border='1'>
	<tr>
		<td>번호</td><td>지역코드</td><td>법정동</td><td>아파트</td><td>건축년도</td><td>지번</td><td>전용면적(㎡)</td><td>년</td><td>월</td><td>거래금액(만원)</td><td>층</td>
	</tr>
";

$i=0;
foreach($obj_addr->item as $item) { 
	echo "<tr>".PHP_EOL;
	 echo "<td>".++$i. "</td>"; 
	 echo "<td>".$item->지역코드 . "</td>"; 
	 echo "<td>".$item->법정동 . "</td>"; 
	 echo "<td>".$item->아파트 . "</td>"; 
	 echo "<td>".$item->건축년도 . "</td>"; 
	 echo "<td>".$item->지번 . "</td>"; 
	 echo "<td>".$item->전용면적 . "</td>"; 
	 echo "<td>".$item->년 . "</td>"; 
	 echo "<td>".$item->월 . "</td>"; 
	 echo "<td>".str_replace(",","",$item->거래금액) . "</td>"; 
	 echo "<td>".$item->층 . "</td>".PHP_EOL;
	 echo "</tr>".PHP_EOL;
} 
echo "</table>";
?>