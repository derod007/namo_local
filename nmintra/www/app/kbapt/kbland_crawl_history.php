<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

$code = $_GET['rcode'];
$region = $_GET['region'];
if($code && !$region) {
	$region = substr($code, 0, 5);
}
?>
<!-- CONTENT START -->

<div class="page-header">
	<div class="btn-div">
		<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
		<!-- a class="btn btn-success btn-sm" href="">등록</a -->
	</div>	
	<h1>KB부동산 크롤링 현황</h1>
</div>

<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
		
			<div class="col-sm-3">
				<label>키타입</label>
				<select id="keyName" name="keyName" class="form-control">
					<?php
					echo option_selected("", $keyName, "선택");
					echo option_selected("KB일련번호", $keyName);
					echo option_selected("KB시세", $keyName);
					echo option_selected("법정동코드", $keyName);
					?>
				</select>
			</div>
			<div class="col-sm-2">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
			<div class="col-sm-2">
			<!--
				<label>자료입력</label>
				<a href="./kbapt_sise_import.php" class="btn btn-default btn-block">XLS입력</a>
			-->
			</div>
		</div>
	</form>
</div>

<p align='right'></p>
<table id="datatotal" class="table table-striped table-bordered jsb-table1">
	<tr>
		<th class="col-sm-2">총 데이터수</th>
		<td id="total_count" class="col-sm-10">0</td>
	</tr>
</table>

<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"></table>

<iframe id="hiddenframe" style="display:none;"></iframe>

<script>
$(function () {
    //////////////////////////////////////////////////////////////////////////////////////
    commonjs.selectNav("navbar", "kbland_crawl");
	
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
                data: 'log_id',
                title: 'PK',
                className : 'align-center',
				orderable: true
            },
            //{ data: 'tax_id' },
            {
                data: 'api_url',
                title: 'API 요청',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'keyName',
                title: '키타입',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'keyNo',
                title: '키값',
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					if(rowData.keyName == '법정동코드') {
						var btn = $("<span>" + rowData.dong + "</span>");
					} else if(rowData.keyName == 'KB일련번호' || rowData.keyName == 'KB시세') {
						var btn = $("<span>" + rowData.danzi + "</span>");
					}
					$(cell).append(btn);
				}
            },
            {
                data: 'log_datetime',
                title: '호출시간',
				orderable: false
            },
            {
                data: 'result',
                title: '결과',
				orderable: false
            },
            {
                data: 'log_id',
                title: '상세보기',
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					if(rowData.keyName == '법정동코드') {
						var btn = $("<a href='./kbland_danzi.php?rcode=" + rowData.keyNo + "' target='_blank'>법정동 ("+ rowData.data_cnt + ")</a>");
					} else if(rowData.keyName == 'KB일련번호' || rowData.keyName == 'KB시세') {
						var btn = $("<a href='./kbland_danzi_detail.php?data_no=" + rowData.keyNo + "' target='_blank'>단지상세 ("+ rowData.data_cnt + ")</a>");
					}
					$(cell).append(btn);
				}
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
            params.keyName = $('#keyName').val();

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: '/api/list_kbland_crawl_history.php',
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
					$('#total_count').html(json.total);
					//$('#total_dist').html(json.total.dist_cnt);

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