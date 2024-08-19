<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://nmintra.event-on.kr/app/p2p/publicofficial_view.php?grcode=3
include_once '../../header.php';

$grcode = $_GET['grcode'];

if(!$grcode) {
	alert('잘못된 접근입니다.');
	die();	
}

$sql = " select count(*) cnt, reg_date from namo_member where 1=1";
$row = sql_fetch($sql);
$namo_count = $row['cnt'];
$namo_update = $row['reg_date'];

$sql = " select count(*) cnt, filename from p2p_publicofficial where grcode = '{$grcode}'";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$filename = $row['filename'];

?>
<!-- CONTENT START -->

<div class="page-header">
<p align='right'>
	<a href="./publicofficial.php" class="btn btn-default">목록으로</a>
</p>
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>공직자윤리위원회 <span style="font-size:0.7em;"><?php echo $filename; ?></span></h1>
</div>

<!--
<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-2">
				<label>등록일</label>
				<input type="text" name="reg_date" id="reg_date" value="<?php echo $reg_date;?>"  class="form-control datepicker">
			</div>
			<div class="col-sm-3">
				<label>파일명</label>
				<input type="text" name="filename" id="filename" value="<?php echo $filename;?>"  class="form-control">
			</div>
			<div class="col-sm-1">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
			<div class="col-sm-6">
			</div>
		</div>
	</form>
</div>
-->

<!--p align='left'>나모회원</p -->
<table id="datatotal" class="table table-striped table-bordered jsb-table1">
	<tr>
		<th class="col-sm-2">회원수</th>
		<td id="namo_count" class="col-sm-2"><?php echo number_format($namo_count); ?></td>
		<th class="col-sm-2">업데이트일시</th>
		<td id="namo_update" class="col-sm-2"><?php echo substr($namo_update, 0, 16); ?></td>
		<td class="col-sm-2"><a href="./namodata_xlsimport.php" class="btn btn-success btn-sm btn-block">회원정보갱신(XLS)</a></td>
	</tr>
</table>

<!--p align='left'>조회요청 데이터</p -->
<table id="datatotal" class="table table-striped table-bordered jsb-table1">
	<tr>
		<th class="col-sm-2">총 데이터수</th>
		<td id="total_count" class="col-sm-2"><?php echo number_format($total_count); ?></td>
		<th class="col-sm-2"></th>
		<td id="total_dist" class="col-sm-2"></td>
		<td class="col-sm-2"></td>
	</tr>
</table>

<?php
$log_sql = "";

// 이름일치 / 생년월일 일치
$sql = " 
select a.*, b.cnt from p2p_publicofficial as a 
	left join (select nm_mbname, nm_birth, count(*) as cnt from namo_member group by nm_mbname, nm_birth ) b on b.nm_mbname = a.d_name  and b.nm_birth = LEFT(a.d_jumin,7)

where a.grcode = '{$grcode}' 
order by a.d_num asc
";

// 이름 일치
$sql = " 
select a.*, b.cnt from p2p_publicofficial as a 
	left join (select nm_mbname, nm_birth, count(*) as cnt from namo_member group by nm_mbname ) b on b.nm_mbname = a.d_name 

where a.grcode = '{$grcode}' 
order by a.d_num asc
";

// , nm_birth
// and b.nm_birth = LEFT(a.d_jumin,7)
$log_sql .= $sql.PHP_EOL;
$result = sql_query($sql);
$no = $total_count;
?>

<div id="div_loading" style="text-align:center; height:600px;">
	<h1 style="margin-top:100px;">데이터를 읽고 있는중 입니다. 기다려 주세요!!! </h1>
</div>

<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%">
	<thead>
	<tr>
		<th>일련번호</th>
		<th>주민등록번호</th>
		<th>성명</th>
		<!-- 
		<th>기준일</th>
		<th>요구자소속</th>
		<th>요구자성명</th>
		<th>요구자전화번호</th>
		-->
		<th>채무내역</th>
		<th>투자내역</th>
		<th>이름일치</th>
		<th>비고</th>
	</tr>
	</thead>
<?php

while($row=sql_fetch_array($result)){
	$row['no'] = $no;
	$row['d_jumin'] = substr($row['d_jumin'],0,7)."******";
	$row['nm_invest'] = "-";
	$row['nm_loan'] = "-";
	$row['info'] = "";
	if($row['cnt'] > 0) {
		//$sql1 = "select * from namo_member where nm_mbname = '".$row['d_name']."' and nm_birth like '".substr($row['d_jumin'],0,7)."%' ";
		$sql1 = "select * from namo_member where nm_mbname = '".$row['d_name']."' ";
		//$log_sql .= $sql1.PHP_EOL;
		$res1 = sql_query($sql1);
		$info_str = "";
		while($row1=sql_fetch_array($res1)) {
			
			if(substr($row1['nm_birth'],2,6) == substr($row['d_jumin'],0,6)) {
				$info_str .= $row1['nm_mbnum'].") 이름/생년월일 일치<br/>";
				$info_str .= "투자금액 : ".number_format($row1['nm_invest'])." / 예치금 : ".number_format($row1['nm_deposit'])."<br/> ";
				$info_str .= "대출금액 : ".number_format($row1['nm_loan'])." ";
				$row['nm_invest'] += $row1['nm_invest'];
				$row['nm_loan'] += $row1['nm_loan'];
			} else {
				$info_str .= $row1['nm_mbnum'].") 이름 일치 / 생년월일 불일치(".substr($row1['nm_birth'],2,6).")<br/>";
				$info_str .= "투자금액 : ".number_format($row1['nm_invest'])." / 예치금 : ".number_format($row1['nm_deposit'])."<br/> ";
				$info_str .= "대출금액 : ".number_format($row1['nm_loan'])." ";
			}
			$info_str .= "<br/>";
		}
		$row['info'] = $info_str;
	}
	
	//$row['grcode'] = $row['grcode'];
?>
	<tr>
		<td><?php echo $row['d_num'];?></td>
		<td><?php echo $row['d_jumin'];?></td>
		<td><?php echo $row['d_name'];?></td>
		<!--
		<td><?php echo $row['d_rqdate'];?></td>
		<td><?php echo $row['d_rqdept'];?></td>
		<td><?php echo $row['d_rqname'];?></td>
		<td><?php echo $row['d_rqtel'];?></td>
		-->
		<td><?php echo number_format($row['nm_loan']);?></td>
		<td><?php echo number_format($row['nm_invest']);?></td>
		<td><?php echo number_format($row['cnt']);?></td>
		<td><?php echo $row['info'];?></td>
	</tr>

<?php	
	$no--;
}
?>
</table>

<?php
	//echo "<pre>".$log_sql."</pre>";
?>

<iframe id="hiddenframe" style="display:none;"></iframe>

<script>
$(function () {
    //////////////////////////////////////////////////////////////////////////////////////
    commonjs.selectNav("navbar", "publicofficial");
	
    //$(".datepicker").datepicker();
	//$('#region').select2();
	
	//$('#datalist').DataTable();
    var dataTable = $('#datalist').DataTable({
        paging: true,
        searching: true,
        scrollX : true,
        ordering: true,
        "order": [
            [0, 'asc']
        ],
        orderable: true,
        info: true,
		
    });
	$('#div_loading').hide();
	
});
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>
