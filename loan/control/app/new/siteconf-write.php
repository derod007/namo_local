<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

include_once '../../header.php';

$list_table = "site_conf";

	
$sql = " select * from {$list_table} where 1 order by idx desc limit 1";
$row = sql_fetch($sql);
	
if(!$row['idx']) {
	alert('설정정보가 없습니다. 관리자에게 문의해주세요.');
}
$btntxt = "수정";
$btnclass = "btn-warning";
	
?>
<!-- CONTENT START -->
<div class="page-header">
	<h1>자동한도 설정</h1>
</div>

<div style="padding:15px;margin:auto;">
	<form id="fwrite" name="fwrite" action="/app/new/siteconf-act.php" method="post" class="jsb-form">
	 <input type="hidden" name="idx" value="<?php echo $row['idx']; ?>">
	   <div class="form-group col-sm-12">
	   
			<div class="row"><label for="auto_interest" class="col-sm-2 control-label">자동한도 금리설정</label>
				<div class="col-sm-3"><input type="text" id="auto_interest" name="auto_interest" value="<?php echo $row["auto_interest"]; ?>" class="form-control" style="display:inline-block;width:150px" placeholder="숫자만(소수점 1자리)"> %</div>
				<div class="col-sm-7"><p class="help_txt">※ 지역별 LTV가 설정되지 않은 경우 부여되는 한도 입니다.</p></div>
			</div>
		</div>

		<br class="clear"/>
			<!-- div class="row">
				<div class="col-sm-12 blue"> &nbsp; </div>
			</div -->
			<div class="row">
				<div class="col-sm-4"><button class="btn <?php echo $btnclass; ?> btn-block col-sm-4" type="submit"><?php echo $btntxt; ?></button></div>
				<div class="col-sm-4"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="document.location.href='/app/new/loan-list.php';">접수목록으로</button></div>
			</div>

	</form>

</div>

<script>
$(function () {
	commonjs.selectNav("navbar", "siteconf");

});
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>