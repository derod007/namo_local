<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

// 파트너명을 SELECT 형식으로 얻음(사용중인 회원만)
function get_partner_select_use($name, $selected='', $event='')
{
	//global $jsb;
    $sql = " select * from partner_member where mb_use=1 order by mb_bizname asc ";
    $result = sql_query($sql);
    $str = "<select id=\"$name\" name=\"$name\" $event class=\"form-control\">\n";
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        if ($i == 0) $str .= "<option value=\"\">전체</option>";
		$row['mb_use_txt'] = ($row['mb_use']!='1')?"(중지)":"";
		$row['sub_txt'] = ($row['is_sub']=='1')?"(".$row['mb_name'].")":"";
        $str .= option_selected($row['idx'], $selected, $row['mb_bizname'].$row['sub_txt'].$row['mb_use_txt']);
    }
    $str .= "</select>";
    return $str;
}

//$partners = get_partner_select_use();

$w = $_GET['w'];

	$btntxt = "등록";
	$btnclass = "btn-primary";
	
$row['wr_ca']='A';
$row['wr_part']='A';
	
?>
<style>
.ui-widget {
    font-family: font-family: "Pretendard Variable", Pretendard, -apple-system, BlinkMacSystemFont, system-ui, Roboto, "Helvetica Neue", "Segoe UI", "Apple SD Gothic Neo", "Noto Sans KR", "Malgun Gothic", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", sans-serif;
}
.info_window {display:none;}
.info_window p {margin:0; font-size:0.9em;}
.btn_infowin {display:inline-block; width:20px; height:20px; background:#ff0000; border-radius:20%; font-size:15px; color:#fff; cursor:pointer; text-align:center; vertical-align:middle; }

.winreal { color: #337ab7;cursor:pointer; }

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
.row { border:1px solid #eee;}

.jsb-form div[class*="col-"] {
   padding-top: 5px;
	padding-bottom: 5px;
}

#result_list {max-height:200px; overflow:scroll;}
.hands {cursor:pointer;}
</style>

<div class="page-header">
	<div class="btn-div">
	<!-- a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a -->
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>대출신청(아파트) <?php echo $btntxt; ?></h1>
</div>

<!-- CONTENT START -->

<div style="padding:15px;margin:auto;">
	<form id="fapt" name="fapt" action="/app/new/loan-apt-act.php" method="post" class="jsb-form" onSubmit="return fapt_submit(this);">
	 <input type="hidden" name="w" value="">
	 <input type="hidden" name="wr_id" value="">
	
	   <div class="form-group col-sm-12">	
			
	   
			<div class="row"><label class="col-sm-2 control-label">등록자</label>
				<div class="col-sm-10">
				<?php 
					if($w == '') { 
						echo get_partner_select_use("pt_idx", '');
					} else {
						echo "<h4>".$partners[$row['pt_idx']]['mb_bizname']."</h4>";
					}
				?>
				</div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">담보구분</label>
			  <div class="col-sm-10 bs-padding10">
				  <input type="radio" id="control_01" name="wr_ca" value="A" required <?php echo ($row['wr_ca']=='A')?"checked":"";?>>
				  <label for="control_01">아파트 &nbsp;</label>
				  <a href="./loan-write.php">[빌라/토지 등록하러 가기]</a>
				  <!-- 
				  <input type="radio" id="control_02" name="wr_ca" value="B1" required <?php echo ($row['wr_ca']=='B')?"checked":"";?>>
				  <label for="control_02">빌라 &nbsp;</label>
				  <input type="radio" id="control_03" name="wr_ca" value="E" required <?php echo ($row['wr_ca']=='E')?"checked":"";?>>
				  <label for="control_02">기타 &nbsp;</label>
				  <input type="radio" id="control_02" name="wr_ca" value="C">
				  <label for="control_02">신용</label>
				  -->
			  </div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">지분여부</label>
			  <div class="col-sm-10 bs-padding10">
				  <input type="radio" id="control_02" name="wr_part" value="A" required <?php echo ($row['wr_part']=='A')?"checked":"";?>>
				  <label for="control_02">단독소유 &nbsp;</label>
				  <input type="radio" id="control_03" name="wr_part" value="P" required <?php echo ($row['wr_part']=='P')?"checked":"";?>>
				  <label for="control_03">지분소유(50%) &nbsp;</label>
				  <input type="radio" id="control_04" name="wr_part" value="PE" required <?php echo ($row['wr_part']=='PE')?"checked":"";?>>
				  <label for="control_04">지분소유(기타) &nbsp;</label>
				  <input type="number" id="control_05" name="wr_part_percent" value="<?php echo $row['wr_part_percent'];?>" min="1" max="99" placeholder="30" style="width:50px;">%
				   (보유지분이 50%가 아닌 경우 보유지분율을 입력하세요)
			  </div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">담보주소검색</label>
				<div class="col-sm-3">
					<label for="s_region">지역(시군구)</label>
					<?php echo get_regioncode_select("s_region", $region, ""); ?>
				</div>
				<div class="col-sm-3">
					<label for="s_danzi">단지/지번 검색어</label>
					<input type="text" name="s_danzi" id="s_danzi" value="<?php echo $danzi;?>"  class="form-control" onkeypress="if( event.keyCode == 13 ){ event.preventDefault(); $('#search_danzi').trigger('click'); }" >
				</div>
				
				<div class="col-sm-2">
					<label class="hidden-xs" style="width:100%">&nbsp;</label>
					<button class="btn btn-primary btn-block" id="search_danzi" type="button">검색</button>
				</div>
			</div>
			<div class="row" id="danzi_result"><label class="col-sm-2 control-label">&nbsp;</label>
				<div class="col-sm-10" id="result_list"></div>
				<div class="text-center col-sm-12" id="result_close"><span class="hands">X닫기</span></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">담보정보입력</label>
				<div class="col-sm-4">
					<label for="wr_addr1">주소</label>
					<input type="text" name="wr_addr1" id="wr_addr1" value="<?php echo $wr_addr1;?>" required readonly=readonly class="form-control required readonly">
				</div>
				<div class="col-sm-3">
					<label for="wr_addr2">단지명</label>
					<input type="text" name="wr_addr2" id="wr_addr2" value="<?php echo $wr_addr2;?>" required class="form-control required">
				</div>
				<div class="col-sm-3">
					<label for="wr_addr3">상세주소(동/호/층)</label>
					<input type="text" name="wr_addr3" id="wr_addr3" value="<?php echo $wr_addr3;?>" required class="form-control required">
				</div>
				<div class="col-sm-2">&nbsp;</div>
				<div class="col-sm-3">
					<label for="wr_addr_ext1">세대수/전체층</label>
					<input type="text" name="wr_addr_ext1" id="wr_addr_ext1" value="<?php echo $wr_addr_ext1;?>" class="form-control">
				</div>
				<div class="col-sm-2">
					<label for="wr_m2">전용면적(㎡)</label>
					<input type="text" name="wr_m2" id="wr_m2" value="<?php echo $wr_m2;?>" required class="form-control required" style="width:100px;" placeholder="000.00">
				</div>
				
			</div>
			<div class="row text-center">
				<div class="col-sm-4"><button id="btn_submit" class="btn btn-primary btn-block" type="submit">다음으로</button></div>
				<div class="col-sm-4">&nbsp;</div>
				<div class="col-sm-4"><button class="btn btn-default btn-block" type="button" onclick="document.location.href='./loan-list.php';">등록취소</button></div>
			</div>
			
		</div>
	
	</form>
</div>

	<form name="faptfail" action="/app/new/loan-apt-fail.php" method="post" class="jsb-form">
	 <input type="hidden" name="s_region" value="">
	 <input type="hidden" name="s_danzi" value="">
	</form>


<script>

function set_addr(addr, danzi) {
	$("#wr_addr1").val(addr);
	$("#wr_addr2").val(danzi);
	$("#wr_addr3").val('');
	alert('상세주소를 입력해주세요.');
	$("#wr_addr3").focus();
}

function fapt_submit(f) {
	
	// 입력사항체크
	
	// 지분(기타)인 경우 퍼센트 확인
	var wr_part = $('input[name=wr_part]:checked').val();
	var wr_part_percent = $('input[name=wr_part_percent]').val();
	if(wr_part == 'PE' && wr_part_percent.length == 0 ) {
		alert("보유지분을 %로 작성해주세요");
		document.fapt.wr_part_percent.focus();
		return false;
	}
	
	if($("#wr_addr1").val().length < 8) {
		alert("담보주소를 검색후 단지를 선택해주세요.");
		document.fapt.s_danzi.focus();
		return false;
	}
	
	//alert(wr_part + ":" + wr_part_percent.length);

	document.getElementById("btn_submit").disabled = "disabled";

	return true;
}

function fapt_searchfail() {
	var f = document.faptfail;
	f.s_region.value = $('input[name=s_region]:selected').val();
	f.s_danzi.value = $('input[name=s_danzi]').val();
	f.action = './loan-apt-fail.php';
	f.submit();	
}


$(function () {
    commonjs.selectNav("navbar", "newloan");

    //$(".datepicker").datepicker();
	$('#s_region').select2();
	
	$('#danzi_result').hide();
	
	$('#result_close').on('click', function() {
		$('#danzi_result').hide();
	});
	
	$('#search_danzi').on('click', function(){

		var sch = $('#s_danzi').val();
		console.log(sch);
		if(!sch.length) {
			alert('검색어를 1글자 이상 입력해주세요');
			$("#s_danzi").focus();
		} else {
			
			var sendData = {region:$('#s_region').val(), searchtxt:$('#s_danzi').val()};
			$('#result_list').html(' ');
			$.ajax({
				type: 'POST',
				url : '/api/list_danzi.php',
				data: sendData,
				dataType: "json",
				success : function(result, status, xhr) {
					//console.log(result);
					var res = result.data;
					var str = "";
					var i = cnt = 0; 
					$.each(res, function(i){
							console.log(res[i]);
							var seladdr = res[i].sigungu + " " +  res[i].dong + " " +  res[i].zibun;
							str += "<p><span class='hands' onclick='set_addr(\""+ seladdr +"\", \""+ res[i].danzi +"\");'>";
							str += res[i].dong + ' ' + res[i].zibun + ' [ ' + res[i].danzi + ' ] ';
							str += "<i class='fas fa-check-square'></i></span> ";
							str += '</p>';
						//console.log(i);
						cnt = i+1;
					});
					console.log(cnt);
					if(cnt == 0) {
						str += "<h3>해당 주소/단지명으로 검색된 단지가 없습니다.</h3>";
						str += "<h4>다시 검색하시거나 <a href='javascript:fapt_searchfail();'>[수동등록]</a>으로 진행하실 수 있습니다.</h4>";
					}
					
					$('#result_list').append(str);
					$('#danzi_result').show();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR.responseText);
				}
			});
		
		}
		
	});
	
	/*
	var status = '<?php echo $row['wr_status'];?>';
	if(status > '1') {
		$('input:radio:not(:checked)').attr('disabled', 'disabled');
		$('input').attr('readonly', 'readonly');
		$('textarea').attr('readonly', 'readonly');
		$('.btn-warning').attr('disabled', 'disabled');
	}
	*/
});
</script>	

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
