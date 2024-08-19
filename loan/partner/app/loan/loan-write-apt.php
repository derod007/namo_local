<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

// $_POST['jd'] 값으로 넘어온 아이디로 loan_apt_tmp 에서 가져옴
// 상태값은 접수 또는 가승인 상태 (fail_code 가 있는 경우는 접수, 아니면 가승인)
// 담보구분은 아파트
// 제목에 지분인 경우 지분율 기재
// 담보정보에 보증금 / 선순위 채권


if(!$_GET['jd']) {
	alert("잘못된 접근입니다.");
}

$jd_id = $_GET['jd'];

$w = '';
$wr_id = '';

if($member['is_sub']) {
	$pt_idx = $member['parent_id'];
} else {
	$pt_idx = $member['idx'];
}

// 로그인 세션(등록자 아이디 등)과 wr_id 를 확인해봐야함.(파트너)
$sql = "select * from `loan_apt_tmp` where wr_id = '{$jd_id}' and pt_idx='{$pt_idx}' limit 1";
$row = sql_fetch($sql);

if(!$row['wr_id']) {
	alert('해당되는 데이터가 없습니다');
}

$btntxt = "등록";
$btnclass = "btn-primary";

$jd_auto_confirm = false;
$frm_readonly = "";
$next_status = "1";
// 자동승인 한도가 있는 경우
if($row['wr_id'] && $row['wr_judge_code'] == '0') {
	
	$jd_data = json_decode($row['wr_judge'], true);
	$jd_amount = $jd_data['judge']['last_judge'];
	$jd_interest = $jd_data['judge']['interest'];
	$jd_condition = '';
	$next_status = "10";		// 가승인
	
	/*
	// 2024-06-26 가승인으로만 접수되도록 수정. 
	if($jd_amount > 0 && !empty($jd_interest)) {
		$jd_auto_confirm = true;		// 자동승인 한도가 있음.
		$frm_readonly = " readonly=readonly ";
		$btntxt = "진행요청";
		$btnclass = "btn-info";
		$next_status = "30";	// 진행요청
	}
	*/
}

$row['wr_ca'] = 'A';
$row["wr_link1_subj"] = "KB시세조회";

// 담보정보
$cont2 = "";
if($row["wr_part"] == "A") {
	$cont2 .= "* 전체지분".PHP_EOL.PHP_EOL;
} else if($row["wr_part"] == "P") {
	$cont2 .= "* 지분(50%)".PHP_EOL.PHP_EOL;	
} else if($row["wr_part"] == "PE") {
	$cont2 .= "* 지분(".$row["wr_part_percent"]."%)".PHP_EOL.PHP_EOL;	
}

if($row['wr_live'] != 'O') {
	if($row['wr_live'] == 'F') {
		$cont2 .= "* 무상거주".PHP_EOL.PHP_EOL;
	} else if($row['wr_live'] == 'T') {
		$cont2 .= "* 세입자 거주 / 보증금 : ".number_format($row["wr_deposit"])."만원".PHP_EOL.PHP_EOL;
	}
}

$mortgage = json_decode($row["wr_mortgage"],true);
unset($mortgage['total']);
if(count($mortgage) > 0) {
	$no = 1;
	$total = 0;

	$cont2 .= "* 기대출 금액".PHP_EOL;
	foreach($mortgage as $k => $v) {
		if($v['amt']) {
			$same = "";
			if($v['same'] == "1") $same = "(동일차주)";
			$cont2 .= "{$no} / {$v['bank']} / ".number_format($v['amt'])."만원".$same.PHP_EOL;
			$total += $v['amt'];
			$no++;
		}
	}
	$cont2 .= "* 기대출 총액 : ".number_format($total)."만원".PHP_EOL;
}
$row["wr_cont2"] = $cont2;

// 중복체크
//$duppop = check_duplicate($row['wr_addr1'], $row['address2'], 0);

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
</style>

<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>자동한도 APT 대출신청  <?php echo $btntxt; ?></h1>
</div>

<!-- CONTENT START -->

