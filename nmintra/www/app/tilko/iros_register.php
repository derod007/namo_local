<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/iros_register.php
include_once '../../header.php';

/*
if($yymm) {
	if(strlen($yymm) != 6 || is_int($yymm)) {
		alert('yyyymm 형식으로 입력해주세요.', '/iros_register.php');
	}
	
	if(!$region) {
		alert('지역을 선택해주세요.', '/iros_register.php');
	}
}
*/

?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>IROS 등기물건주소 조회</h1>
</div>

<div class="search-box max-768-target">
	<form name="fsearch1" id="fsearch1" method="get">
		<div class="row">
			<div class="col-sm-1">
				<label>현행/폐쇄</label>
				<select id="Sangtae" name="Sangtae"  class="form-control">
					<option value="">선택</option>
				<?php
					$sangtae_arr = array(
						"0" => "현행",
						"1" => "폐쇄",
						"2" => "현행+폐쇄",
					);
					
					foreach($sangtae_arr as $k => $v) {
						echo option_selected_exact($k, $Sangtae, $v);
					}
				?>	
				</select>
			</div>
			<div class="col-sm-1">
				<label>부동산구분</label>
				<select id="KindClsFlag" name="KindClsFlag"  class="form-control">
					<option value="">선택</option>
				<?php
					$kindcls_arr = array(
						"0" => "전체",
						"1" => "집합건물",
						"2" => "건물",
						"3" => "토지"						
					);
					
					foreach($kindcls_arr as $k => $v) {
						echo option_selected_exact($k, $kindcls_arr, $v);
					}
				?>	
				</select>
			</div>
			<div class="col-sm-4">
				<label><a onclick="execDaumPostcode();">☞주소검색</a></label><!-- 277개 -->
				<input type="text" name="address1" id="address1" value="" class="form-control">
				<span id="guide" style="color:#999;display:none"></span>
				<input type="text" name="address2" id="address2" value="" class="form-control" readonly="readonly" style="display:none">
				<input type="text" name="address3" id="address3" value="" class="form-control" placeholder="상세주소(동/호,건물명)">
			</div>
			<div class="col-sm-1">
				<label class="hidden-xs" style="width:100%">Page</label>
				<select id="CurPage" name="CurPage"  class="form-control">
					<option value="">선택</option>
				</select>
			</div>
			<div class="col-sm-1">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="btn_search1" type="button">검색</button>
			</div>
			<div class="col-sm-1">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-default btn-block" id="btn_reset1" type="button" onclick="form_reset();">초기화</button>
			</div>
		</div>
		
	</form>
</div>
<div>
	※ 검색결과는 10건씩 조회되므로 최대한 정확한 주소로 검색을 해주세요.(동/호수 및 건물명 포함)<br/>
	※ Total 조회건수가 10건 이상인 경우 Page 를 변경해서 조회하거나, 상세주소를 입력해서 조회범위를 좁혀서 조회해주세요.<br/>
	※ 토지 조회시 주소검색에서 나오지 않는 경우 동/리까지만 검색후 지번주소를 직접 수정후 조회해주세요.<br/>
</div>

<div id="debug_result" style="100%"><pre></pre></div>
<div id="debug_result2" style="100%"><pre></pre></div>
<table id="datatable1" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"><thead><caption>Total:<span id="dcount">0</span>건</caption></thead><tbody></tbody></table>

<script>
	$(function () {
		commonjs.selectNav("navbar", "iros_register");
		
		$('#btn_search1').click(function(){   //submit 버튼을 클릭하였을 때
			
			
			if(document.fsearch1.address1.value == '') {
				alert("주소검색을 하신뒤 검색해주세요.");
				return;
			}
			
			$('#btn_search1').attr("disabled", true);
			$('#debug_result>pre').html('');
			$('#dcount').html('0');
			
			var sendData = $( "#fsearch1" ).serialize();   // 폼의 값을 변수 안에 담아줌
			$('#CurPage').html('<option value="">선택</option>');
			$.ajax({
				type:'post',   //post 방식으로 전송
				url:'/app/tilko/api_risuconfirm.php',   // action url	
				//url:'/test/test2.json',   // action url	
				data:sendData,   	// 전송할 데이터
				dataType:'json',   	
				success : function(data){   //파일 주고받기가 성공했을 경우. data 변수 안에 값을 담아온다.
					console.log(data);
					$('#debug_result>pre').html(JSON.stringify(data));
					$('#debug_result2>pre').val(data);
					if(data.Status == "OK") {
						
						var items = data.ResultList;
						var totalpage = data.TotalPages;
						var curpage = data.CurrentPages;
						
						$('#dcount').html(data.TotalCount);
						$('#datatable1>tbody').html('');
						$('#datatable1>tbody').append('<tr><th>고유번호</th><th>구분</th><th>소재지번</th><th>상태</th><th>기능</th></tr>\n');
						if(items !== null) {
							$.each(items, function(k, v) {
								$('#datatable1>tbody').append('<tr><td>' + v.UniqueNo + '</td><td>' + v.Gubun + '</td><td>' + v.BudongsanSojaejibeon + '</td><td>' + v.Sangtae + '</td><td><a href="/app/tilko/iros_risuretrieve_form.php?data_no=' + v.UniqueNo + '" target="_blank">조회</a></td></tr>\n');
							});
							//$('#datatable1').select2({width: "100%"});
						}
						
						// 검색 페이지가 1페이지(10건) 이상인 경우
						if(totalpage > 1) {
							for(p=1;p<=totalpage; p++) {
								$('#CurPage').append('<option value="'+p+'">'+p+'</option>');
							}
							$('#CurPage').val(curpage);
						}
						
					} else {
						$('#datatable1>tbody').append('<tr><td colspan="5">' + data.Status + " : " + data.Message + '</td></tr>\n');
						$('#debug_result2>pre').html(data.Status + " : " + data.Message);
					}
				},
				error:function(request,status,error){
					console.log("error");
					$('#debug_result2>pre').html(error);
				}
			});
			$('#btn_search1').attr("disabled", false);
		});
		
    });
	
	function form_reset() {
		document.fsearch1.reset();
		$('#btn_search1').attr("disabled", false);
		$('#debug_result>pre').html('');
	}
</script>



<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    //본 예제에서는 도로명 주소 표기 방식에 대한 법령에 따라, 내려오는 데이터를 조합하여 올바른 주소를 구성하는 방법을 설명합니다.
    function execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

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