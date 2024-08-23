<?php
error_reporting(E_ALL);
ini_set("display_errors", 0);
// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';
require '../../vendor/autoload.php';

use Smalot\PdfParser\Parser;

$new_post = $_GET['new_post'] ?? '0';
$pjfile = get_writefile($wr_id);
$filteredFiles = array_filter($pjfile, function($item) {
	return isset($item['category']) && $item['category'] === '등기부등본';
});

$filteredFiles = array_values($filteredFiles);
// var_dump($filteredFiles);
// 최초입력에 등기부등본이 존재할경우 파싱 및 기입
if(!empty($filteredFiles) && $new_post=='1'){
	// file과 name을 합친 변수 생성
	foreach ($filteredFiles as &$filepath) {
		$filepath['full_path'] = "../..".$filepath['path'] . '/' . $filepath['file'];
	}
	// PDF 파일 경로
	$pdfFilePath = $filepath['full_path'];

	$parser = new Parser();
	$pdf = $parser->parseFile($pdfFilePath);
	$text = $pdf->getText();

	$cate = 'E'; // 기본값으로 'E'(기타) 설정
	if (strpos($text, '아파트') !== false) {
		$cate = 'A';
	} elseif (strpos($text, '빌라') !== false) {
		$cate = 'B';
	}

	// park 전용면적
	$startSearch0  = '전유부분의 건물의 표시 )';
	$endSearch0  = '대지권의';
	$startPos0 = strpos($text, $startSearch0);
	$endPos0 = strpos($text, $endSearch0, $startPos0);
	$text0 = '';
	$area = [];
	if ($startPos0 !== false && $endPos0 !== false) {
		$startPos0 += strlen($startSearch0);
		$text0 = substr($text, $startPos0, $endPos0 - $startPos0);
		$text0= trim($text0);
		// 제곱미터 앞에 숫자 추출
		preg_match_all('/\d+(\.\d+)?(?=\s*㎡)/', $text0, $matches);
		if (!empty($matches[0])) {
			$area = $matches[0];
		}
	}

	// park 소유자
	$startSearch1 = '소유지분현황 ( 갑구 )';
	$endSearch1 = '2. 소유지분을';
	$startPos1 = strpos($text, $startSearch1);
	$endPos1 = strpos($text, $endSearch1, $startPos1);
	$owner = '';
	if ($startPos1 !== false && $endPos1 !== false) {
		$startPos1 += strlen($startSearch1);
		$owner = substr($text, $startPos1, $endPos1 - $startPos1);
		$owner = trim($owner);
	}

	// park 근저당권 및 전세권 등
	$startSearch2 = '전세권 등 ( 을구 )';
	$endSearch2 = '[ 참';
	$startPos2 = strpos($text, $startSearch2);
	$endPos2 = strpos($text, $endSearch2, $startPos2);

	$mortgage = '';
	if ($startPos2 !== false && $endPos2 !== false) {
		$startPos2 += strlen($startSearch2);
		$mortgage = substr($text, $startPos2, $endPos2 - $startPos2);
		$mortgage = trim($mortgage);
	}

	// 테스트중!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	preg_match_all('/(전세권|근저당권설정|근저당권변경|질권)[^\d]*(\d{4}년\d{1,2}월\d{1,2}일)[^\d]*금([\d,]+)원(?:[^채권자근저당권자전세권자]*(채권자|근저당권자|전세권자)\s+([^\s]+))?/u', $text, $matches);

	// 	// 임의 계산
	// 	echo preg_replace("/[^\d]/","",$t3[$key])*1.5."<br/>";
	// }

	// 행을 생성하여 표시
	$output = "";
	for ($i = 0; $i < count($matches[0]); $i++) {
		$t1 = $matches[1][$i];
		$t2 = $matches[2][$i];
		$t3 = $matches[3][$i];
		$t4 = isset($matches[4][$i]) ? $matches[4][$i] : '';
		$t5 = isset($matches[5][$i]) ? $matches[5][$i] : '';

		// 각 행을 HTML로 출력
		$output .= "<div class='row' id='row_$i' style='width:65%;  margin:5px 0 5px 0; border-bottom: 1px solid #ccc'>";
		$output .= "<span class='line-text'>$t1 / $t2 / $t3 / $t4 $t5 </span>";
		$output .= "<button type='button' onclick='highlightRow($i)' style='float:right;' class='btn-warning'>대환</button>";
		$output .= "</div>";
	}

	// park 신규주소
	$startSearch3 = ']';
	$endSearch3 = '고유번호';
	$startPos3 = strpos($text, $startSearch3);
	$endPos3 = strpos($text, $endSearch3, $startPos3);

	$new_addr = '';
	if ($startPos3 !== false && $endPos3 !== false) {
		$startPos3 += strlen($startSearch3);
		$new_addr = substr($text, $startPos3, $endPos3 - $startPos3);
		$new_addr = trim($new_addr);
	}
	
	$pattern = '/^(.*?\d+[-\d]*)(.*)$/u';
	preg_match($pattern, $new_addr, $matches);

	if (isset($matches[1])) {
		$new_addr1 = trim($matches[1]);
		$new_addr2 = trim($matches[2]);
	} else {
		$new_addr1 = $new_addr;
		$new_addr2 = '';
	}


	// 불필요한 헤더 및 메타데이터 제거
	function removeHeaders($text) {
		$patterns = [
			'/등기명의인\s*\(주민\)등록번호\s*최종지분\s*주\s*소\s*순위번호\s*/u',
			'/순위번호\s*등기목적\s*접수정보\s*주요등기사항\s*대상소유자\s*/u',
			
		];

		foreach ($patterns as $pattern) {
			$text = preg_replace($pattern, '', $text);
		}

		return trim($text);
	}

	$owner = removeHeaders($owner);
	$mortgage = removeHeaders($mortgage);
}

