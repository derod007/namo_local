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
	</div>
	<h1>공지사항 목록</h1>
</div>

<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"></table>

<iframe id="hiddenframe" style="display:none;"></iframe>

<script>
$(function () {
	commonjs.selectNav("navbar", "notice");
		
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
                data: 'wr_ca',
                title: '분류',
                className : 'align-center',
				width: '15%',
				orderable: false
            },
            {
                data: 'wr_subject',
                title: '제목',
                className : 'align-left font-w600',					
				width: '50%',
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<a href='./notice-view.php?wr_id=" + rowData.wr_id + "'>" + rowData.wr_subject + "</a>");
					$(cell).append(btn);
				}
            },
            {
                data: 'wdate',
                title: '등록일',
                className : 'align-center',	
				orderable: false
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
            params.bo_table = 'notice';
            //params.pt_idx = $('#pt_idx').val();
            //params.regdate = $('#regdate').val();

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: '/api/v2/list_notice.php',
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
	
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>
