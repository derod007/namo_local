<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

include_once '../../header.php';

$list_table = "region_preferential";

$w = $_GET['w'];

if($w == 'u') {
	$rp_id = $_GET['rp_id'];
	if(!$rp_id) {
		alert("잘못된 접근입니다.");
	}
	
	$sql = " select * from {$list_table} where rp_id='{$rp_id}' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['rp_id']) {
		alert('해당되는 데이터가 없습니다');
	}
	$btntxt = "수정";
	$btnclass = "btn-warning";
	
} else {
	$btntxt = "등록";
	$btnclass = "btn-primary";
}
?>
<!-- CONTENT START -->
<div class="page-header">
	<h1>소액임차보증금 우선변제금</h1>
</div>

<div style="padding:15px;margin:auto;">
	<form id="fwrite" name="fwrite" action="/app/new/preferential-act.php" method="post" class="jsb-form">
	 <input type="hidden" name="w" value="<?php echo $w; ?>">
	 <input type="hidden" name="rp_id" value="<?php echo $rp_id; ?>">
	   <div class="form-group col-sm-12">
	   
			<div class="row"><label class="col-sm-2 control-label">지역</label>
				<div class="col-sm-10"><?php echo get_sidocode_select("rp_rcode", $row['rp_rcode'], ""); ?></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">보증금 기준금액</label>
				<div class="col-sm-10"><input type="text" id="rp_deposit_amt" name="rp_deposit_amt" value="<?php echo $row["rp_deposit_amt"]; ?>" class="form-control" style="display:inline-block;width:150px" placeholder="숫자만(만원단위)"> 만원</div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">소액임차보증금</label>
				<div class="col-sm-10"><input type="text" id="rp_repay_amt" name="rp_repay_amt" value="<?php echo $row["rp_repay_amt"]; ?>" class="form-control" style="display:inline-block;width:150px" placeholder="숫자만(만원단위)"> 만원</div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">사용여부</label>
				<div class="col-sm-10">
					<input type="radio" id="control_01" name="rp_use" value="1" required <?php echo ($row['rp_use']=='1')?"checked":"";?>>
					<label for="control_01">사용 &nbsp;</label>
					<input type="radio" id="control_02" name="rp_use" value="0" required <?php echo ($row['rp_use']=='0')?"checked":"";?>>
					<label for="control_02">미사용 &nbsp;</label>
				</div>
			</div>
		
		</div>

		<br class="clear"/>
			<!-- div class="row">
				<div class="col-sm-12 blue"> &nbsp; </div>
			</div -->
			<div class="row">
				<div class="col-sm-4"><button class="btn <?php echo $btnclass; ?> btn-block col-sm-4" type="submit"><?php echo $btntxt; ?></button></div>
				<div class="col-sm-4"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="document.location.href='./preferential-list.php';">목록으로</button></div>
			</div>

	</form>

</div>


<script>
$(function () {
	commonjs.selectNav("navbar", "preferential");

});
	
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>