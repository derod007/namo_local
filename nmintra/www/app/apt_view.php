<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../header.php';

if(!$aptcode) {
	alert("잘못된 접근입니다.", "./apt_list.php");
}

?>
<!-- CONTENT START -->

<div class="page-header">
	<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-eye"></i> Show All</a> 
	</div>
	<h1>아파트 상세정보</h1>
</div>

<?php

if($aptcode) {

	$ch = curl_init();
	$url = 'http://apis.data.go.kr/1611000/AptBasisInfoService/getAphusBassInfo'; /*URL*/
	$queryParams = '?' . urlencode('ServiceKey') . '=PkmvtK%2BS63cjV8jQpYHUDoqVM2akCl%2FX4Z0iI7710fIB84CJy2HeRwxOIx%2FYtySzD5KspW3B10M7bUex9vjaKw%3D%3D'; /*Service Key*/
	//$queryParams .= '&' . urlencode('pageNo') . '=' . urlencode('1'); /* 페이징 */
	//$queryParams .= '&' . urlencode('numOfRows') . '=' . urlencode('100'); /* 요청갯수 */
	$queryParams .= '&' . urlencode('kaptCode') . '=' . urlencode($aptcode); /* 법정 동코드 - 수지구 신봉동 '4146510500' */	

	curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	$response = curl_exec($ch);
	curl_close($ch);

	//var_dump($response);
	/*  
	header('Content-type: text/xml');
	 echo "<pre>";
	 echo $response;
	 echo "</pre>";
	die();
	 */

	$xml=simplexml_load_string($response) or die("Error: Cannot create object");

	$result_code = $xml->header[0]->resultCode;
	$result_msg = $xml->header[0]->resultMsg;

	if($result_code == "00") {
		echo "<H3>조회성공</H3>";
		//echo "<div>전체 ".$total_cnt ."건</div>";
	} else {
		echo "<H3>조회실패</H3>";
		echo "<div>MSG : [".$result_code."] ".$result_msg ."</div>";
		echo "</body></html>";
		die();
	}

	$item = $xml->body[0]->item[0]; // ->item[0]
	
}
?>

<table class="table table-bordered bs-xs-table jsb-info">
	<tr class="max-768-target">
		<th>CODE</th>
		<td>
			<?php echo $item->kaptCode ?>
		</td>
		<th>아파트명</th>
		<td>
			<?php echo $item->kaptName ?>
		</td>
		<th>세대수</th>
		<td>
			<?php echo $item->kaptdaCnt ?> 세대
		</td>
	</tr>
	<tr>
		<th>주소</th>
		<td colspan="3">
			<?php echo $item->kaptAddr ?><br/>
			<?php echo $item->doroJuso ?>			
		</td>
		<th>동수</th>
		<td>
			<?php echo $item->kaptDongCnt ?> 개동
		</td>
	</tr>
	<tr>
		<th>단지분류</th>
		<td>
			<?php echo $item->codeAptNm ?>
		</td>
		<th>시공사</th>
		<td>
			<?php echo $item->kaptBcompany ?>
		</td>
		<th>준공일</th>
		<td>
			<?php echo $item->kaptUsedate ?>
		</td>
	</tr>
	<tr>
		<th>난방방식</th>
		<td>
			<?php echo $item->codeHeatNm ?>
		</td>
		<th>복도유형</th>
		<td>
			<?php echo $item->codeHallNm ?>
		</td>
		<th>전용면적합</th>
		<td>
			<?php echo $item->privArea ?> ㎡
		</td>
	</tr>
</table>

<div class="bs-padding20 align-center">
	<a class="btn btn-default" onclick="history.back();">검색으로 돌아가기</a>
</div>

<script>
	$(function () {
		commonjs.selectNav("navbar", "apt_list");
		
    });
	
</script>

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>