<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://nmintra.event-on.kr/app/p2p/data_txtimport.php
include_once '../../header.php';

$w = $_POST['w'];
if($w == 'w') {	// 등록
	
	define('_VAPI_', true);
	require_once './inc_data_txtimport_update.php';
	
	//die();
	goto_url('./publicofficial.php');
	//goto_url($PHP_SELF);
	die();
}

?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>공직자윤리위원회 요청자료 TXT 업로드</h1>
</div>

<div class="search-box max-768-target">
	<form name="fupload" id="fupload" method="post" action="<?php echo $PHP_SELF;?>" enctype="multipart/form-data">
	<input type="hidden" name="w" value="w">
		<div class="row">
			<div class="col-sm-2">
				<label>요청기준일자</label>
				<input type="text" name="cdate_ymd" value="<?php echo date("Y-m-d");?>" class="form-control readonly" readonly=readonly>
			</div>
			<div class="col-sm-4">
				<label>TXT파일</label>
				<input type="file" name="p2ppublic_txt" id="p2ppublic_txt" value=""  class="form-control">
			</div>
			
			<div class="col-sm-2">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="submit">등록</button>
			</div>
			<div class="col-sm-3">
			</div>
			<div class="col-sm-1">
				<label>목록</label>
				<a href="./publicofficial.php" class="btn btn-default btn-block">목록으로</a>
			</div>
			
		</div>
		<p align='left'></p>
		<p align='left'>
		※ 공직자윤리위원회 제공 데이터 기준(EUC-KR TEXT 형식)<br/>
		※ 데이터파일은 TXT 파일만 입력이 가능하며, | 로 구분된 데이터만 입력됩니다.<br/>
		※ 데이터파일은 2Mbyte 미만만 업로드 가능합니다.<br/>
		</p>
	</form>
</div>

<p align='right'></p>
<table id="datatotal" class="table table-striped table-bordered jsb-table1">
	<tr>
		<th class="col-sm-2">총 데이터수</th>
		<td id="total_count" class="col-sm-2">0</td>
		<th class="col-sm-2">그룹코드</th>
		<td id="total_dist" class="col-sm-2">0</td>
	</tr>
</table>

<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"></table>

<iframe id="hiddenframe" style="display:none;"></iframe>

<script>
$(function () {
    //////////////////////////////////////////////////////////////////////////////////////
    commonjs.selectNav("navbar", "publicofficial");
	
    //var dataTable = $('#datalist').DataTable({
    //});
	
});	
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>