<div style="padding:15px;margin:auto;">
	<form id="flogin" name="flogin" action="/app/loan/loan-act.php" method="post" class="jsb-form">
	 <input type="hidden" name="w" value="">
	 <input type="hidden" name="wr_id" value="">
	 <input type="hidden" name="prev_status" value="">
	 <input type="hidden" name="next_status" value="<?php echo $next_status;?>">
	 <input type="hidden" name="jd_autoid" value="<?php echo $jd_id;?>">
	 
	   <div class="form-group">
	   
			<div class="row"><label class="col-sm-2 control-label">진행상태</label>
				<div class="col-sm-10"><span class="loan-status-0">등록</span></div>
			</div>
			

			<?php if($jd_auto_confirm) { ?>
			
			<div class="row" style="border:1px solid #ccc; padding-bottom:10px;"><label class="col-sm-2 control-label">심사결과</label>
				<div class="col-sm-10">
					
					<table class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%">
						<tr>
							<th class="col-sm-2 text-center">한도(만원)</th>
							<th class="col-sm-2 text-center">금리(%)</th>
							<th class="col-sm-8 text-center">부대조건</th>
						</tr>
						<tr>
							<td class="text-center"><?php echo ($jd_amount)?number_format($jd_amount)."만원":""; ?></td>
							<td class="text-center"><?php echo ($jd_interest)?$jd_interest."%":""; ?></td>
							<td>자동 심사 승인</td>
						</tr>
					</table>
					
					<div class="row"><label class="col-sm-2 control-label">차주명</label>
						<div class="col-sm-4"><input type="text" id="wr_name" name="wr_name" value="<?php echo $row["wr_name"]; ?>" required class="form-control" placeholder="차주명"></div>
						<label class="col-sm-1 control-label">연락처</label>
						<div class="col-sm-5"><input type="text" id="wr_tel" name="wr_tel" value="<?php echo $row["wr_tel"]; ?>" required class="form-control" placeholder="차주 연락처"></div>
					</div>
					<div class="row">
						<div class="col-sm-12"><span style="font-size:1.0em; color:#111;"><textarea id="wr_memo" name="wr_memo" class="form-control" style="height:60px;" placeholder="진행요청 메모"><?php echo $row["wr_memo"]; ?></textarea></div>
					</div>
				</div>
			</div>
			<?php } ?>
			
			<div class="row"><label class="col-sm-2 control-label">담보구분</label>
			  <div class="col-sm-10 bs-padding10">
				  <input type="radio" id="control_01" name="wr_ca" value="A" required <?php echo ($row['wr_ca']=='A')?"checked":"";?> <?php echo $frm_readonly; ?>>
				  <label for="control_01">아파트 &nbsp;</label>
				  <!-- 
				  <input type="radio" id="control_02" name="wr_ca" value="C">
				  <label for="control_02">신용</label>
				  -->
			  </div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">지분여부</label>
			  <div class="col-sm-10 bs-padding10">
				  <input type="radio" id="control_02" name="wr_part" value="A" required <?php echo ($row['wr_part']=='A')?"checked":"disabled=disabled";?>>
				  <label for="control_02">단독소유 &nbsp;</label>
				  <input type="radio" id="control_03" name="wr_part" value="P" required <?php echo ($row['wr_part']=='P')?"checked":"disabled=disabled";?>>
				  <label for="control_03">지분소유(50%) &nbsp;</label>
				  <input type="radio" id="control_04" name="wr_part" value="PE" required <?php echo ($row['wr_part']=='PE')?"checked":"disabled=disabled";?>>
				  <label for="control_04">지분소유(기타) &nbsp;</label>
				  <input type="number" id="control_05" name="wr_part_percent" value="<?php echo $row['wr_part_percent'];?>" min="1" max="99" placeholder="30" style="width:50px;" <?php if($row['wr_part']!='PE') echo "disabled=disabled";?>>%
				   (보유지분이 50%가 아닌 경우 보유지분율을 입력하세요)
			  </div>
			</div>
		  
			<div class="row"><label class="col-sm-2 control-label">제목</label>
				<div class="col-sm-10"><input type="text" id="wr_subject" name="wr_subject" value="<?php echo $row["wr_subject"]; ?>" required class="form-control required" placeholder="홍길동 (신청자명 등)"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">대출자정보</label>
				<div class="col-sm-10"><textarea id="wr_cont1" name="wr_cont1" class="form-control" style="height:80px;" placeholder="자유양식 작성"><?php echo $row["wr_cont1"]; ?></textarea></div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">담보주소 <?php echo $duppop;?></label>
				<div class="col-sm-10">
					<?php if(!$jd_auto_confirm) { ?><label><a onclick="execDaumPostcode();">☞주소검색</a></label><?php } ?>
					<input type="text" name="address1" id="address1" value="<?php echo $row["wr_addr1"]; ?>" class="form-control" <?php echo $frm_readonly; ?> placeholder="기본주소(시/군/구/동) - 주소검색시 자동입력">
					<span id="guide" style="color:#999;display:block"></span>
					<input type="text" name="address2" id="address2" value="<?php echo $row["wr_addr2"]; ?>" class="form-control" readonly="readonly" style="display:none">
					<input type="text" name="address3" id="address3" value="<?php echo $row["wr_addr3"]; ?>" class="form-control" <?php echo $frm_readonly; ?>  placeholder="상세주소(동/호,건물명)">
					<input type="text" name="address_ext" id="address_ext" value="<?php echo $row["wr_addr_ext1"]; ?>" class="form-control" <?php echo $frm_readonly; ?>  placeholder="추가정보(세대수/층)">
				</div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">전용면적</label>
				<div class="col-sm-10"><input type="text" name="wr_m2" id="wr_m2" value="<?php echo $row["wr_m2"]; ?>" class="form-control" <?php echo $frm_readonly; ?> style="display:inline-block; width:100px;" placeholder="000.00"> ㎡ (제곱미터)</div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">담보정보</label>
				<div class="col-sm-10"><textarea id="wr_cont2" name="wr_cont2" class="form-control" style="height:140px;" placeholder="자유양식 작성"><?php echo $row["wr_cont2"]; ?></textarea></div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">희망금액</label>
				<div class="col-sm-10"><input type="text" id="wr_amount" name="wr_amount" value="<?php echo $row["wr_amount"]; ?>" class="form-control"></div>
			</div>
			
			<div class="row"><hr/></div>
			
			<div class="row"><label class="col-sm-2 control-label">참고링크#1<br/>(KB시세 URL)</label>
				<div class="col-sm-10">
					<input type="text" id="wr_link1" name="wr_link1" value="<?php echo $row["wr_link1"]; ?>" class="form-control" placeholder="https://링크URL">
					<input type="text" id="wr_link1_subj" name="wr_link1_subj" value="<?php echo $row["wr_link1_subj"]; ?>" class="form-control" placeholder="링크제목">
