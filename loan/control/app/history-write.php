<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../header.php';

$w = $_GET['w'];

if($w == 'u') {
	$la_id = $_GET['la_id'];
	if(!$la_id) {
		alert("잘못된 접근입니다.");
	}
	$sql = "select * from loanaddr_history where la_id = '{$la_id}'  limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['la_id']) {
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
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>심사접수 <?php echo $btntxt; ?></h1>
</div>

<!-- CONTENT START -->

<div style="padding:15px;margin:auto;">
	<form id="fwrite" name="fwrite" action="/app/history-act.php" method="post" class="jsb-form">
	 <input type="hidden" name="w" value="<?php echo $w; ?>">
	 <input type="hidden" name="la_id" value="<?php echo $la_id; ?>">
	   <div class="form-group" >
			
			<div class="row"><label class="col-sm-2 control-label">접수일</label>
				<div class="col-sm-10"><input type="text" id="la_date" name="la_date" class="form-control datepicker" style="display:inline-block; width:120px;" maxlength="20" value="<?php echo $row['la_date'];?>" placeholder="일자선택"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">접수처</label>
				<div class="col-sm-10"><input type="text" id="la_partner" name="la_partner" value="<?php echo $row["la_partner"]; ?>" class="form-control" maxlength="20" placeholder="접수처"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">차주명</label>
				<div class="col-sm-10"><input type="text" id="la_name" name="la_name" value="<?php echo $row["la_name"]; ?>" class="form-control" maxlength="100" placeholder="차주명"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">담보주소</label>
				<div class="col-sm-10"><input type="text" id="la_addr" name="la_addr" value="<?php echo $row["la_addr"]; ?>" class="form-control" placeholder="담보주소"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">전용면적</label>
				<div class="col-sm-10"><input type="text" id="la_m2" name="la_m2" value="<?php echo $row["la_m2"]; ?>" class="form-control" style="display:inline-block; width:100px;" placeholder="000.00"> ㎡ (제곱미터)</div>
			</div>

			<div class="row"><label class="col-sm-2 control-label">담보가산정</label>
				<div class="col-sm-10"><input type="text" id="la_guarantee" name="la_guarantee" value="<?php echo $row["la_guarantee"]; ?>" class="form-control" placeholder="담보가산정"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">선순위원금</label>
				<div class="col-sm-10"><input type="text" id="la_priority_amount" name="la_priority_amount" value="<?php echo $row["la_priority_amount"]; ?>" class="form-control" placeholder="선순위원금"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">선순위최고액</label>
				<div class="col-sm-10"><input type="text" id="la_maximum_credit" name="la_maximum_credit" value="<?php echo $row["la_maximum_credit"]; ?>" class="form-control" placeholder="선순위최고액"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">한도제시액</label>
				<div class="col-sm-10"><input type="text" id="la_loan_amount" name="la_loan_amount" value="<?php echo $row["la_loan_amount"]; ?>" class="form-control" placeholder="한도제시액"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">담보구분</label>
				<div class="col-sm-10"><select id="la_category" name="la_category" class="form-control">
					<option value=''>선택</option>
					<?php 
						echo option_selected("아파트", $row['la_category'], "아파트"); 
						echo option_selected("오피", $row['la_category'], "오피"); 
						echo option_selected("다세대", $row['la_category'], "다세대"); 
						echo option_selected("다가구", $row['la_category'], "다가구"); 
						echo option_selected("주상복합", $row['la_category'], "주상복합"); 
						echo option_selected("단독", $row['la_category'], "단독"); 
						echo option_selected("연립", $row['la_category'], "연립"); 
						echo option_selected("도생", $row['la_category'], "도생"); 
						echo option_selected("노인복지", $row['la_category'], "노인복지"); 
						echo option_selected("상가", $row['la_category'], "상가"); 
						echo option_selected("구분상가", $row['la_category'], "구분상가"); 
						echo option_selected("근생", $row['la_category'], "근생"); 
						echo option_selected("토지", $row['la_category'], "토지"); 
						echo option_selected("기타", $row['la_category'], "기타"); 
					?>
				</select>
				</div>
			</div>

			<div class="row"><label class="col-sm-2 control-label">대출구분</label>
				<div class="col-sm-10"><select id="la_caloan" name="la_caloan" class="form-control">
					<option value=''>선택</option>
					<?php 
						echo option_selected("매매", $row['la_caloan'], "매매"); 
						echo option_selected("매매동시", $row['la_caloan'], "매매동시"); 
						echo option_selected("경매낙찰", $row['la_caloan'], "경매낙찰"); 
						echo option_selected("경매취하", $row['la_caloan'], "경매취하"); 
						echo option_selected("분양", $row['la_caloan'], "분양"); 
						echo option_selected("분양동시", $row['la_caloan'], "분양동시"); 
						echo option_selected("신탁해지", $row['la_caloan'], "신탁해지"); 
						echo option_selected("전세퇴거", $row['la_caloan'], "전세퇴거"); 
						echo option_selected("지분", $row['la_caloan'], "지분"); 
						echo option_selected("기타", $row['la_caloan'], "기타"); 
					?>
				</select>
				</div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">비고</label>
				<div class="col-sm-10"><input type="text" id="la_remark" name="la_remark" value="<?php echo $row["la_remark"]; ?>" class="form-control" placeholder="비고"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">참고1</label>
				<div class="col-sm-10"><input type="text" id="la_ref1" name="la_ref1" value="<?php echo $row["la_ref1"]; ?>" class="form-control" placeholder="참고1"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">참고2</label>
				<div class="col-sm-10"><input type="text" id="la_ref2" name="la_ref2" value="<?php echo $row["la_ref2"]; ?>" class="form-control" placeholder="참고2"></div>
			</div>
			
			<div class="row"><hr/></div>
			
		</div>
		<br class="clear"/>
		<div class="row">
			<div class="col-sm-4"><button class="btn <?php echo $btnclass; ?> btn-block col-sm-4" type="submit"><?php echo $btntxt; ?></button></div>
			<div class="col-sm-4"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="history.back();">돌아가기</button></div>
		</div>
    </form>
</div>

<script>
$(function () {
	commonjs.selectNav("navbar", "history");
		
	$(".datepicker").datepicker();
		
});

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
include_once '../footer.php';
?>