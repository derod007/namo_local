<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

$uno = $_GET['data_no'];
if(!$uno) {
	alert('잘못된 접근입니다.');
	die();
}

$sql = "SELECT * FROM `tilkoapi_risuconfirmsimplec` WHERE UniqueNo='{$uno}' limit 1";
$data = sql_fetch($sql);

if(!$data['UniqueNo']) {
	alert('해당되는 고유번호가 없습니다. 등기물건주소조회후 신청해주세요.');
	die();
}

$data['Gubun'];	//<!-- 구분(공백시 건물) 토지 : 0 / 건물 : 1 / 집합건물 : 2 -->
if($data['Gubun'] == "토지") {
	$data['GubunCode'] = '0';
} else if($data['Gubun'] == "건물") {
	$data['GubunCode'] = '1';
} else if($data['Gubun'] == "집합건물") {
	$data['GubunCode'] = '2';
} else {
	$data['GubunCode'] = 'E';
}

?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<a class="btn btn-default btn-sm" href="./iros_saved_list.php">목록</a></div>
	<h1>IROS 등기부등본 조회</h1>
</div>

<?php
//print_r2($data);
?>
<h4>물건 정보</h4>
<form name="fretrieve" id="fretrieve" method="get">
<input type="hidden" name="UniqueNo" value="<?php echo $data['UniqueNo'];?>">
<input type="hidden" name="JoinYn" value="Y">	<!-- 공동담보/전세목록 추출여부 -->
<input type="hidden" name="CostsYn" value="Y">	<!-- 매매목록추출여부 -->

<table class="table table-striped table-bordered jsb-table">
<tbody>
	<tr>
		<th class="col-sm-2">고유번호</th>
		<td class="col-sm-2"><?php echo $data['UniqueNo'];?></td>
		<th class="col-sm-2">구분</th>
		<td class="col-sm-2"><?php echo $data['Gubun'];?></td>
		<th class="col-sm-2">상태</th>
		<td class="col-sm-2"><?php echo $data['Sangtae'];?></td>
	</tr>
	<tr>
		<th class="col-sm-2">소유자명(1명만 기재)<br/>(등기사건조회시 必)</th>
		<td class="col-sm-8" colspan="4"><input type="text" name="Owner" value="<?php echo $data['Owner'];?>"  class="form-control"></td>
		<td class="col-sm-2"><button class="btn btn-sm btn-secondary btn-block" id="btn_owner" type="button">소유자 저장</button></td>
	</tr>
	<tr>
		<th class="col-sm-2">소재지번</th>
		<td class="col-sm-8" colspan="4"><?php echo $data['BudongsanSojaejibeon'];?></td>
		<th class="col-sm-2"><button class="btn btn-sm btn-danger btn-block" id="btn_retrieve" type="button">등기부등본 신규조회(과금)</button></th>
	</tr>
</tbody>
</table>
<div>
	※ 등기부등본 조회시 마다 과금됩니다. 기 조회한 자료인 경우 아래 최근 조회내역에서 [새창보기]/[PDF보기]로 확인해주세요.<br/><br/>
</div>
</form>


<h4>최근 등기부등본 조회내역(Max. 5건)</h4>
<table class="table table-striped table-bordered jsb-table">
	<tr>
		<th class="col-sm-2">조회일시</th>
		<th class="col-sm-5">요청데이터</th>
		<th class="col-sm-5">결과데이터</th>
	</tr>
<?php


