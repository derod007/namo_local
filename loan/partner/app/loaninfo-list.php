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
	<a class="btn btn-success btn-sm" href="./loaninfo-write.php">등록</a></div>
	<h1>대출신청 목록</h1>
</div>

<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-2">
				<label>진행상태</label>
				<select id="status" name="status" class="form-control">
					<?php
					echo option_selected('', $_GET['status'], '전체');
					foreach($status_arr as $k => $v) {
						echo option_selected($k, $_GET['status'], $v);
					}
					?>
				</select>
			</div>
			<div class="col-sm-8">
				<label>검색어</label>
				<input type="text" id="searchtxt" name="searchtxt" class="form-control" value="<?php echo $searchtxt;?>" placeholder="제목, 담보주소">
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
	commonjs.selectNav("navbar", "loaninfo");
		
		
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
                data: 'wr_ca',
                title: '구분',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'wr_subject',
                title: '제목',
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<a href='./loaninfo-write.php?w=u&wr_id=" + rowData.wr_id + "'>" + rowData.wr_subject + "</a>");
					$(cell).append(btn);
				}
            },
            {
                data: 'address',
                title: '담보주소',
				orderable: false
            },
            {
                data: 'filecnt',
                title: '첨부',
                className : 'align-center',
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<a href='./loaninfo-file.php?wr_id=" + rowData.wr_id + "'>" + rowData.filecnt + "</a>");
					$(cell).append(btn);
				}
            },
            {
                data: 'status',
                title: '진행상태',
                className : 'align-center',	
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					if(rowData.status == '부결') {
						var btn = $("<span class='red'>" + rowData.status + "</span>");
					} else if (rowData.status == '진행취소') {
						var btn = $("<span class='red'><B>" + rowData.status + "</B></span>");
					} else if (rowData.status == '가승인') {
						var btn = $("<span class='blue'>" + rowData.status + "</span>");
					} else if (rowData.status == '진행요청') {
						var btn = $("<span class='magenta'><B>" + rowData.status + "</B></span>");
					} else if (rowData.status == '대출실행') {
						var btn = $("<span class='green'><B>" + rowData.status + "</B></span>");
					} else {
						var btn = $("<span>" + rowData.status + "</span>");
					}
					$(cell).append(btn);
				}
			},
            {
                data: 'wdate',
                title: '등록일',
                className : 'align-center',	
				orderable: false
            },
            {
                data: 'jd_amount',
                title: '한도(만원)',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'jd_interest',
                title: '금리(%)',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'jd_condition',
                title: '부대조건',
                className : 'align-left',
				orderable: false
            }
			
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
            params.status = $('#status').val();

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: '/api/list_loaninfo.php',
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