<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../header.php';

$w = $_GET['w'];

if($w == 'u') {
	$wr_id = $_GET['wr_id'];
	if(!$wr_id) {
		alert("잘못된 접근입니다.");
	}
	$sql = "select * from write_loaninfo where wr_id = '{$wr_id}' and pt_idx='".$member['idx']."' and wr_datetime >= '".LIMIT_YMD."' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['wr_id']) {
		alert('해당되는 데이터가 없습니다');
	}
	$btntxt = "수정";
	$btnclass = "btn-warning";
	
} else {
	$btntxt = "등록";
	$btnclass = "btn-primary";
	
	$row["wr_link1_subj"] = "KB시세조회";
}
?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>대출신청 <?php echo $btntxt; ?></h1>
</div>

<!-- CONTENT START -->

<div style="padding:15px;margin:auto;">
	<form id="fwrite" name="fwrite" action="/app/loaninfo-act.php" method="post" class="jsb-form"  onSubmit="return fsubmit(this);">
	 <input type="hidden" name="w" value="<?php echo $w; ?>">
	 <input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
	   <div class="form-group" >
	   
			<?php if($row['wr_status'] > 1) { ?>
			<div class="row"><label class="col-sm-2 control-label">진행상태</label>
				<div class="col-sm-10"><span class="loan-status-<?php echo $row['wr_status']; ?>"><?php echo $status_arr[$row['wr_status']]; ?></span></div>
			</div>
			<?php } ?>

			<?php if($row['wr_status'] > 1) { ?>
			<div class="row" style="border:1px solid #ccc; padding-bottom:10px;"><label class="col-sm-2 control-label">심사결과</label>
				<div class="col-sm-10">
					<table class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%">
						<tr>
							<th class="col-sm-2 text-center">한도(만원)</th>
							<th class="col-sm-2 text-center">금리(%)</th>
							<th class="col-sm-8 text-center">부대조건</th>
						</tr>
						<tr>
							<td class="text-center"><?php echo ($row["jd_amount"])?number_format($row["jd_amount"])."만원":""; ?></td>
							<td class="text-center"><?php echo ($row["jd_interest"])?$row["jd_interest"]."%":""; ?></td>
							<td><?php echo $row["jd_condition"]; ?></td>
						</tr>
					</table>
					
					<?php 
						if($row['wr_tel']) { 
							$hp = str_replace('-', '', trim($row['wr_tel']));
							$row['wr_tel'] = substr($hp,0,3)."-****-".substr($hp,-4);
					?>
					<div class="row"><label class="col-sm-2 control-label">차주명</label>
						<div class="col-sm-4"><span style="font-size:1.5em; color:#0033ff; font-weight:800; letter-spacing: 1px;"><?php echo $row["wr_name"]; ?></span></div>
						<label class="col-sm-1 control-label">연락처</label>
						<div class="col-sm-5"><span style="font-size:1.5em; color:#0033ff; font-weight:800; letter-spacing: 1px;"><?php echo $row["wr_tel"]; ?></span></div>
					</div>
					<div class="row">
						<div class="col-sm-12"><span style="font-size:1.0em; color:#111;"><?php echo nl2br($row["wr_memo"]); ?></span></div>
					</div>
					
					<?php } else { ?>
					<div class="row"><label class="col-sm-2 control-label">차주명</label>
						<div class="col-sm-4"><input type="text" id="wr_name" name="wr_name" value="<?php echo $row["wr_name"]; ?>" class="form-control" placeholder="차주명"></div>
						<label class="col-sm-1 control-label">연락처</label>
						<div class="col-sm-5"><input type="text" id="wr_tel" name="wr_tel" value="<?php echo $row["wr_tel"]; ?>" class="form-control" placeholder="차주 연락처"></div>
					</div>
					<div class="row">
						<div class="col-sm-12"><span style="font-size:1.0em; color:#111;"><textarea id="wr_memo" name="wr_memo" class="form-control" style="height:60px;" placeholder="진행요청 메모"><?php echo $row["wr_memo"]; ?></textarea></div>
					</div>
					<?php } ?>
					<hr/>
					<?php
						if($row['wr_status'] == '10') {
							echo '<div class="row">';
							echo '<div class="col-sm-6"><button class="btn btn-info btn-block" type="button" id="loan_processing" onclick="javascript:;">진행요청</button></div>';
							echo '<div class="col-sm-6"> ※ 심사결과 확인후 <b>진행요청</b>을 클릭해주세요.</div><br/>';
							echo '</div>';
							echo '<br/>';
						} 
						if($row['wr_status'] != '9' && $row['wr_status'] != '20' && $row['wr_status'] != '60' && $row['wr_status'] != '99') {
							echo '<div class="row">';
							echo '<div class="col-sm-6"><button class="btn btn-danger btn-block" type="button" id="loan_cancel" onclick="javascript:;">진행취소</button></div>';
							echo '<div class="col-sm-6"> ※ 진행을 취소하고자 하시면 <b>진행취소</b>를 클릭해주세요.</div><br/>';
							echo '</div>';
							echo '<br/>';
						}
					?>
				</div>
			</div>
			<?php } ?>

			
			<div class="row"><label class="col-sm-2 control-label">담보구분</label>
			  <div class="col-sm-10 bs-padding10">
				  <input type="radio" id="control_01" name="wr_ca" value="B" required <?php echo ($row['wr_ca']=='B')?"checked":"";?>>
				  <label for="control_01">부동산 담보 &nbsp;</label>
				  <input type="radio" id="control_02" name="wr_ca" value="B1" required <?php echo ($row['wr_ca']=='B1')?"checked":"";?>>
				  <label for="control_02">부동산 담보(지분) &nbsp;</label>
				  <input type="radio" id="control_03" name="wr_ca" value="E" required <?php echo ($row['wr_ca']=='E')?"checked":"";?>>
				  <label for="control_02">기타 &nbsp;</label>
				  <!-- 
				  <input type="radio" id="control_02" name="wr_ca" value="C">
				  <label for="control_02">신용</label>
				  -->
			  </div>
			</div>
		  
			<div class="row"><label class="col-sm-2 control-label">제목</label>
				<div class="col-sm-10"><input type="text" id="wr_subject" name="wr_subject" value="<?php echo $row["wr_subject"]; ?>" class="form-control" required placeholder="홍길동 / 담보종류 / 자금용도 (확인된 사항만 기재)"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">대출자정보</label>
				<div class="col-sm-10"><textarea id="wr_cont1" name="wr_cont1" class="form-control" style="height:80px;" placeholder="자유양식 작성"><?php echo $row["wr_cont1"]; ?></textarea></div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">담보주소</label>
				<div class="col-sm-10">
					<?php if($w=='' || $row['wr_status'] <= 1) { ?><label><a onclick="execDaumPostcode();">☞주소검색</a></label>
					<input type="hidden" id="schpost_chk" name="schpost_chk" value=""><?php } ?>
					<input type="text" name="address1" id="address1" value="<?php echo $row["wr_addr1"]; ?>" class="form-control" required  placeholder="기본주소(시/군/구/동) - 주소검색시 자동입력">
					<span id="guide" style="color:#999;display:block"></span>
					<input type="text" name="address2" id="address2" value="<?php echo $row["wr_addr2"]; ?>" class="form-control" readonly="readonly" style="display:none">
					<input type="text" name="address3" id="address3" value="<?php echo $row["wr_addr3"]; ?>" class="form-control" placeholder="상세주소(동/호,건물명)">
					<input type="text" name="address_ext" id="address_ext" value="<?php echo $row["wr_addr_ext1"]; ?>" class="form-control" placeholder="추가정보(세대수/층)">
				</div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">전용면적</label>
				<div class="col-sm-10"><input type="text" name="wr_m2" id="wr_m2" value="<?php echo $row["wr_m2"]; ?>" class="form-control" style="display:inline-block; width:100px;" placeholder="000.00"> ㎡ (제곱미터)</div>
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
			
		</div>
			
