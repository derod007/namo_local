<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

$partners = get_partnerlist();

$w = $_GET['w'];

if($w == 'u') {
	$wr_id = $_GET['wr_id'];
	if(!$wr_id) {
		alert("잘못된 접근입니다.");
	}
	$sql = "select * from loan_write where wr_id = '{$wr_id}' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['wr_id']) {
		alert('해당되는 데이터가 없습니다');
	}
	$btntxt = "수정";
	$btnclass = "btn-warning";
	$duppop = check_duplicate($row['wr_addr1'], $row['address2'], $row['wr_id']);
	
} else {
	$btntxt = "등록";
	$btnclass = "btn-primary";
	
	$row['wr_ca']='B';
	$row["wr_link1_subj"] = "KB시세조회";
}

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
	<h1>대출신청(빌라/토지)  <?php echo $btntxt; ?></h1>
</div>

<!-- CONTENT START -->

<div style="padding:15px;margin:auto;">
	<form id="flogin" name="flogin" action="/app/new/loan-act.php" method="post" class="jsb-form">
	 <input type="hidden" name="w" value="<?php echo $w; ?>">
	 <input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
	 <input type="hidden" name="prev_status" value="<?php echo $row['wr_status']; ?>">
	   <div class="form-group col-sm-8">
	   
			<div class="row"><label class="col-sm-2 control-label">등록자</label>
				<div class="col-sm-10">
				<?php 
					if($w == '') { 
						echo get_partner_select("pt_idx", '');
					} else {
						echo "<h4>".$partners[$row['pt_idx']]['mb_bizname']."</h4>";
					}
				?>
				</div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">진행상태</label>
				<div class="col-sm-10"><span class="loan-status-<?php echo $row['wr_status']; ?>"><?php echo ($row['wr_status'])?$status_arr[$row['wr_status']]:"등록"; ?></span></div>
			</div>
			<?php
				if($row['wr_status'] >= 30) {
					
					$process_date = "";
					$sql = "SELECT reg_date FROM `log_action` WHERE `wr_id` = '{$row['wr_id']}' and next_status='30' order by log_id desc limit 1" ;
					$row_date = sql_fetch($sql);
					if($row_date['reg_date']) {
						$process_date = "<br/><br/>".$row_date['reg_date'];
					}
					
			?>
			<div class="row"><label class="col-sm-2 control-label">차주명</label>
				<div class="col-sm-4"><input type="text" id="wr_name" name="wr_name" value="<?php echo $row["wr_name"]; ?>" class="form-control" placeholder="차주명"></div>
				<label class="col-sm-1 control-label">연락처</label>
				<div class="col-sm-5"><span style="font-size:1.5em; color:#0033ff; font-weight:800; letter-spacing: 1px;"><?php echo $row["wr_tel"]; ?></span></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">진행메모</label>
				<div class="col-sm-10"><span style="font-size:1.0em; color:#111;"><?php echo nl2br($row["wr_memo"]); ?><?php echo $process_date; ?></span></div>
			</div>
			<hr/>
			
			<?php
				}
			?>
			
			<div class="row"><label class="col-sm-2 control-label">담보구분</label>
			  <div class="col-sm-10 bs-padding10">
				  <input type="radio" id="control_01" name="wr_ca" value="A" required <?php echo ($row['wr_ca']=='A')?"checked":"";?>>
				  <label for="control_01">아파트 &nbsp;</label>
				  <input type="radio" id="control_02" name="wr_ca" value="B" required <?php echo ($row['wr_ca']=='B')?"checked":"";?>>
				  <label for="control_02">빌라 &nbsp;</label>
				  <input type="radio" id="control_03" name="wr_ca" value="E" required <?php echo ($row['wr_ca']=='E')?"checked":"";?>>
				  <label for="control_03">기타 &nbsp;</label>
				  <!-- 
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
				  <input type="number" id="control_05" name="wr_part_percent" value="<?php echo $row['wr_part_percent'];?>" placeholder="30" style="width:50px;">%
				   (보유지분이 50%가 아닌 경우 보유지분율을 입력하세요)
			  </div>
			</div>
		  
			<div class="row"><label class="col-sm-2 control-label">제목</label>
				<div class="col-sm-10"><input type="text" id="wr_subject" name="wr_subject" value="<?php echo $row["wr_subject"]; ?>" class="form-control" placeholder="홍길동 / 담보종류 / 자금용도 (확인된 사항만 기재)"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">대출자정보</label>
				<div class="col-sm-10"><textarea id="wr_cont1" name="wr_cont1" class="form-control" style="height:80px;" placeholder="자유양식 작성"><?php echo $row["wr_cont1"]; ?></textarea></div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">담보주소 <?php echo $duppop;?></label>
				<div class="col-sm-10">
					<label><a onclick="execDaumPostcode();">☞주소검색</a></label>
					<input type="text" name="address1" id="address1" value="<?php echo $row["wr_addr1"]; ?>" class="form-control" placeholder="기본주소(시/군/구/동) - 주소검색시 자동입력">
					<span id="guide" style="color:#999;display:block"></span>
					<input type="text" name="address2" id="address2" value="<?php echo $row["wr_addr2"]; ?>" class="form-control" readonly="readonly" style="display:none">
					<input type="text" name="address3" id="address3" value="<?php echo $row["wr_addr3"]; ?>" class="form-control" placeholder="상세주소(동/호,건물명)">
					<input type="text" name="address_ext" id="address_ext" value="<?php echo $row["wr_addr_ext1"]; ?>" class="form-control" placeholder="추가정보(세대수/층)">
				</div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">전용면적 &nbsp; <span id="win_real" class="winreal"><i class="fas fa-chart-bar"></i></span></label>
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
			
