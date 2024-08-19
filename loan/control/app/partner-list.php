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
	<a class="btn btn-success btn-sm" href="./partner-write.php">등록</a></div>
	<h1>파트너 목록</h1>
</div>

<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-10">
				<label>검색</label>
				<input type="text" id="searchtxt" name="searchtxt" class="form-control" value="<?php echo $searchtxt;?>" placeholder="업체, 담당자명">
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
	commonjs.selectNav("navbar", "partner");
		
		
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
            //{ data: 'tax_id' },
            {
                data: 'mb_id',
                title: '아이디',
                className : 'align-center',
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<a href='./partner-write.php?w=u&idx=" + rowData.idx + "'>" + rowData.mb_id + " (" + rowData.sub_cnt + ")" + "</a>");
					$(cell).append(btn);
				}
				
            },
            {
                data: 'mb_bizname',
                title: '업체명',
				orderable: false
            },
            {
                data: 'mb_name',
                title: '담당자명',
				orderable: false
            },
            {
                data: 'mb_joindate',
                title: '등록일',
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
                data: 'display',
                title: '표시',
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

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: '/api/list_partner.php',
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

</script>

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>