<?php
//error_reporting(E_ALL);
ini_set("display_errors", 0);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

$idx = $_GET['data_no'];
if(!$idx) {
	alert('잘못된 접근입니다.');
	die();
}

$sql = " select a.*, b.Gubun, b.BudongsanSojaejibeon, b.Sangtae, b.Owner  
			from tilkoapi_risuretrieve as a left join tilkoapi_risuconfirmsimplec as b on a.UniqueNo = b.UniqueNo
			where a.idx = '{$idx}' limit 1";
$data = sql_fetch($sql);

if(!$data['idx']) {
	alert('해당되는 데이터가 없습니다.');
	die();
}

if($data['Status'] == 'Error') {
	$sql = "select * from tilko_api_log where api_url='/api/v1.0/iros/risuretrieve' and UniqueNo = '{$data['UniqueNo']}' and log_datetime='{$data['wdatetime']}' limit 1";
	$error = sql_fetch($sql);
	$error_dec = json_decode($error['result'], TRUE);
	//print_r2($error_dec);
	$data['Result'] .= "\n".$error_dec['TargetMessage'];
}

//print_r2($data);

?>
<style>
.glyphicon-refresh-animate {
    -animation: spin .7s infinite linear;
    -webkit-animation: spin2 .7s infinite linear;
}

@-webkit-keyframes spin2 {
    from { -webkit-transform: rotate(0deg);}
    to { -webkit-transform: rotate(360deg);}
}

@keyframes spin {
    from { transform: scale(1) rotate(0deg);}
    to { transform: scale(1) rotate(360deg);}
}
</style>

<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<a class="btn btn-default btn-sm" href="./iros_risuretrieve_history.php">목록</a></div>
	<h1>IROS 등기부등본 상세보기</h1>
</div>

<?php
//print_r2($data);
?>
<h4>물건 정보</h4>

<form name="fdetail" id="fdetail" method="post">
<input type="hidden" name="idx" value="<?php echo $idx;?>">
<input type="hidden" name="TransactionKey" value="<?php echo $data['TransactionKey'];?>">
<input type="hidden" name="UniqueNo" value="<?php echo $data['UniqueNo'];?>">

<table class="table table-striped table-bordered jsb-table">
<tbody>
	<tr>
		<th class="col-sm-2">TransactionKey</th>
		<td class="col-sm-10" colspan="3"><?php echo $data['TransactionKey'];?></td>
		<th class="col-sm-2">조회일시</th>
		<td class="col-sm-2"><?php echo $data['wdatetime'];?></td>
	</tr>
	<tr>
		<th class="col-sm-2">고유번호</th>
		<td class="col-sm-2"><a href="./iros_risuretrieve_form.php?data_no=<?php echo $data['UniqueNo'];?>"><?php echo $data['UniqueNo'];?></a></td>
		<th class="col-sm-2">구분</th>
		<td class="col-sm-2"><?php echo $data['Gubun'];?></td>
		<th class="col-sm-2">상태</th>
		<td class="col-sm-2"><?php echo $data['Sangtae'];?></td>
	</tr>
	<tr>
		<th class="col-sm-2">소재지번</th>
		<td class="col-sm-8" colspan="4"><?php echo $data['BudongsanSojaejibeon'];?></td>
		<th class="col-sm-2"><?php if($data['pdf_filename']) { ?><button class="btn btn-sm btn-danger btn-block" id="btn_pdfdown" type="button">PDF 보기</button>
			<?php } else if(date("Y-m-d H:i:s", strtotime($data['wdatetime']." +1 hour")) < TIME_YMDHIS ) { ?>
				<button class="btn btn-sm btn-danger btn-block" type="button" onclick="alert('등본 발급 후 1시간이 지난 건은 열람이 불가능합니다.');">생성시간 초과</button>
				
			<?php } else if(!$data['TransactionKey']) { echo $data['Result']; ?>
			<?php } else { ?><div id="div_pdfsave"><button class="btn btn-sm btn-success btn-block" id="btn_pdfsave" type="button">PDF 생성</button></div>
			<?php } ?>
		</th>
	</tr>
	<tr>
		<th class="col-sm-2">소유자명(1명만 기재)<br/>(등기사건조회시 必)</th>
		<td class="col-sm-8" colspan="4"><input type="text" name="Owner" value="<?php echo $data['Owner'];?>"  class="form-control"></td>
		<td class="col-sm-2"><button class="btn btn-sm btn-secondary btn-block" id="btn_owner" type="button">소유자 저장</button></td>
	</tr>	
</tbody>
</table>
</form>
<div>
	※ 등기부 조회후 소유자명을 저장해주세요.<br/>
	※ 고유번호 클릭시 물건 상세페이지로 이동됩니다.<br/>
	<?php 
	// PDF 파일이 생성되었고, 1시간 이내인 경우 재새성 버튼 활성화
		if($data['pdf_filename'] && $data['TransactionKey'] && date("Y-m-d H:i:s", strtotime($data['wdatetime']." +1 hour")) >= TIME_YMDHIS) { 
	?>
	※ 생성된 PDF 파일에 오류가 있는 경우 PDF 파일을 재생성 해주시기 바랍니다.(등기부등본 조회후 1시간 이내만 가능) <br/>
	<div class="row"><div id="div_pdfresave" class="col-sm-2"><button class="btn btn-sm btn-success btn-block" id="btn_pdfresave" type="button">PDF 재생성</button></div></div>
	<br/>
	<?php 
		} 
	?>
