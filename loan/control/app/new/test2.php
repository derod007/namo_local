<?php
//include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
include_once '../../header.php';

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

$wr_id 	= '51';

if(!$wr_id) {
	alert("잘못된 접근입니다.");
}

// 로그인 세션(등록자 아이디 등)과 wr_id 를 확인해봐야함.
$sql = "select * from `loan_apt_tmp` where wr_id = '{$wr_id}' limit 1";
$row = sql_fetch($sql);

$row['wr_deposit'] = 0;
$row['wr_agree'] = 'Y';

$row['pt_id'] = 1;
$row['wr_live'] = 'O';

?>
<style>
</style>


<div class="page-header">
	<div class="btn-div">
	<!-- a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a -->
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>대출신청(아파트) <?php echo $btntxt; ?></h1>
</div>

<!-- CONTENT START -->

<div style="padding:15px;margin:auto;">
	<form id="flogin" name="flogin" action="/app/new/loan-apt-calc-loading.php" method="post" class="jsb-form" onSubmit="aptCalcSubmit();">
	 <input type="hidden" name="w" value="">
	 <input type="hidden" name="wr_id" value="<?php echo $wr_id;?>">
	 <input type="hidden" name="pt_id" value="<?php echo $row['pt_id'];?>">
	   <div class="form-group col-sm-12">	
			
			<h3>거주정보 및 채권정보</h3><hr/>

			<div class="row"><label class="col-sm-2 control-label">거주인 정보</label>
			  <div class="col-sm-10">
				  <input type="radio" id="control_01" name="wr_live" value="T" required <?php echo ($row['wr_live']=='T')?"checked":"";?>>
				  <label for="control_01">세입자 &nbsp;</label>
				  <input type="radio" id="control_02" name="wr_live" value="O" required <?php echo ($row['wr_live']=='O')?"checked":"";?>>
				  <label for="control_02">본인거주 &nbsp;</label>
				  <input type="radio" id="control_03" name="wr_live" value="F" required <?php echo ($row['wr_live']=='F')?"checked":"";?>>
				  <label for="control_03">무상거주 &nbsp;</label>
				  <!-- 
				  <input type="radio" id="control_02" name="wr_ca" value="C">
				  <label for="control_02">신용</label>
				  -->
			  </div>
			</div>

			<div class="row"><label class="col-sm-2 control-label">보증금</label>
				<div class="col-sm-10">
					<input type="text" name="wr_deposit" id="wr_deposit" value="<?php echo $row['wr_deposit'];?>" class="form-control" style="display:inline-block; width:150px;"> 만원
				</div>
			</div>

			<div class="row"><label class="col-sm-2 control-label">세입자 동의</label>
				<div class="col-sm-10">
				  <input type="radio" id="control_04" name="wr_agree" value="Y" required <?php echo ($row['wr_agree'] == 'Y')?" checked":"";?>>
				  <label for="control_04">동의 &nbsp;</label>
				  <input type="radio" id="control_05" name="wr_agree" value="N" required <?php echo ($row['wr_agree'] != 'Y')?" checked":"";?>>
				  <label for="control_05">미동의 &nbsp;</label>
				</div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">선순위 채권정보</label>
				<div  id="tb_mortgage" class="col-sm-10">
					<div><button id="hide_raws" class="btn btn-sm">텍스트입력창 접기</button></div>
					<div><button id="show_raws" class="btn btn-sm">텍스트입력창 열기</button></div>
					<div id="tabs_raws" style="display:none; margin-bottom: 15px;">
						<textarea id="mortext" rows="8" class="table table-striped table-bordered nowrap" style="width:100%; max-width:600px; margin-bottom: 0;"></textarea>
						<div><button onclick="morConvert();" class="btn btn-sm">적용하기</button></div>
					</div>
					<div id="tabs_table">
						<table class="table table-striped table-bordered nowrap" style="width:100%; max-width:600px;margin-bottom: 5px;">
							<thead>
							<tr>
								<th>번호</th>
								<th>설정금액</th>
								<th>설정권자</th>
								<th>삭제</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td class="row_idx">1<input type="hidden" name="mor_no[]" value="1"></td>
								<td><input type="text" name="mor_amount[]" value="" class="form-control" maxlength="8" placeholder="숫자만입력" style="display:inline-block; width:150px;"> 만원</td>
								<td><input type="text" name="mor_bank[]" value="" class="form-control" maxlength="20" placeholder="기관명" style="display:inline-block; width:150px;"></td>
								<td align="center"><button class="btn btn-sm morDelete">삭제</button></td>
							</tr>
							</tbody>
						</table>
						<div><button onclick="morAddRow();" class="btn btn-sm">행추가</button></div>
					</div>
					
				</div>
			</div>
			
			<div class="row"><label class="col-sm-12">&nbsp;</label></div>
			

			<div class="row text-center">
				<div class="col-sm-4"><button class="btn btn-primary btn-block" type="submit">다음으로</button></div>
				<div class="col-sm-4">&nbsp;</div>
				<div class="col-sm-4"><button class="btn btn-default btn-block" type="button" onclick="document.location.href='./loan-list.php';">등록취소</button></div>
			</div>

		</div>
		
	</form>
