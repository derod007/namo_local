<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../header.php';

$w = $_GET['w'];

$wr_id = $_GET['wr_id'];
if(!$wr_id) {
	alert("잘못된 접근입니다.");
}
$sql = "select * from write_loaninfo where wr_id = '{$wr_id}' limit 1";
$row = sql_fetch($sql);

if(!$row['wr_id']) {
	alert('해당되는 데이터가 없습니다');
}

$sql = "select * from file_loaninfo where wr_id = '{$wr_id}' order by file_no";
$result = sql_query($sql);

$btntxt = "파일관리";
$btnclass = "btn-primary";

$pjfile = get_writefile($wr_id);
?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>대출신청 <?php echo $btntxt; ?></h1>
</div>

<!-- CONTENT START -->

<div style="padding:15px;margin:auto;">
	 
	<table class="table table-bordered bs-xs-table jsb-info">
		<tr>
			<th class="col-sm-2 thbold">담보구분</th>
			<td class="col-sm-4 align-center">
				<?php 
					if($row["wr_ca"] =="B1") { 
						echo "부동산 담보(지분)";
					} else if($row["wr_ca"] =="B") { 
						echo "부동산 담보";
					} else {
						echo "기타";
					}
				?>
			</td>
			<th class="col-sm-2 thbold">진행상태</th>
			<td class="col-sm-4 align-center"><?php echo $status_arr[$row['wr_status']]; ?></td>
		</tr>
		<tr>
			<th class="col-sm-2 thbold">제목</th>
			<td class="col-sm-10 align-left" colspan="3"><?php echo $row["wr_subject"]; ?></td>
		</tr>
		<tr>
			<th class="col-sm-2 thbold">담보주소</th>
			<td class="col-sm-10 align-left" colspan="3"><?php echo $row["wr_addr1"]." ".$row["wr_addr3"]; ?><br/><?php echo $row["wr_addr_ext1"]; ?></td>
		</tr>
	</table>


<table class="table table-bordered bs-xs-table jsb-info">
	<tr>
		<th class="col-sm-2 thbold">파일업로드</th>
		<td>
				<form name="fpfilereg" id="fpfilereg" method="post" enctype="multipart/form-data" action="./loaninfo-upload.php"
				 class="form-inline">
					<input type="hidden" name="w" value="file">
					<input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
					
					<select id="category" name="category[]" class="form-control">
						<option value="">선택</option>
						<option value="등기부등본">등기부등본</option>
						<option value="건축물/토지대장">건축물/토지대장</option>
						<option value="일반">일반</option>
					</select>
					
					<input type="file" id="uploadfile" name="uploadfile[]" value="" required class="form-control">
					<button class="btn btn-success" type="submit">파일등록</button>
				</form>

		</td>
	</tr>
	<tr>
		<th class="col-sm-2 thbold">첨부파일</th>
		<td>
		<?php
			$cnt = $pjfile['count'];
			if ($cnt) {
				?>
				<!-- 첨부파일 시작 { -->
				<div id="project_v_file">
					<table class="table">
					<?php // 가변 파일
						//print_r2($pjfile);
					foreach ($pjfile as $i => $file) {
						if (isset($file['source']) && $file['source']) {
					?>
						<tr style="border-bottom: 1px solid #ddd">
							<td style="padding-left: 10px;padding-right:10px;">
								<?php echo "[" . $file['category'] . "] "; ?>
							</td>
							<td style="padding-left: 10px;padding-right:10px;">
								<a href="<?php echo $file['href']; ?>" class="view_file_download">
									<strong>
										<?php echo $file['source']; ?></strong>
									( <?php echo $file['size']; ?> ) <i class="fa fa-download" aria-hidden="true"></i></a>
									<?php echo $file['memo']; ?>	</td>
							<td style="padding-left: 10px;padding-right:10px;"><span class="project_v_file_date">
									<?php echo substr($file['datetime'], 0, 16); ?></span></td>
							<?php if($row['wr_status'] <= 1) { ?><td><span class="btn btn-danger btn-xs project_file_del" data-file-no='<?php echo $i; ?>' data-pid='<?php echo $wr_id; ?>'>삭제</span></td><?php } ?>
						</tr>
					<?php
						}
					}
					?>
					</table>
				</div>
				<!-- } 첨부파일 끝 -->
		<?php 
			} else {
				echo "<span style='color:gray'>등록된 첨부파일이 없습니다.</span>";
			}
		?>
		</td>
	</tr>
</table>

		<br class="clear"/>
		<div class="row">
			<div class="col-sm-6"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="document.location.href='./loaninfo-write.php?w=u&wr_id=<?php echo $wr_id;?>';">신청정보</button></div>
			<div class="col-sm-6"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="document.location.href='./loaninfo-list.php';">목록으로</button></div>
		</div>

</div>

<script>
$(function () {
	commonjs.selectNav("navbar", "loaninfo");
	

	$('.project_file_del').click(function () {
		if (confirm("파일을 삭제하시겠습니까?")) {
			var file_no = $(this).attr("data-file-no");
			var delform = $('<form></form>');
			delform.attr('action', './loaninfo-upload.php');
			delform.attr('method', 'post');
			delform.appendTo('body');
			delform.append('<input type="hidden" name="w" value="filedel" />');
			delform.append('<input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>" />');
			delform.append('<input type="hidden" name="file_no" value="' + file_no + '" />');
			delform.submit();
		}
	});
	
});

</script>

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>