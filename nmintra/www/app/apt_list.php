<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../header.php';

$sido_arr = array(
	"11" => "서울특별시",
	"26" => "부산광역시",
	"27" => "대구광역시",
	"28" => "인천광역시",
	"29" => "광주광역시",
	"30" => "대전광역시",
	"31" => "울산광역시",
	"41" => "경기도",
	"42" => "강원도",
	"43" => "충청북도",
	"44" => "충청남도",
	"45" => "전라북도",
	"46" => "전라남도",
	"47" => "경상북도",
	"48" => "경상남도",
	"50" => "제주도",
);

?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>전국 단지(아파트)정보 조회</h1>
</div>

<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-12">
			<!--div class="col-sm-2">
				<label>시도</label>
				<select id="sido" name="sido" class="form-control">
				<option value="">선택</option>
				<?php					
					foreach ($sido_arr as $k =>$v) {
						echo option_selected($k, $sido, $v);
					}
				?>
				</select>
			</div -->
			<div class="col-sm-5">
				<label>지역(법정동)</label>
				<select id="dongcode" name="dongcode" class="form-control">
				</select>
			</div>
			<div class="col-sm-2">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
		</div>
	</form>
</div>

<?php

if($dongcode) {

	$ch = curl_init();
	$url = 'http://apis.data.go.kr/1611000/AptListService/getLegaldongAptList'; /*URL*/
	$queryParams = '?' . urlencode('ServiceKey') . '=PkmvtK%2BS63cjV8jQpYHUDoqVM2akCl%2FX4Z0iI7710fIB84CJy2HeRwxOIx%2FYtySzD5KspW3B10M7bUex9vjaKw%3D%3D'; /*Service Key*/
	//$queryParams .= '&' . urlencode('pageNo') . '=' . urlencode('1'); /* 페이징 */
	//$queryParams .= '&' . urlencode('numOfRows') . '=' . urlencode('100'); /* 요청갯수 */
	$queryParams .= '&' . urlencode('loadCode') . '=' . urlencode($dongcode); /* 법정 동코드 - 수지구 신봉동 '4146510500' */	

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
	$total_cnt = $xml->body[0]->totalCount;
	$page = $xml->body[0]->pageNo;
	$page_rows = $xml->body[0]->numOfRows;

	if($result_code == "00") {
		$sql = " select dong from {$jsb['regioncode_table']} where code='{$dongcode}' limit 1 ";
		$dd = sql_fetch($sql);

		echo "<H4>조회성공</H4>";
		echo "<H4>법정동 : {$dd['dong']} ({$dongcode})</H4>";
		echo "<div>전체 ".$total_cnt ."건</div>";
		//echo "<div>전체 ".$total_cnt ."건 중 ".$page_rows ."건, ".$page." page </div>";
	}

	$obj_addr = $xml->body[0]->items[0]; // ->item[0]


	echo "
	<table id='datatables' class='table table-striped table-bordered jsb-table1 nowrap'>
		<thead>
		<tr>
			<th>번호</th><th>아파트코드</th><th>아파트명</th>
		</tr>
		</thead>
		<tbody>
	";

	$i=0;
	foreach($obj_addr->item as $item) { 
		echo "<tr>".PHP_EOL;
		 echo "<td>".++$i. "</td>"; 
		 echo "<td>".$item->kaptCode . "</td>"; 
		 echo "<td>".$item->kaptName . "</td>".PHP_EOL;
		 echo "</tr>".PHP_EOL;
	} 
	echo "</tbody></table>";
	echo "<br/>";
}
?>

<script>
	$(function () {
		commonjs.selectNav("navbar", "apt_list");
		
		/* Select2 자동완성 코드 */
		$('#dongcode').select2({
			placeholder: '선택후 동명 검색',
			minimumInputLength: 2,
			formatInputTooShort: function (input, min) { var n = min - input.length; return n + "글자 이상 더 입력하시면 검색합니다."; },
			ajax: {
				url: '/app/api_select_dong.php',
				dataType: 'json',
				quietMillis: 500,
				data: function (term, page) {
					return {
						flag: 'auto',
						q: term // search term
					}
				},
				results: function (data, page) {
					return { results: data };
				},
				cache: true
			},
			/* 리턴데이터는 id / text 로 반환되어야 함 */
			/*
			initSelection: function($el, callback){
				callback({
					id: $el.val(),
					text: $el.val()
				});
			},
			*/
			formatSearching: function(){ return '검색중...'; }
		});
		
		
    });

    $('#search').on('click', function (event) {
		$('#fsearch').submit();
    });

	$(document).ready( function () {
		$('#datatables').DataTable({
			"paging": true,
			"pageLength": 20,
			"dom": 'frtip',
			"columnDefs": [
			{
				targets: 1,
				render: function (data, type, row, meta)
				{
					if (type === 'display')
					{
						data = '<a href="./apt_view.php?aptcode=' + encodeURIComponent(data) + '">' + encodeURIComponent(data) + '</a>';
					}
					return data;
				}
			}]
		});
	} );
	
</script>

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>