<?php
//error_reporting(E_ALL);
ini_set("display_errors", 0);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

$list_table = "region_preferential";
$where_sql = "where 1";
$orderby = "order by rp_id desc";

$sql = " select count(*) as cnt from {$list_table} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select * from {$list_table} {$where_sql} {$orderby} ";
//echo "<div>".$sql."</div>";
$result = sql_query($sql);
?>
<!-- CONTENT START -->
<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<a class="btn btn-warning btn-sm" href="./preferential-write.php">등록</a></div>
	<h1>소액임차보증금 우선변제금 목록</h1>
</div>

<div class="table-responsive">
	<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%; max-width:600px;">
	<thead>
	<tr>
		<th>번호</th>
		<th>지역</th>
		<th>보증금 기준금액</th>
		<th>소액임차보증금</th>
		<th>사용</th>
		<th>등록/수정일</th>
		<th>관리</th>
	</tr>	
	</thead>
	<tbody>
	<?php
	$no = $total_count - $start;
	while($row=sql_fetch_array($result)){
		//print_r2($row);
	?>
	<tr>
		<td class="text-center"><?php echo $row['rp_id'];?></td>
		<td class="text-center"><?php echo $row['rp_rname'];?></td>
		<td class="text-center"><?php echo number_format($row['rp_deposit_amt']);?> 만원</td>
		<td class="text-center"><?php echo number_format($row['rp_repay_amt']);?> 만원</td>
		<td class="text-center"><?php echo ($row['rp_use']=='1')?"사용":"<font color='red'>미사용</font>";?></td>
		<td class="text-center"><?php echo $row['rp_datetime'];?></td>
		<td class="text-center"><a href='./preferential-write.php?w=u&rp_id=<?php echo $row['rp_id'];?>'>수정</a></td>
	</tr>	
	<?php	
		$no--;
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
	commonjs.selectNav("navbar", "preferential");
	
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