$sql = "SELECT * FROM `tilko_api_log` WHERE api_url='/api/v1.0/iros/risuretrieve' and data_cnt > 0 and query like '{\"UniqueNo\":\"".$uno."\"%' order by log_id desc limit 5";
$result = sql_query($sql);
$no = 0;
while($row=sql_fetch_array($result)){
	$item = json_decode($row['result'], true);
	$res_view = "Status:".$item['Status'].PHP_EOL;
	$res_view .= "StatusSeq:".$item['StatusSeq'].PHP_EOL;
	$res_view .= "TransactionKey:".$item['TransactionKey'].PHP_EOL;
	if($item['Status'] == "Error") {
		$res_view .= "TargetCode:".$item['TargetCode'].PHP_EOL;
		$res_view .= "TargetMessage:".$item['TargetMessage'].PHP_EOL;
	}
	
	$sql = "SELECT idx, TransactionKey, pdf_filename FROM `tilkoapi_risuretrieve` WHERE UniqueNo = '{$uno}' and TransactionKey='{$item['TransactionKey']}' limit 1";
	$idxs = sql_fetch($sql);
	$link = "";
	if($idxs['idx']) {
		$link = "<br/><a href='./iros_risuretrieve_detail.php?data_no={$idxs['idx']}' target='_blank'>[새창보기]</a>";
		if($idxs['pdf_filename']) {
			$link .= "&nbsp; <a href='/data/tilko_pdf/{$idxs['pdf_filename']}' target='_blank'>[PDF보기]</a>";
		}
	}
	
?>
	<tr>
		<td class="col-sm-2"><?php echo $row['log_datetime'].$link;?></td>
		<td class="col-sm-5"><pre><?php echo $row['query'];?></pre></td>
		<td class="col-sm-5"><pre><?php echo $res_view;?></pre></td>
	</tr>
<?php
	$no++;
}
if(!$no) {
?>
	<tr>
		<td class="col-sm-12" colspan="3">조회내역이 없습니다.</td>
	</tr>
<?php
}
?>
</table>

<div id="debug_result" style="100%; max-height:200px; overflow:auto;"><pre></pre></div>

<?php
$sql = "SELECT * FROM `tilko_managed_data` WHERE UniqueNo='{$uno}' limit 1";
$mng = sql_fetch($sql);

?>
<h4>등기관리정보</h4> 
<form name="frevtwelcome" id="frevtwelcome" method="post">
<input type="hidden" name="UniqueNo" value="<?php echo $data['UniqueNo'];?>">
<input type="hidden" name="InsRealCls" value="<?php echo $data['Gubun'];?>">	<!-- 구분(공백시 건물) 토지 : 0 / 건물 : 1 / 집합건물 : 2 -->
<input type="hidden" name="A103Name" id="A103Name" value="<?php echo ($mng['Owner'])?$mng['Owner']:$data['Owner'];?>">	<!-- 소유자명 -->
<table class="table table-striped table-bordered jsb-table">
<tbody>
	<tr>
		<th class="col-sm-2">고유번호</th>
		<td class="col-sm-2"><?php echo $mng['UniqueNo'];?></td>
		<th class="col-sm-2">자동체크</th>
		<td class="col-sm-2"><?php echo $mng['autocheck'];?></td>
		<?php 
			if($mng['UniqueNo']) {
		?>
		<td class="col-sm-2"><a href="./iros_managed_form.php?data_no=<?php echo $mng['idx'];?>" target="_blank"><B>등기관리 수정</B></a></td>
		<td class="col-sm-2"><button class="btn btn-sm btn-warning btn-block" id="btn_revtwelcome" type="button">등기사건조회</button></td>
		<?php 
			} else {
		?>
		<td class="col-sm-2"><button class="btn btn-sm btn-success btn-block" id="btn_regmng" type="button">등기관리 등록</button></td>
		<td class="col-sm-2"><button class="btn btn-sm btn-warning btn-block" id="btn_revtwelcome" type="button">등기사건조회</button></td>
		<?php 
			}
		?>
	</tr>
	<tr>
		<th class="col-sm-2">소유자명</th>
		<td class="col-sm-2"><?php echo $mng['Owner'];?></td>
		<th class="col-sm-2">대출자명</th>
		<td class="col-sm-2"><?php echo $mng['NM_borrower'];?></td>
		<th class="col-sm-2">상품명</th>
		<td class="col-sm-2"><?php echo $mng['NM_pname'];?></td>
	</tr>
