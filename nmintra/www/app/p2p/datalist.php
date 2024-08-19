<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';
?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>P2P업체정보 목록(미드레이트 자료)</h1>
</div>

<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-2">
				<label>기준일자</label>
				<input type="text" name="base_date" id="base_date" value="<?php echo $base_date;?>"  class="form-control">
			</div>
			<div class="col-sm-2">
				<label>업체명</label>
				<input type="text" name="com_name" id="com_name" value="<?php echo $com_name;?>"  class="form-control">
			</div>
			<div class="col-sm-2">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
			<div class="col-sm-5">
			</div>
			<div class="col-sm-1">
				<label>자료입력</label>
				<a href="./data_xlsimport.php" class="btn btn-default btn-block">XLS입력</a>
			</div>
		</div>
	</form>
</div>

<p align='right'>단위:원</p>
<table id="datatotal" class="table table-striped table-bordered jsb-table1">
	<tr>
		<th class="col-sm-2">총 데이터수</th>
		<td id="total_count" class="col-sm-2">0</td>
		<th class="col-sm-2">총 업체수</th>
		<td id="total_dist" class="col-sm-2">0</td>
	</tr>
</table>

<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"></table>

<iframe id="hiddenframe" style="display:none;"></iframe>

<script>
$(function () {
    //////////////////////////////////////////////////////////////////////////////////////
    commonjs.selectNav("navbar", "p2pdata");
	
    //$(".datepicker").datepicker();
	$('#region').select2();

    var dataTable = $('#datalist').DataTable({
        paging: true,
        searching: false,
        scrollX : true,
        ordering: true,
        "order": [
            [3, 'desc']
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
                data: 'base_date',
                title: '기준일자',
				orderable: true
            },
            {
                data: 'com_name',
                title: '업체명',
				orderable: true
            },
            {
                data: 'loan_integral',
                title: '누적대출액',
                className : 'align-right',
				orderable: true
            },
            {
                data: 'loan_remain',
                title: '대출잔액',
                className : 'align-right',
				orderable: true
            },
            {
                data: 'loan_return',
                title: '상환원금',
                className : 'align-right',
				orderable: true
            },
				/*
            {
                data: 'danzi',
                title: '단지명',
				orderable: true,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<a href='./realprice_aptlink.php?data_no=" + rowData.data_no + "' target='_blank'>" + rowData.danzi + "</a>");
					$(cell).append(btn);
				}
            },
				*/
		
            {
                data: 'rate_return',
                title: '상환률',
                className : 'align-center',
				orderable: true
            },
            {
                data: 'rate_late',
                title: '연체율',
                className : 'align-center',
				orderable: true
            },
            {
                data: 'rate_poor',
                title: '부실률',
                className : 'align-center',
				orderable: true
            },
            {
                data: 'rate_earn',
                title: '수익률',
                className : 'align-center',
				orderable: true
            },
            {
                data: 'url',
                title: '홈페이지 URL',
				orderable: true
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
            params.base_date = $('#base_date').val();
            params.com_name = $('#com_name').val();

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: '/api/list_p2p_status.php',
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
					$('#total_dist').html(json.total.dist_cnt);

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