<?php
if(!empty($row["wr_link1"])) {
	if(!empty(trim($row["wr_link1_subj"]))) {
		echo "<div><a href='{$row['wr_link1']}' target='_blank'>".$row["wr_link1_subj"]."</a></div>";
	} else {
		echo "<div><a href='{$row['wr_link1']}' target='_blank'>새창링크</a></div>";
	}	
}
?>					
				</div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">참고링크#2<br/>(추가정보 URL)</label>
				<div class="col-sm-10">
					<input type="text" id="wr_link2" name="wr_link2" value="<?php echo $row["wr_link2"]; ?>" class="form-control" placeholder="https://링크URL">
					<input type="text" id="wr_link2_subj" name="wr_link2_subj" value="<?php echo $row["wr_link2_subj"]; ?>" class="form-control" placeholder="링크제목">
<?php
if(!empty($row["wr_link2"])) {
	if(!empty(trim($row["wr_link2_subj"]))) {
		echo "<div><a href='{$row['wr_link2']}' target='_blank'>".$row["wr_link2_subj"]."</a></div>";
	} else {
		echo "<div><a href='{$row['wr_link2']}' target='_blank'>새창링크</a></div>";
	}	
}
?>					
				</div>
			</div>
			
<?php
$pjfile = get_writefile($wr_id);
$filecnt = number_format($pjfile['count']);
?>
			<div class="row"><label class="col-sm-2 control-label">첨부파일 <?php echo "(".$filecnt .")";?></label>
				<div class="col-sm-10">
		<?php
			$cnt = $pjfile['count'];
			if ($cnt) {
				?>
				<!-- 첨부파일 시작 { -->
				<div id="project_v_file">
					<table class="table">
					<?php // 가변 파일
						//print_r2($pjfile);
					foreach ($pjfile as $i => $file) {
						if (isset($file['source']) && $file['source']) {
					?>
						<tr style="border-bottom: 1px solid #ddd">
							<td style="padding-left: 10px;padding-right:10px;">
								<?php echo "[" . $file['category'] . "] "; ?>
							</td>
							<td style="padding-left: 10px;padding-right:10px;">
								<a href="<?php echo $file['href']; ?>" class="view_file_download">
									<strong>
										<?php echo $file['source']; ?></strong>
									( <?php echo $file['size']; ?> ) <i class="fa fa-download" aria-hidden="true"></i></a>
									<?php echo $file['memo']; ?>	</td>
							<td style="padding-left: 10px;padding-right:10px;"><span class="project_v_file_date">
									<?php echo substr($file['datetime'], 0, 16); ?></span></td>
						</tr>
					<?php
						}
					}
					?>
					</table>
				</div>
				<!-- } 첨부파일 끝 -->
		<?php 
			} else {
				echo "<span style='color:gray'>등록된 첨부파일이 없습니다.</span>";
			}
		?>
				</div>
			</div>

			<div class="row"><hr/></div>

			<div class="row">
			<?php if($w == '') { ?><div class="col-sm-12 blue"> ※ 첨부파일을 추가하시려면 저장후 첨부파일 버튼을 눌러 업로드해주세요. </div>
			<?php } else { ?><div class="col-sm-12 blue"> ※ 첨부파일을 추가 또는 삭제하시려면 첨부파일 버튼을 눌러 업로드해주세요. </div><?php } ?>
			</div>
			<div class="row">
				<div class="col-sm-4"><button class="btn <?php echo $btnclass; ?> btn-block col-sm-4" type="submit"><?php echo $btntxt; ?></button></div>
				<?php if($wr_id) { ?><div class="col-sm-4"><button class="btn btn-info btn-block col-sm-4" type="button" onclick="document.location.href='./loaninfo-file.php?wr_id=<?php echo $wr_id;?>';">첨부파일<?php echo "(".$filecnt .")";?></button></div><?php } ?>
				<div class="col-sm-4"><button class="btn btn-danger btn-block col-sm-4" type="button" onclick="document.location.href='./loan-list.php';">등록취소</button></div>
			</div>
		</form>

		</div>
		
	   <div class="form-group col-sm-4" >
			&nbsp;
		</div>

		<br class="clear"/>

