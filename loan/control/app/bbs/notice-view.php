<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

$write_table = "bbs_write";
$bo_table = "notice";

$sql = "select * from {$write_table} where bo_table='{$bo_table}' and wr_id = '{$wr_id}' limit 1";
$view = sql_fetch($sql);
$view['wr_subject'] = clean_xss_tags(trim($view['wr_subject']));
$view['wr_content'] = clean_xss_tags(trim($view['wr_content']));

//print_r2($view);

?>
<style>
.form-group .row div.col-sm-10 { padding-top:10px; }
</style>

<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<a class="btn btn-default btn-sm" href="./notice-list.php">목록</a>
	<a class="btn btn-success btn-sm" href="./notice-write.php?w=u&wr_id=<?php echo $wr_id;?>">수정</a></div>
	<h1>공지사항</h1>
</div>

<!-- CONTENT START -->

<div style="padding:15px;margin:auto;">

	   <div class="form-group col-sm-8">
	   
			<div class="row"><label class="col-sm-2 control-label">제목</label>
				<h4 class="col-sm-10"><?php echo (!empty($view['wr_ca']))?"[".$view['wr_ca']."] ":""; ?><?php echo $view['wr_subject']; ?></h4>
			</div>
			<div class="row"><label class="col-sm-2 control-label">내용</label>
				<div class="col-sm-10" style="min-height:100px;"><?php echo $view['wr_content']; ?></div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">등록일시</label>
				<div class="col-sm-10"><?php echo $view['wr_datetime']; ?></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">공개여부</label>
				<div class="col-sm-10"><?php echo ($view['wr_open'])?"공개":"비공개"; ?></div>
			</div>
		</div>
</div>

<script>
$(function () {
	commonjs.selectNav("navbar", "notice");
		
});
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>