<?php
$pjfile = get_writefile($wr_id);
$filecnt = number_format($pjfile['count']);
?>
			<div class="row"><label class="col-sm-2 control-label">첨부파일 <?php echo "(".$filecnt .")";?><?php if($w=='u') { ?><br/><a href="./loaninfo-file.php?wr_id=<?php echo $wr_id;?>">관리 &gt;&gt;</a><?php } ?></label>
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
			<?php } else { ?><div class="col-sm-12 blue"> ※ 첨부파일을 추가하시려면 첨부파일 버튼을 눌러 업로드해주세요. </div><?php } ?>
			</div>

		<div class="row">
			<?php if($row['wr_status'] <= 1) { ?><div class="col-sm-4"><button class="btn <?php echo $btnclass; ?> btn-block col-sm-4" type="submit"><?php echo $btntxt; ?></button></div><?php } ?>
			<?php if($wr_id) { ?><div class="col-sm-4"><button class="btn btn-info btn-block col-sm-4" type="button" onclick="document.location.href='./loaninfo-file.php?wr_id=<?php echo $wr_id;?>';">첨부파일<?php echo "(".$filecnt .")";?></button></div><?php } ?>
			<div class="col-sm-4"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="history.back();">돌아가기</button></div>
		</div>
    </form>
	<form id="fprocessing" name="fprocessing" action="/app/loaninfo-act.php" method="post" style="display:none;">
	 <input type="hidden" name="w" value="pr">
	 <input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
	 <input type="hidden" name="wr_status" value="<?php echo $row['wr_status']; ?>">
	 <input type="hidden" name="wr_name" value="">
	 <input type="hidden" name="wr_tel" value="">
	 <input type="hidden" name="wr_memo" value="">
    </form>