<?php
$pjfile = get_writefile($wr_id);
$filecnt = number_format($pjfile['count']);
?>
			<div class="row"><label class="col-sm-2 control-label">첨부파일 <?php echo "(".$filecnt .")";?><br/><a href="./loan-file.php?wr_id=<?php echo $wr_id;?>">관리 &gt;&gt;</a></label>
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
				<?php if($wr_id) { ?><div class="col-sm-4"><button class="btn btn-info btn-block col-sm-4" type="button" onclick="document.location.href='./loan-file.php?wr_id=<?php echo $wr_id;?>';">첨부파일<?php echo "(".$filecnt .")";?></button></div><?php } ?>
				<div class="col-sm-4"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="document.location.href='./loan-list.php';">목록으로</button></div>
			</div>
		</form>

		</div>
		
	   <div class="form-group col-sm-4" >
			<form id="fjudge" name="fjudge" action="/app/new/loan-act.php" method="post" class="jsb-form">
			 <input type="hidden" name="w" value="pr">
			 <input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
		 	 <input type="hidden" name="prev_status" value="<?php echo $row['wr_status']; ?>">
			 <input type="hidden" name="next_status" value="">
	   
			<h3>심사의견<?php if($row['jd_autoid']) { ?> (<a href='javascript:autojudgeModalPopup(<?php echo $row['jd_autoid']; ?>);' data-jdid='<?php echo $row['jd_autoid']; ?>'><i class='fas fa-balance-scale'></i>자동한도</a>)<?php } ?></h3><hr/>
			
	   
			<div class="row">
				<div class="col-sm-6"><label class="control-label" for="jd_amount">한도(만원)</label><input type="text" id="jd_amount" name="jd_amount" value="<?php echo $row["jd_amount"]; ?>" class="form-control" placeholder="숫자만 입력"></div>
				<div class="col-sm-6"><label class="control-label" for="jd_interest">금리(%)</label><input type="text" id="jd_interest" name="jd_interest" value="<?php echo $row["jd_interest"]; ?>" class="form-control" placeholder="연 10%"></div>
			</div>
			<div class="row">
				<div class="col-sm-12"><label class="control-label">부대조건</label><textarea id="jd_condition" name="jd_condition" class="form-control" style="height:60px;" placeholder="자유양식"><?php echo $row["jd_condition"]; ?></textarea></div>
			</div>
			<div class="row"><?php
				$sql = "select * from log_judge where wr_id = '{$wr_id}' order by jd_id desc limit 3";
				$res = sql_query($sql);
				while($jd = sql_fetch_array($res)) {
					echo '<div class="col-sm-12">'.$jd['manage_id']." / ".$jd['jd_amount']." / ".$jd['jd_interest']." : ".$jd['reg_date'].'</div>';
				}
			?></div>
			
			<hr/>
			<div class="row">
				<div class="col-sm-12"><label class="control-label">검토메모(내부전용)</label><textarea id="jd_memo" name="jd_memo" class="form-control" style="height:80px;" placeholder="제휴사 노출안되는 내부기록"><?php echo $row["jd_memo"]; ?></textarea></div>
			</div>
			<div class="row">
				<div class="col-sm-12"><label class="control-label">담보가 산정(만원 / 내부)</label><input type="text" id="rf_first3" name="rf_first3" value="<?php echo $row["rf_first3"]; ?>" class="form-control" placeholder="자유양식"></div>
			</div>
			<div class="row">
				<div class="col-sm-12"><label class="control-label">선순위 원금(만원 / 내부)</label><input type="text" id="rf_first1" name="rf_first1" value="<?php echo $row["rf_first1"]; ?>" class="form-control" placeholder="자유양식"></div>
			</div>
			<div class="row">
				<div class="col-sm-12"><label class="control-label">선순위 설정액(만원 / 내부)</label><input type="text" id="rf_first2" name="rf_first2" value="<?php echo $row["rf_first2"]; ?>" class="form-control" placeholder="자유양식"></div>
			</div>
			
			<br class="clear"/>
			<?php if($row['wr_status'] != 60 && $row['wr_status'] != 99) { ?>
			<div class="row">
				<div class="col-sm-4"><button class="btn btn-warning btn-block col-sm-4" type="button" onclick="judge_save()">저장</button></div>
				<?php if($row['wr_status'] >= 30) { ?>
				<div class="col-sm-4"><button class="btn btn-success btn-block col-sm-4" type="button" onclick="judge2_ok()">대출실행</button></div>
				<div class="col-sm-4"><button class="btn btn-danger btn-block col-sm-4" type="button" onclick="judge2_deny()">진행취소</button></div>
				<?php } else { ?>
				<div class="col-sm-4"><button class="btn btn-success btn-block col-sm-4" type="button" onclick="judge_ok()">가승인</button></div>
				<div class="col-sm-4"><button class="btn btn-danger btn-block col-sm-4" type="button" onclick="judge_deny()">부결</button></div>
				<div class="col-sm-4"><button class="btn btn-danger btn-block col-sm-4" type="button" onclick="judge_dupl()">중복</button></div>
				<?php } ?>
			</div>
			<?php } ?>
			<div class="row">
			<?php if($row['wr_status'] != 60 && $row['wr_status'] != 99) { ?>
				<?php if($row['wr_status'] >= 30) { ?>
				<div class="col-sm-12 red"> ※ 대출실행/진행취소시 해당 상태로 변경됩니다.</div>
				<?php } else { ?>
				<div class="col-sm-12 red"> ※ 가승인/부결시 해당 상태로 변경됩니다.</div>
				<div class="col-sm-12"> ※ 중복으로 체크하시면 목록에서 안보이게 됩니다.</div>
				<?php } ?>
			<?php } else { ?>
				<div class="col-sm-12"> ※ 대출실행 / 진행취소에서는 심사의견 수정이 불가합니다.</div>
			<?php } ?>
			</div>
			
			</form>
		</div>

		<br class="clear"/>

