<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://manage.xfund.co.kr/app/test.php
include_once '../header.php';

$sigungu = "서울특별시 강동구";
$dong = "상일동";
$zibun = "513";
$danzi = "고덕센트럴아이파크";
$py = "84.97";
$year = "2020";
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>

<style>
	#result  {width:100%; height:auto;}
	#result pre { width:100%; height:auto; word-break:break-all; }
	#result2 .realprice_datatable { margin-top:20px; font-size:1.1em; }
	#result2 .realprice_datatable caption { font-size:1.4em; font-weight:800; }
	#result2 .realprice_datatable th { padding:2px; padding-right:10px; }
	#result2 .realprice_datatable td { padding:2px; padding-right:10px; }
	.td_center {text-align:center;}
</style>


<div class="search-box">
	<form name="fsearch" id="fsearch" method="POST">
		<div class="row">
			<div class="col-sm-3">
				<label>시군구</label>
				<input type="text" id="sigungu" name="sigungu" class="form-control" value="<?php echo $sigungu;?>" placeholder="시군구">
			</div>
			<div class="col-sm-1">
				<label>동</label>
				<input type="text" id="dong" name="dong" class="form-control" value="<?php echo $dong;?>" placeholder="동">
			</div>
			<div class="col-sm-1">
				<label>지번</label>
				<input type="text" id="zibun" name="zibun" class="form-control" value="<?php echo $zibun;?>" placeholder="지번">
			</div>
			<div class="col-sm-2">
				<label>단지명</label>
				<input type="text" id="danzi" name="danzi" class="form-control" value="<?php echo $danzi;?>" placeholder="단지명">
			</div>
			<div class="col-sm-1">
				<label>전용면적</label>
				<input type="text" id="py" name="py" class="form-control" value="<?php echo $py;?>" placeholder="전용면적">
			</div>
			<div class="col-sm-1">
				<label>조회연도</label>
				<input type="text" id="year" name="year" class="form-control" value="<?php echo $year;?>" placeholder="조회연도">
			</div>
			<div class="col-sm-1">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
			
		</div>
	</form>
</div>

<div id="result"></div>

<script>
$(function () {
    $('#search').on('click', function (event) {
		var params = $("#fsearch").serialize();
		$.ajax({
			url: './real/get_realprice.php',
			type: "post",
			data: params,
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8', 
			dataType: "text",
			success: function (data) {
				//let json = $.parseJSON(data);
				$("#result").html("<div>"+data+"</div>");
			}
		});
    });
	
});
</script>

<?php
$addr1 = "서울특별시 강동구 상일동 513";
$danzi = "고덕센트럴아이파크";
$py = "84.97";
$year = "2020";
?>

<a href="javascript:;" id="newwin_real">새창</a>
<form name="fnewwin_real" id="fnewwin_real" method="POST">
<input type="hidden" name="addr1" value="<?php echo $addr1;?>">
<input type="hidden" name="danzi" value="<?php echo $danzi;?>">
<input type="hidden" name="py" value="<?php echo $py;?>">
<input type="hidden" name="year" value="<?php echo $year;?>">
</form>
<script>
$(function () {
    $('#newwin_real').on('click', function (event) {
		window.open('about:blank;', 'newwin_real', 'scrollbars=yes,width=650,height=600,top=10,left=100');
		document.fnewwin_real.target = "newwin_real";
		document.fnewwin_real.action = "./real/newwin_real.php";
		document.fnewwin_real.submit();
	});
});

// url = '/app/real/newwin_real.php?addr1=' + '<?php echo $addr1;?>' + '&py=' + '<?php echo $py;?>' 
// window.open('/app/real/newwin_real.php', 'newwin_real', 'scrollbars=yes,width=650,height=600,top=10,left=100');
</script>


<div class="search-box">
	<form name="fsearch" id="fsearch2" method="POST">
		<div class="row">
			<div class="col-sm-4">
				<label>지번주소</label>
				<input type="text" id="addr1" name="addr1" class="form-control" value="<?php echo $addr1;?>" placeholder="시군구">
			</div>
			<div class="col-sm-2">
				<label>단지명</label>
				<input type="text" id="danzi" name="danzi" class="form-control" value="<?php echo $danzi;?>" placeholder="단지명">
			</div>
			<div class="col-sm-1">
				<label>전용면적</label>
				<input type="text" id="py" name="py" class="form-control" value="<?php echo $py;?>" placeholder="전용면적">
			</div>
			<div class="col-sm-1">
				<label>조회연도</label>
				<input type="text" id="year" name="year" class="form-control" value="<?php echo $year;?>" placeholder="조회연도">
			</div>
			<div class="col-sm-1">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search2" type="button">검색</button>
			</div>
			
		</div>
	</form>
</div>

<canvas id="myChart2" width="600" height="200"></canvas>

<div id="result2"></div>

<script>
// https://www.chartjs.org/docs/3.9.1/samples/bar/stacked.html
const ctx2 = document.getElementById('myChart2').getContext('2d');
const myChart2 = new Chart(ctx2, {
    type: 'line',
	data: { 
		datasets: [{
			label: '',
			data: [],
		}],
		//labels: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
	},
    options: {
		//responsive: true,
		borderColor: 'rgba(255, 255, 255, 0)',
		backgroundColor: 'rgba(255, 99, 132, 1)',
		scales: {
                y: {
                    type: 'linear',
                    position: 'right',
                    grace: '20%',
                    grid: {
                        display: true,
                    },
                    ticks: {}
                },	
                x: {
                    grid: {
                        display: true,
                    },
					ticks: {
						// Include a dollar sign in the ticks
						callback: function(value, index, ticks) {
							return (value+1)+'월';
						}
					}
                },	
		},
		plugins: {
			tooltip: {
                callbacks: {
					title : function(tooltipItems, data) {
						console.log(tooltipItems);
						return   tooltipItems[0].raw.year.toString() + '년 ' + tooltipItems[0].label + '월';
						//return null;
					},
					label: function(tooltipItems, data) { 
						//console.log(tooltipItems);
                        return  ' ' + tooltipItems.parsed.y.toLocaleString('ko-KR') + ' 만원('+tooltipItems.raw.floor+')';
                    }
                }
			}
		}
    }
});

</script>

<script>
$(function () {
    $('#search2').on('click', function (event) {
		var params = $("#fsearch2").serialize();
		$.ajax({
			url: './real/get_realprice2.php',
			type: "post",
			data: params,
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8', 
			dataType: "text",
			success: function (data) {
				// 그래프 데이터
				let json = $.parseJSON(data);
				myChart2.data.labels = json.data.days;
				myChart2.data.datasets[0].label = json.data.year + "년도";
				myChart2.data.datasets[0].data = json.data.datas;
				//console.log(json.data.datas);
				myChart2.update();
				$("#result2").html("<div>"+json.data.htmltable+"</div>");	// 목록출력
			}
		});
    });
	
});
</script>

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>