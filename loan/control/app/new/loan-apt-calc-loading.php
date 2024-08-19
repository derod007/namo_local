<?php
//include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
include_once '../../header.php';

$sendData = json_encode($_POST);

$wr_id = $_POST['wr_id'];

// 로그인 세션(등록자 아이디 등)과 wr_id 를 확인해봐야함.(파트너)
$sql = "select * from `loan_apt_tmp` where wr_id = '{$wr_id}' limit 1";
$row = sql_fetch($sql);

//print_r2($row);

?>
<style>
#loading { text-align:center; margin:auto;}
#loading img {width:60%; max-width:450px;}
#result {display:none;}
#result .msgbox {margin-top:30px; margin-bottom:30px;}
.fs-11em {font-size:1.1em;}
</style>

<div class="page-header">
	<div class="btn-div">
	<!-- a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a -->
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>대출신청(아파트) <?php echo $btntxt; ?></h1>
</div>

<!-- CONTENT START -->

<div style="padding:15px;margin:auto; height:100%;">

   <div class="form-group col-sm-12">
		<div class="col-sm-2"></div>
		<div class="col-sm-8 fs-11em">
			
			<h4>물건지 정보</h4><hr/>
			
			<div class="row"><label class="col-sm-2 control-label">주소</label>
			  <div class="col-sm-10"><?php echo $row['wr_addr1']." ".$row['wr_addr2']." ".$row['wr_addr3']; ?> </div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">전용면적</label>
			  <div class="col-sm-10"><?php echo $row['wr_m2']; ?>㎡</div>
			</div>
		
		</div>
		<div class="col-sm-2"></div>
	</div>
	<div class="row">&nbsp;</div>
	

	<div id="loading" class="row">
		<img src="/assets/img/loading_img.gif" alt="Loading...">
	</div>
	<div id="result">
	</div>
	
</div>

<script>

function number2hangul(number) {
  var num = ['', '일', '이', '삼', '사', '오', '육', '칠', '팔', '구'];
  var unit4 = ['', '만', '억', '조', '경'];
  var unit1 = ['', '십', '백', '천'];
  var res = [];
  number = number.toString().replace(',', '');
  var split4 = number.split('').reverse().join('').match(/.{1,4}/g);

  for (var i = 0; i < split4.length; i++) {
	  
    var temp = [];
    var split1 = split4[i].split('');
	
    for (var j = 0; j < split1.length; j++) {
      var u = parseInt(split1[j]);
      if (u > 0) {
        temp.push(u + unit1[j]);
      }
    }
    if (temp.length > 0) {
      res.push(temp.reverse().join('') + unit4[i]);
    }
  }

  return res.reverse().join('');
}

function sleep(num) {
    var now = new Date();
    var stop = now.getTime() + num;
    while(true) {
        now = new Date();
        if(now.getTime() > stop) return;
    }
}

$(document).ready(function () {
 
	var sendData = <?php echo $sendData;?>;
	$('#result_list').html(' ');
	$.ajax({
		type: 'POST',
		url : './ajax.loan-apt-calc.php',
		data: sendData,
		dataType: "json",
		success : function(result, status, xhr) {
			console.log(result);
			var res = result.judge;
			var hando = res.last_judge;
			var str = "";
			
			if(res.fail_code == 0 && hando > 1000) {
				str += "<div class='row text-center msgbox'><h3>가승인 자동한도는 ";
				str += number2hangul(res.last_judge*10000) + "원 입니다.";
				str += "</h3> ";
				str += "<h3>조회하신 내용으로 대출신청을 진행하시겠습니까? </h3></div> ";
				str += "<div class='row text-center'>";
				str += "<div class='col-sm-1'>&nbsp;</div>";
				str += "<div class='col-sm-4'><button onclick='go_loanwrite();' class='btn btn-primary btn-block'>진행</button></div>";
				str += "<div class='col-sm-2'>&nbsp;</div>";
				str += "<div class='col-sm-4'><button onclick='go_loanlist();' class='btn btn-default btn-block'>취소</button></div>";
				str += "<div class='col-sm-1'>&nbsp;</div>";
				str += "</div> ";
				
			} else {
				if(res.fail_code == 80) {
					str += "<div class='row text-center msgbox'><h3>기준시세 조회가 실패했습니다. 수동심사를 등록하시겠습니까? </h3></div> ";
					str += "<div class='row text-center'>";
					str += "<div class='col-sm-1'>&nbsp;</div>";
					str += "<div class='col-sm-4'><button onclick='go_loanwrite();' class='btn btn-primary btn-block'>수동심사 등록</button></div>";
					str += "<div class='col-sm-2'>&nbsp;</div>";
					str += "<div class='col-sm-4'><button onclick='go_loanlist();' class='btn btn-default btn-block'>등록취소</button></div>";
					str += "<div class='col-sm-1'>&nbsp;</div>";
					str += "</div> ";
				} else {
					str += "<div class='row text-center msgbox'><h3>한도가 부족해서 자동부결됩니다. 수동심사를 등록하시겠습니까?</h3></div> ";
					str += "<div class='row text-center'>";
					str += "<div class='col-sm-1'>&nbsp;</div>";
					str += "<div class='col-sm-4'><button onclick='go_loanwrite();' class='btn btn-primary btn-block'>수동심사 등록</button></div>";
					str += "<div class='col-sm-2'>&nbsp;</div>";
					str += "<div class='col-sm-4'><button onclick='go_loanlist();' class='btn btn-default btn-block'>등록취소</button></div>";
					str += "<div class='col-sm-1'>&nbsp;</div>";
					str += "</div> ";
				}
			}
			sleep(2000);
			$('#loading').hide();
			$('#result').append(str);
			$('#result').show();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(jqXHR.responseText);
		}
	});
	
	$('#go_loanwrite').on("click",function(f) {
	});
 
});
function go_loanwrite() {
	document.location.href='./loan-write-apt.php?jd=<?php echo $wr_id;?>';
}

function go_loanlist() {
	document.location.href='./loan-list.php';
}

</script>
	   
<?php
//print_r2($_GET);
//print_r2($row);
?>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';

