<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/real/danzi-view.php
include_once '../../header.php';

$addr1 = $_GET['addr1'];

$src_arr = array("서울특별시 ", "경기도 ", "인천시 ");
$dst_arr = array("서울 ", "경기 ", "인천 ");
$addr1 = str_replace($dst_arr, $src_arr, trim($addr1));
//if(!$year) $year = '2019';

$targeturl = "http://nmapi.xfund.co.kr/get_realprice3.php";

$dst_arr = array("서울 ", "경기 ", "인천 ", "강원특별자치도 " );
$src_arr = array("서울특별시 ", "경기도 ", "인천시 ", "강원도 ");
$addr1 = str_replace($dst_arr, $src_arr, trim($_GET['addr1']));

//print_r($_POST);
//die();
$param_list = array(
	"addr1" => $_GET['addr1'],
	"danzi" => $_GET['danzi'],
	"py" => $_GET['py'],
	"year" => $_GET['year'],
);


//$params_json = json_encode($param_list);
$post_field_string = http_build_query($param_list, '', '&');

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $targeturl);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field_string);
//curl_setopt($ch, CURLOPT_HEADER, TRUE);
//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);


$response = curl_exec($ch);
$curlinfo = curl_getinfo($ch);
curl_close($ch);

//$res = array();
//$res['search'] = $search;
//$res['data'] = json_decode($response);
//print_r2($res['data']);
//die();
$jsondata = json_decode($response, true);
//print_r2($jsondata);

$danzi = array();
$trade_cnt = 0;
$total_price = $ave_price = 0;
$datasets = array();

foreach($jsondata['data'] as $kk => $vv) {
	//print_r2($vv);
	//$label = $vv['mm']."/".$vv['dd']." ".($vv['floor'])?$vv['floor']."F":"";
	$danzi = $vv['danzi'];
	$datasets[] = array(
		"floor" => $vv['floor']."F",
		"year" => $_POST['year'],
		"x" => intval($vv['mm']),
		"y" => $vv['price'],
	);
	$trade_cnt++;
	$total_price += $vv['price'];
}
if($trade_cnt) {
	$ave_price = round($total_price / $trade_cnt, 0);
}

$htmltable = "
		<div class='realprice_datatable'>
			<div class=''>
				<label>최근 {$trade_cnt} 건 평균 거래금액</label>
				<span>".number_format($ave_price)." 만원</span>
			</div>
		</div>

		<table class='realprice_datatable'>
		<caption>실거래 내역</caption>
        <thead>
        <tr>
            <th scope='col'>거래일자</th>
            <th scope='col'>전용면적</th>
            <th scope='col'>층</th>
            <th scope='col'>거래금액</th>
        </tr>
        </thead>
        <tbody>
";

foreach($jsondata['data'] as $kk => $vv) {
		
		$bg = 'bg'.($i%2);
$htmltable .= "
			<tr class='".$bg."'>
				<td class='td_center'>".$vv['yyyy'].".".$vv['mm'].".".$vv['dd']."</td>
				<td class='td_center'>".$vv['py']."</td>
				<td class='td_center'>".$vv['floor']."</td>
				<td class='td_center'>".number_format($vv['price'])."</td>
			</tr>
";
	}
$htmltable .= "			
		</table>
		<div>&nbsp;</div>
";

?>
<style>
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

<!-- CONTENT START -->


<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>실거래가 단지 상세</h1>
</div>

<!-- CONTENT START -->

<div style="padding:15px;margin:auto;">

		<div class="search-box max-768-target">
			<div class="row">
				<div class="col-sm-4">
					<label>주소</label>
					<?php echo $addr1; ?>
				</div>
				<div class="col-sm-3">
					<label>단지명</label>
					<?php echo $danzi; ?>
				</div>
				
				<div class="col-sm-2">
					<label class="hidden-xs" style="width:100%">&nbsp;</label>
					
				</div>
			</div>
		</div>


		<div class="row">
			<div id="result2" class="col-sm-12">
				<?php echo $htmltable; ?>
			</div>
		</div>


		<br class="clear"/>
		<div class="row">
			<div class="col-sm-12"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="history.back();">돌아가기</button></div>
		</div>

</div>

<script>
$(function () {
	commonjs.selectNav("navbar", "working");
});

</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';