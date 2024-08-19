<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

$src_arr = array("서울특별시 ", "경기도 ", "인천시 ", "제주특별자치도 ", "강원특별자치도 ");
$dst_arr = array("서울 ", "경기 ", "인천 ", "제주", "강원 ");
$addr1 = str_replace($dst_arr, $src_arr, trim($addr1));
if(!$year) $year = '2019';
if(!$danzi) $danzi = '';
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NAMO funding - 대출신청 관리시스템</title>

    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	 <link rel="stylesheet" as="style" crossorigin href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.6/dist/web/static/pretendard.css" />
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/iamks-basic.css">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?php echo JS_VERSION; ?>">
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
	
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
   <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>	
   <script src="/assets/js/main.js?v=<?php echo JS_VERSION; ?>"></script>

<!--
	jquery-ui/1.12.1/themes
	base black-tie blitzer cupertino dark-hive dot-luv eggplant excite-bike flick hot-sneaks humanity le-frog mint-choc overcast pepper-grinder redmond smoothness south-street start sunny swanky-purse trontastic ui-darkness ui-lightness vader
-->
	<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.12.1/themes/black-tie/jquery-ui.css" />
	<script src="//code.jquery.com/ui/1.13.2/jquery-ui.js" integrity="sha256-xLD7nhI62fcsEZK2/v8LsBcb4lG7dgULkuXoXB/j91c=" crossorigin="anonymous"></script>
	
	<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
   
</head>

<body>

<style>
	body { padding-top: 10px; padding:20px; }
	#result  {width:100%; height:auto;}
	#result pre { width:100%; height:auto; word-break:break-all; }
	#result2 .realprice_datatable { margin-top:20px; font-size:1.0em; }
	#result2 .realprice_datatable caption { font-size:1.4em; font-weight:800; }
	#result2 .realprice_datatable th { padding:2px; padding-right:10px; }
	#result2 .realprice_datatable td { padding:2px; padding-right:10px; }
	#result2 .realprice_datatable tr { border-bottom:1px solid #eee; }
	.td_center {text-align:center;}
	#fnewwin_real label { margin-right: 10px;}
</style>

<div class="search-box">
<form name="fnewwin_real" id="fnewwin_real" method="GET">
<input type="hidden" name="addr1" value="<?php echo $addr1;?>">
<input type="hidden" name="danzi" value="<?php echo $danzi;?>">
<input type="hidden" name="py" value="<?php echo $py;?>">
<input type="hidden" name="year" value="<?php echo $year;?>">
		<div class="row">
			<div class="col-sm-4">
				<label>지번주소</label>
				<span><?php echo $addr1;?></span>
			</div>
			<div class="col-sm-3">
				<label>단지명</label>
				<span id="danzi"><?php echo $danzi;?></span>
			</div>
			<div class="col-sm-2">
				<label>전용면적</label>
				<span><?php echo $py;?> ㎡</span>
			</div>
			<div class="col-sm-2">
				<label>조회연도</label>
				<span>
					<select name="year" onchange="this.form.submit();">
						<?php
							for($yy = date("Y"); $yy > 2017; $yy--) {
								echo option_selected($yy, $year);
							}
						?>
					</select>
				</span>
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
						//console.log(tooltipItems);
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
		var params = $("#fnewwin_real").serialize();
		$.ajax({
			url: '/app/real/get_realprice2.php',
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
				$("#danzi").html(json.data.danzi);	// 단지명
				$("#result2").html("<div>"+json.data.htmltable+"</div>");	// 목록출력
			}
		});
});
</script>

</body>
</html>