</tbody>
</table>
<div>
	※ 등기관리정보 소유자명 우선 적용<br/>
	※ 등기사건조회 시점에 최근 2개월 이내에 접수된 등기사항만 조회됩니다.<br/><br/>
</div>
</form>


<form name="fregmanaged" id="fregmanaged" method="post">
<input type="hidden" name="UniqueNo" value="<?php echo $data['UniqueNo'];?>">
<input type="hidden" name="GubunCode" value="<?php echo $data['GubunCode'];?>">	<!-- 구분(공백시 건물) 토지 : 0 / 건물 : 1 / 집합건물 : 2 -->
<input type="hidden" name="Owner" id="Owner" value="<?php echo $data['Owner'];?>">	<!-- 소유자명 -->
<input type="hidden" name="BudongsanSojaejibeon" id="BudongsanSojaejibeon" value="<?php echo $data['BudongsanSojaejibeon'];?>">	<!-- 부동산지번 -->
</form>


<h4>최근 등기사건조회내역(Max. 10건)</h4>
<table class="table table-striped table-bordered jsb-table">
	<tr>
		<th class="col-sm-2">조회일시</th>
		<th class="col-sm-10">요청데이터/결과데이터</th>
	</tr>
<?php

$sql = "SELECT * FROM `tilko_api_log` WHERE api_url='/api/v1.0/iros/revtwelcomeevtc' and query like '{\"UniqueNo\":\"".$uno."\"%' order by log_id desc limit 5";
$result = sql_query($sql);
$no = 0;
while($row=sql_fetch_array($result)){
	$item = json_decode($row['result'], true);
	$res_view = "Status:".$item['Status'].PHP_EOL;
	$res_view .= "StatusSeq:".$item['StatusSeq'].PHP_EOL;
$res_view .= "Result:\n".str_replace("},{","},\n{",json_encode($item['ResultList'], JSON_UNESCAPED_UNICODE)).PHP_EOL;
	if($item['Status'] == "Error") {
		$res_view .= "TargetCode:".$item['TargetCode'].PHP_EOL;
		$res_view .= "TargetMessage:".$item['TargetMessage'].PHP_EOL;
	}
	
?>
	<tr>
		<td class="col-sm-2"><?php echo $row['log_datetime'];?></td>
		<td class="col-sm-10">
			<pre style="max-width: 1100px;"><?php echo $row['query'];?></pre>
			<pre style="overflow: scroll;word-wrap: break-word;word-break: break-all;max-width: 1100px;"><?php echo $res_view;?></pre>
		</td>
	</tr>
<?php
	$no++;
}
if(!$no) {
?>
	<tr>
		<td class="col-sm-12" colspan="3">조회내역이 없습니다.</td>
	</tr>
<?php
}
?>
</table>


<script>

