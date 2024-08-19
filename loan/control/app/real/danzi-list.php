<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

//$partners = get_partnerlist();

/*
$w = $_GET['w'];

if($w == '') {
	$btntxt = "등록";
	$btnclass = "btn-primary";
	
	$row["wr_link1_subj"] = "KB시세조회";
}
*/
?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>실거래가 단지명검색</h1>
</div>

<!-- CONTENT START -->

<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-4">
				<label>지역(시군구)</label><!-- 250개 -->
				<?php echo get_regioncode_select("region", $region, ""); ?>
			</div>
			<div class="col-sm-3">
				<label>단지명</label>
				<input type="text" name="danzi" id="danzi" value="<?php echo $danzi;?>"  class="form-control">
			</div>
			
			<div class="col-sm-2">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
		</div>
	</form>
</div>

<p align='right'></p>
<table id="datatotal" class="table table-striped table-bordered jsb-table1">
	<tr>
		<th class="col-sm-2">총 데이터수</th>
		<td id="total_count" class="col-sm-2">0</td>
	</tr>
</table>

<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"></table>

<iframe id="hiddenframe" style="display:none;"></iframe>

<script>
$(function () {
    //////////////////////////////////////////////////////////////////////////////////////
    commonjs.selectNav("navbar", "working");

    //$(".datepicker").datepicker();
	$('#region').select2();

    var dataTable = $('#datalist').DataTable({
        paging: true,
        searching: false,
        scrollX : true,
        ordering: false,
        "order": [
            [1, 'asc']
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
                data: 'region_code',
                title: '법정동코드',
                className : 'align-center',
				orderable: true
            },
            {
                data: 'sigungu',
                title: '시군구',
				orderable: true
            },
            {
                data: 'dong',
                title: '동',
				orderable: true
            },
            {
                data: 'zibun',
                title: '지번',
				orderable: false
            },
            {
                data: 'danzi',
                title: '단지명',
				orderable: true,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<a href='./danzi-view.php?addr1=" + rowData.sigungu + ' '+ rowData.dong + ' ' + rowData.zibun + "'>" + rowData.danzi + "</a>");
					$(cell).append(btn);
				}
            },
            {
                data: 'open_yy',
                title: '입주년',
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
            params.region = $('#region').val();
            params.danzi = $('#danzi').val();

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: '/api/list_danzi.php',
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