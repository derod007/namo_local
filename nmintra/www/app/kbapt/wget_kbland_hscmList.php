<?php
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

if(!$_GET['rcode']) {
	echo "<H3>실패 : 법정동코드가 누락되었습니다.</H3>";
	die();
}

$rcode = $_GET['rcode']; // 법정동코드

$ch = curl_init();
$url = 'https://api.kbland.kr/land-complex/complexComm/hscmList'; // URL
$queryParams = '?' . urlencode('법정동코드') .'=' . urlencode($rcode); 
//$queryParams .= '&' . urlencode('pageNo') . '=' . urlencode('1'); /* 페이징 */
//$queryParams .= '&' . urlencode('numOfRows') . '=' . urlencode('100'); /* 요청갯수 */

$agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.67 Safari/537.36";

curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_USERAGENT, $agent);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
$response = curl_exec($ch);
curl_close($ch);

//var_dump($response);
/*  */
if($response) {
	//header('Content-type: text/xml');
	//echo $response;
	echo "Read Complete<br/>\n";
}
//die();
 /* */

$getdata = json_decode($response, true);
//print_r2($getdata);


$result_code = $getdata['dataHeader']['resultCode'];
$result_msg = $getdata['dataHeader']['message'];
$datas = $getdata['dataBody']['data'];	// 단지데이터 목록

ob_start();

if($result_code == "10000") {
	echo "<H3>성공</H3>";
} else {
	echo "<H3>실패 - {$result_msg}</H3>";
}

/*
      {
        "단지기본일련번호": 12616,
        "물건식별자": "KBM014126",
        "wgs84포인트": "AAAAAAEBAAAAxyEsdO/EX0CzgjpAi79CQA==",
        "단지명": "LG개포자이",
        "법정동코드": "1168010300",
        "매물종별구분명": "아파트",
        "매물종별구분": "01",
        "wgs84경도": "127.0771151",
        "재건축여부": "0",
        "wgs84위도": "37.4964371"
      },
*/

$sql = "select kbno from kbland_danzi where regioncode = '{$rcode}'";
$result = sql_query($sql);
$indata = array();
while($row=sql_fetch_array($result)){
	$indata[] = $row['kbno'];
}

echo "
<table border='1'>
	<tr>
		<td>번호</td><td>단지기본일련번호</td><td>물건식별자</td><td>단지명</td><td>법정동코드</td><td>구분</td><td>위도/경도</td><td>재건축여부</td>
	</tr>
";

$i=0;
$saved = 0;
foreach($datas as $item) { 
	echo "<tr>".PHP_EOL;
	 echo "<td>".++$i. "</td>"; 
	 echo "<td>".$item['단지기본일련번호']. "</td>"; 
	 echo "<td>".$item['물건식별자']. "</td>"; 
	 echo "<td>".$item['단지명']. "</td>"; 
	 echo "<td>".$item['법정동코드']. "</td>"; 
	 echo "<td>".$item['매물종별구분명']. "</td>"; 
	 echo "<td>".$item['wgs84위도']. "/".$item['wgs84경도']."</td>"; 
	 echo "<td>".$item['재건축여부']. "</td>".PHP_EOL;
	 echo "</tr>".PHP_EOL;
	 
	 if(!in_array($item['단지기본일련번호'], $indata)) {
		// 크롤링 시작 로그기록
		$sql = "insert into kbland_danzi set
					kbno = '".$item['단지기본일련번호']."',
					kbcode = '".$item['물건식별자']."',
					danzi = '".addslashes($item['단지명'])."',
					regioncode = '".$item['법정동코드']. "',
					wgs84 = '".$item['wgs84포인트']."',
					mmgubun = '".$item['매물종별구분명']."',
					mmjong = '".$item['매물종별구분']."',
					lat = '".$item['wgs84위도']."',
					lng = '".$item['wgs84경도']."',
					rebuild = '".$item['재건축여부']."',
					regdate = '".TIME_YMDHIS."',
					lastdate = '".TIME_YMDHIS."' ";
		sql_query($sql);
		$saved++;
	 }
}
echo "</table>";

// 크롤링 시작 로그기록
$sql = "insert into kbland_api_log set
			api_url = '".$url."',
			query = '".urldecode($queryParams)."',
			result = '".$result_msg."',
			data_cnt = '".$i. "',
			keyNo = '".$rcode."',
			keyName = '법정동코드',
			log_datetime = '".TIME_YMDHIS."' ";
sql_query($sql);

ob_end_flush();
//ob_end_clean();

echo "<div>CNT : {$i}, SAVED : {$saved}</div>\n";
?>
