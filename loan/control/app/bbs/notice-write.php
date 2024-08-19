<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

include_once '../../header.php';
include_once JSB_EDITOR_PATH.'/editor.lib.php';

$write_table = "bbs_write";
$bo_table = "notice";

$w = $_GET['w'];

if($w == 'u') {
	$wr_id = $_GET['wr_id'];
	if(!$wr_id) {
		alert("잘못된 접근입니다.");
	}
	
	$sql = " select * from {$write_table} where wr_id='{$wr_id}' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['wr_id']) {
		alert('해당되는 데이터가 없습니다');
	}
	$btntxt = "수정";
	$btnclass = "btn-warning";
	
} else {
	$btntxt = "등록";
	$btnclass = "btn-primary";
	$row['wr_open'] = "1";
}

$is_dhtml_editor = true;
//if(is_file(JSB_PATH.'/vendor/smarteditor2/autosave.editor.js'))
//        $editor_content_js = '<script src="'.JSB_URL.'/vendor/smarteditor2/autosave.editor.js"></script>'.PHP_EOL;
	
$editor_html = editor_html('wr_content', $row['wr_content'], $is_dhtml_editor);
$editor_js = '';
$editor_js .= get_editor_js('wr_content', $is_dhtml_editor);
$editor_js .= chk_editor_js('wr_content', $is_dhtml_editor);

// 임시 저장된 글 수
//$autosave_count = autosave_count($member['mb_id']);

?>
<!-- CONTENT START -->
<div class="page-header">
	<h1>공지사항 <?php echo $btntxt;?></h1>
</div>

<div style="padding:15px;margin:auto;">
	<form id="fwrite" name="fwrite" action="/app/bbs/write_update.php" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off" class="jsb-form">
	 <input type="hidden" name="w" value="<?php echo $w; ?>">
	 <input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
	 <input type="hidden" name="bo_table" value="<?php echo $bo_table; ?>">
	   <div class="form-group col-sm-12">
			
			<div class="row"><label for="wr_subject" class="col-sm-2 control-label">분류</label>
				<div class="col-sm-10">
					<select id="wr_ca" name="wr_ca" class="form-control">
						<?php
						$wr_ca_arr = array("공지", "참고");
						//echo option_selected('', $_GET['wr_ca'], '전체');
						foreach($wr_ca_arr as $k => $v) {
							echo option_selected($v, $_GET['wr_ca'], $v);
						}
						?>
					</select>
				</div>
			</div>
			
			<div class="row"><label for="wr_subject" class="col-sm-2 control-label">제목</label>
				<div class="col-sm-10"><input type="text" id="wr_subject" name="wr_subject" value="<?php echo $row["wr_subject"]; ?>" class="form-control" placeholder="제목"></div>
			</div>
			<div class="row"><label for="wr_content" class="col-sm-2 control-label">내용</label>
				<div class="col-sm-10">
					<?php echo $editor_html; // 에디터 사용시는 에디터로, 아니면 textarea 로 노출 ?>
					<!-- <textarea  id="wr_content" name="wr_content" class="form-control" style="height:350px;"><?php /* echo $row["wr_content"]; */ ?></textarea> -->
				</div>
			</div>
			<div class="row"><label for="wr_open" class="col-sm-2 control-label">공개여부</label>
				<div class="col-sm-10">
					<input type="radio" id="control_01" name="wr_open" value="1" required <?php echo ($row['wr_open']=='1')?"checked":"";?>>
					<label for="control_01">공개 &nbsp;</label>
					<input type="radio" id="control_02" name="wr_open" value="0" required <?php echo ($row['wr_open']=='0')?"checked":"";?>>
					<label for="control_02">미공개 &nbsp;</label>
				</div>
			</div>
		
		</div>

		<br class="clear"/>
			<!-- div class="row">
				<div class="col-sm-12 blue"> &nbsp; </div>
			</div -->
			<div class="row">
				<div class="col-sm-4"><button class="btn <?php echo $btnclass; ?> btn-block col-sm-4" type="submit"><?php echo $btntxt; ?></button></div>
				<div class="col-sm-4">&nbsp;</div>
				<div class="col-sm-4"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="document.location.href='./notice-list.php';">목록으로</button></div>
			</div>

	</form>

</div>


<script>
$(function () {
	commonjs.selectNav("navbar", "notice");

});

function fwrite_submit(f)
{
	<?php echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>

	return true;
}
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>