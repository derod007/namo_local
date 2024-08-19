<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

$region_arr = array();
$sql = " select * from {$jsb['regioncode_table']} where step='2' order by code desc";
$result = sql_query($sql);
while($row=sql_fetch_array($result)){
	$region_arr[$row['region']] = $row['dong'];
}

?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>국토부 실거래가 크롤링 현황</h1>
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
					while($i < 38) {
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
				<label>지역(시군구)</label><!-- 250개 -->
				<?php echo get_regioncode_select("region", $region, ""); ?>
			</div>
			<div class="col-sm-1">
				<label>상태</label>
				<select id="status" name="status"  class="form-control">
					<option value="">선택</option>
					<option value="S01">미완료</option>
					<option value="S02">완료</option>
				</select>
			</div>
			<div class="col-sm-2">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
		</div>
	</form>
</div>

<table id="datatotal" class="table table-striped table-bordered jsb-table1">
	<tr>
		<th class="col-sm-2">총 크롤링 횟수</th>
		<td id="total_count" class="col-sm-2">0</td>
		<th class="col-sm-2">총 데이터수</th>
		<td id="total_datacnt" class="col-sm-2">0</td>
		<th class="col-sm-2">총 지역수</th>
		<td id="total_region" class="col-sm-2">250</td>
	</tr>
</table>

<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"></table>

<iframe id="hiddenframe" style="display:none;"></iframe>

<script>
$(function () {
    //////////////////////////////////////////////////////////////////////////////////////
    commonjs.selectNav("navbar", "realprice_history");

    //$(".datepicker").datepicker();
	$('#region').select2();

    var dataTable = $('#datalist').DataTable({
        paging: true,
        searching: false,
        scrollX : true,
        ordering: true,
        "order": [
            [0, 'desc']
        ],
        orderable: true,
        info: true,
        "bProcessing": true,
        "serverSide": true,
        columns: [{
                data: 'no',
                title: 'No',
                className : 'align-center',
				orderable: true
            },
            //{ data: 'tax_id' },
            {
                data: 'rdate',
                title: '날짜',
                className : 'align-center',
				orderable: true
            },
            {
                data: 'region',
                title: '지역',
                //className : 'align-right',
				orderable: false
            },
            {
                data: 'data_cnt',
                title: '수량',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'status',
                title: '상태',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'wdate',
                title: '날짜',
                className : 'align-center',
				orderable: false,
            },
        ],
        // AJAX
        ajax: function (data, callback, settings) {
            // data = dt가 만들어주는 원래 파라미터 원래 날아오는 부분
            //console.log(data);
            // 새로 params를 정의 재정의
            var params = {};

            params.start = data.start;
            params.length = data.length;
            params.yymm = $('#yymm').val();
            params.region = $('#region').val();
            params.status = $('#status').val();

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: '/api/list_crawl_history.php',
                type: "post",
                data: params,
                dataType: "json",
                success: function (json) {

                    var result = {
                        draw: data.draw,
                        data: json.data,
                        recordsFiltered: json.recordsFiltered,
                        recordsTotal: json.recordsTotal
                    };
					$('#total_count').html(json.total.cnt);
					$('#total_datacnt').html(json.total.datacnt);

                    callback(result);
                }
            });
        }
		
    });

    $('#search').on('click change', function (event) {
        event.preventDefault();
        dataTable.draw();
    });

});	
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>