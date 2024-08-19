<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/actual_price.php
include_once '../header.php';

if($yymm) {
	if(strlen($yymm) != 6 || is_int($yymm)) {
		alert('yyyymm 형식으로 입력해주세요.', '/actual_price.php');
	}
	
	if(!$region) {
		alert('지역을 선택해주세요.', '/actual_price.php');
	}
}


?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>국토부 실거래가 조회</h1>
</div>

<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-2">
				<label>조회년월(<?php echo date("Ym");?>)</label>
				<select id="yymm" name="yymm"  class="form-control">
					<option value="">선택</option>
				<?php
					$ym = date('Ym');
					$i = 0;
					while($i < 10) {
						if($i != 0) {
							$ym = date("Ym", strtotime("-$i month"));
						}
						echo option_selected($ym, $yymm, $ym);
						$i++;
					}
				?>	
				</select>
			</div>
			<div class="col-sm-5">
				<label>지역(시군구)</label><!-- 277개 -->
				<?php echo get_regioncode_select("region", $region, ""); ?>
			</div>
			<div class="col-sm-2">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
		</div>
	</form>
</div>

<?php

if($yymm && $region) {

	$ch = curl_init();
	$url = 'http://openapi.molit.go.kr:8081/OpenAPI_ToolInstallPackage/service/rest/RTMSOBJSvc/getRTMSDataSvcAptTrade'; /*URL*/
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
		echo "<H3>조회성공</H3>";
		echo "<div>전체 ".$total_cnt ."건</div>";
		//echo "<div>전체 ".$total_cnt ."건 중 ".$page_rows ."건, ".$page." page </div>";
	}

	$obj_addr = $xml->body[0]->items[0]; // ->item[0]


	echo "
	<table id='datatables' class='table table-striped table-bordered jsb-table1 nowrap'>
		<thead>
		<tr>
			<th>번호</th><th>지역코드</th><th>법정동</th><th>아파트</th><th>건축년도</th><th>지번</th><th>전용면적(㎡)</th><th>년</th><th>월</th><th>거래금액(만원)</th><th>층</th>
		</tr>
		</thead>
		<tbody>
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
	echo "</tbody></table>";

}
?>

<script>
	$(function () {
		commonjs.selectNav("navbar", "actual_price");
		
		if(false) {
		$.ajax({
			type: 'get',
			url: '/api/select_project.php',
			dataType: 'json',
			success:function (data) {
				console.log(data);
				var items = data.results;
				if(items !== null) {
					$.each(items, function(k, v) {
						$('#sch_project_id').append('<option value="' + v.id + '">' + v.text + '</option>');
					});
					$('#sch_project_id').select2({width: "100%"});
				}
			}
		}); 
		}
		$('#region').select2();
		
		
    });

    $('#search').on('click', function (event) {
		$('#fsearch').submit();
    });

	$(document).ready( function () {
		$('#datatables').DataTable({
			"paging": true,
			"pageLength": 20,
			"dom": 'Bfrtip',
			"buttons": [
				'copy',
				{
					extend: 'csv',
					messageTop: 'V 국토부 실거래가 조회',
					title: 'actual_price_'+$("#yymm").val()+'-'+$("#region").val()
				},
				{
					extend: 'excel',
					messageTop: 'V 국토부 실거래가 조회',
					title: 'actual_price_'+$("#yymm").val()+'-'+$("#region").val()
				},
				{
					extend: 'pdf',
					messageTop: 'V 국토부 실거래가 조회',
					title: 'actual_price_'+$("#yymm").val()+'-'+$("#region").val()
				},
				{
					extend: 'print',
					messageTop: 'V 국토부 실거래가 조회',
					title: 'actual_price_'+$("#yymm").val()+'-'+$("#region").val()
				}
			]
			
		});
	} );
	
</script>

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>