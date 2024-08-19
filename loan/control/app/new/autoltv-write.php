<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

include_once '../../header.php';

/*
$setcode_arr = array(
	"realave2020" => "2020년 실거래평균가",
	"realave2019" => "2019년 실거래평균가",
	"realave3y" => "3년 실거래평균가",
	"realave2y" => "2년 실거래평균가",
	"realave1y" => "1년 실거래평균가",
);

// array 를 SELECT 형식으로 얻음  --- inc/data.inc.php 에 저장 2023-09-04
function get_array_select($arr, $name, $selected='', $event='')
{
    $str = "<select id=\"$name\" name=\"$name\" $event>\n";
	$i = 0;
	foreach($arr as $k => $v) {
        if ($i == 0) $str .= "<option value=\"\">선택</option>";
        $str .= option_selected($k, $selected, $v);
		$i++;
    }
    $str .= "</select>";
    return $str;
}

*/

$list_table = "region_ltvconf";

$w = $_GET['w'];

if($w == 'u') {
	$ltv_id = $_GET['ltv_id'];
	if(!$ltv_id) {
		alert("잘못된 접근입니다.");
	}
	
	$sql = " select * from {$list_table} where ltv_id='{$ltv_id}' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['ltv_id']) {
		alert('해당되는 데이터가 없습니다');
	}
	$btntxt = "수정";
	$btnclass = "btn-warning";
	
} else {
	$btntxt = "등록";
	$btnclass = "btn-primary";
}
?>
<!-- CONTENT START -->
<div class="page-header">
	<h1>지역별 LTV 자동한도 설정</h1>
</div>

<div style="padding:15px;margin:auto;">
	<form id="fwrite" name="fwrite" action="/app/new/autoltv-act.php" method="post" class="jsb-form">
	 <input type="hidden" name="w" value="<?php echo $w; ?>">
	 <input type="hidden" name="ltv_id" value="<?php echo $ltv_id; ?>">
	   <div class="form-group col-sm-12">
	   
			<div class="row"><label for="ltv_rcode" class="col-sm-2 control-label">지역</label>
				<div class="col-sm-10"><?php echo get_sidocode_select("ltv_rcode", $row['ltv_rcode'], ""); ?></div>
			</div>
			<div class="row"><label for="ltv_val" class="col-sm-2 control-label">LTV한도</label>
				<div class="col-sm-10"><input type="text" id="ltv_val" name="ltv_val" value="<?php echo $row["ltv_val"]; ?>" class="form-control" style="display:inline-block;width:150px" placeholder="숫자만"> %</div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">지분여부</label>
				<div class="col-sm-10"><?php echo get_array_select($ltv_part_arr, 'ltv_part', $row["ltv_part"], 'class="form-control"'); ?></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">선순위여부</label>
				<div class="col-sm-10"><?php echo get_array_select($ltv_priority_arr, 'ltv_priority', $row["ltv_priority"], 'class="form-control"'); ?></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">LTV기준선택</label>
				<div class="col-sm-10"><?php echo get_array_select($setcode_arr, 'ltv_setcode', $row["ltv_setcode"], 'class="form-control"'); ?></div>
			</div>
			<div class="row"><label for="ltv_interest" class="col-sm-2 control-label">자동이율</label>
				<div class="col-sm-10"><input type="text" id="ltv_interest" name="ltv_interest" value="<?php echo $row["ltv_interest"]; ?>" class="form-control" style="display:inline-block;width:150px" placeholder="숫자만(소수점 1자리)"> %</div>
			</div>
			
			<div class="row"><label for="ltv_use" class="col-sm-2 control-label">사용여부</label>
				<div class="col-sm-10">
					<input type="radio" id="control_01" name="ltv_use" value="1" required <?php echo ($row['ltv_use']=='1')?"checked":"";?>>
					<label for="control_01">사용 &nbsp;</label>
					<input type="radio" id="control_02" name="ltv_use" value="0" required <?php echo ($row['ltv_use']=='0')?"checked":"";?>>
					<label for="control_02">미사용 &nbsp;</label>
				</div>
			</div>
		
		</div>

		<br class="clear"/>
			<!-- div class="row">
				<div class="col-sm-12 blue"> &nbsp; </div>
			</div -->
			<div class="row">
				<div class="col-sm-4"><button class="btn <?php echo $btnclass; ?> btn-block col-sm-4" type="submit"><?php echo $btntxt; ?></button></div>
				<div class="col-sm-4"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="document.location.href='./autoltv-list.php';">목록으로</button></div>
			</div>

	</form>

</div>


<script>
$(function () {
	commonjs.selectNav("navbar", "ltvconf");

});
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>