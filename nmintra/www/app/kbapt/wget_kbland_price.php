<?php
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

echo "<h3>데이터 수집이 완료되면 본 창을 닫고 단지 상세정보 창을 새로고침 해주세요</h3>\n";

if(!$_GET['kbno']) {
	echo "<H3>실패 : 단지기본일련번호가 누락되었습니다.</H3>";
	die();
}

$kbno = $_GET['kbno']; // 단지코드

$sql = " select max(kbprice_date) as lastdate from kbland_kbprice where kbno='{$kbno}' ";
$chk = sql_fetch($sql);
$lastdate = $chk['lastdate'];

$sql = "SELECT a.*, b.kbprice_date, b.kbprice_basic 
				FROM kbland_danzi_py a
					LEFT JOIN kbland_kbprice b on a.kbno=b.kbno and a.pyno = b.pyno
				WHERE a.pyno != '0' and a.kbno='{$kbno}' and b.kbprice_date = '{$lastdate}' "; //  and b.kbprice_basic is NULL
//echo $sql;
$result = sql_query($sql);
$tdata = array();
$i = 0; $k = 0; 
while($row=sql_fetch_array($result)){
	if($row['pyno']) {
		$tdata[$i]['kbno'] = $row['kbno'];
		$tdata[$i]['pyno'] = $row['pyno'];
		$tdata[$i]['kbprice_date'] = $row['kbprice_date'];
		$i++;
	}
}

if(!count($tdata)) {
	die('NO MORE TARGET!!!');
}

function view_null_zero($v) {
	$v = trim($v);
	if(!$v) {
		return 0;
	} else {
		return $v;
	}
}