</div>

<div id="debug_result2" style="100%"><pre></pre></div>
<div id="debug_result" style="100%; max-height:200px; overflow:auto; border:1px solid #aaa; padding:10px;"><pre><?php echo htmlspecialchars(($data['Result']));?></pre></div>
<?php
$DataResult = $data['Result'];
$DataResult = str_replace("^","&#94;", $DataResult);
$DataResult = str_replace("3ndHS","HS3nd", $DataResult);

$DataResult = substr($DataResult, strpos($DataResult, '<summary>') + 0);		// <summary> 앞부분 제거

$DataResult = substr($DataResult, 0, strpos($DataResult, '</summary>') + 10);	// </summary> 뒤 제거


$xmldata = "<?xml version='1.0' encoding='UTF-8'?>".PHP_EOL;
$xmldata .= "<documents>".PHP_EOL;
$xmldata .= $DataResult;
$xmldata .= "</documents>".PHP_EOL;

$xml = simplexml_load_string($xmldata);
//$summary = $xml->summary;
$json = json_encode($xml);
$obj = json_decode($json, TRUE);
?>
<div id="detail_view" style="100%; max-height:300px; overflow:auto; border:1px solid #aaa; padding:10px;"><pre><?php echo htmlspecialchars($DataResult);?></pre></div>
<!--div id="detail_view" style="100%; max-height:600px; overflow:auto; border:1px solid #aaa; padding:10px;"><pre><?php //echo print_r($obj);?></pre></div-->


<iframe id="hiddenframe" style="display:none;"></iframe>

<script>

$(function () {
		
    commonjs.selectNav("navbar", "iros_risuretrieve_history");
	
	$('#btn_pdfdown').on('click', function (event) {   // 다운로드 버튼을 클릭하였을 때
		var file_url = '<?php echo "/data/tilko_pdf/".$data['pdf_filename'];?>';
		window.open(file_url);
	});

	$('#btn_pdfsave').on('click', function (event) {   // 생성 버튼을 클릭하였을 때
		
		$('#debug_result2>pre').html('PDF 파일을 생성중입니다. 생성에는 수분정도 걸릴수 있습니다.\n생성이 완료된후 PDF 보기 버튼을 눌러 파일을 다운로드 하세요.');
		$('#div_pdfsave').html('<button class="btn btn-sm btn-warning btn-block"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...</button>');
		
		var sendData = $( "#fdetail" ).serialize();   // 폼의 값을 변수 안에 담아줌
		$.ajax({
			type:'post',   //post 방식으로 전송
			url:'/app/tilko/get_pdffile.php',   // action url	
			data:sendData,   	// 전송할 데이터
			dataType:'json',   	
			success : function(data){   //파일 주고받기가 성공했을 경우. data 변수 안에 값을 담아온다.
				if(data.Status == "OK") {
					alert('PDF 파일이 생성되었습니다.');
					location.reload();
				} else {
					$('#debug_result2>pre').html(data.Status + " : " + data.Message + ' ' + data.TargetMessage);
				}
			},
			error:function(request,status,error){
				console.log("error : " + error);
				console.log(status);
				$('#debug_result2>pre').html(error);
			}
		});

	});
	
	$('#btn_pdfresave').on('click', function (event) {   // 재생성 버튼을 클릭하였을 때
		
		$('#debug_result2>pre').html('PDF 파일을 생성중입니다. 생성에는 수분정도 걸릴수 있습니다.\n생성이 완료된후 PDF 보기 버튼을 눌러 파일을 다운로드 하세요.');
		$('#div_pdfsave').html('<button class="btn btn-sm btn-warning btn-block"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...</button>');
		
		var sendData = $( "#fdetail" ).serialize();   // 폼의 값을 변수 안에 담아줌
		$.ajax({
			type:'post',   //post 방식으로 전송
			url:'/app/tilko/get_pdffile.php',   // action url	
			data:sendData,   	// 전송할 데이터
			dataType:'json',   	
			success : function(data){   //파일 주고받기가 성공했을 경우. data 변수 안에 값을 담아온다.
				if(data.Status == "OK") {
					alert('PDF 파일이 생성되었습니다.');
					location.reload();
				} else {
					$('#debug_result2>pre').html(data.Status + " : " + data.Message + ' ' + data.TargetMessage);
				}
			},
			error:function(request,status,error){
				console.log("error : " + error);
				console.log(status);
				$('#debug_result2>pre').html(error);
			}
		});

	});
	
	
	<?php if(!$data['pdf_filename'] && $data['TransactionKey'] && date("Y-m-d H:i:s", strtotime($data['wdatetime']." +1 hour")) >= TIME_YMDHIS) { ?>
	// PDF 자동생성
	$('#btn_pdfsave').trigger("click");
	<?php } ?>
	
	$('#btn_owner').click(function(){   // 소유자 변경 버튼을 클릭하였을 때
		
		var sendData = $( "#fdetail" ).serialize();   // 폼의 값을 변수 안에 담아줌
		
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
					$('#debug_result>pre').html("소유자명이 저장되었습니다.");
				} else {
					alert("알수없는 오류");
				}
			},
			error:function(request,status,error){
				alert("알수없는 오류");
				$('#debug_result>pre').html(data.Status + " : " + data.Message);
			}
		});
	
	});
	
})
	
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>

