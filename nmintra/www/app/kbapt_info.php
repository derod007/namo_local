<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../header.php';

/*
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
*/ 

?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>KB아파트정보조회</h1>
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
			<div class="col-sm-5">
				<label>아파트</label>
				<select id="aptcode" name="aptcode" class="form-control">
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

if($aptcode) {
	$sql = " select si,gun,dong,danzi,kbcode from {$jsb['kbapt_table']} where kbcode='{$aptcode}' limit 1";
	$apt = sql_fetch($sql);
	
	$sql = " select * from {$jsb['kbapt_sise_table']} where kbcode='{$aptcode}'";
	$result = sql_query($sql);
	
	echo "
	<p>&nbsp;</p>
	<table class='table table-striped table-bordered jsb-table1 nowrap'>
		<tr>
			<th>KB물건지코드</th>
			<td>{$apt['kbcode']}</td>
		</tr>
		<tr>
			<th>아파트명</th>
			<td>{$apt['danzi']}</td>
		</tr>
		<tr>
			<th>주소</th>
			<td>{$apt['si']} {$apt['gun']} {$apt['dong']}</td>
		</tr>
	</table>
	
	<p align='right'>단위:만원</p>
	<table id='datatables' class='table table-striped table-bordered jsb-table1 nowrap'>
		<thead>
		<tr>
			<th>번호</th><th>아파트명</th><th>공급/전용</th><th>하위평균가</th><th>중위평균가</th><th>상위평균가</th><th>업데이트일</th>
		</tr>
		</thead>
		<tbody>
	";

	$i=0;
	
	while($row=sql_fetch_array($result)){
		
		echo "<tr>".PHP_EOL;
		 echo "<td>".++$i. "</td>".PHP_EOL;
		 echo "<td>".$apt['danzi']."</td>".PHP_EOL;
		 echo "<td>".$row['py']."</td>".PHP_EOL;
		 echo "<td>".$row['price_low']."</td>".PHP_EOL;
		 echo "<td>".$row['price_mid']."</td>".PHP_EOL;
		 echo "<td>".$row['price_high']."</td>".PHP_EOL;
		 echo "<td>".$row['wdate']."</td>".PHP_EOL;
		 echo "</tr>".PHP_EOL;
		
	}
	
	echo "</tbody></table>";
	echo "<br/>";
}
?>

<script>
	$(function () {
		commonjs.selectNav("navbar", "kbapt_info");

		/* Select2 자동완성 코드 */
		$('#dongcode').select2({
			placeholder: '선택후 동명 검색',
			minimumInputLength: 2,
			formatInputTooShort: function (input, min) { var n = min - input.length; return n + "글자 이상 더 입력하시면 검색합니다."; },
			ajax: {
				url: '/app/api_select_apt_dong.php',
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
		
		$('#dongcode').on('select2:select', function (e) {
			
			console.log('아파트 목록');
			
			$.ajax({
				url: "/app/api_select_apt.php",
				method: "POST",
				dataType: "json",
				data: {
					dongcode: $('#dongcode').val()
				},
				error : function(error) {
					console.log("Error!");
				},
				success : function(data) {
					console.log("success!");
					console.log(data);
					$.each(data.results, function (i, item) {
						$('#aptcode').append($('<option>', { 
							value: item.id,
							text : item.text 
						}));
					});
				},
				complete : function() {
					console.log("complete!");    
				}
			});
			
		});
		
    });

    $('#search').on('click', function (event) {
		$('#fsearch').submit();
    });

	$(document).ready( function () {
	/*
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
						data = '<a href="./apt_view.php?aptcode=' + encodeURIComponent(data) + '">' + decodeURIComponent(data) + '</a>';
					}
					return data;
				}
			}]
		});
	*/
	} );
	
</script>

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>