<?php 
error_reporting(E_ALL);
ini_set("display_errors", 1);

// http://apitest.ddiablo.net/yt_test.php
// part=snippet&channelId=UCbp4AndsC9wE4kz9wJVtqaQ&maxResults=10&order=date&type=video&key=[YOUR_API_KEY]'

$ch = curl_init();
$url = 'https://www.googleapis.com/youtube/v3/search'; /*URL*/
$queryParams = '?' . urlencode('part') . '=' . urlencode('snippet'); /*  part */
$queryParams .= '&' . urlencode('maxResults') . '=' . urlencode('10'); /* 페이지당 데이터 */
$queryParams .= '&' . urlencode('channelId') . '=' . urlencode('UCbp4AndsC9wE4kz9wJVtqaQ'); /* 검색할 채널아이디 */	
$queryParams .= '&' . urlencode('type') . '=' . urlencode('video'); /* 데이터타입 (video, channel, playlists) */
$queryParams .= '&' . urlencode('order') . '=' . urlencode('date'); /* 데이터타입 (video, channel, playlists) */
$queryParams .= '&' . urlencode('key') . '=' . 'AIzaSyD6gBAL4srAjXJk4KNiBGSd8bsl4H-zIz8'; /* Google API Key*/
//$queryParams .= '&' . urlencode('pageToken') . '='  . 'CAoQAA'; /* 다음페이지*/

curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
$response = curl_exec($ch);
curl_close($ch);

//var_dump($response);
/* 
header('Content-type: text/xml');
echo $response;
die();
 */

$result = json_decode($response);

echo "<pre>";
print_r($result);
echo "</pre>";
die();



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