</div>

<script>
$(function () {
	commonjs.selectNav("navbar", "newloan");
	
	$('#wr_name').focusout(function() {
		var wr_name = $('#wr_name').val();
		$('#wr_subject').val(wr_name);
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

// 중복 클릭시 해당 윈도우 보이기
$(document).ready(function () {
    $('.btn_infowin').on('click', function () {
		//console.log( this);
		var winsn = $(this).data("winsn");
		//$("#"+winsn).toggle();
		$("#"+winsn).dialog({
			  title: "검색결과 최대 6개",
			  resizable: false,
			  height: "auto",
			  width: 450,
			  modal: true,
  			  open: function() {
				$('.ui-widget-overlay').off('click');
				$('.ui-widget-overlay').on('click', function() {
					$("#"+winsn).dialog('close');
				})
			}
		});
    });
	
	// 실거래가 조회
	$('#win_real').on('click', function () {
		var addr1 = $("#address1").val();
		var py = $("#wr_m2").val();
		var url = '/app/real/newwin_real.php?addr1=' + addr1 + '&py=' + py;
		window.open(url, 'newwin_real', 'scrollbars=yes,width=650,height=600,top=10,left=100');
    });
	
});

</script>

<?php if(!$jd_auto_confirm) { ?>
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    //본 예제에서는 도로명 주소 표기 방식에 대한 법령에 따라, 내려오는 데이터를 조합하여 올바른 주소를 구성하는 방법을 설명합니다.
    function execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
				console.log(data);
                // 도로명 주소의 노출 규칙에 따라 주소를 표시한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var roadAddr = data.roadAddress; // 도로명 주소 변수
                var extraRoadAddr = ''; // 참고 항목 변수

                // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                    extraRoadAddr += data.bname;
                }
                // 건물명이 있고, 공동주택일 경우 추가한다.
                if(data.buildingName !== '' && data.apartment === 'Y'){
                   extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                if(extraRoadAddr !== ''){
                    extraRoadAddr = ' (' + extraRoadAddr + ')';
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                //document.getElementById('postcode').value = data.zonecode;
                //document.getElementById("roadAddress").value = roadAddr;
                document.getElementById("address1").value = data.jibunAddress;
				//document.getElementById("address1").value = data.address;
                
                // 참고항목 문자열이 있을 경우 해당 필드에 넣는다.
                var addr2TextBox = document.getElementById("address2");
                if(roadAddr !== ''){
                    document.getElementById("address2").value = extraRoadAddr;
                    addr2TextBox.style.display = 'block';
                } else {
                    document.getElementById("address2").value = '';
					addr2TextBox.style.display = 'none';
                }

                var guideTextBox = document.getElementById("guide");
                // 사용자가 '선택 안함'을 클릭한 경우, 예상 주소라는 표시를 해준다.
                if(data.autoRoadAddress) {
                    var expRoadAddr = data.autoRoadAddress + extraRoadAddr;
                    guideTextBox.innerHTML = '(예상 도로명 주소 : ' + expRoadAddr + ')';
                    guideTextBox.style.display = 'block';

                } else if(data.autoJibunAddress) {
                    var expJibunAddr = data.autoJibunAddress;
                    guideTextBox.innerHTML = '(예상 지번 주소 : ' + expJibunAddr + ')';
                    guideTextBox.style.display = 'block';
                } else {
                    guideTextBox.innerHTML = '';
                    guideTextBox.style.display = 'none';
                }
            }
        }).open();
    }
</script>
<?php } ?>


<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>