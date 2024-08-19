<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../header.php';

$chk_pt_idx_json = "[]";
if(isset($_GET['pt_idx'])) {
	$pt_idx = $_GET['pt_idx'];
	$chk_pt_idx = array();
	if(count($pt_idx) > 0) {
		foreach($pt_idx as $v) {
			$chk_pt_idx[] = $v; 
		}
		$chk_pt_idx_json = json_encode($chk_pt_idx, JSON_NUMERIC_CHECK);
		set_session("ss_pt_idx", implode(",",$chk_pt_idx));
	}
} else {
	$ss_pt_idx = get_session("ss_pt_idx");
	if($ss_pt_idx) {
		//echo "ss_pt_idx : ".$ss_pt_idx;
		$chk_pt_idx = explode(",",$ss_pt_idx);
		$chk_pt_idx_json = json_encode($chk_pt_idx, JSON_NUMERIC_CHECK);
	}
}

if(isset($_GET['chk_reset'])) {
	if($_GET['chk_reset'] == '1') {
		set_session("ss_pt_idx", '');
		$chk_pt_idx = array();
		$chk_pt_idx_json = "[]";
	}
}

?>
<style>
.ui-widget {
    font-family: font-family: "Pretendard Variable", Pretendard, -apple-system, BlinkMacSystemFont, system-ui, Roboto, "Helvetica Neue", "Segoe UI", "Apple SD Gothic Neo", "Noto Sans KR", "Malgun Gothic", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", sans-serif;
}
.info_window {display:none;}
.info_window p {margin:0; font-size:0.9em;}
.btn_infowin {cursor:pointer;}

.ui-widget-content a {
    color: #337ab7;
}
.ptlist {
	display: flex; flex-wrap: wrap; margin: 0; padding: 0; list-style: none; margin-bottom:10px;
}
.ptlist li {
	display:inline-block;
	position: relative;
	letter-spacing: 1px;
   font-size: 14px;
   padding:0 5px;
   word-break: keep-all;
}

.w380el {
  display: block;
  min-width:300px;
  max-width:380px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

</style>

<!-- CONTENT START -->

<div class="page-header">
	<h1>진행요청 목록 <a class="btn btn-default btn-sm" href="./loanproc-list.php"><i class="fas fa-sync-alt"></i></a></h1>
</div>

<?php
$partners = get_partnerlist();

$sql = "select count(*) as cnt from write_loaninfo where wr_status = '30'";
$prcnt = sql_fetch($sql);

$sql = " select *, (select reg_date from log_action where wr_id = write_loaninfo.wr_id and next_status='30' limit 1 ) as procdate from write_loaninfo where wr_status = '30' order by procdate desc";
$result = sql_query($sql);
$data = array();
$i = 0;
while($row=sql_fetch_array($result)) {
	$data[] = $row;
	$i++;
}
if($i > 0) {
?>
<h4>진행요청 (전체 <?php echo $prcnt['cnt']?> 건)</h4>
<table id="datalist2" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%">
  <thead>
    <tr role="row">
      <th class="align-center">요청일시</th>
      <th class="align-center">진행상태</th>
      <th class="align-center">등록일시</th>
      <th class="align-center">등록업체</th>
      <th class="align-center">제목</th>
      <th class="align-center">담보주소</th>
      <th class="align-center">한도</th>
      <th class="align-center">금리</th>
      <th class="align-center">부대조건</th>
    </tr>
  </thead>
  <tbody>
<?php
	foreach($data as $k => $v) {
?>  
    <tr role="row">
      <td class=" align-center"><?php echo substr($v['procdate'],5,11);?></td>
      <td class="align-center"><span class='magenta'><B><?php echo $status_arr[$v['wr_status']];?></B></span></td>
      <td class=" align-center"><?php echo substr($v['wr_datetime'],5,11);?></td>
      <td class="align-center"><?php echo $partners[$v['pt_idx']]['mb_bizname'];?></td>
      <td class="align-center"><a href="./loaninfo-write.php?w=u&wr_id=<?php echo $v['wr_id'];?>"><?php echo $v['wr_subject'];?></a></td>
      <td><?php echo $v['wr_addr1']." ".$v['wr_addr3']." ".$v['wr_addr2'];?></td>
      <td><?php echo $v['jd_amount'];?></td>
      <td><?php echo $v['jd_interest'];?></td>
      <td><?php echo $v['jd_condition'];?></td>
    </tr>
<?php
	}
?>	
	</tbody>
</table>
<?php
}
?>


<iframe id="hiddenframe" style="display:none;"></iframe>

<script>
$(function () {
	commonjs.selectNav("navbar", "loanproc");
});
</script>

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>