</div>

<div id="autojudgeModalPopup"></div>

<script>
    function autojudgeModalPopup(jdid){
		$.ajax({
			url: "./modal.autojudge.php",
			data: { jd_id : jdid },
			dataType: "html",
			success: function(data) {
				//console.log(data);	// .load(data)
				//$('#autojudgeModalPopup').html(data);
				$("#autojudgeModalPopup").html(data).dialog({
				    title: "자동심사 결과",
					height: "auto",
					width: 450,
					modal:true,
					open: function() {
						$('.ui-widget-overlay').off('click');
						$('.ui-widget-overlay').on('click', function() {
							$("#autojudgeModalPopup").dialog('close');
						})
					}
				}).dialog('open');
			}
		});
    }
</script>

<script>
$(function () {
	commonjs.selectNav("navbar", "newloan");
	
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


function judge_save() {
	var f = document.fjudge;
	f.submit();
}

function judge_ok() {
	var f = document.fjudge;
	f.next_status.value = '10';	// 승인
	f.submit();
}

function judge_deny() {
	var f = document.fjudge;
	f.next_status.value = '20';	// 부결
	f.submit();
}

function judge_dupl() {
	var f = document.fjudge;
	f.next_status.value = '90';	// 중복
	f.submit();
}

function judge2_ok() {
	var f = document.fjudge;
	f.next_status.value = '60';	// 대출실행
	f.submit();
}

function judge2_deny() {
	var f = document.fjudge;
	f.next_status.value = '99';	// 진행취소
	f.submit();
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
include_once '../../footer.php';
?>