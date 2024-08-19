<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

$ap_no = $_GET['data_no'];
if(!$ap_no) {
	alert('잘못된 접근입니다.');
	die();
}

$sql = "SELECT * FROM `{$jsb['actualprice_table']}` WHERE no='{$ap_no}' limit 1";
$data = sql_fetch($sql);

?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>아파트 연결정보</h1>
</div>

<?php
//print_r2($data);
?>
<h4>실거래가 정보</h4>
<table class="table table-striped table-bordered jsb-table">
	<tr>
		<th class="col-sm-2">거래일</th>
		<td class="col-sm-2"><?php echo $data['yyyy'].'-'.str_pad($data['mm'], 2, '0',STR_PAD_LEFT).'-'.str_pad($data['dd'], 2, '0',STR_PAD_LEFT);?></td>
		<th class="col-sm-2">단지명</th>
		<td class="col-sm-2"><?php echo $data['danzi'];?></td>
		<th class="col-sm-2">전용면적</th>
		<td class="col-sm-2"><?php echo $data['py'];?></td>
	</tr>
	<tr>
		<th class="col-sm-2">시군구</th>
		<td class="col-sm-2"><?php echo $data['sigungu'];?></td>
		<th class="col-sm-2">동</th>
		<td class="col-sm-2"><?php echo $data['dong'];?></td>
		<th class="col-sm-2">번지</th>
		<td class="col-sm-2"><?php echo $data['zibun'];?></td>
	</tr>
	<tr>
		<th class="col-sm-2">거래금액</th>
		<td class="col-sm-2"><?php echo number_format($data['price']).'만원';?></td>
		<th class="col-sm-2">층</th>
		<td class="col-sm-2"><?php echo $data['floor'];?></td>
		<th class="col-sm-2">동코드</th>
		<td class="col-sm-2"><?php echo $data['region_code'];?></td>
	</tr>
</table>

<h4>KB 단지 정보</h4>
<div class="form-box jsb-form">
<?php
$sql = "SELECT * FROM `{$jsb['kbapt_link_table']}` WHERE ap_region='{$data['region_code']}' and ap_danzi='{$data['danzi']}' and ap_zibun='{$data['zibun']}' limit 1";
$lnk = sql_fetch($sql);
if($lnk['danzi']) {
?>
<p>기존 연결된 정보</p>
<table class="table table-striped table-bordered jsb-table">
	<tr>
		<th class="col-sm-2">동</th>
		<th class="col-sm-2">단지명</th>
		<th class="col-sm-2">KBcode</th>
	</tr>
	<tr>
		<td class="col-sm-2"><?php echo $lnk['dong'];?></td>
		<td class="col-sm-2"><?php echo $lnk['danzi'];?></td>
		<td class="col-sm-2"><?php echo $lnk['kbcode'];?></td>
	</tr>
</table>
<?php
}
?>

	<form name="faptlink" id="faptlink" method="post" class="form-horizontal" action="./realprice_aptlink-act.php" novalidation autocomplete="off">
		<input type="hidden" name="w" value="<?php echo $w; ?>">
		<input type="hidden" name="ap_no" value="<?php echo $ap_no; ?>">
		<input type="hidden" name="sigungu" value="<?php echo $data['sigungu']; ?>">
		<input type="hidden" name="dong" id="dong" value="<?php echo $data['dong']; ?>">
		<input type="hidden" name="zinun" value="<?php echo $data['zinun']; ?>">
		<input type="hidden" name="danzi" value="<?php echo $data['danzi']; ?>">
		
		<div class="form-group">
			<label for="kbapt_id" class="col-sm-2 control-label">KB아파트</label>
			<div class="col-sm-10">
				<select class="form-control" name="kbapt_id" id="kbapt_id" required>
					<option value="">선택</option>
				</select>
				<p>※ 번지수를 기준으로 확인하시기 바랍니다. </p>
			</div>
		</div>
		<div class="form-group"><label class="col-sm-2 control-label">비고</label>
			<div class="col-sm-10"><input type="text" id="memo" name="memo" value="<?php echo $row["memo"]; ?>" class="form-control"></div>
		</div>
		<hr>
		<div class="align-center">
			<button class="btn btn-primary" type="submit">저장</button>
		</div>
	</form>
</div>

<script>

$(function () {
		
    commonjs.selectNav("navbar", "realprice_list");

	// 새로 params를 정의 재정의
	var params = $("#faptlink").serialize();
	
	$.ajax({
		type: 'get',
		url: '/api/search_kbapt.php',
		data: params,
		dataType: 'json',
		success: function (data) {
			
			var items = data.results;
			if (items !== null) {
				$.each(items, function (k, v) {
					$('#kbapt_id').append('<option value="' + v.idx + '">' + v.kbcode + ' : ' + v.text + '</option>');
				});
				$('#kbapt_id').select2({ width: '100%' });
				$('#select2-kbapt_id-container').addClass("required");
			}

		}
	});

})
	
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>