</div>

<script>
$(function () {
	commonjs.selectNav("navbar", "loaninfo");
	
	<?php if($w =='') {?>
	$('#address1').attr('readonly', 'readonly');
	$('#address1').on('click',function() {
		var wr_ca = $("input[name='wr_ca']:checked").val();
		if(wr_ca != 'E') {
			execDaumPostcode();
		}
	});
	<?php } ?>
	
	var status = '<?php echo $row['wr_status'];?>';
	if(status > '1') {
		$('input:radio:not(:checked)').attr('disabled', 'disabled');
		$('input').attr('readonly', 'readonly');
		$('textarea').attr('readonly', 'readonly');
		$('.btn-warning').attr('disabled', 'disabled');
	}
	
	if(status == '10') {
		$('#wr_name').removeAttr('readonly');
		$('#wr_tel').removeAttr('readonly');
		$('#wr_memo').removeAttr('readonly');
	}
	
	$('#loan_processing').on('click', function (event) {
		var f = document.fprocessing;
		f.wr_name.value = document.fwrite.wr_name.value;
		f.wr_tel.value = document.fwrite.wr_tel.value;
		f.wr_memo.value = document.fwrite.wr_memo.value;
		if(!f.wr_tel.value) {
			alert('대출자 연락처를 입력해주세요.');
		} else {
			f.submit();
		}
    });

	$('#loan_cancel').on('click', function (event) {
		var f = document.fprocessing;
		f.w.value = 'pc';	// 진행취소
		f.submit();
    });
	
	$("input[name='wr_ca']").on('change', function() {
		var wr_ca = $("input[name='wr_ca']:checked").val();
		console.log(wr_ca);
		if(wr_ca != 'E') {
			$('#address1').attr("readonly", "readonly");
		} else {
			$('#address1').removeAttr("readonly");
		}
	});
	
});

function fsubmit(f) {
	
	if(!f.wr_ca.value) {
		alert("담보구분을 선택해주세요");
		return false;
	}
	
	<?php if($w =='') {?>
	if(!f.address1.value) {
		alert("주소검색으로 담보주소를 입력해주세요");
		return false;
	}
	if(f.wr_ca.value != 'E' && f.schpost_chk.value != '1') {
		alert("담보주소는 주소검색을 한뒤 입력해주세요");
		return false;
	}
	<?php } ?>
	
	return true;
	
}

</script>


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
				document.getElementById("schpost_chk").value = "1";		// 검색체크 항목을 1 로 설정
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

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>