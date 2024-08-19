<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../header.php';

$pt_idx = $_GET['pt_idx'];
if(!$pt_idx) {
	alert("잘못된 접근입니다.");
}
$sql = " select * from partner_member where idx = '{$pt_idx}' ";
$partner = sql_fetch($sql);
if(!$partner['idx']) {
	alert("잘못된 접근입니다.");
}
$parent_id = $partner['idx'];

$w = $_GET['w'];

if($w == 'su') {

	$idx = $_GET['idx'];
	$sql = " select * from partner_member where idx = '{$idx}' ";
	$row = sql_fetch($sql);
	
	
	if(!$row['idx']) {
		alert('해당되는 데이터가 없습니다');
	}
	$btntxt = "수정";
	$btnclass = "btn-warning";
	
} else {
	$w = 'sw';
	$btntxt = "등록";
	$btnclass = "btn-primary";
}
?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>파트너 서브아이디 <?php echo $btntxt; ?> (<?php echo $partner['mb_bizname'];?>)</h1>
</div>

<!-- CONTENT START -->

<div style="padding:15px;margin:auto;">
	<form id="flogin" name="flogin" action="/app/partner-act.php" method="post" class="jsb-form">
	 <input type="hidden" name="w" value="<?php echo $w; ?>">
	 <input type="hidden" name="idx" value="<?php echo $idx; ?>">
	 <input type="hidden" name="pt_idx" value="<?php echo $parent_id; ?>">
	   <div class="form-group" >
			
			<div class="row"><label class="col-sm-2 control-label">아이디</label>
				<div class="col-sm-10">
					<?php if($w != 'u') { ?><input type="text" id="mb_id" name="mb_id" value="<?php echo $row["mb_id"]; ?>" required class="form-control" placeholder="아이디/영문소문자+숫자 조합 4자이상">
					<?php } else { echo $row["mb_id"]; ?>
						<input type="hidden" name="mb_id" value="<?php echo $row["mb_id"]; ?>">
					<?php } ?>
				</div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">비밀번호</label>
				<div class="col-sm-10"><input type="password" id="mb_pw" name="mb_pw" value="" class="form-control" placeholder="비밀번호(신규/변경시에만 입력)"></div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">업체명</label>
				<div class="col-sm-10"><input type="text" id="mb_bizname" name="mb_bizname" value="<?php echo $partner["mb_bizname"]; ?>" readonly class="form-control readonly"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">담당자(표시명)</label>
				<div class="col-sm-10"><input type="text" id="mb_name" name="mb_name" value="<?php echo $row["mb_name"]; ?>" required class="form-control" placeholder=""></div>
			</div>
			
			<div class="row"><hr/></div>
			
			<div class="row"><label class="col-sm-2 control-label">가입일</label>
				<div class="col-sm-10"><?php echo $row["mb_joindate"]; ?></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">최종로그인</label>
				<div class="col-sm-10"><?php echo $row["mb_lastlogin"]; ?></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">상태</label>
				<div class="col-sm-10"><?php echo get_use_select("mb_use",$row["mb_use"]); ?></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">표시</label>
				<div class="col-sm-10"><?php echo get_use_select("mb_display",$row["mb_display"]); ?></div>
			</div>
			
		</div>
		<br class="clear"/>
		<div class="row">
			<div class="col-sm-6"><button class="btn <?php echo $btnclass; ?> btn-block col-sm-4" type="submit"><?php echo $btntxt; ?></button></div>
			<div class="col-sm-6"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="history.back();">돌아가기</button></div>
		</div>
    </form>

</div>

<script>
$(function () {
	commonjs.selectNav("navbar", "partner");
});

</script>

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>