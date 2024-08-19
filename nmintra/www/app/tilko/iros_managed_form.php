<?php
include_once '../../header.php';



if($data_no) {
	$w = 'u';
	$sql = " select * from tilko_managed_data where idx='{$data_no}' limit 1";
    $row = sql_fetch($sql);
	
} else {
	$w = '';
	//alert("잘못된 접근입니다.");
}
?>
<!-- CONTENT START -->

<div class="page-header">
  <h1>IROS 등기관리 등록/수정 <small></small></h1>
</div>

<div class="form-box jsb-form">
<form name="fcustomreg" id="fcustomreg" method="post" class="form-horizontal" action="./iros_managed-act.php">
<input type="hidden" name="w" value="<?php echo $w;?>">
<input type="hidden" name="data_no" value="<?php echo $row["idx"];?>">

<div class="" style="padding:10px; line-height:20px; font-size:1.1em;">
	- <a href="./iros_register.php" target="_blank"><B>등기물건주소 조회</B></a>를 먼저 진행해서 등기부상의 정확한 주소를 확인하여 고유번호를 가져와야 등록이 가능합니다.
</div>
    <div class="form-group">
		<div class="">
		  <label class="col-sm-2 control-label">저장된 주소검색</label>
        <div class="col-sm-8"><input type="text" id="searchtxt" name="searchtxt" value="" placeholder="검색어를 입력해주세요" class="form-control"></div>
		  <div class="col-sm-2"><button class="btn btn-default" type="button" id="btn_search">검색</button></div>
		</div>
		<div class="">
		  <div class="col-sm-2">&nbsp;</div>
		  <div class="col-sm-10"><select id="risuconfirm" name="risuconfirm" value="" class="form-control"><option value="">선택</option></select></div>
		</div>
    </div>
	<hr/>
    <div class="form-group"><label class="col-sm-2 control-label">고유번호(*)</label>
        <div class="col-sm-10"><input type="text" id="UniqueNo" name="UniqueNo" value="<?php echo $row["UniqueNo"];?>" required class="form-control"></div>
    </div>
    <div class="form-group"><label class="col-sm-2 control-label">구분(*)</label>
        <div class="col-sm-10"><select id="GubunCode" name="GubunCode" class="form-control" required>
			<?php
    echo option_selected('0', $row["GubunCode"], "토지");
    echo option_selected('1', $row["GubunCode"], "건물");
    echo option_selected('2', $row["GubunCode"], "집합건물");
    ?>
                </select></div>
    </div>
    <div class="form-group"><label class="col-sm-2 control-label">소재지번(*)</label>
        <div class="col-sm-10"><input type="text" id="BudongsanSojaejibeon" name="BudongsanSojaejibeon" value="<?php echo $row["BudongsanSojaejibeon"];?>" required class="form-control"></div>
    </div>
    <div class="form-group"><label class="col-sm-2 control-label">소유자명(*)</label>
        <div class="col-sm-10"><input type="text" id="Owner" name="Owner" value="<?php echo $row["Owner"];?>" required class="form-control"></div>
    </div>
    <div class="form-group"><label class="col-sm-2 control-label">[관리번호] 상품명</label>
        <div class="col-sm-10"><input type="text" id="NM_pname" name="NM_pname" value="<?php echo $row["NM_pname"];?>" class="form-control"></div>
    </div>
    <div class="form-group"><label class="col-sm-2 control-label">상품번호</label>
        <div class="col-sm-10"><input type="text" id="NM_ncode" name="NM_ncode" value="<?php echo $row["NM_ncode"];?>" class="form-control"></div>
    </div>
    <div class="form-group"><label class="col-sm-2 control-label">차주명</label>
        <div class="col-sm-10"><input type="text" id="NM_borrower" name="NM_borrower" value="<?php echo $row["NM_borrower"];?>" class="form-control"></div>
    </div>
    <div class="form-group"><label class="col-sm-2 control-label">자동체크</label>
        <div class="col-sm-10"><input type="checkbox" id="autocheck" name="autocheck" value="1" <?php echo ($row["autocheck"]==1)?" checked":"";?> style="width:30px;height:30px;"></div>
    </div>
    <div class="form-group"><label class="col-sm-2 control-label">비고</label>
        <div class="col-sm-10"><textarea id="memo" name="memo" rows="3" class="form-control"><?php echo $row["memo"];?></textarea></div>
    </div>
    <div class="form-group"><label class="col-sm-2 control-label">삭제</label>
        <div class="col-sm-10"><input type="checkbox" id="delchk" name="delchk" value="1" <?php echo ($row["delchk"]==1)?" checked":"";?> style="width:30px;height:30px;">
				<span class="red" style="display:inline-block;line-height:30px; vertical-align:top;">(삭제에 체크하시면 목록에서 노출되지 않습니다.)</span>
		  </div>
    </div>
    <div class="align-center">
            <!-- button class="btn btn-default" type="submit">취소</button -->
            <button class="btn btn-primary" type="submit">저장</button>
            <button class="btn btn-default" type="button" onclick="history.back();">취소</button>
    </div>
</form>
</div>

<form name="frisulist" id="frisulist" method="post">
<input type="hidden" id="ipt_length" name="length" value="1000">
<input type="hidden" id="ipt_searchtxt" name="searchtxt" value="">

</form>

<script>
$(function () {
    commonjs.selectNav("navbar", "iros_managed_list");
	
	$('#btn_search').on("click", function() {
		$('#ipt_searchtxt').val($('#searchtxt').val());
		var sendData = $( "#frisulist" ).serialize();   // 폼의 값을 변수 안에 담아줌
		$('#risuconfirm').html('<option value="">선택</option>');
		
		$.ajax({
			type: 'get',
			url: './api_saved_risulist.php',
			data:sendData,   	// 전송할 데이터
			dataType: 'json',
			success:function (data) {
				console.log(data);
				var items = data.data;
				if(items !== null) {
					$.each(items, function(k, v) {
						$('#risuconfirm').append('<option value="' + v.UniqueNo + '" data-owner="' + v.Owner + '" data-gubun="' + v.Gubun + '" >' + v.BudongsanSojaejibeon + '</option>');
					});
					$('#risuconfirm').select2({width: "100%"});
					//$('#risuconfirm').trigger("focus","click");
				}
			}
		}); 
	});
	
	$('#risuconfirm').on('change', function() {
		$('#UniqueNo').val(this.value);
		$('#BudongsanSojaejibeon').val($("#risuconfirm option:checked").text());
		$('#Owner').val($("#risuconfirm option:checked").data('owner'));
		var gubun = $("#risuconfirm option:checked").data('gubun');
		if(gubun == '건물') {
			$('#GubunCode').val('1');
		} else if (gubun == '토지') {
			$('#GubunCode').val('0');
		} else if (gubun == '집합건물') {
			$('#GubunCode').val('2');
		} else {
			$('#GubunCode').val('0');
		}
		//alert(this.value);
		
	});
	
	
	$( ".datepicker" ).datepicker();
})

</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>