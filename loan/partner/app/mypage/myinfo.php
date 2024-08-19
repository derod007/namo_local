<?php
include_once '../../header.php';


?>
<!-- CONTENT START -->

<div class="page-header">
  <h1>사용자정보 <small></small></h1>
</div>

<div></div>

<div>
	<table class="table table-striped table-bordered">
		<tr>
			<th class="col-sm-2">업체명</th>
			<td class="col-sm-10"><?php echo $member["mb_bizname"];?></td>
		</tr>
		<tr>
			<th class="col-sm-2">담당자</th>
			<td class="col-sm-10"><?php echo $member["mb_name"];?></td>
		</tr>
		<tr>
			<th class="col-sm-2">로그인 ID</th>
			<td class="col-sm-10"><?php echo $member["mb_id"];?></td>
		</tr>
		<tr>
			<th class="col-sm-2">등록일</th>
			<td class="col-sm-10"><?php echo $member["mb_joindate"];?></td>
		</tr>
	</table>

<p class="help-block"></p>
</div>



<script>
$(function () {
    commonjs.selectNav("navbar", "mypage_info");
})
</script>

<!-- CONTENT END -->

<?php
include_once '../../footer.php';
?>