foreach($tdata as $dd) {

	echo "<hr/><br/>TARGET : {$dd['kbno']} : {$dd['pyno']} ".PHP_EOL;

	// https://api.kbland.kr/land-price/price/BasePrcInfoNew?단지기본일련번호=2005&면적일련번호=51190

	$ch = curl_init();
	$url = 'https://api.kbland.kr/land-price/price/BasePrcInfoNew'; // URL
	$queryParams = '?' . urlencode('단지기본일련번호') .'=' . urlencode($dd['kbno']); 
	$queryParams .= '&' . urlencode('면적일련번호') . '=' . urlencode($dd['pyno']);
	//$queryParams .= '&' . urlencode('numOfRows') . '=' . urlencode('100'); /* 요청갯수 */
	$agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36";

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
	$pdata = $getdata['dataBody']['data']['시세'];	// 시세데이터

	ob_start();

	if($result_code == "10000") {
		echo "<H4>SUCCESS</H4>".PHP_EOL;
	} else {
		echo "<H4>ERROR - {$result_msg}</H4>".PHP_EOL;
	}

	/*
	{
	  "dataHeader": {
		"resultCode": "10000",
		"message": "NO_ERROR"
	  },
	  "dataBody": {
		"data": {
		  "매물월세평균가": null,
		  "매물월세보증금평균가": null,
		  "시세": [
			{
			  "단지기본일련번호": 13073,
			  "면적일련번호": 133178,
			  "물건식별자": "KBA011997",
			  "공급면적": 168.5960,
			  "계약면적": 193.6250,
			  "전용면적": 134.9600,
			  "연결구분명": "일반",
			  
			  "기준년월일": "20220509",
			  
			  "매매평균가": 83500.000,
			  "매매일반거래가": 83500,
			  "매매상한가": 86000,
			  "매매하한가": 81000,

			  "전세평균가": 62500.000,
			  "전세일반거래가": 62500,
			  "전세상한가": 65000,
			  "전세하한가": 60000,
			  
			  "월세보증금액": 5000,
			  "월세금액": 150,
			  "월임대최저금액": 145,
			  "월임대최고금액": 155,

			  "시세제공여부": "1",
			  
			  "연결구분코드": "000",
			  "시세기준년월일": "20220513",
			  "매매거래금액": 83500,
			  "매매해당층수": "10",
			  "매매계약시작년월일": "20220422",
			  "매매계약종료년월일": "20220422",
			  "매매변동금액": 0,
			  "시세공급면적": 168.59,
			  "전세거래금액": 44100,
			  "전세계약시작년월일": "20220330",
			  "전세계약종료년월일": "20220330",
			  "전세해당층수": "19",
			  "전세변동금액": 0,
			  "시세미제공사유": "",
			  "공급면적평수": 51,
			  "계약면적평수": 58,
			  "전용면적평수": 40,
			  "시세조사버튼구분": "시세조사요청 안내 사항",
			  "시세미제공사유코드": "",
			  "시세미제공사유상세": "",
			  "기타전용면적": "",
			  "동일주택타입구분": ""
			  "주택형순번": 4,
			  "주택형타입내용": "",
			}
		  ],
		  "월세건수": 0,
		  "매물전세평균가": 70000,
		  "매물매매평균가": 88156,
		  "매매건수": 16,
		  "전세건수": 1
		},
		"resultCode": 11000
	  }
	}
	*/

	if(count($pdata)) {
		
		$i=0;
		$saved = 0;
		
		//$item = $datas['시세'];
		foreach($pdata as $item) { 
			 if($dd['kbprice_date'] == $item['기준년월일']) {
				echo "kbno:".$item['단지기본일련번호'].PHP_EOL;
				echo "pyno:".$item['면적일련번호'].PHP_EOL;
				echo "시세기준일:". $item['기준년월일'].PHP_EOL;
				echo "<B>시세기준일 기데이터와 동일하여 업데이트 안함.</B>".PHP_EOL;
			 } else {
				// 크롤링 시작 로그기록
				$sql = "insert into kbland_kbprice set
							kbno = '".$item['단지기본일련번호']."',
							kbcode = '".$item['물건식별자']."',
							pyno = '".$item['면적일련번호']."',
							area_sp = '".$item['공급면적']."',
							area_cr = '".$item['계약면적']."',
							area_de = '".$item['전용면적']."',
							is_kbprice = '".$item['시세제공여부']."',
							kbprice_date = '".$item['기준년월일']. "',
							kbprice_basic = '".view_null_zero($item['매매일반거래가']). "',
							kbprice_high = '".view_null_zero($item['매매상한가']). "',
							kbprice_low = '".view_null_zero($item['매매하한가']). "',
							kbjunse_basic = '".view_null_zero($item['전세일반거래가']). "',
							kbjunse_high = '".view_null_zero($item['전세상한가']). "',
							kbjunse_low = '".view_null_zero($item['전세하한가']). "',
							kbwolse_deposit = '".view_null_zero($item['월세보증금액']). "',
							kbwolse_high = '".view_null_zero($item['월임대최고금액']). "',
							kbwolse_low = '".view_null_zero($item['월임대최저금액']). "',
							regdate = '".TIME_YMDHIS."' ";
				sql_query($sql);
				$saved++;
				echo "kbno:".$item['단지기본일련번호'].PHP_EOL;
				echo "pyno:".$item['면적일련번호'].PHP_EOL;
			 }
		}

	}
	// 크롤링 시작 로그기록
	$sql = "insert into kbland_api_log set
				api_url = '".$url."',
				query = '".urldecode($queryParams)."',
				result = '".$result_msg."',
				data_cnt = '1',
				keyNo = '".$dd['kbno'].":".$dd['pyno']."',
				keyName = 'KB시세',
				log_datetime = '".TIME_YMDHIS."' ";
	sql_query($sql);

	ob_end_flush();
	//ob_end_clean();

}

echo "<div>CNT : {$i}, SAVED : {$saved}</div>\n";

echo "<h3>데이터 수집이 완료되면 본 창을 닫고 단지 상세정보 창을 새로고침 해주세요</h3>\n";
?>
