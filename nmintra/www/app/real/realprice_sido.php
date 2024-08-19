<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

if(!$_GET['sido']) {
	$sd_key = '11';
} else {
	$sd_key = $_GET['sido'];
}

if(!$start_date) {
	$start_date = '2018-01';
}

$sql = "SELECT * FROM `sigungu_code_ext` WHERE `step` = 1 order by region asc";
$result = sql_query($sql);
$sido_arr = array();
while($row=sql_fetch_array($result)){
	$rcode = substr($row['region'],0,2);
	$sido_arr[$rcode]['title'] = $row['dong'];
	$sido_arr[$rcode]['region'] = $row['region'];
}

function sido_statics_value($rcode, $start_date='2017-01') {
	global $jsb;
	$sql = " select concat(yyyy,'-',LPAD(mm,2,'0')) as ym,avg(price_pyavg) as pyavg, sum(price_cnt) as cnt from {$jsb['actualprice_statics_table']} where region_code like '{$rcode}%' and concat(yyyy,'-',LPAD(mm,2,'0')) >= '{$start_date}' group by yyyy, mm order by concat(yyyy,'-',LPAD(mm,2,'0'))";
	//echo $sql;
	$result = sql_query($sql);
	$data = array();
	$my_val = $my_label = $my_cnt = array();
	$last_pyavg = 0;
	$i = 0;
	while($row=sql_fetch_array($result)){
		$data[$i] = $row;
		$my_label[$i] = str_replace("-",".",$row['ym']);
		$my_val[$i] = round(floatval($row['pyavg']));
		$my_cnt[$i] = $row['cnt'];
		$last_pyavg = $row['pyavg'];
		$i++;
	}
	$con_val = implode(',', $my_val);
	$con_cnt = implode(',', $my_cnt);
	$data['first'] = $my_val[0];
	$data['last'] = $last_pyavg;
	if($data['first']) {
		$data['rate'] = number_format(($data['last']-$data['first'])/$data['first']*100,1);
	} else {
		$data['rate'] = '100';
	}
	$data['val'] = $con_val;
	$data['cnt'] = $con_cnt;
	$con_label = implode("','", $my_label);
	$data['label'] = "'".$con_label."'";
	
	return $data;
}

$mv_val = sido_statics_value($sd_key,$start_date);


$sql = "SELECT * FROM `sigungu_code_ext` WHERE region like '{$sd_key}%' and `step` = 2 order by dong asc";
$result = sql_query($sql);
$gugun_arr = array();
while($row=sql_fetch_array($result)){
	$rcode = $row['region'];
	$gugun_arr[$rcode]['gugun'] = trim(str_replace($sido_arr[$sd_key]['title'], '', $row['dong']));
	$gugun_arr[$rcode]['region'] = $row['region'];
}

?>
<style>
#gugunChart { width:100%; }
#gugunChart div { padding:5px; border:1px solid #eee;}
#gugunChart ul { list-style-type:none; display:flex; flex-wrap:wrap; padding:0; }
#gugunChart ul li { display:inline-block; width:25%; }
//#gugunChart .spark {  }
</style>
<script src="/assets/js/jquery.sparkline.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<!-- CONTENT START -->
<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>국토부 실거래가 그래프</h1>
</div>

<div id="wrap">
	
	<h3><?php echo $sido_arr[$sd_key]['title'];?></h3>
	
	<div>
		<h4>평균매매가(3.3㎡당) : <?php echo number_format($mv_val['last']*10000); ?>원</h4>
	</div>
	
	<div class="mainchart">
		<canvas id="myChart" style="width:100%; height:200px;"></canvas>
	</div>
	
	<p><?php echo $start_date;?> 부터 2019-12월까지</p>
	<div id="gugunChart">
		<ul>
			<?php
			foreach($gugun_arr as $k => $v) {
				
			?>
			<li>
				<div class="sparkchart" onClick="javascipt:popGugun('<?php echo $k;?>');">
						<span class="sido"><?php echo $v['gugun'];?></span>
						<span id="sparkline<?php echo $k;?>_rate" class="state">0%</span>
						<span id="javascript:;" class="spark"><span id="sparkline<?php echo $k;?>" style="width: 100%;"></span></span>
				</div>
			</li>
			<?php
			}
			?>
		</ul>
	</div>
	
	
</div>


<iframe id="hiddenframe" style="display:none;"></iframe>

<?php
//print_r2($mv_val);

function gugun_statics_value($rcode, $start_date='2017-01') {
	global $jsb;
	$sql = " select concat(yyyy,'-',LPAD(mm,2,'0')) as ym,round(avg(price_pyavg)) as pyavg from {$jsb['actualprice_statics_table']} where region_code like '{$rcode}%' and concat(yyyy,'-',LPAD(mm,2,'0')) >= '{$start_date}' group by yyyy, mm order by concat(yyyy,'-',LPAD(mm,2,'0'))";
	//echo $sql;
	$result = sql_query($sql);
	$data = array();
	$my_val = array();
	$i = 0;
	while($row=sql_fetch_array($result)){
		$data[$i] = $row;
		$my_val[$i] = $row['pyavg'];
		$i++;
	}
	$con_val = implode(',', $my_val);
	$data['first'] = $my_val[0];
	$data['last'] = $my_val[($i-1)];
	if($data['first']) {
		$data['rate'] = number_format(($data['last']-$data['first'])/$data['first']*100,1);
	} else {
		$data['rate'] = '100';
	}
	$data['val'] = $con_val;
	
	return $data;
}

function gugun_statics_graph($dom_id, $rcode='11', $start_date='2017-01') {
	global $jsb;
	
	$ret = '';
	$mv_val = gugun_statics_value($rcode,$start_date);
	$ret .= "	var myvalues    = [".$mv_val['val']."];".PHP_EOL;
	$ret .= "	$(\"#".$dom_id."\").sparkline(myvalues, { 
		width: '100%',
		height: '40px',
		lineColor: '#FFBF00',
		fillColor: '#F7D358',
		spotColor: '#FF0000',
		minSpotColor: false,
		maxSpotColor: false,
		disableInteraction: true,
	});
	";
	$ret .= "	$(\"#".$dom_id."_rate\").html('".$mv_val['rate']."%');".PHP_EOL;
	return $ret;
	
}

?>

<script>
function popGugun(u) {
	
}
</script>
<script>
$(function () {
    //////////////////////////////////////////////////////////////////////////////////////
    commonjs.selectNav("navbar", "realprice");
	
<?php 
	// chart.js 메인 차트
?>
	var ctx = document.getElementById('myChart');
	var myChart = new Chart(ctx, {
		type: 'line',
		data: {
			labels: [<?php echo $mv_val['label'];?>],
			datasets: [{
				label: '평당매매가(만원)',
				data: [<?php echo $mv_val['val'];?>],
				backgroundColor: [
					'rgba(255, 159, 64, 0.2)'
				],
				borderColor: [
				],
				borderWidth: 0
			}]
		},
		options: {
			scales: {
				yAxes: [{
					ticks: {
						stepSize: 250
						//beginAtZero: true
					}
				}]
			},
			events: ['mousemove', 'mouseout', 'touchstart', 'touchmove'],
			responsive : false
		}
	});
	
	
<?php 
	// Gugun 차트
	foreach($gugun_arr as $k => $v) {
		echo gugun_statics_graph('sparkline'.$k, $k ,$start_date);
	}
?>

});	
</script>
<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>
