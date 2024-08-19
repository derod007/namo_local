<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://nmintra.event-on.kr/app/p2p/publicofficial_reset.php
include_once '../../header.php';

$w = $_POST['w'];
if($w == 'reset') {
	

	echo '<br/><br/><a href="./publicofficial.php" class="btn btn-default btn-block">목록으로</a><br/><br/>';
	echo "<pre>";

	$sql = " truncate table p2p_publicofficial";
	$result = sql_query($sql, TRUE);
	echo "deleted DataBase Research Data".PHP_EOL;
	
	$sql = " truncate table namo_member";
	$result = sql_query($sql, TRUE);
	echo "deleted DataBase Member Data".PHP_EOL;
	
	// 자료등록 저장경로
	$dir = $_SERVER['DOCUMENT_ROOT']."/data/file";
	
	// 핸들 획득  
	$handle  = opendir($dir);  
	$files = array();  
	
	// 디렉터리에 포함된 파일을 저장한다.  
	while (false !== ($filename = readdir($handle))) {  
		if($filename == "." || $filename == "..") {
			continue;  
		}
		  
		// 파일인 경우만 목록에 추가한다.  
		if(is_file($dir . "/" . $filename)){
			$files[] = $filename;  
			echo "deleted ".$filename.PHP_EOL;
			unlink($dir . "/" . $filename);
		}
	}  
	echo "</pre>";
	  
	// 핸들 해제  
	closedir($handle);  	
	
	die();
}

?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>데이터 초기화</h1>
</div>


<?php
$sql = " select count(*) as cnt, reg_date from namo_member where 1=1";
$row = sql_fetch($sql);
$namo_count = $row['cnt'];

$sql = " select count(distinct grcode) cnt, count(*) as total_cnt from p2p_publicofficial {$where_sql}";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_dist = $row['total_cnt'];
?>
<div class="search-box">
	<form name="freset" id="freset" method="post" action="<?php echo $PHP_SELF;?>" enctype="multipart/form-data">
	<input type="hidden" name="w" value="reset">
		<div class="row">
			<div class="col-sm-4">
				<label>총 회원수</label>
				<p><?php echo number_format($namo_count);?> 명</p>
			</div>
			<div class="col-sm-3">
				<label>총 자료수</label>
				<p><?php echo number_format($total_count);?> 건 / <?php echo number_format($total_dist);?> 명</p>
			</div>
			
			<div class="col-sm-2">
				<label class="hidden-xs" style="width:100%">데이터 초기화</label>
				<button class="btn btn-danger btn-block" id="search" type="submit">초기화</button>
			</div>
			<div class="col-sm-1">
			</div>
			<div class="col-sm-2">
				<label>목록</label>
				<a href="./publicofficial.php" class="btn btn-default btn-block">목록으로</a>
			</div>
			
		</div>
		<p align='left'></p>
		<p align='left' style="font-size:1.2em; font-weight:600;">
		※ 초기화시 현재 등록된 회원정보 데이터와 조회요청자료가 <span class="red">모두 삭제</span>됩니다.<br/>
		※ 재 조회시 회원정보를 다시 등록하셔야 합니다.<br/>
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