$(function () {
		
    commonjs.selectNav("navbar", "iros_saved_list");
	
	$('#btn_retrieve').click(function(){   // 등기부등본조회 버튼을 클릭하였을 때
		
		var sendData = $( "#fretrieve" ).serialize();   // 폼의 값을 변수 안에 담아줌
		
		$.ajax({
			type:'post',   //post 방식으로 전송
			url:'/app/tilko/api_risuretrieve.php',   // action url	
			//url:'/test/test2.json',   // action url	
			data:sendData,   	// 전송할 데이터
			dataType:'json',   	
			success : function(data){   //파일 주고받기가 성공했을 경우. data 변수 안에 값을 담아온다.
				//console.log(data);
				$('#debug_result>pre').html(JSON.stringify(data));
				
				if(data.Status == "OK") {
					var gourl = '/app/tilko/iros_risuretrieve_detail.php?data_no=' + data.Data_id;
					location.replace(gourl);
					
				} else {
					$('#debug_result>pre').html(data.Status + " : " + data.Message + "\n" + data.TargetCode + " : " + data.TargetMessage);
				}
			},
			error:function(request,status,error){
				console.log("error");
				$('#debug_result>pre').html(error);
			}
		});
		$('#btn_retrieve').attr("disabled", true);
	});
	
	$('#btn_owner').click(function(){   // 소유자 변경 버튼을 클릭하였을 때
		var sendData = $( "#fretrieve" ).serialize();   // 폼의 값을 변수 안에 담아줌
		
		$.ajax({
			type:'post',   //post 방식으로 전송
			url:'/app/tilko/api_save_owner.php',   // action url	
			//url:'/test/test2.json',   // action url	
			data:sendData,   	// 전송할 데이터
			dataType:'json',   	
			success : function(data){   //파일 주고받기가 성공했을 경우. data 변수 안에 값을 담아온다.
				//console.log(data);
				if(data.Status == "OK") {
					alert("소유자명이 저장되었습니다.");
					var gourl = '/app/tilko/iros_risuretrieve_form.php?data_no=<?php echo $uno;?>';
					location.replace(gourl);
				} else {
					alert("알수없는 오류");
				}
			},
			error:function(request,status,error){
				alert("알수없는 오류");
				//$('#debug_result>pre').html(data.Status + " : " + data.Message);
				$('#debug_result>pre').html(data.Status + " : " + data.Message + "\n" + data.TargetCode + " : " + data.TargetMessage);
			}
		});
	
	});
	
	$('#btn_revtwelcome').click(function(){   // 등기사건조회 버튼을 클릭하였을 때
		
		if($( "#A103Name" ).val() == '') {
			alert('소유자명을 저장후 조회해주세요');
			return;
		}
		
		var sendData = $( "#frevtwelcome" ).serialize();   // 폼의 값을 변수 안에 담아줌
		
		$.ajax({
			type:'post',   //post 방식으로 전송
			url:'/app/tilko/api_revtwelcomeevtc.php',   // action url	
			//url:'/test/test2.json',   // action url	
			data:sendData,   	// 전송할 데이터
			dataType:'json',   	
			success : function(data){   //파일 주고받기가 성공했을 경우. data 변수 안에 값을 담아온다.
				//console.log(data);
				$('#debug_result>pre').html(JSON.stringify(data));
				
				if(data.Status == "OK") {
					alert(data.Message);
					var gourl = '/app/tilko/iros_risuretrieve_form.php?data_no=<?php echo $uno;?>';
					location.replace(gourl);
					
				} else {
					$('#debug_result>pre').html(data.Status + " : " + data.Message);
				}
			},
			error:function(request,status,error){
				console.log("error");
				$('#debug_result>pre').html(error);
			}
		});
		$('#btn_revtwelcome').attr("disabled", true);
	});
		
	$('#btn_regmng').click(function(){   // 등기관리 등록 버튼을 클릭하였을 때
		
		if($( "#A103Name" ).val() == '') {
			alert('소유자명을 저장후 조회해주세요');
			return;
		}
		
		var sendData = $( "#fregmanaged" ).serialize();   // 폼의 값을 변수 안에 담아줌
		
		$.ajax({
			type:'post',   //post 방식으로 전송
			url:'/app/tilko/api_regmanaged.php',   // action url	
			data:sendData,   	// 전송할 데이터
			dataType:'json',   	
			success : function(data){   //파일 주고받기가 성공했을 경우. data 변수 안에 값을 담아온다.
				//console.log(data);
				$('#debug_result>pre').html(JSON.stringify(data));
				
				if(data.Status == "OK") {
					alert("등기관리 목록에 등록되었습니다.");
					var gourl = '/app/tilko/iros_risuretrieve_form.php?data_no=<?php echo $uno;?>';
					location.replace(gourl);
					
				} else {
					//$('#debug_result>pre').html(data.Status + " : " + data.Message);
					$('#debug_result>pre').html(data.Status + " : " + data.Message + "\n" + data.TargetCode + " : " + data.TargetMessage);
				}
			},
			error:function(request,status,error){
				console.log("error");
				$('#debug_result>pre').html(error);
			}
		});
		$('#btn_regmng').attr("disabled", true);
	});
	

})
	
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>

