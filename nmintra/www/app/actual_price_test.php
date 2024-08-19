<?php 
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

// 수집구간 : 201701 ~ 201912
// 기초데이터
//$region_code = '111110';
//$rdate = '201812';

$sql = "select rdate, count(*) as cnt from actualprice_history where status='S02' group by rdate order by rdate desc";
$result = sql_query($sql);
$last_date = $rdate = "";
while($row=sql_fetch_array($result)){
	$last_date = $row['rdate'];
	if($row['cnt'] >= 250) {
		continue;
	} else {
		$rdate = $row['rdate'];
		break;
	}
}
if(!$rdate) {
	$rdate = $last_date - 1;
}

if($rdate <= '201901') {
	echo "<h1>".$rdate." 수집완료</h1>";
	die();
}

$sql = "select a.*, b.rdate, b.cnt 
			from sigungu_code_ext as a 
				left join (select region_code, rdate, count(*) as cnt from actualprice_history where status = 'S02' and rdate='{$rdate}' group by region_code, rdate) as b on a.region = b.region_code and a.step='2'
			where a.step = '2' and b.cnt IS NULL limit 1
";
$region = sql_fetch($sql);
$region_code = $region['region'];
$region_name = $region['dong'];

if(!$region_code) {
	echo "<h1>".$rdate." 수집완료</h1>";
	die();
}

die();
/*
$sql = "select * from sigungu_code_ext where code = '".$region_code.'00000'."' limit 1 ";
$region = sql_fetch($sql);
$region_name = $region['dong'];
*/

// 크롤링 시작 로그기록
$sql = "insert into actualprice_history set
			rdate = '{$rdate}',
			region_code = '{$region_code}',
			data_cnt = '0',
			status = 'S01',
			wdate = '".TIME_YMDHIS."' ";
//echo $sql;
//echo "<hr/>";
sql_query($sql);
$log_id = sql_insert_id();

$ch = curl_init();
$url = 'http://openapi.molit.go.kr:8081/OpenAPI_ToolInstallPackage/service/rest/RTMSOBJSvc/getRTMSDataSvcAptTrade'; /*URL*/
$queryParams = '?' . urlencode('ServiceKey') . '=PkmvtK%2BS63cjV8jQpYHUDoqVM2akCl%2FX4Z0iI7710fIB84CJy2HeRwxOIx%2FYtySzD5KspW3B10M7bUex9vjaKw%3D%3D'; /*Service Key*/
//$queryParams .= '&' . urlencode('pageNo') . '=' . urlencode('1'); /* 페이징 */
//$queryParams .= '&' . urlencode('numOfRows') . '=' . urlencode('100'); /* 요청갯수 */
$queryParams .= '&' . urlencode('LAWD_CD') . '=' . urlencode($region_code); /* 각 지역별 코드 - 송파구 */	
$queryParams .= '&' . urlencode('DEAL_YMD') . '=' . urlencode($rdate); /* 월 단위 신고자료 */

curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
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

$xml=simplexml_load_string($response) or die("Error: Cannot create object");

$result_code = $xml->header[0]->resultCode;
$result_msg = $xml->header[0]->resultMsg;
$total_cnt = $xml->body[0]->totalCount;
$page = $xml->body[0]->pageNo;
$page_rows = $xml->body[0]->numOfRows;

if($result_code == "00") {
	echo "<H3>성공 - {$region_name}</H3>";
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
	 
	// 크롤링 시작 로그기록
	$sql = "insert into actual_price set
				yyyy = '".$item->년."',
				mm = '".$item->월."',
				dd = '".$item->일."',
				region_code = '{$region_code}',
				load_no = '{$i}',
				sigungu = '{$region_name}',
				dong = '".$item->법정동."',
				zibun = '".$item->지번."',
				danzi = '".addslashes($item->아파트)."',
				py = '".$item->전용면적."',
				price = '".str_replace(",","",$item->거래금액) ."',
				open_yy = '".$item->건축년도."',
				floor = '".$item->층."',
				wdate = '".TIME_YMDHIS."' ";
	sql_query($sql);
} 
echo "</table>";


// 크롤링 끝 로그기록
$sql = "update actualprice_history set data_cnt = '{$i}', status = 'S02', edate = NOW() where no ='{$log_id}'";
sql_query($sql);

$sql = "select a.*, b.rdate, b.cnt 
from sigungu_code_ext as a 
	left join (select region_code, rdate, count(*) as cnt from actualprice_history where status = 'S02' and rdate='{$rdate}' group by region_code, rdate) as b on a.region = b.region_code and a.step='2'
where a.step = '2' and b.cnt IS NULL";
$result = sql_query($sql);
$remain_rows = sql_num_rows($result);

echo "<div>{$i}건 읽어오기 종료. {$rdate}:{$remain_rows}지역 남음.</div>";
?>