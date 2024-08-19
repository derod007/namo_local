<?php
include_once '../../header.php';

// 7일전 부터 현재까지 날짜표시
$edate = $_GET['edate'];
$sdate = $_GET['sdate'];

if(!$edate) {
	$edate = date("Y-m-d", strtotime("now"));
}
if(!$sdate) {
	$sdate = date("Y-m-d", strtotime($edate."-7days")); 
}

?>
<style>
.h2_frm {font-size:24px;}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>

<!-- CONTENT START -->

<div class="page-header">
  <h1>접수통계 <small></small></h1>
</div>

<div class="search-box">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-2">
				<label>시작일자</label>
				<input type="text" id="sdate" name="sdate" class="form-control datepicker" value="<?php echo $sdate;?>" placeholder="일자검색">
			</div>
			<div class="col-sm-2">
				<label>종료일자</label>
				<input type="text" id="edate" name="edate" class="form-control datepicker" value="<?php echo $edate;?>" placeholder="일자검색">
			</div>
			<div class="col-sm-1">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
		</div>
	</form>
</div>

<div><h4>조회기간 : <?php echo $sdate." ~ ".$edate;?></h4></div>

<div style="display:flex; width:100%;">
	<section class="tbl_wrap" style="width:33%;">
		<h2 class="h2_frm">접수상태별 비율(%)</h2>
	
		<div class="tbl_wrap" style="max-width:400px;">
			<canvas id="myChart" width="400" height="400"></canvas>
		 </div>

		<div class="tbl_wrap" style="max-width:400px; margin:auto 10px;">
			<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"></table>
		 </div>
	</section>

	<section class="tbl_wrap" style="width:33%;">
		<h2 class="h2_frm">지역별 접수 진행건수</h2>
	
		<div class="tbl_wrap" style="max-width:400px;">
			<canvas id="myChart2" width="400" height="400"></canvas>
		 </div>

		<div class="tbl_wrap" style="max-width:400px; margin:auto 10px;">
			<table id="datalist2" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"></table>
		 </div>
	</section>

	<section class="tbl_wrap" style="width:33%;">
		<h2 class="h2_frm">파트너별 접수 진행건수</h2>
	
		<div class="tbl_wrap" style="max-width:400px;">
			<canvas id="myChart3" width="400" height="400"></canvas>
		</div>

		<div class="tbl_wrap" style="max-width:400px; margin:auto 10px;">
			<table id="datalist3" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"></table>
		</div>
	</section>

</div>


<div>
<p class="help-block"></p>
</div>


<script>
const ctx = document.getElementById('myChart').getContext('2d');

const myChart = new Chart(ctx, {
	type: 'pie',
	data: { 
		labels: [],
		datasets: [{
			data: [],
            backgroundColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ]
		}]
	},
    options: {
		responsive: true,
		plugins: {
			legend: {
				position: 'left',
			},
			tooltip: {
                callbacks: {
					label: function(tooltipItems, data) { 
						//console.log(tooltipItems.parsed);
                        return  ' ' + tooltipItems.parsed + ' %';
                    }
                }
			}
		}
    }
});


$(document).ready(function () {
	$.ajax({
		dataType: "text",
		url: './ajax.status_pie.php',
		type: "post",
		data: "sdate=<?php echo $sdate;?>&edate=<?php echo $edate;?>",
		success: function (data) {
			let json = $.parseJSON(data);
			myChart.data.labels = json.data.labels;
			myChart.data.datasets[0].data = json.data.datas;
			myChart.update();
		}
	});
});

$(function () {

    var dataTable = $('#datalist').DataTable({
        paging: false,
        searching: false,
        scrollX : true,
        ordering: false,
        "order": [
            [0, 'desc']
        ],
        orderable: false,
        info: false,
        "bProcessing": true,
        "serverSide": true,
        columns: [{
                data: 'label',
                title: '상태',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'cnt',
                title: '건수',
                className : 'align-center',	
				orderable: false
            },
            {
                data: 'rate',
                title: '비율',
                className : 'align-center',	
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<span>" + rowData.rate + " %</span>");
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
            params.sdate = '<?php echo $sdate;?>';
            params.edate = '<?php echo $edate;?>';

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: './ajax.status_rows.php',
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
					//$('#total_count').html(json.total);
					//$('#total_dist').html(json.total.dist_cnt);

                    callback(result);
                }
            });
        }
		
    });
	
});

