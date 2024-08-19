<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://nmintra.event-on.kr/app/p2p/publicofficial.php
include_once '../../header.php';

$sql = " select count(*) cnt, reg_date from namo_member where 1=1";
$row = sql_fetch($sql);
$namo_count = $row['cnt'];
$namo_update = $row['reg_date'];

?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>공직자윤리위원회</h1>
</div>

<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-2">
				<label>등록일</label>
				<input type="text" name="reg_date" id="reg_date" value="<?php echo $reg_date;?>"  class="form-control datepicker">
			</div>
			<div class="col-sm-3">
				<label>파일명</label>
				<input type="text" name="filename" id="filename" value="<?php echo $filename;?>"  class="form-control">
			</div>
			<div class="col-sm-1">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
			<div class="col-sm-6">
			</div>
		</div>
	</form>
</div>


<!--p align='left'>나모회원</p -->
<table id="datatotal" class="table table-striped table-bordered jsb-table1">
	<tr>
		<th class="col-sm-1">등록회원</th>
		<td id="namo_count" class="col-sm-1"><?php echo number_format($namo_count); ?></td>
		<th class="col-sm-2">업데이트일시</th>
		<td id="namo_update" class="col-sm-2"><?php echo substr($namo_update, 0, 16); ?></td>
		<td class="col-sm-2"><a href="./publicofficial_reset.php" class="btn btn-primary btn-sm">초기화</a></td>
		<td class="col-sm-2">&nbsp;</td>
		<td class="col-sm-2"><a href="./namodata_xlsimport.php" class="btn btn-success btn-sm btn-block">회원정보갱신(XLS)</a></td>
	</tr>
</table>

<!--p align='left'>조회요청 데이터</p -->
<table id="datatotal" class="table table-striped table-bordered jsb-table1">
	<tr>
		<th class="col-sm-1">총 파일</th>
		<td id="total_count" class="col-sm-1">0</td>
		<th class="col-sm-2">총 데이터</th>
		<td id="total_dist" class="col-sm-2">0</td>
		<td class="col-sm-4">&nbsp;</td>
		<td class="col-sm-2"><a href="./data_txtimport.php" class="btn btn-success btn-sm btn-block">자료입력(TXT)</a></td>
	</tr>
</table>

<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"></table>

<iframe id="hiddenframe" style="display:none;"></iframe>

<script>
$(function () {
    //////////////////////////////////////////////////////////////////////////////////////
    commonjs.selectNav("navbar", "publicofficial");
	
    $(".datepicker").datepicker();
	//$('#region').select2();

    var dataTable = $('#datalist').DataTable({
        paging: true,
        searching: false,
        scrollX : true,
        ordering: true,
        "order": [
            [0, 'desc']
        ],
        orderable: false,
        info: true,
        "bProcessing": true,
        "serverSide": true,
        columns: [{
                data: 'grcode',
                title: 'No',
                className : 'align-center',
				orderable: true
            },
            //{ data: 'tax_id' },
            {
                data: 'filename',
                title: '업로드파일명',
				orderable: true,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<a href='./publicofficial_view.php?grcode=" + rowData.grcode + "'>" + rowData.filename + "</a>");
					$(cell).append(btn);
				}
            },
            {
                data: 'cnt',
                title: '데이터수',
				orderable: false
            },
            {
                data: 'reg_date',
                title: '등록일시',
				orderable: false
            },
            {
                data: 'grcode',
                title: '기능',
				orderable: true,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<a class='btn btn-danger btn-xs data_del' data-grcode='" + rowData.grcode + "'>삭제</a>");
					
                    var btn = $('<button type="button" class="btn btn-danger btn-xs">삭제</button>');
                    btn.click(function () {
                        if (confirm("삭제하시겠습니까?") !== true) {
                            return;
                        }
						
						var grcode = $(this).attr("data-grcode");
						var delform = $('<form></form>');
						delform.attr('action', './publicofficial_del.php');
						delform.attr('method', 'post');
						delform.appendTo('body');
						delform.append('<input type="hidden" name="w" value="d" />');
						delform.append('<input type="hidden" name="grcode" value="' + rowData.grcode + '" />');
						delform.submit();
						
                    });
				
					$(cell).append(btn);
				}
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
        ],
        // AJAX
        ajax: function (data, callback, settings) {
            // data = dt가 만들어주는 원래 파라미터 원래 날아오는 부분
            //console.log(data);
            // 새로 params를 정의 재정의
            var params = {};

            params.start = data.start;
            params.length = data.length;
            params.reg_date = $('#reg_date').val();
            params.filename = $('#filename').val();

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: '/api/list_p2p_public.php',
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
