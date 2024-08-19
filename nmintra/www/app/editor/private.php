<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

if($_GET['view'] != 1) {
include_once '../../header.php';
} else {
include_once '../../head.sub.php';
}

include_once JSB_PATH."/vendor/smarteditor2/editor.lib.php";

$code = $_GET['code'];
$view = $_GET['view'];

$sql = " select * from namo_private where pcode='{$code}' ";
$row = sql_fetch($sql);

//print_r2($row);

$editor_content_js = '';
//if(is_file(G5_EDITOR_PATH.'/'.$config['cf_editor'].'/autosave.editor.js'))
//	$editor_content_js = '<script src="'.G5_EDITOR_URL.'/'.$config['cf_editor'].'/autosave.editor.js"></script>'.PHP_EOL;

$editor_html = editor_html("ir1", $row['contents'], true);
$editor_js = '';
$editor_js .= get_editor_js('ir1', true);
$editor_js .= chk_editor_js('ir1', true);


?>
<!-- CONTENT START -->
<?php if($view != 1) { ?>
<div class="page-header">
	<div class="btn-div">
		<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
		<!-- a class="btn btn-success btn-sm" href="">등록</a -->
	</div>	
	<h1><?php echo $row['title'];?></h1>
</div>
<?php } ?>

<div style="max-width:1084px;">
<?php 
	if($view == 1) {
		echo $row['contents'];
	} else {
?>
	<a href="./private.php?code=<?php echo $code;?>&view=1" target="_blank">보기</a>
	<form name="fwrite" id="fwrite" action="./write_update.php" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="code" value="<?php echo $code; ?>">
	<input type="hidden" name="idx" value="<?php echo $row['idx']; ?>">

	<?php echo $editor_html;?>
	
    <div class="btn_confirm write_div">
        <button type="submit" id="btn_submit" accesskey="s" class="btn_submit btn">작성완료</button>
    </div>
	
	</form>
	
<?php
	}
?>
</div>

<iframe id="hiddenframe" style="display:none;"></iframe>

<script>
$(function () {
    //////////////////////////////////////////////////////////////////////////////////////
    commonjs.selectNav("navbar", "pdsupload");
});
</script>
<script type="text/javascript">

    function fwrite_submit(f)
    {
        <?php echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>
		
        document.getElementById("btn_submit").disabled = "disabled";

        return true;
    }

</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>