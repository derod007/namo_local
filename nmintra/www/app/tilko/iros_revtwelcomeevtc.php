<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';
?>
<style>
.fs_small { font-size:0.8em; }
</style>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!--a class="btn btn-success btn-sm" href="">등록</a--></div>
	<h1>IROS 등기신청사건 조회목록</h1>
</div>

<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-5">
				<label>검색</label>
				<input type="text" name="SearchTxt" id="SearchTxt" value="<?php echo $SearchTxt;?>" placeholder="고유번호, 주소 일부분, 소유자명" class="form-control">
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
		<th class="col-sm-2">총 데이터수</th>
		<td id="total_count" class="col-sm-2">0</td>
		<th class="col-sm-2">&nbsp;</th>
		<td id="total_datacnt" class="col-sm-2">&nbsp;</td>
	</tr>
</table>

<div>
	※ 접수번호 > OK : 등기사건 조회내용 없음.<br/>
	※ 접수번호 > <font color='red'>Error</font> : 사건조회요청 오류<br/><br/>
</div>

<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"></table>

<iframe id="hiddenframe" style="display:none;"></iframe>

<script>
$(function () {
    //////////////////////////////////////////////////////////////////////////////////////
    commonjs.selectNav("navbar", "iros_revtwelcomeevtc");

    //$(".datepicker").datepicker();
	$('#region').select2();

    var dataTable = $('#datalist').DataTable({
        paging: true,
        searching: false,
        scrollX : true,
        ordering: true,
        "order": [],
        orderable: true,
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
                data: 'wdatetime',
                title: '조회일시',
                className : 'align-center',
				orderable: true,
            },
            {
                data: 'UniqueNo',
                title: '고유번호',
                //className : 'align-center',
				orderable: true
            },
            {
                data: 'Gubun',
                title: '구분',
                //className : 'align-right',
				orderable: false
            },
            {
                data: 'BudongsanSojaejibeon',
                title: '소재지번',
                className : 'fs_small',
				orderable: true,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<a href='./iros_risuretrieve_form.php?data_no=" + rowData.UniqueNo + "' target='_self'>" + rowData.BudongsanSojaejibeon + "</a>");
					$(cell).append(btn);
				}
            },
            {
                data: 'Owner',
                title: '소유자',
                className : 'align-center',
				orderable: true,
            },
            {
                data: 'RDJeobsuIlja',
                title: '접수일자',
                className : 'align-center',
				orderable: true,
            },
            {
                data: 'RDJeobsuBeonho',
                title: '접수번호',
                className : 'align-center',
				orderable: false,
            },
            {
                data: 'RDGwanhalDeunggiso',
                title: '관할',
                className : 'fs_small',
				orderable: false
            },
            {
                data: 'RDDeunggiMogjeog',
                title: '등기목적',
                //className : 'align-right',
				orderable: false
            },
            {
                data: 'RDCheoliSangtae',
                title: '처리상태',
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
            params.SearchTxt = $('#SearchTxt').val();

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: './api_revtwelcomeevtc_list.php',
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