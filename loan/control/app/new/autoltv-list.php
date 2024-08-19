<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

$list_table = "region_ltvconf";
$where_sql = "where 1";
$orderby = "order by ltv_rcode asc, ltv_id desc";

$sql = " select count(*) as cnt from {$list_table} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;

//if(!isset($start)) $start = 0;
//if(!isset($length)) $length = $config['rows'];

$sql = " select * from {$list_table} {$where_sql} {$orderby} ";
//echo "<div>".$sql."</div>";
$result = sql_query($sql);
?>
<!-- CONTENT START -->
<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<a class="btn btn-warning btn-sm" href="./autoltv-write.php">등록</a></div>
	<h1>지역별 LTV 자동한도 설정 목록</h1>
</div>

<div class="table-responsive">
	<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%; max-width:600px;">
	<thead>
	<tr>
		<th>번호</th>
		<th>지역</th>
		<th>지분여부</th>
		<th>선순위여부</th>
		<th>LTV</th>
		<th>시세기준</th>
		<th>자동이율</th>
		<th>사용</th>
		<th>등록/수정일</th>
		<th>ltv_id</th>
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
		<td class="text-center"><?php echo $no;?></td>
		<td class="text-center"><?php echo $row['ltv_rname'];?></td>
		
		<td class="text-center"><?php 
				if($row['ltv_part'] == "A") echo "전체지분"; 
				else if($row['ltv_part'] == "H") echo "50% 지분"; 
				else if($row['ltv_part'] == "P") echo "기타 지분"; 
				else echo "";
			?></td>
		<td class="text-center"><?php 
				if($row['ltv_priority'] == "F") echo "선순위"; 
				else if($row['ltv_priority'] == "A") echo "후순위"; 
				else echo "";
			?></td>
		<td class="text-center"><?php echo number_format($row['ltv_val']);?> %</td>
		<td class="text-center"><?php echo $setcode_arr[$row['ltv_setcode']];?></td>
		<td class="text-center"><?php echo $row['ltv_interest'];?> %</td>
		<td class="text-center"><?php echo ($row['ltv_use']=='1')?"사용":"<font color='red'>미사용</font>";?></td>
		<td class="text-center"><?php echo $row['ltv_datetime'];?></td>
		<td class="text-center"><?php echo $row['ltv_id'];?></td>
		<td class="text-center"><a href='./autoltv-write.php?w=u&ltv_id=<?php echo $row['ltv_id'];?>'>수정</a></td>
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
	commonjs.selectNav("navbar", "ltvconf");
	
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