</div>

<script>

$(function () {
    commonjs.selectNav("navbar", "newloan");
});

function morReset() {
	$("#tb_mortgage>table>tbody").html("");
}

function morAddRow(one='',two='') {
	
	event.preventDefault();	// submit 금지
	
	var row_idx = parseInt($(".row_idx:last").text());
	
	if(arguments.length > 1 && isNaN(row_idx)) {
		row_idx = 1; 
	} else if(!row_idx){ 
		row_idx = 2; 
	} else {
		row_idx += 1; 
	}
	var str = "";

	str += "<tr>";
	str += "	<td class=\"row_idx\">"+row_idx+"<input type=\"hidden\" name=\"mor_no[]\" value=\""+row_idx+"\"></td>";
	str += "	<td><input type=\"text\" name=\"mor_amount[]\" value=\""+one+"\" class=\"form-control\" style=\"display:inline-block; width:150px;\" maxlength=\"8\" placeholder=\"숫자만입력\" onkeyup=\"this.value=this.value.replace(/[^0-9]/g,'');\">만원</td>";
	str += "	<td><input type=\"text\" name=\"mor_bank[]\" value=\""+two+"\" class=\"form-control\" class=\"form-control\" style=\"display:inline-block; width:150px;\" maxlength=\"20\" placeholder=\"기관명\"></td>";
	str += "	<td align=\"center\"><button class=\"btn btn-sm morDelete\">삭제</button></td>";
	str += "</tr>";
	
	$("#tb_mortgage>#tabs_table>table>tbody").append(str);
	
}

function aptCalcSubmit() {
	console.log(this);
}

function morConvert() {
	
	event.preventDefault();	// submit 금지
	
	var mortext = $('#mortext').val();
	
	$.ajax({
		type: 'POST',
		url : './ajax.morgage-parse.php',
		data: {"mortext":mortext},
		dataType: "json",
		success : function(result, status, xhr) {
			console.log(result);
			var res = result;
			
			morReset();
			
			if(res.priority == '선순위') {
				console.log(res.loanProviders);
			
				$("#tb_mortgage>#tabs_table>table>tbody>tr").remove();
			
				$.each(res.loanProviders, function(index, item) {
					morAddRow(item.totalAmount,item.name);
				});				
				
			} else {
				alert("읽어올수 있는 선순위 정보가 없습니다.");
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR.responseText);
			alert("입력내용 양식을 다시 확인해주세요.");
		}
	});
	
	//console.log(mortext);
	
}

$(document).ready(function () {

	$('#tabs_raws').hide();
	$('#hide_raws').hide();
	
    $('#tb_mortgage').on('click', 'button#show_raws', function () {
		//console.log( this);
		event.preventDefault();	// submit 금지
		
		$('#tabs_raws').show();
		$('#show_raws').hide();
		$('#hide_raws').show();
	});
	
    $('#tb_mortgage').on('click', 'button#hide_raws', function () {
		//console.log( this);
		event.preventDefault();	// submit 금지
		
		$('#tabs_raws').hide();
		$('#show_raws').show();
		$('#hide_raws').hide();
			
    });
	
    $('#tb_mortgage').on('click', 'button.morDelete', function () {
		//console.log( this);
		event.preventDefault();	// submit 금지
		
		//console.log($(this).parent().parent().prop('tagName'));
		$(this).parent().parent().remove();
		
		
    });
});

</script>
	   
<?php
//print_r2($_GET);
//print_r2($row);
?>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';

