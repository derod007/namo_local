<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../header.php';

?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<a class="btn btn-success btn-sm" href="./history-write.php">등록</a></div>
	<h1>심사접수 목록</h1>
</div>

<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-2">
				<label>차주/등록업체</label>
				<input type="text" id="la_name" name="la_name" class="form-control" value="<?php echo $la_name;?>" placeholder="차주/등록업체 ">
			</div>
			<div class="col-sm-8">
				<label>주소검색</label>
				<input type="text" id="searchtxt" name="searchtxt" class="form-control" value="<?php echo $searchtxt;?>" placeholder="담보주소">
			</div>
			<div class="col-sm-2">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
			
		</div>
	</form>
</div>

<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"></table>

<iframe id="hiddenframe" style="display:none;"></iframe>

<script>
$(function () {
	commonjs.selectNav("navbar", "history");
		
	$(".datepicker").datepicker();
	
    var dataTable = $('#datalist').DataTable({
        paging: true,
        searching: false,
        scrollX : true,
        ordering: false,
        "order": [
            [0, 'desc']
        ],
        orderable: false,
        info: true,
        "bProcessing": true,
        "serverSide": true,
        columns: [{
                data: 'no',
                title: 'No',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'wdate',
                title: '등록일',
                className : 'align-center',	
				orderable: false
            },
            {
                data: 'la_partner',
                title: '등록업체',
                className : 'align-center font-w600',					
				orderable: false
            },
            //{ data: 'tax_id' },
            {
                data: 'la_name',
                title: '차주명',
                className : 'align-center',					
				orderable: false
            },
            {
                data: 'la_addr',
                title: '담보주소',
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<a href='./history-write.php?w=u&la_id=" + rowData.la_id + "'>" + rowData.la_addr + "</a>");
					$(cell).append(btn);
				}
            },
            {
                data: 'la_guarantee',
                title: '담보가산정',
                className : 'align-right',
				orderable: false
            },
            {
                data: 'la_priority_amount',
                title: '선순위원금',
                className : 'align-right',
				orderable: false
            },
            {
                data: 'la_maximum_credit',
                title: '선순위최고액',
                className : 'align-right',
				orderable: false
            },
            {
                data: 'la_loan_amount',
                title: '한도제시액',
                className : 'align-right',
				orderable: false
            },
            {
                data: 'la_category',
                title: '담보구분',
                className : 'align-center',					
				orderable: false
            },
            {
                data: 'la_caloan',
                title: '대출구분',
                className : 'align-center',					
				orderable: false
            },
            {
                data: 'la_remark',
                title: '부대조건',
                className : 'align-left',
				orderable: false
            },
            {
                data: 'la_id',
                title: '기능',
                className : 'align-center',	
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<a href='./history-write.php?w=u&la_id=" + rowData.la_id + "'>수정</a>");
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
            params.searchtxt = $('#searchtxt').val();
            params.la_name = $('#la_name').val();

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: '/api/list_history.php',
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
	
});

    $('#search').on('click', function (event) {
		$('#fsearch').submit();
    });

    //////////////////////////////////////////////////////////////////////////////////////
	
	$('#exceldown').on('click', function (event) {
		var params = $("#fsearch").serialize();		
        var excelform = $('<form id="fexceldown"></form>');
        excelform.attr('action', '/api/excel_loaninfo.php?'+params);
        excelform.attr('method', 'post');
		excelform.attr('target', 'hiddenframe');
		excelform.appendTo('body');
        excelform.submit();
		$('#fexceldown').remove();
    });
	
</script>

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>