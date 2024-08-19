<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../header.php';

$idx = $_GET['idx'];
if(!$idx) {
	alert("잘못된 접근입니다.");
}

$sql = " select * from partner_member where idx = '{$idx}' ";
$partner = sql_fetch($sql);
if(!$partner['idx']) {
	alert("잘못된 접근입니다.");
}
$parent_id = $idx;

$where_sql = " where is_sub=1 and parent_id='{$parent_id}' ";

$sql = " select count(*) as cnt from partner_member {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;

$sql = " select * from partner_member {$where_sql} order by idx desc ";
$result = sql_query($sql);

$data = array();
$i = 0;
?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<a class="btn btn-success btn-sm" href="./partner-sub-write.php?pt_idx=<?php echo $parent_id; ?>">서브아이디 등록</a></div>
	<h1>파트너 서브아이디 목록 (<?php echo $partner['mb_bizname'];?>)</h1>
</div>

<div class="table-responsive">
<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%; max-width:600px;">
<thead>
	<tr role="row">
		<th>No</th>
		<th>아이디</th>
		<th>업체명</th>
		<th>담당자명</th>
		<th>등록일</th>
		<th>상태</th>
		<th>관리</th>
	</tr>
</thead>
<tbody>
<?php
	if($total_count == 0) {
?>
<tr role="row" class="odd">
	<td class="align-center" colspan="7" style="height:150px;vertical-align:middle;font-size:1.2em;">등록된 서브아이디가 없습니다.</td>
</tr>
<?php
	} else {
		$no = $total_count;
		while($row=sql_fetch_array($result)){
?>
	<tr>
		<td class="text-center"><?php echo $no;?></td>
		<td class="text-center"><?php echo $row['mb_id'];?></td>
		<td class="text-center"><?php echo $row['mb_bizname'];?></td>
		<td class="text-center"><?php echo $row['mb_name'];?></td>
		<td class="text-center"><?php echo $row['mb_joindate'];?></td>
		<td class="text-center"><?php echo ($row['mb_use']=='1')?"사용":"<font color='red'>미사용</font>";?></td>
		<td class="text-center"><a href='./partner-sub-write.php?w=su&pt_idx=<?php echo $parent_id;?>&idx=<?php echo $row['idx'];?>'>수정</a></td>
	</tr>	
<?php
			$no--;
		}
	}
?>
</tbody>
<tfoot>
</tfoot>
</table>
</div>

<iframe id="hiddenframe" style="display:none;"></iframe>

<script>
$(function () {
	commonjs.selectNav("navbar", "partner");
		
	$('#datalist').DataTable({
		responsive: true,
		paging: true,
		ordering: false,
		info: true
	});
	
});
</script>

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>