$w = $_GET['w'];

if($member['is_sub']) {
	$pt_idx = $member['parent_id'];
} else {
	$pt_idx = $member['idx'];
}


if($w == 'u') {
	$wr_id = $_GET['wr_id'];
	if(!$wr_id) {
		alert("잘못된 접근입니다.");
	}
	$sql = "select * from loan_write where wr_id = '{$wr_id}' and pt_idx='".$pt_idx."' and wr_datetime >= '".LIMIT_YMD."' limit 1";
	$row = sql_fetch($sql);
	
	if(!$row['wr_id']) {
		alert('해당되는 데이터가 없습니다');
	}
	if($new_post=='1'){
		$btntxt = "등록";
		$btnclass = "btn-primary";
	}else{
		$btntxt = "수정";
		$btnclass = "btn-warning";
	}
	
} else {
	$btntxt = "등록";
	$btnclass = "btn-primary";
	
	$row['wr_ca']='B';
	$row["wr_link1_subj"] = "KB시세조회";
}

?>

<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<h2>등기부 우선 등록</h2>
    <p>우선 등록시 일부 정보가 자동 기입 됩니다.</p>
	<span style="color:red">
		이미 입력 된 상태에서 신규 등록할 경우 정보가 변경됩니다.<br/>
		임시저장된 게시글일 경우 자동 기입은 진행하지 않습니다.
	</span>
</div>

<form name="fpfilereg" id="fpfilereg" method="post" enctype="multipart/form-data" action="./loan-upload.php"
    class="form-inline">
    <input type="hidden" name="w" value="file">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
    
	<?php
		if(empty($filteredFiles)){
	?>
	    <select id="category" name="category[]" class="form-control">
			<option value="등기부등본">등기부등본</option>
		</select>
		<input type="file" id="uploadfile" name="uploadfile[]" value="" required class="form-control">
		<button class="btn btn-success" type="submit">파일등록</button>
	<?php
		}else{
	?>
		<p>이미 등록된 등기부등본이 있습니다.
	<?php }?>
</form>
<br/><br/>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>대출신청<?php if(!$wr_id) { echo "(빌라/토지/수동등록)"; } ?> <?php echo $btntxt; ?></h1>
</div>

<!-- CONTENT START -->

