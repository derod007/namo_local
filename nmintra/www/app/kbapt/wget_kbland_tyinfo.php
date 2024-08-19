<?php
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

if(!$_GET['kbno']) {
	echo "<H3>실패 : 단지일련번호가 누락되었습니다.</H3>";
	die();
}

$kbno = $_GET['kbno']; // 단지일련번호

$ch = curl_init();

$url = 'https://api.kbland.kr/land-complex/complex/typInfo'; // URL
$queryParams = '?' . urlencode('단지기본일련번호') .'=' . urlencode($kbno); 
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
        "단지기본일련번호": 13073,
        "물건식별자": "KBM014612",
        "네이버단지코드": 10065,
        "KMS단지코드": 14612,
        "면적일련번호": 12864,
        "공급면적": "109.33",
        "계약면적": "124.61",
        "전용면적": "82.40",
        "주택형타입내용": "",
        "평면도보기주소": "https://land.naver.com/info/groundPlanGallery.naver?rletNo=10065&ptpId=1&newComplex=Y",
        "방수": 3,
        "복층여부": "0",
        "세대수": 354,
      },

*/

$sql = "select regioncode from kbland_danzi where kbno = '{$kbno}'";
$row = sql_fetch($sql);
$regioncode = $row['regioncode'];

$sql = "select kbno,pyno from kbland_danzi_py where kbno = '{$kbno}'";
$result = sql_query($sql);
$indata = array();
while($row=sql_fetch_array($result)) {
	$indata[] = $row['pyno'];
}

echo "
<table border='1'>
	<tr>
		<td>번호</td><td>KB일련번호</td><td>물건식별자</td><td>면적일련번호</td><td>공급면적</td><td>주택형타입내용</td><td>평면도보기주소</td><td>세대수</td>
	</tr>
";

$i=0;
$saved = 0;
foreach($datas as $item) { 
	echo "<tr>".PHP_EOL;
	 echo "<td>".++$i. "</td>"; 
	 echo "<td>".$item['단지기본일련번호']. "</td>"; 
	 echo "<td>".$item['물건식별자']. "</td>"; 
	 echo "<td>".$item['면적일련번호']. "</td>"; 
	 echo "<td>".$item['공급면적']. "</td>"; 
	 echo "<td>".$item['주택형타입내용']. "</td>"; 
	 echo "<td>".$item['평면도보기주소']."</td>"; 
	 echo "<td>".$item['세대수']. "</td>".PHP_EOL;
	 echo "</tr>".PHP_EOL;
	 
	 if(!in_array($item['면적일련번호'], $indata)) {
		// 크롤링 시작 로그기록
		$sql = "insert into kbland_danzi_py set
					kbno = '".$item['단지기본일련번호']."',
					kbcode = '".$item['물건식별자']."',
					navercode = '".$item['네이버단지코드']."',
					kmscode = '".$item['KMS단지코드']."',
					pyno = '".$item['면적일련번호']."',
					area_sp = '".$item['공급면적']."',
					area_cr = '".$item['계약면적']."',
					area_de = '".$item['전용면적']."',
					house_type = '".$item['주택형타입내용']."',
					naverfloor_url = '".$item['평면도보기주소']."',
					room = '".$item['방수']."',
					is_duplex = '".$item['복층여부']."',
					households = '".$item['세대수']."',
					regioncode = '".$regioncode."',
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
			keyNo = '".$kbno."',
			keyName = 'KB일련번호',
			log_datetime = '".TIME_YMDHIS."' ";
sql_query($sql);

ob_end_flush();
//ob_end_clean();

echo "<div>CNT : {$i}, SAVED : {$saved}</div>\n";
?>