</script>	


<script>
const ctx2 = document.getElementById('myChart2').getContext('2d');

const myChart2 = new Chart(ctx2, {
	type: 'pie',
	data: { 
		labels: [],
		datasets: [{
			data: [],
            backgroundColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ]
		}]
	},
    options: {
		responsive: true,
		plugins: {
			legend: {
				position: 'left',
			},
			tooltip: {
                callbacks: {
					label: function(tooltipItems, data) { 
						//console.log(tooltipItems.parsed);
                        return  ' ' + tooltipItems.parsed + ' %';
                    }
                }
			}
		}
    }
});

$(document).ready(function () {
	$.ajax({
		dataType: "text",
		url: './ajax.sido_pie.php',
		type: "post",
		data: "sdate=<?php echo $sdate;?>&edate=<?php echo $edate;?>",
		success: function (data) {
			let json = $.parseJSON(data);
			myChart2.data.labels = json.data.labels;
			myChart2.data.datasets[0].data = json.data.rates;
			myChart2.update();
		}
	});
});

$(function () {

    var dataTable2 = $('#datalist2').DataTable({
        paging: false,
        searching: false,
        scrollX : true,
        ordering: false,
        "order": [
            [0, 'desc']
        ],
        orderable: false,
        info: false,
        "bProcessing": true,
        "serverSide": true,
        columns: [{
                data: 'sido',
                title: '지역',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'label',
                title: '상태',
                className : 'align-center',	
				orderable: false
            },
            {
                data: 'cnt',
                title: '건수',
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
            params.sdate = '<?php echo $sdate;?>';
            params.edate = '<?php echo $edate;?>';

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: './ajax.sido_rows.php',
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
					//$('#total_count').html(json.total);
					//$('#total_dist').html(json.total.dist_cnt);

                    callback(result);
                }
            });
        }
		
    });
	
});

</script>	

<script>
const ctx3 = document.getElementById('myChart3').getContext('2d');

const myChart3 = new Chart(ctx3, {
	type: 'pie',
	data: { 
		labels: [],
		datasets: [{
			data: [],
            backgroundColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ]
		}]
	},
    options: {
		responsive: true,
		plugins: {
			legend: {
				position: 'left',
			},
			tooltip: {
                callbacks: {
					label: function(tooltipItems, data) { 
						//console.log(tooltipItems.parsed);
                        return  ' ' + tooltipItems.parsed + ' %';
                    }
                }
			}
		}
    }
});

$(document).ready(function () {
	$.ajax({
		dataType: "text",
		url: './ajax.partner_pie.php',
		type: "post",
		data: "sdate=<?php echo $sdate;?>&edate=<?php echo $edate;?>",
		success: function (data) {
			let json = $.parseJSON(data);
			myChart3.data.labels = json.data.labels;
			myChart3.data.datasets[0].data = json.data.rates;
			myChart3.update();
		}
	});
});

$(function () {

    var dataTable3 = $('#datalist3').DataTable({
        paging: false,
        searching: false,
        scrollX : true,
        ordering: false,
        "order": [
            [0, 'desc']
        ],
        orderable: false,
        info: false,
        "bProcessing": true,
        "serverSide": true,
        columns: [{
                data: 'partner',
                title: '파트너',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'label',
                title: '상태',
                className : 'align-center',	
				orderable: false
            },
            {
                data: 'cnt',
                title: '건수',
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
            params.sdate = '<?php echo $sdate;?>';
            params.edate = '<?php echo $edate;?>';

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: './ajax.partner_rows.php',
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
					//$('#total_count').html(json.total);
					//$('#total_dist').html(json.total.dist_cnt);

                    callback(result);
                }
            });
        }
		
    });
	
});

</script>	

<script>
$(function () {
    commonjs.selectNav("navbar", "statics");
	
	$(".datepicker").datepicker();
	
    $('#search').on('click', function (event) {
		$('#fsearch').submit();
    });
	
})
</script>

<!-- CONTENT END -->

<?php
include_once '../../footer.php';
?>