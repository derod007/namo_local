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
	<a class="btn btn-success btn-sm" href="./notice-write.php">등록</a></div>
	<h1>공지사항 목록</h1>
</div>

<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
	<input type="hidden" name="bo_table" value="notice">
		<div class="row">
			<div class="col-sm-6">
				<label>검색어</label>
				<input type="text" id="searchtxt" name="searchtxt" class="form-control" value="<?php echo $searchtxt;?>" placeholder="제목">
			</div>
			<div class="col-sm-1">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
			<div class="col-sm-1">
			</div>
			
		</div>
	</form>
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
                data: 'open',
                title: '공개여부',
				className : 'align-center',
				orderable: false
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
                url: '/api/v2/bbs_list.php',
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
