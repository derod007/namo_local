<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// http://nmintra.event-on.kr/app/p2p/namodata_xlsimport.php
include_once '../../header.php';

$w = $_POST['w'];
if($w == 'w') {	// 등록
	
	define('_VAPI_', true);
	require_once './inc_data_import_update.php';
	
	//die();
	goto_url($PHP_SELF);
	die();
} else if($w == 'u') {
	
	define('_VAPI_', true);
	require_once './inc_loan_update.php';
	
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
	<h1>NAMO 회원자료 XLS 업로드</h1>
</div>

<div class="search-box max-768-target">
	<form name="fupload" id="fupload" method="post" action="<?php echo $PHP_SELF;?>" enctype="multipart/form-data">
	<input type="hidden" name="w" value="w">
		<div class="row">
			<div class="col-sm-2">
				<label>기준일자</label>
				<input type="text" name="cdate_ymd" value="<?php echo date("Y-m-d");?>" class="form-control readonly" readonly=readonly>
			</div>
			<div class="col-sm-4">
				<label>XLS파일</label>
				<input type="file" name="p2pdata_xls" id="p2pdata_xls" value=""  class="form-control">
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
		※ 회원정보 데이터 기준<br/>
		※ 데이터파일은 XLS 형태만 입력이 가능합니다. 엑셀에서 EXCEL2003 용 파일로 저장해서 등록해주세요.<br/>
		※ 데이터파일은 5Mbyte 미만만 업로드 가능합니다.<br/>
		※ 데이터 등록시 기존 자료는 <span class="red">초기화</span> 되고 재등록이 진행됩니다.<br/>
		※ 회원자료를 업로드후에 대출명세 자료를 업데이트 합니다.<br/>
		</p>
	</form>
</div>

<?php
$sql = " select count(*) as cnt, reg_date from namo_member where 1=1";
$row = sql_fetch($sql);
$namo_count = $row['cnt'];
?>
<p align='right'></p>
<table id="datatotal" class="table table-striped table-bordered jsb-table1">
	<tr>
		<th class="col-sm-2">총 회원수</th>
		<td id="total_count" class="col-sm-2"><?php echo number_format($namo_count);?></td>
	</tr>
</table>

<div class="search-box max-768-target">
	<form name="fupload" id="fupload" method="post" action="<?php echo $PHP_SELF;?>" enctype="multipart/form-data">
	<input type="hidden" name="w" value="u">
		<div class="row">
			<div class="col-sm-4">
				<label>대출명세 XLS 파일</label>
				<input type="file" name="p2pdata_xls" id="p2pdata_xls" value=""  class="form-control">
			</div>
			
			<div class="col-sm-2">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="submit">등록</button>
			</div>
			<div class="col-sm-3">
			</div>
			
		</div>
		<p align='left'></p>
		<p align='left'>
		※ 보고서 > 대출명세 데이터 기준<br/>
		※ 데이터파일은 XLS 형태만 입력이 가능합니다. 엑셀에서 EXCEL2003 용 파일로 저장해서 등록해주세요.<br/>
		※ 데이터파일은 5Mbyte 미만만 업로드 가능합니다.<br/>
		※ 대출명세 자료에서 <span class="red">대출잔액 항목</span> 만 등록된 회원데이터에 업데이트 됩니다.<br/>
		※ 회원자료를 업로드후에 대출명세 자료를 업데이트 합니다.<br/>
		</p>
	</form>
</div>


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