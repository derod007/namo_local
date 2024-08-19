<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

if($yyyy && $mm) {
	$start_date = $yyyy."-".str_pad($mm, 2, '0',STR_PAD_LEFT);
} else {
	$start_date = '2019-01';
}

function sido_statics_value($rcode, $start_date='2017-01') {
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

function sido_statics_graph($dom_id, $rcode='11', $start_date='2017-01') {
	global $jsb;
	
	$ret = '';
	$mv_val = sido_statics_value($rcode,$start_date);
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

$sql = "SELECT * FROM `sigungu_code_ext` WHERE `step` = 1 order by region asc";
$result = sql_query($sql);
$sido_arr = $sido_key = array();
while($row=sql_fetch_array($result)){
	$rcode = substr($row['region'],0,2);
	$sido_arr[$rcode]['title'] = $row['dong'];
	$sido_arr[$rcode]['region'] = $row['region'];
	$sido_key[] = substr($row['region'],0,2);
}

?>
<style>
#sidoChart { width:100%; }
#sidoChart div { padding:5px; border:1px solid #eee;}
#sidoChart ul { list-style-type:none; display:flex; flex-wrap:wrap; padding:0; }
#sidoChart ul li { display:inline-block; width:25%; }
</style>
<script src="/assets/js/jquery.sparkline.min.js"></script>
<!-- CONTENT START -->
<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>국토부 실거래가 그래프</h1>
</div>

<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-2">
				<label>조회시작 년월</label>
				<select id="yyyy" name="yyyy"  class="form-control">
					<option value="">(년)선택</option>
				<?php
					$yy = date('Y');
					$i = 0;
					while($yy >= 2016 ) {
						echo option_selected($yy, $yyyy, $yy);
						$yy--;
						$i++;
					}
				?>	
				</select>
			</div>
			<div class="col-sm-2">
				<label>&nbsp;</label>
				<select id="mm" name="mm"  class="form-control">
					<option value="">(월)선택</option>
				<?php
					for($i=1;$i<=12;$i++) {
						echo option_selected($i, $mm, $i);
					}
				?>	
				</select>
			</div>
			<div class="col-sm-2">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="submit">검색</button>
			</div>
		</div>
	</form>
</div>

<!-- p align='right'>단위:만원</p -->
<p>&nbsp;</p>
<table id="datatotal" class="table table-striped table-bordered jsb-table1" style="display:none;">
	<tr>
		<th class="col-sm-2">총 데이터수</th>
		<td id="total_count" class="col-sm-2">0</td>
		<th class="col-sm-2">총 거래금액</th>
		<td id="total_price" class="col-sm-2">0</td>
		<th class="col-sm-2">평단가</th>
		<td id="total_pyprice" class="col-sm-2">0</td>
	</tr>
</table>

<p><?php echo $start_date;?> 부터</p>
<div id="sidoChart">
					<div class="sparkchart" onClick="location.href='./realprice_sido.php?sido=11'">
							<span class="sido">서울특별시</span>
							<span id="sparkline11_rate" class="state">0%</span>
							<span id="javascript:;" class="spark"><span id="sparkline11" style="width: 100%;"></span></span>
					</div>
                <ul>
						<?php
						foreach($sido_arr as $k => $v) {
							if($k == '11') continue;
							//print_r2($v);
						?>
						<li>
                        <div class="sparkchart" onClick="location.href='./realprice_sido.php?sido=<?php echo $k;?>'">
									<span class="sido"><?php echo $v['title'];?></span>
									<span id="sparkline<?php echo $k;?>_rate" class="state">0%</span>
									<span id="javascript:;" class="spark"><span id="sparkline<?php echo $k;?>" style="width: 100%;"></span></span>
							</div>
						</li>
						<?php
						}
						?>
					</ul>
</div>

<iframe id="hiddenframe" style="display:none;"></iframe>

<?php
?>

<script>
$(function () {
    //////////////////////////////////////////////////////////////////////////////////////
    commonjs.selectNav("navbar", "realprice");
	
<?php 

foreach($sido_key as $sd) {
	echo sido_statics_graph('sparkline'.$sd, $sd ,$start_date);
}

/*
//  서울 = 11
echo sido_statics_graph('sparkline11', '11',$start_date);

//  부산 = 26
echo sido_statics_graph('sparkline26', '26',$start_date);

//  대구 = 27
echo sido_statics_graph('sparkline27', '27',$start_date);
*/
?>

});	
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>