<div style="padding:15px;margin:auto;">
	<form id="fwrite" name="fwrite" action="/app/loan/loan-act.php" method="post" class="jsb-form">
	 <input type="hidden" name="w" value="<?php echo $w; ?>">
	 <input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
	 <input type="hidden" name="prev_status" value="<?php echo $row['wr_status']; ?>">
	   <div class="form-group" >
			<?php if($row['wr_status'] > 1) { ?>
			<div class="row"><label class="col-sm-2 control-label">진행상태</label>
				<div class="col-sm-10"><span class="loan-status-<?php echo $row['wr_status']; ?>"><?php echo $status_arr[$row['wr_status']]; ?></span></div>
			</div>
			<?php } ?>

			<?php if($row['wr_status'] > 1) { ?>
			
				<?php
					$judge_date = "";
					if($row['wr_status'] >= 30) {
						
						$sql = "SELECT reg_date FROM `log_judge` WHERE `wr_id` = '{$row['wr_id']}' order by jd_id desc limit 1" ;
						$row_date = sql_fetch($sql);
						if($row_date['reg_date']) {
							$judge_date = "<br/>(".substr($row_date['reg_date'],0,16).")";
						}
					}
				?>
			
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
							<td><?php echo $row["jd_condition"].$judge_date; ?></td>
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
				  <input type="radio" id="control_01" name="wr_ca" value="A" required <?php echo ($row['wr_ca']=='A' || $cate=='A')?"checked":"";?>>
				  <label for="control_01">아파트 &nbsp;</label>
				  <input type="radio" id="control_02" name="wr_ca" value="B" required <?php echo ($row['wr_ca']=='B' || $cate=='B')?"checked":"";?>>
				  <label for="control_02">빌라 &nbsp;</label>
				  <input type="radio" id="control_03" name="wr_ca" value="E" required <?php echo ($row['wr_ca']=='E' || $cate=='E')?"checked":"";?>>
				  <label for="control_03">기타 &nbsp;</label>
			  </div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">지분여부</label>
			  <div class="col-sm-10 bs-padding10">
				  <input type="radio" id="control_04" name="wr_part" value="A" required <?php echo ($row['wr_part']=='A' || strpos($owner, '단독소유'))?"checked":"";?>>
				  <label for="control_04">단독소유 &nbsp;</label>
				  <input type="radio" id="control_05" name="wr_part" value="P" required <?php echo ($row['wr_part']=='P')?"checked":"";?>>
				  <label for="control_05">지분소유(50%) &nbsp;</label>
				  <input type="radio" id="control_06" name="wr_part" value="PE" required <?php echo ($row['wr_part']=='PE')?"checked":"";?>>
				  <label for="control_06">지분소유(기타) &nbsp;</label>
				  <input type="number" id="control_07" name="wr_part_percent" value="<?php echo $row['wr_part_percent'];?>" min="0" max="100" placeholder="30" style="width:50px;" <?php if($row['wr_part']!='PE') echo "";?>>%
				   (보유지분이 50%가 아닌 경우 보유지분율을 입력하세요)
			  </div>
			</div>
		  
			<div class="row"><label class="col-sm-2 control-label">제목</label>
				<div class="col-sm-10"><input type="text" id="wr_subject" name="wr_subject" value="<?php echo $row["wr_subject"]; ?>" required class="form-control required" placeholder="홍길동 / 담보종류 / 자금용도 (확인된 사항만 기재)"></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">대출자정보</label>
				<div class="col-sm-10"><textarea id="wr_cont1" name="wr_cont1" class="form-control" style="height:80px;" placeholder="자유양식 작성"><?php echo $row["wr_cont1"]; ?></textarea></div>
			</div>
			
            <!-- park 신규주소-->
            <div class="row"><label class="col-sm-2 control-label">담보주소</label>
				<div class="col-sm-10">
					<input type="hidden" id="schpost_chk" name="schpost_chk" value="">
					<input type="text" name="address1" value="<?php echo isset($row["wr_addr1"]) && !empty($row["wr_addr1"]) ? htmlspecialchars(trim($row["wr_addr1"])) : htmlspecialchars(trim($new_addr1)); ?>" class="form-control">
					<input type="text" name="address2" value="<?php echo isset($row["wr_addr2"]) && !empty($row["wr_addr2"]) ? htmlspecialchars(trim($row["wr_addr2"])) : htmlspecialchars(trim($new_addr2)); ?>" class="form-control">
				</div>
			</div>
			<!-- <div class="row"><label class="col-sm-2 control-label">담보주소</label>
				<div class="col-sm-10">
					<label><a onclick="execDaumPostcode();">☞주소검색</a></label>
					<input type="hidden" id="schpost_chk" name="schpost_chk" value="">
					<input type="text" name="address1" id="address1" value="<?php echo $row["wr_addr1"]; ?>" class="form-control" placeholder="기본주소(시/군/구/동) - 주소검색시 자동입력">
					<span id="guide" style="color:#999;display:block"></span>
					<input type="text" name="address2" id="address2" value="<?php echo $row["wr_addr2"]; ?>" class="form-control" readonly="readonly" style="display:none">
					<input type="text" name="address3" id="address3" value="<?php echo $row["wr_addr3"]; ?>" class="form-control" placeholder="상세주소(동/호,건물명)">
					<input type="text" name="address_ext" id="address_ext" value="<?php echo $row["wr_addr_ext1"]; ?>" class="form-control" placeholder="추가정보(세대수/층)">
				</div>
			</div> -->
            
            <!-- park 전용면적 신규 -->
			<div class="row"><label class="col-sm-2 control-label">전용면적</label>
				<div class="col-sm-10"><input type="text" name="wr_m2" id="wr_m2" value="<?php echo isset($row["wr_m2"]) && !empty($row["wr_m2"]) ? htmlspecialchars(trim($row["wr_m2"])) : htmlspecialchars(trim($area[0])); ?>" class="form-control" style="display:inline-block; width:100px;" placeholder="000.00"> ㎡ (제곱미터)</div>
			</div>
			<!-- <div class="row"><label class="col-sm-2 control-label">전용면적</label>
				<div class="col-sm-10"><input type="text" name="wr_m2" id="wr_m2" value="<?php echo ($row["wr_m2"]) ?>" class="form-control" style="display:inline-block; width:100px;" placeholder="000.00"> ㎡ (제곱미터)</div>
			</div> -->



            <div class="row">
                <label class="col-sm-2 control-label">소유지분현황</label>
                <div class="col-sm-10">
                    <textarea id="wr_cont2" name="wr_cont2" class="form-control" style="height:100px;" placeholder="자유양식 작성"><?php echo isset($row["wr_cont2"]) && !empty($row["wr_cont2"]) ? htmlspecialchars(trim($row["wr_cont2"])) : htmlspecialchars(trim($owner)); ?></textarea>
                </div>
            </div>

			<!-- park 대환기능 / 기준 날짜 이전은 텍스트에어리어, 이후는 대환 기능 추가된 -->
			<style>
				.highlighted {
					color: red;
					text-decoration: line-through;
				}
			</style>

			<script>
				function highlightRow(rowId) {
					var row = document.getElementById('row_' + rowId);
					var button = row.querySelector('button');
					var span = row.querySelector('.line-text'); // 각 행의 텍스트를 담고 있는 span 요소 선택
					
					// 버튼 텍스트 변경 및 스타일 적용
					if (button.textContent === '대환') {
						button.textContent = '대환됨';
						span.textContent = span.textContent.replace(/대환/, '대환됨'); // 텍스트 변경
						row.classList.add('highlighted');
					} else {
						button.textContent = '대환';
						span.textContent = span.textContent.replace(/대환됨/, '대환'); // 텍스트 변경
						row.classList.remove('highlighted');
					}

					if (!(span.textContent.includes('대환') || span.textContent.includes('대환됨'))) {
						span.textContent += "대환됨";
					}
				}

				function saveOutputToTextarea() {
					var rows = document.querySelectorAll('.output-container .row');
					var combinedText = '';

					rows.forEach(function(row) {
						var span = row.querySelector('.line-text'); // 각 행의 텍스트를 포함한 span 요소
						var rowText = span.textContent.trim(); // span 요소 내의 텍스트를 가져옴

							// 마지막 세 글자를 확인
						var lastThreeChars = rowText.slice(-3);

						if (lastThreeChars === '대환') {
							rowText = rowText.slice(0, -3) + '  대환됨';
						} else if (lastThreeChars === '환됨') {
							rowText = rowText.slice(0, -3) + ' 대환';
						} else {
							rowText += ' 대환';
						}

						if (combinedText !== '') {
							combinedText += '\n';
						}

						rowText = rowText.slice(0,-3);
						rowText = rowText.replace(/[\r\n]+/g, '');


						combinedText += rowText;
					});

					var textarea = document.getElementById('wr_cont3');
					if (!textarea) {
						textarea = document.createElement('textarea');
						textarea.id = 'wr_cont3';
						textarea.name = 'wr_cont3';
						textarea.style.display = 'none';
						document.body.appendChild(textarea);
					}

					// 최종 텍스트를 textarea에 업데이트
					if (textarea.value !== combinedText) {
						textarea.value = combinedText;
					}
				}


				document.addEventListener('DOMContentLoaded', function() {
					var form = document.getElementById('fwrite');
					form.onsubmit = function() {
						saveOutputToTextarea();
					};
				});
			</script>

<div class="row">
    <label class="col-sm-2 control-label">(근)저당권 및 전세권 등</label>
    <div class="col-sm-10 output-container" style="border:1px solid #ccc; width:81%; margin: 5px 0px 5px 15px;">
        <?php
        if ($row["wr_datetime"] < '2024-08-21 00:00:00') {
        ?>
            <textarea id="wr_cont3" name="wr_cont3" class="form-control" style="height:100px;" placeholder="자유양식 작성">
                <?php echo isset($row["wr_cont3"]) && !empty($row["wr_cont3"]) ? htmlspecialchars(trim($row["wr_cont3"])) : htmlspecialchars(trim($mortgage)); ?>
            </textarea>
        <?php
        } else {
            if (!$row["wr_cont3"]) {
                // 등록 시
                echo $output;
                echo '<textarea id="wr_cont3" name="wr_cont3" class="form-control" style="display:none;"></textarea>';
            } else {
                $lines = explode("\n", $row['wr_cont3']);
                foreach ($lines as $i => $line) {
                    $line = htmlspecialchars($line);
                    $lastTwoChars = mb_substr($line, -4);
                    if (strpos($lastTwoChars, '대환됨') !== false) {
                        $buttonText = "대환됨";
                        $class = "highlighted";
                    } else {
                        $buttonText = "대환";
                        $class = "";
                    }

                    echo "<div class='row $class' id='row_$i' style='width:65%; margin:5px 0 5px 0; border-bottom: 1px solid #ccc;'>";
                    echo "<span class='line-text'>".$line."</span>";
                    echo "<button type='button' onclick='highlightRow($i)' style='float:right;' class='btn-warning'>$buttonText</button>";
                    echo "</div>";
                }
                echo '<textarea id="wr_cont3" name="wr_cont3" class="form-control" style="display:none;"></textarea>';
            }
        }
        ?>
    </div>
</div>



			<!-- park 기타메모 임시 삭제 -->
			<!-- <div class="row"><label class="col-sm-2 control-label">기타메모</label>
				<div class="col-sm-10"><textarea id="wr_cont2" name="wr_cont2" class="form-control" style="height:50px;" placeholder="자유양식 작성"><?php echo $row["wr_cont2"]; ?></textarea></div>
			</div> -->
			 
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
			<?php if($row['wr_status'] <= 1) { ?><div class="col-sm-4"><button class="btn <?php echo $btnclass; ?> btn-block col-sm-4" type="submit"><?php echo $btntxt; ?></button></div><?php } ?>
			<?php if($wr_id) { ?><div class="col-sm-4"><button class="btn btn-info btn-block col-sm-4" type="button" onclick="document.location.href='./loan-file.php?wr_id=<?php echo $wr_id;?>';">첨부파일<?php echo "(".$filecnt .")";?></button></div><?php } ?>
			<div class="col-sm-4"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="document.location.href='./loan-list.php';">목록으로</button></div>
			</div>
		</form>
	<form id="fprocessing" name="fprocessing" action="/app/loan/loan-act.php" method="post" style="display:none;">
	 <input type="hidden" name="w" value="pr">
	 <input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
	 <input type="hidden" name="wr_status" value="<?php echo $row['wr_status']; ?>">
	 <input type="hidden" name="wr_name" value="">
	 <input type="hidden" name="wr_tel" value="">
	 <input type="hidden" name="wr_memo" value="">
    </form>

</div>

<!-- 파싱한 주소를 이용해 위도 경도 계산 -->
<!-- <script type="text/javascript">
	var new_addr = "<?php echo $new_addr; ?>";
	var gps = '';
	
	$(document).ready(function() {
		$.ajax({
			url: "https://api.vworld.kr/req/address?",
			type: "GET",
			dataType: "jsonp",
			data: {
				service: "address",
				request: "GetCoord",
				version: "2.0",
				crs: "EPSG:4326",
				type: "ROAD",
				address: new_addr,
				format: "json",
				errorformat: "json",
				key: "BF663BFA-4217-3D64-94BE-466B998EE83F"
			},success: function (ret) {
				if(ret.response.result){
					gps = ret.response.result.point;
					console.log(gps);
				}
			}
		})

		$.ajax({
			url: "https://api.vworld.kr/req/address?",
			type: "GET",
			dataType: "jsonp",
			data: {
				service: "address",
				request: "GetCoord",
				version: "2.0",
				crs: "EPSG:4326",
				type: "PARCEL",
				address: new_addr,
				format: "json",
				errorformat: "json",
				key: "BF663BFA-4217-3D64-94BE-466B998EE83F"
			},success: function (ret) {
				if(ret.response.result){
					gps = ret.response.result.point;
					console.log(gps);
				}
			}
		})

	});  
</script> -->

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
include_once '../../footer.php';
?>