<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/kbapt/kbland_statics.php
include_once '../../header.php';

function sido_statics_val($sido) {
	$cnt = array();
	
	$sql = "SELECT count(*) as cnt FROM `sigungu_code_ext` WHERE region like '{$sido}%' and step='0' and ext IS NULL";
	$row = sql_fetch($sql);
	$cnt['dong'] = $row['cnt'];
	
	$sql = "SELECT count(*) as cnt FROM `kbland_danzi` WHERE regioncode like '{$sido}%'";
	$row = sql_fetch($sql);
	$cnt['danzi'] = $row['cnt'];
	
	$sql = "SELECT count(b.kbno) as cnt FROM `kbland_danzi` as b 
				LEFT JOIN `kbland_danzi_py` as a on a.kbno = b.kbno
				WHERE b.regioncode like '{$sido}%' and a.kbno IS NULL";
	$row = sql_fetch($sql);
	$cnt['notpy'] = $row['cnt'];
	$cnt['danzipy'] = $cnt['danzi'] - $row['cnt'];

	$sql = "SELECT count(b.kbno) as cnt FROM `kbland_danzi` as b 
				LEFT JOIN `kbland_kbprice` as a on a.kbno = b.kbno
				WHERE b.regioncode like '{$sido}%' and a.kbno IS NULL";
	$row = sql_fetch($sql);
	$cnt['notprice'] = $row['cnt'];
	$cnt['price'] = $cnt['danzi'] - $row['cnt'];
	
	//print_r2($cnt);
	
	return $cnt;
	
}

$sql = "SELECT * FROM `sigungu_code_ext` WHERE `step` = 1 order by region asc";
$result = sql_query($sql);
$sido_arr = $sido_key = array();
while($row=sql_fetch_array($result)){
	$rcode = substr($row['region'],0,2);
	$data = sido_statics_val($rcode);
	$sido_arr[$rcode]['title'] = $row['dong'];
	$sido_arr[$rcode]['region'] = $row['region'];
	$sido_arr[$rcode]['dong'] = $data['dong'];
	$sido_arr[$rcode]['danzi'] = $data['danzi'];
	$sido_arr[$rcode]['danzipy'] = $data['danzipy'];
	$sido_arr[$rcode]['notpy'] = $data['notpy'];
	$sido_arr[$rcode]['price'] = $data['price'];
	//print_r2($rcode);
	//print_r2($data);
}

?>

<style>
#sidoChart { width:100%; }
#sidoChart div { padding:5px; border:1px solid #eee;}
#sidoChart ul { list-style-type:none; display:flex; flex-wrap:wrap; padding:0; }
#sidoChart ul li { display:inline-block; width:25%; }
.sparkchart div {padding:5px; border:1px solid #eee; margin-bottom:5px; line-height:1.2em;}
.sido { background-color:#5cb85c; }
</style>
<script src="/assets/js/jquery.sparkline.min.js"></script>
<!-- CONTENT START -->
<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>KB데이터 현황</h1>
</div>

<!--
<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-2">
				<label>조회시작 년월</label>
				<select id="yyyy" name="yyyy"  class="form-control">
					<option value="">(년)선택</option>
				</select>
			</div>
			<div class="col-sm-2">
				<label>&nbsp;</label>
				<select id="mm" name="mm"  class="form-control">
					<option value="">(월)선택</option>
				</select>
			</div>
			<div class="col-sm-2">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="submit">검색</button>
			</div>
		</div>
	</form>
</div>
-->

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

<p><?php echo date("Y-m-d H:i");?> 현재</p>
<div id="sidoChart">
		<ul>
			<?php
			foreach($sido_arr as $k => $v) {
				//if($k == '11') continue;
				//print_r2($v);
			?>
			<li>
			<div class="sparkchart">
						<div class="sido" onClick="location.href='./kbland_danzi.php?rcode=<?php echo $k;?>'">
							<span><?php echo $v['title'];?></span>
							<span id="sparkline<?php echo $k;?>_rate" class="state"></span>
						</div>
						<div class="spark"><span id="sparkline<?php echo $k;?>" style="width: 100%;">
							동코드 : <?php echo number_format($sido_arr[$k]['dong']); ?>개<br/>
							단지 : <?php echo number_format($sido_arr[$k]['danzi']); ?>개<br/>
							단지(평형) : <?php echo number_format($sido_arr[$k]['danzipy']); ?>개 / 
							평형미수집 : <?php echo number_format($sido_arr[$k]['notpy']); ?>개<br/>
							KB시세 : <?php echo number_format($sido_arr[$k]['price']); ?>개<br/>
						</span></div>
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
    commonjs.selectNav("navbar", "kbland_statics");

});	
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>
