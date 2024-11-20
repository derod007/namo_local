<?php
error_reporting(E_ALL);
ini_set("display_errors", 0);
// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';
require '../../vendor/autoload.php';

use Smalot\PdfParser\Parser;

$new_post = $_GET['new_post'] ?? '0';
$pjfile = get_writefile($wr_id);
$filteredFiles = array_filter($pjfile, function ($item) {
	return isset($item['category']) && $item['category'] === '등기부등본';
});

$filteredFiles = array_values($filteredFiles);
// var_dump($filteredFiles);
// 최초입력에 등기부등본이 존재할경우 파싱 및 기입
if (!empty($filteredFiles) && $new_post == '1') {
	// file과 name을 합친 변수 생성
	foreach ($filteredFiles as &$filepath) {
		$filepath['full_path'] = "../.." . $filepath['path'] . '/' . $filepath['file'];
	}
	// PDF 파일 경로
	$pdfFilePath = $filepath['full_path'];

	$parser = new Parser();
	$pdf = $parser->parseFile($pdfFilePath);
	$text = $pdf->getText();

	// park 전용면적
	$startSearch0  = '전유부분의 건물의 표시 )';
	$endSearch0  = '대지권의';
	$startPos0 = strpos($text, $startSearch0);
	$endPos0 = strpos($text, $endSearch0, $startPos0);
	if (!$endPos0) {
		$endSearch0 = '( 소유권에 관한 사항';
		$endPos0 = strpos($text, $endSearch0, $startPos0);
	}
	$text0 = '';
	$area = [];
	if ($startPos0 !== false && $endPos0 !== false) {
		$startPos0 += strlen($startSearch0);
		$text0 = substr($text, $startPos0, $endPos0 - $startPos0);
		$text0 = trim($text0);
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

	// park 소유율
	if (preg_match_all('/(\S+)\s*\(공유자\)\s*\d{6}-\*{7}\s*(\d+)분의\s*(\d+)/', $owner, $matches)) {
		$result = [];
		foreach ($matches[0] as $index => $match) {
			$name = $matches[1][$index];
			$denominator = (int)$matches[2][$index];
			$numerator = (int)$matches[3][$index];
			$owner_percent = round(($numerator / $denominator) * 100);
			$result[] = "$name, {$owner_percent}";
		}
		$owner_percent = implode(' / ', $result); // 결과값을 원하는 형식으로 합침
	} elseif (strpos($owner, '단독소유') !== false) {
		$owner_percent = "100";
	}

	// park 제목 요약
	preg_match_all('/([\p{L}]+) \((공유자|소유자)\)/u', $owner, $matches);
	$auto_sub = implode(' , ', $matches[1]);

	// park 토지구분
	$firstDashPos = strpos($text, '-');
	$secondDashPos = strpos($text, '-', $firstDashPos + 1);

	if ($firstDashPos !== false && $secondDashPos !== false) {
		$land = trim(substr($text, $firstDashPos + 1, $secondDashPos - $firstDashPos - 1));
	} else {
		echo "해당하는 값이 없습니다.";
	}

	// park 담보 구분 및 제목 요약
	$startSearch_cate = '( 1동의';
	$endSearch_cate = '( 소유권에';
	$startPos_cate = strpos($text, $startSearch_cate);
	$endPos_cate = strpos($text, $endSearch_cate, $startPos_cate);
	$cate_range = '';
	if ($startPos_cate !== false && $endPos_cate !== false) {
		$startPos_cate += strlen($startSearch_cate);
		$cate_range = substr($text, $startPos_cate, $endPos_cate - $startPos_cate);
		$cate_range = trim($cate_range);
	}

	$cate = 'E'; // 기본값으로 'E'(기타) 설정
	if ((strpos($cate_range, '아파트') !== false) && $land != "토지") {
		$cate = 'A';
		$auto_sub .= ' / 아파트';
	} elseif ((strpos($cate_range, '빌라') !== false) || (strpos($cate_range, '다세대') !== false) || (strpos($cate_range, '연립') !== false) || (strpos($cate_range, '공동주택') !== false) || (strpos($cate_range, '도시형') !== false) || (strpos($cate_range, '근린') !== false)) {
		$cate = 'B';
		$mapping = [
			'다세대' => ' (다세대주택)',
			'연립' => ' (연립주택)',
			'공동주택' => ' (공동주택)',
			'도시형' => ' (도시형생활주택)',
			'근린' => ' (근린생활시설)'
		];

		$auto_sub .= '/ 빌라';
		foreach ($mapping as $key => $suffix) {
			if (strpos($cate_range, $key) !== false) {
				$auto_sub .= $suffix;
				break;
			}
		}
	} else {
		$auto_sub .= ' / 기타';
	}

	// park 토지 면적
	if ($land == "토지") {
		$startSearch0  = '토지의 표시 )';
		$endSearch0  = '소유권에';
		$startPos0 = strpos($text, $startSearch0);
		$endPos0 = strpos($text, $endSearch0, $startPos0);
		$text0 = '';
		$area = [];
		if ($startPos0 !== false && $endPos0 !== false) {
			$startPos0 += strlen($startSearch0);
			$text0 = substr($text, $startPos0, $endPos0 - $startPos0);
			$text0 = trim($text0);
			// 제곱미터 앞에 숫자 추출
			preg_match_all('/\d+(\.\d+)?(?=\s*㎡)/', $text0, $matches);
			if (!empty($matches[0])) {
				$area = $matches[0];
			}
		}
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

	preg_match_all('/(전세권|근저당권설정|근저당권변경|질권|근질권|압류|가압류)[^\d]*(\d{4}년\d{1,2}월\d{1,2}일)[^\d]*금([\d,]+)원(?:[^채권자근저당권자전세권자]*(채권자|근저당권자|전세권자)\s+([^\s]+))?/u', $text, $matches);


	// 행을 생성하여 표시
	$output = "";
	for ($i = 0; $i < count($matches[0]); $i++) {
		$t1 = $matches[1][$i];
		$t2 = $matches[2][$i];
		$t3 = $matches[3][$i];
		$t4 = isset($matches[4][$i]) ? $matches[4][$i] : '';
		$t5 = isset($matches[5][$i]) ? $matches[5][$i] : '';

		// 각 행을 HTML로 출력
		$output .= "<div class='row' id='row_$i' style='width:100%;  margin:5px 0 5px 0; border-bottom: 1px solid #ccc'>";
		$output .= "<span class='line-text'>$t1 / $t2 / $t3 / $t4 $t5 </span>";
		$output .= "<button type='button' onclick='highlightRow($i)' style='float:right;' class='btn-warning'>말소</button>";
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
		$new_addr3 = trim($matches[2]);
	} else {
		$new_addr1 = $new_addr;
		$new_addr3 = '';
	}


	// 불필요한 헤더 및 메타데이터 제거
	function removeHeaders($text)
	{
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

if ($member['is_sub']) {
	$pt_idx = $member['parent_id'];
} else {
	$pt_idx = $member['idx'];
}


if ($w == 'u') {
	$wr_id = $_GET['wr_id'];
	if (!$wr_id) {
		alert("잘못된 접근입니다.");
	}
	$sql = "select * from loan_write where wr_id = '{$wr_id}' and pt_idx='" . $pt_idx . "' and wr_datetime >= '" . LIMIT_YMD . "' limit 1";
	$row = sql_fetch($sql);

	if (!$row['wr_id']) {
		alert('해당되는 데이터가 없습니다');
	}
	if ($new_post == '1') {
		$btntxt = "등록";
		$btnclass = "btn-primary";
	} else {
		$btntxt = "수정";
		$btnclass = "btn-warning";
	}
} else {
	$btntxt = "등록";
	$btnclass = "btn-primary";

	$row['wr_ca'] = 'B';
	$row["wr_link1_subj"] = "KB시세조회";
}

// park 소액임차보증금
$large_regions = [
	"서울특별시",
	"인천광역시",
	"세종특별자치시",
	"경기도",
	"부산광역시",
	"대구광역시",
	"광주광역시",
	"대전광역시",
	"울산광역시",
	"강원도",
	"충청북도",
	"충청남도",
	"전라북도",
	"전라남도",
	"경상북도",
	"경상남도",
	"제주특별자치도"
];

if ($row["wr_addr1"]) {
	$address = $row["wr_addr1"];
} else {
	$address = $new_addr1;
}

$region_mapping = [
	"서울" => "서울특별시",
	"인천" => "인천광역시",
	"경기" => "경기도",
	"제주" => "제주특별자치도",
	"강원" => "강원특별자치도",
	"전북" => "전북특별자치도",
	"부산" => "부산광역시",
	"울산" => "울산광역시",
	"대구" => "대구광역시",
	"대전" => "대전광역시",
	"경남" => "경상남도",
	"충남" => "충청남도",
	"광주" => "광주광역시",
	"세종" => "세종특별자치시"
];

foreach ($region_mapping as $abb => $full_name) {
	if (strpos($address, $abb) !== false) {
		$address = str_replace($abb, $full_name, $address);
		break;
	}
}


$add1 = '';
foreach ($region_mapping as $full_name) {
	if (strpos($address, $full_name) !== false) {
		//시,도
		$add1 = $full_name;
		break;
	}
}

$detail_address = str_replace($add1, '', $address);

$add2 = [];

// 추가로 동, 구를 찾기 위한 보다 세부적인 패턴 추가
$sub_patterns = [
	'/(\b[가-힣]{2,}시\b)(?!.*\b[가-힣]{2,}시\b)/u',    // 마지막 시
	'/(\b[가-힣]{2,}동\b)(?!.*\b[가-힣]{2,}동\b)/u',  // 마지막 동
	'/(\b[가-힣]{2,}구\b)(?!.*\b[가-힣]{2,}구\b)/u',  // 마지막 구
	'/(\b[가-힣]{2,}면\b)(?!.*\b[가-힣]{2,}면\b)/u'  // 마지막 면
];

$stop_search = false;

foreach ($sub_patterns as $pattern) {
	if ($stop_search) {
		break;
	}

	// 현재 패턴에 대해 검색
	if (preg_match_all($pattern, $detail_address, $matches)) {
		foreach ($matches[0] as $match) {
			$add2[] = $match; // 모든 매칭된 값을 배열에 추가
		}
	}

	// 현재 패턴이 '동'일 경우, '구', '면' 패턴 검색 중지
	if ($pattern === '/(\b[가-힣]{2,}동\b)(?!.*\b[가-힣]{2,}동\b)/u') {
		$stop_search = true; // '동'이 발견된 경우, 나머지 패턴 검색 중지
	}
}

// 가장 구체적인 조건으로 검색
$add2 = array_unique($add2);
$address_condition = implode(' ', $add2);
if (!$add2) $address_condition = $address;

$sql1 = "SELECT rp_repay_amt FROM region_preferential2 WHERE rp_rcity = '{$address_condition}'";

$result1 = sql_query($sql1);
$row1 = sql_fetch_array($result1);
if ($row1) {
	$repay_amt = $row1['rp_repay_amt'];
} else {
	// 정확히 일치하는 값이 없는 경우, 부분 일치 검색
	// 주소의 각 부분을 포함하는 조건을 생성합니다.
	$sub_conditions = [];

	foreach ($add2 as $part) {
		$sub_conditions[] = "rp_rcity LIKE '%{$part}%'";
	}

	if ($sub_conditions) {
		$sub_condition_sql = implode(' OR ', $sub_conditions);

		$sql2 = "SELECT rp_repay_amt FROM region_preferential2 WHERE {$sub_condition_sql}";

		$result2 = sql_query($sql2);
		if ($result2) {
			$row2 = sql_fetch_array($result2);
			if ($row2) {
				$repay_amt = $row2['rp_repay_amt'];
			}
		}
	}
}

// rp_rcity에 맞는 값이 없을 경우 add1을 기준으로 값을 가져옵니다.
if (!isset($repay_amt)) {
	$sql3 = "SELECT rp_repay_amt FROM region_preferential2 WHERE rp_rname = '{$add1}'";

	$result3 = sql_query($sql3);
	if ($result3) {
		$row3 = sql_fetch_array($result3);
		if ($row3) {
			$repay_amt = $row3['rp_repay_amt'];
		}
	}
}


// park 선순위 최고액 산출

$wr_cont3_lines = explode("\n", $row['wr_cont3']);

$best_entry = null;
foreach ($wr_cont3_lines as $wr_cont3_line) {

	if (strpos($wr_cont3_line, '유지') !== false) {
		continue;
	}

	$parts = explode(' / ', trim($wr_cont3_line));

	if (count($parts) < 3) {
		continue;
	}

	preg_match('/(\d{4})년(\d{1,2})월(\d{1,2})일/', $parts[1], $matches);
	if ($matches) {
		$year = $matches[1];
		$month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
		$day = str_pad($matches[3], 2, '0', STR_PAD_LEFT);
		$date = $year . $month . $day;

		$amount = intval(str_replace(',', '', $parts[2]));

		// 가장 이른 날짜이거나, 같은 날짜면 금액이 더 높은 것을 선택
		if ($best_entry === null || $date < $best_entry['date'] || ($date === $best_entry['date'] && $amount > $best_entry['amount'])) {
			$best_entry = [
				'date' => $date,
				'amount' => $amount
			];
		}
	}
}
if (!empty($best_entry['amount'])) {
	$best_entry['amount'] = substr($best_entry['amount'], 0, -4);
}
?>

<div class="upload-section">
	<form name="fpfilereg2" id="fpfilereg2" method="post" enctype="multipart/form-data" action="./loan-upload.php" class="upload-form">
		<input type="hidden" name="w" value="first_file">
		<input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
		<input type="hidden" name="category[]" value="등기부등본">

		<?php if (empty($filteredFiles)) { ?>
			<div class="upload-box" id="upload-box">
				<div class="upload-content">
					<i class="fas fa-cloud-upload-alt"></i>
					<p>등기부를 업로드 해주세요</p>
					<small>* 우선 등록시 일부 정보가 자동 기입 됩니다.<br>
						* 이미 입력된 상태에서 신규 등록할 경우 정보가 변경됩니다.<br>
						* 임시저장된 게시글일 경우 자동 기입은 진행하지 않습니다.</small>
				</div>
				<input type="file" id="uploadfile" name="uploadfile[]" class="file-input" multiple required style="display:none;" onchange="displayFileName()">
				<span id="file-name-display" style="display: none; margin-top: 10px; font-size: 14px; color: #4a5a6a;"></span>
			</div>
			<button class="upload-button" type="submit">등기부<br />등록</button>
		<?php } else { ?>
			<p>이미 등록된 등기부등본이 있습니다.</p>
		<?php } ?>
	</form>
</div>
<br /><br />
<div style="padding:15px;margin:auto;">
	<form id="fwrite" name="fwrite" action="/app/loan/loan-act.php" method="post" class="jsb-form">
		<input type="hidden" name="w" value="<?php echo $w; ?>">
		<input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
		<input type="hidden" name="prev_status" value="<?php echo $row['wr_status']; ?>">
		<div class="form-group">
			<?php if ($row['wr_status'] > 1) { ?>
				<div class="row"><label class="col-sm-2 control-label">진행상태</label>
					<div class="col-sm-10"><span class="loan-status-<?php echo $row['wr_status']; ?>"><?php echo $status_arr[$row['wr_status']]; ?></span></div>
				</div>
			<?php } ?>

			<?php if ($row['wr_status'] > 1) { ?>

				<?php
				$judge_date = "";
				if ($row['wr_status'] >= 30) {

					$sql = "SELECT reg_date FROM `log_judge` WHERE `wr_id` = '{$row['wr_id']}' order by jd_id desc limit 1";
					$row_date = sql_fetch($sql);
					if ($row_date['reg_date']) {
						$judge_date = "<br/>(" . substr($row_date['reg_date'], 0, 16) . ")";
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
								<td class="text-center"><?php echo ($row["jd_amount"]) ? number_format($row["jd_amount"]) . "만원" : ""; ?></td>
								<td class="text-center"><?php echo ($row["jd_interest"]) ? $row["jd_interest"] . "%" : ""; ?></td>
								<td><?php echo $row["jd_condition"] . $judge_date; ?></td>
							</tr>
						</table>

						<?php
						if ($row['wr_tel']) {
							$hp = str_replace('-', '', trim($row['wr_tel']));
							$row['wr_tel'] = substr($hp, 0, 3) . "-****-" . substr($hp, -4);
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
						<hr />
						<?php
						if ($row['wr_status'] == '10') {
							echo '<div class="row">';
							echo '<div class="col-sm-6"><button class="btn btn-info btn-block" type="button" id="loan_processing" onclick="javascript:;">진행요청</button></div>';
							echo '<div class="col-sm-6"> ※ 심사결과 확인후 <b>진행요청</b>을 클릭해주세요.</div><br/>';
							echo '</div>';
							echo '<br/>';
						}
						if ($row['wr_status'] != '9' && $row['wr_status'] != '20' && $row['wr_status'] != '60' && $row['wr_status'] != '99') {
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

			<!-- css 작업 시작 -->
			<div class="main-container">
				<div class="top-sections">
					<div class="section collateral-info">
						<h2>담보 정보</h2>
						<div class="form-content">
							<div class="row"><label class="col-sm-2 control-label">제목</label>
								<div class="col-sm-10"><input type="text" id="wr_subject" name="wr_subject" value="<?php if ($row["wr_subject"]) {
																														echo $row["wr_subject"];
																													} else if (!$row["wr_subject"] && $new_post == '1') {
																														echo $auto_sub;
																													} ?>"
										required class="form-control required" placeholder="홍길동 / 담보종류 / 자금용도 (확인된 사항만 기재)">
								</div>
							</div>
							<div class="row"><label class="col-sm-2 control-label">대출종류</label>
								<div class="col-sm-10 bs-padding10">
									<input type="radio" id="wr_type_01" name="wr_type" value="A" required <?php echo ($row['wr_type'] != 'B') ? "checked" : ""; ?>>
									<label for="wr_type_01">일반 &nbsp;</label>
									<input type="radio" id="wr_type_02" name="wr_type" value="B" required <?php echo ($row['wr_type'] == 'B') ? "checked" : ""; ?>>
									<label for="wr_type_02">매매/경매 (선택시 일부 정보는 등록되지 않습니다) &nbsp;</label>
								</div>
							</div>
							<!-- park 신규주소-->
							<div class="row"><label class="col-sm-2 control-label">담보주소</label>
								<div class="col-sm-10">
									<input type="hidden" id="schpost_chk" name="schpost_chk" value="">
									<input type="text" id="address1" name="address1" value="<?php echo isset($row["wr_addr1"]) && !empty($row["wr_addr1"]) ? htmlspecialchars(trim($row["wr_addr1"])) : htmlspecialchars(trim($new_addr1)); ?>" class="form-control">
									<input type="text" name="address3" value="<?php echo isset($row["wr_addr3"]) && !empty($row["wr_addr3"]) ? htmlspecialchars(trim($row["wr_addr3"])) : htmlspecialchars(trim($new_addr3)); ?>" class="form-control">
								</div>
							</div>
							<!-- 담보구분 -->
							<div class="row"><label class="col-sm-2 control-label">담보구분</label>
								<div class="col-sm-10 bs-padding10">
									<input type="radio" id="control_01" name="wr_ca" value="A" required <?php echo ($row['wr_ca'] == 'A' || $cate == 'A') ? "checked" : ""; ?>>
									<label for="control_01">아파트 &nbsp;</label>
									<input type="radio" id="control_02" name="wr_ca" value="B" required <?php echo ($row['wr_ca'] == 'B' || $cate == 'B') ? "checked" : ""; ?>>
									<label for="control_02">빌라 &nbsp;</label>
									<input type="radio" id="control_03" name="wr_ca" value="E" required <?php echo ($row['wr_ca'] == 'E' || $cate == 'E') ? "checked" : ""; ?>>
									<label for="control_03">기타 &nbsp;</label>
								</div>
							</div>
							<!-- 지분여부 -->
							<div class="row">
								<label class="col-sm-2 control-label">지분여부</label>
								<div class="col-sm-10 bs-padding10">
									<select id="wr_part_select" name="wr_part" required onchange="updatePercentInput()">
										<option value="X">선택하세요</option>
										<option value="A" <?php echo ($row['wr_part'] == 'A' || $owner_percent == '100') ? 'selected' : ''; ?>>단독소유</option>
										<?php
										if (is_array($result) && !empty($result)) {
											foreach ($result as $owner_info) {
												list($owner_name, $percent) = explode(', ', $owner_info);
												$isSelected = ($row['wr_part'] == 'PE' && $row['wr_part_percent'] == $percent) || ($percent == 100 && $row['wr_part_percent'] != 100);
												echo '<option value="PE" data-percent="' . $percent . '" ' . ($isSelected ? 'selected' : '') . '>' . $owner_name . ' (' . $percent . '%)</option>';
											}
										}
										$percent_values = is_array($result) ? array_column($result, 'percent') : [];

										?>
										<option value="PE" data-percent="" <?php echo ($row['wr_part'] == 'PE' && !in_array($row['wr_part_percent'], $percent_values)) ? 'selected' : ''; ?>>지분소유(기타)</option>

									</select>

									<input type="number" id="control_07" name="wr_part_percent" value="<?php echo ($row['wr_part'] == 'A') ?  '100' : $row['wr_part_percent']; ?>" min="0" max="100" style="width:50px;"> %
								</div>
							</div>


							<!-- park 전용면적 신규 -->
							<div class="row"><label class="col-sm-2 control-label">전용면적</label>
								<div class="col-sm-10"><input type="text" name="wr_m2" id="wr_m2" value="<?php echo isset($row["wr_m2"]) && !empty($row["wr_m2"]) ? htmlspecialchars(trim($row["wr_m2"])) : htmlspecialchars(trim($area[0])); ?>" class="form-control" style="display:inline-block; width:100px;" placeholder="000.00"> ㎡ (제곱미터)</div>
							</div>
						</div>
					</div>


					<div class="section application-info">
						<h2>신청 정보</h2>
						<!-- park 임대차보증금 -->
						<div class="row"><label class="col-sm-2 control-label">임대차보증금</label>
							<div class="col-sm-10"><input type="text" id="wr_rental_deposit" name="wr_rental_deposit" style="display:inline-block; width:200px;" placeholder="있을경우 작성 / 단위 만원" value="<?php echo $row["wr_rental_deposit"]; ?>" class="form-control"> 원</div>
						</div>
						<div class="form-content">
							<!-- 희망금액 -->
							<div class="row"><label class="col-sm-2 control-label">희망금액</label>
								<div class="col-sm-10">
									<input type="text" id="wr_amount" name="wr_amount" style="display:inline-block; width:200px;" value="<?php echo $row["wr_amount"]; ?>" class="form-control"> 원
									<input type="checkbox" id="maximum" name="maximum" value="1" style="display:inline-block; width:30px;" <?php if (strpos($row['wr_amount'], '최대요청') !== false) echo 'checked'; ?>><label for="maximum">최대 요청</label>
								</div>
							</div>
							<!-- 기타정보 -->
							<!-- park 임시 담보정보 (기타정보로??) -->
							<div class="row"><label class="col-sm-2 control-label">기타 담보 정보</label>
								<div class="col-sm-10"><textarea id="wr_cont2" name="wr_cont2" class="form-control" style="height:80px;" placeholder="자유양식 작성"><?php echo $row["wr_cont2"]; ?></textarea></div>
							</div>
							<!-- 참고링크1 -->
							<div class="row"><label class="col-sm-2 control-label">참고링크#1<br />(KB시세 URL)</label>
								<div class="col-sm-10">
									<input type="text" id="wr_link1" name="wr_link1" value="<?php echo $row["wr_link1"]; ?>" class="form-control" placeholder="https://링크URL">
									<input type="text" id="wr_link1_subj" name="wr_link1_subj" value="<?php echo $row["wr_link1_subj"]; ?>" class="form-control" placeholder="링크제목">
									<?php
									if (!empty($row["wr_link1"])) {
										if (!empty(trim($row["wr_link1_subj"]))) {
											echo "<div><a href='{$row['wr_link1']}' target='_blank'>" . $row["wr_link1_subj"] . "</a></div>";
										} else {
											echo "<div><a href='{$row['wr_link1']}' target='_blank'>새창링크</a></div>";
										}
									}
									?>
								</div>
							</div>
							<!-- 첨부파일 있었으면 하는 위치 -->
						</div>
					</div>
					<!-- 신청정보 끝 -->
				</div>
				<!-- 탑섹션 끝 -->

				<!-- 채권 정보 섹션, 상단 두 섹션 아래에 배치 -->
				<div class="bond-section">
					<h3>채권 정보</h3>
					<div class="bond-row">
						<label class="control-label">소유지분현황</label>
						<textarea id="wr_cont4" name="wr_cont4" class="form-control" style="height:100px;" placeholder="자유양식 작성"><?php echo isset($row["wr_cont4"]) && !empty($row["wr_cont4"]) ? htmlspecialchars(trim($row["wr_cont4"])) : htmlspecialchars(trim($owner)); ?></textarea>

					</div>

					<div class="bond-row output-container">
						<label class="control-label">(근)저당권 및 전세권 등</label>
						<div class="form-control" style="height:100%">
							<?php
							if ($row["wr_datetime"] < '2024-08-21 00:00:00') {
							?>
								<textarea id="wr_cont3" name="wr_cont3" class="form-control" style="height:100px;" placeholder="자유양식 작성"><?php echo isset($row["wr_cont3"]) && !empty($row["wr_cont3"]) ? htmlspecialchars(trim($row["wr_cont3"])) : htmlspecialchars(trim($mortgage)); ?></textarea>
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
										$lastTwoChars = mb_substr($line, -3);

										if (strpos($lastTwoChars, '유지') !== false) {
											$buttonText = "유지";
											$class = "highlighted";
										} else {
											$buttonText = "말소";
											// $line = str_replace('말소','',$line);
											$class = "";
										}
										$view_line = str_replace(['유지', '말소'], '', $line); // '유지' 또는 '말소' 제거

										echo "<div class='row $class' id='row_$i' style='width:100%; margin:5px 0 5px 0; border-bottom: 1px solid #ccc;'>";
										echo "<span class='line-text' style='display:none'>" . $line . "</span>"; // 숨겨진 상태로 $line을 유지
										echo "<span>" . $view_line . "</span>"; // $view_line은 화면에 표시
										echo "<button type='button' onclick='highlightRow($i)' style='float:right;' class='btn-warning'>$buttonText</button>";
										echo "</div>";
									}
									echo '<textarea id="wr_cont3" name="wr_cont3" class="form-control" style="display:none;"></textarea>';
								}
							}
							?>
						</div>
					</div>

					<div class="bond-actions">
						<!-- <button class="action-button" type="button">파일등록</button>
						<button class="action-button secondary" type="button">목록으로</button> -->
						<?php if ($row['wr_status'] <= 1) { ?><div class="col-sm-4"><button class="btn <?php echo $btnclass; ?> btn-block col-sm-4" type="submit"><?php echo $btntxt; ?></button></div><?php } ?>
						<div class="col-sm-4"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="document.location.href='./loan-list.php';">목록으로</button></div>
					</div>
				</div>
			</div>

			<!-- 아파트 실거래가 시세 / 희망금액 비교-->
			<input type="hidden" name="addr1" value="<?php echo isset($row["wr_addr1"]) && !empty($row["wr_addr1"]) ? htmlspecialchars(trim($row["wr_addr1"])) : htmlspecialchars(trim($new_addr1)); ?>">
			<input type="hidden" name="py" value="<?php echo isset($row["wr_m2"]) && !empty($row["wr_m2"]) ? htmlspecialchars(trim($row["wr_m2"])) : htmlspecialchars(trim($area[0])); ?>">

			<input type="hidden" id="auto_real_price" name="auto_real_price">
			<input type="hidden" id="auto_ltv" name="auto_ltv" value="80">
			<input type="hidden" id="auto_small_deposit" name="auto_small_deposit" value="<?php echo $repay_amt; ?>">
			<input type="hidden" id="auto_senior_loan" name="auto_senior_loan" value="<?php echo htmlspecialchars($best_entry['amount'], ENT_QUOTES, 'UTF-8'); ?>">

			<!-- css 작업 끝 -->


			<?php
			$pjfile = get_writefile($wr_id);
			$filecnt = number_format($pjfile['count']);
			?>
		</div>

		<!-- <div class="row"><hr/></div> -->


	</form>
</div>

<!-- 첨부파일1111 -->
<?php if ($wr_id) { ?>
	<div class="row" style="order:1;"><label class="col-sm-2 control-label">첨부파일 <?php echo "(" . $filecnt . ")"; ?><br /><a href="./loan-file.php?wr_id=<?php echo $wr_id; ?>">관리 &gt;&gt;</a></label>
		<div class="col-sm-10">
			<div>
				<table class="table table-bordered bs-xs-table ">
					<tr>
						<td>
							<div style="display: flex;">
								<form name="fpfilereg" id="fpfilereg" method="post" enctype="multipart/form-data" action="./loan-upload.php" class="form-inline">
									<input type="hidden" name="w" value="file">
									<input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
									<input type="hidden" name="category[]" value="일반">
									<!-- <select id="category" name="category[]" class="form-control" style="width:150px;">
															<option value="">선택</option>
															<option value="등기부등본">등기부등본</option>
															<option value="건축물/토지대장">건축물/토지대장</option>
															<option value="일반">일반</option>
														</select> -->
									&emsp;
									<input type="file" id="uploadfile" name="uploadfile[]" value="" required class="form-control" style="width: 300px;">
									&emsp;
									<button class="btn btn-success" type="submit">파일등록</button>
								</form>
							</div>
						</td>
					</tr>
					<tr>
						<td>
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
														<a href="<?php echo $file['href']; ?>" class="view_file_download">
															<strong>
																<?php echo $file['source']; ?></strong>
															( <?php echo $file['size']; ?> ) <i class="fa fa-download" aria-hidden="true"></i></a>
														<?php echo $file['memo']; ?>
													</td>
													<td style="padding-left: 10px;padding-right:10px;"><span class="project_v_file_date">
															<?php echo substr($file['datetime'], 0, 16); ?></span></td>
													<?php if ($row['wr_status'] <= 1) { ?><td><span class="btn btn-danger btn-xs project_file_del" data-file-no='<?php echo $i; ?>' data-pid='<?php echo $wr_id; ?>'>삭제</span></td><?php } ?>
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
						</td>
					</tr>
				</table>

				<br class="clear" />
			</div>
		<?php } else { ?>
			<div class="col-sm-12 blue"> ※ 글 최초 등록 혹은 등기부등본 등록 후 첨부파일 등록/삭제가 가능합니다.</div>
		<?php } ?>
		</div>
	</div>
	<!-- 첨부파일 끝 -->
	<!-- 추가 개발 마무리 -->



	<form id="fprocessing" name="fprocessing" action="/app/loan/loan-act.php" method="post" style="display:none;">
		<input type="hidden" name="w" value="pr">
		<input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
		<input type="hidden" name="wr_status" value="<?php echo $row['wr_status']; ?>">
		<input type="hidden" name="wr_name" value="">
		<input type="hidden" name="wr_tel" value="">
		<input type="hidden" name="wr_memo" value="">
	</form>

	</div>

	<script>
		function updatePercentInput() {
			const selectedOption = document.getElementById('wr_part_select').selectedOptions[0];
			const percentInput = document.getElementById('control_07');
			const selectedValue = selectedOption.value;

			if (selectedValue === 'PE') {
				const percent = selectedOption.getAttribute('data-percent');
				percentInput.value = percent || ''; // 빈 값이면 기타 선택 시 입력 가능
			} else if (selectedValue === 'X') {
				percentInput.value = '';
			} else {
				percentInput.value = 100; // 단독소유일 경우 100% 자동 입력
			}

		}

		function setPercent(value) {
			// 지분 값을 설정, 지분소유(기타) 선택 시 빈 값으로 둠
			document.getElementById('control_07').value = value || '';
		}

		function clear_button_7(i) {
			document.getElementById('control_07').value = i;
		}

		function highlightRow(rowId) {
			var row = document.getElementById('row_' + rowId);
			var button = row.querySelector('button');
			var span = row.querySelector('.line-text'); // 각 행의 텍스트를 담고 있는 span 요소 선택

			// 버튼 텍스트 변경 및 스타일 적용
			if (button.textContent === '말소') {
				button.textContent = '유지';
				span.textContent = span.textContent.replace(/말소/, '유지'); // 텍스트 변경
				row.classList.add('highlighted');
			} else {
				button.textContent = '말소';
				span.textContent = span.textContent.replace(/유지/, '말소'); // 텍스트 변경
				row.classList.remove('highlighted');
			}

			if (!(span.textContent.includes('말소') || span.textContent.includes('유지'))) {
				span.textContent += "유지";
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

				if (lastThreeChars === '말소') {
					rowText = rowText.slice(0, -3) + '  유지';
				} else if (lastThreeChars === '유지') {
					rowText = rowText.slice(0, -3) + ' 말소';
				} else {
					rowText += ' 말소';
				}

				if (combinedText !== '') {
					combinedText += '\n';
				}

				rowText = rowText.slice(0, -3);
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

		document.getElementById("fwrite").addEventListener("submit", function(event) {
			const selectElement = document.getElementById("wr_part_select");

			if (selectElement.value === "X") {
				alert("지분 여부를 선택하세요.");
				event.preventDefault(); // 폼 제출을 막음
				selectElement.focus(); // 선택 박스에 포커스 이동
			}
		});


		$(function() {
			// var params = $("#fnewwin_real").serialize();
			var addr1 = $("input[name='addr1']").val();
			var py = $("input[name='py']").val();
			$.ajax({
				url: '/app/real/get_realprice4.php',
				type: "post",
				// data: params,
				data: {
					addr1: addr1,
					py: py
				},
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				dataType: "text",
				success: function(data) {
					// 그래프 데이터
					let json = $.parseJSON(data);
					console.log(json);
					if (json.data.ave_price) {
						var real_price = json.data.ave_price * 10000;
						$("#auto_real_price").val(real_price);
					}
				}
			});
		});

		//파일 등록 이름
		function displayFileName() {
			const fileInput = document.getElementById('uploadfile');
			const fileNameDisplay = document.getElementById('file-name-display');

			if (fileInput.files.length > 0) {
				fileNameDisplay.style.display = 'block';
				fileNameDisplay.textContent = Array.from(fileInput.files).map(file => file.name).join(', ');
			} else {
				fileNameDisplay.style.display = 'none';
				fileNameDisplay.textContent = '';
			}
		}


		// 파일 업로드 드래그앤드랍
		document.addEventListener('DOMContentLoaded', function() {
			const uploadBox = document.getElementById("upload-box");
			const fileInput = document.getElementById("uploadfile");

			uploadBox.addEventListener("click", () => fileInput.click());

			uploadBox.addEventListener("dragover", (e) => {
				e.preventDefault();
				uploadBox.style.backgroundColor = "#f0f0f0";
			});

			uploadBox.addEventListener("dragleave", () => {
				uploadBox.style.backgroundColor = "white";
			});

			uploadBox.addEventListener("drop", (e) => {
				e.preventDefault();
				uploadBox.style.backgroundColor = "white";
				fileInput.files = e.dataTransfer.files;
				displayFileName();
			});
		});

		// 파일업로드 비동기
		document.getElementById('fpfilereg').addEventListener('submit', function(e) {
			e.preventDefault(); // 기본 폼 제출 방지

			var formData = new FormData(this);

			var xhr = new XMLHttpRequest();
			xhr.open('POST', this.action, true);

			xhr.onload = function() {
				if (xhr.status === 200) {
					// console.log(xhr.responseText);

					var files = JSON.parse(xhr.responseText);

					updateFileList(files);
					alert('파일이 성공적으로 업로드되었습니다.');
					// 필요한 경우 데이터를 다시 로드하거나 상태를 업데이트합니다.
				} else {
					alert('파일 업로드에 실패했습니다.');
				}
			};

			xhr.send(formData);
		});

		function updateFileList(files) {
			var fileListContainer = document.getElementById('project_v_file');
			var table = document.createElement('table');
			table.className = 'table';

			if (files.length === 0) {
				fileListContainer.innerHTML = "<span style='color:gray'>등록된 첨부파일이 없습니다.</span>";
				return;
			}

			files.forEach(function(file, index) {
				var tr = document.createElement('tr');
				tr.style.borderBottom = '1px solid #ddd';

				tr.innerHTML = `
            <td style="padding-left: 10px;padding-right:10px;">[${file.category}]</td>
            <td style="padding-left: 10px;padding-right:10px;">
                <a href="${file.href}" class="view_file_download">
                    <strong>${file.source}</strong>
                    (${file.size}) <i class="fa fa-download" aria-hidden="true"></i>
                </a>
                ${file.memo}
            </td>
            <td style="padding-left: 10px;padding-right:10px;">
                <span class="project_v_file_date">${file.datetime}</span>
            </td>
            <td><span class="btn btn-danger btn-xs project_file_del" data-file-no='${file.file_no}' data-pid='${file.wr_id}'>삭제</span></td>
        `;

				table.appendChild(tr);
			});

			fileListContainer.innerHTML = ''; // 기존 파일 목록을 비움
			fileListContainer.appendChild(table); // 새로 만든 테이블 삽입
		}

		// 첨부파일 삭제
		$(function() {
			commonjs.selectNav("navbar", "loaninfo");


			$('.project_file_del').click(function() {
				if (confirm("파일을 삭제하시겠습니까?")) {
					var file_no = $(this).attr("data-file-no");
					var delform = $('<form></form>');
					delform.attr('action', './loan-upload.php');
					delform.attr('method', 'post');
					delform.appendTo('body');
					delform.append('<input type="hidden" name="w" value="filedel" />');
					delform.append('<input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>" />');
					delform.append('<input type="hidden" name="file_no" value="' + file_no + '" />');
					delform.submit();
				}
			});

		});

		$(function() {
			commonjs.selectNav("navbar", "loaninfo");

			<?php if ($w == '') { ?>
				$('#address1').attr('readonly', 'readonly');
				$('#address1').on('click', function() {
					var wr_ca = $("input[name='wr_ca']:checked").val();
					if (wr_ca != 'E') {
						execDaumPostcode();
					}
				});
			<?php } ?>

			var status = '<?php echo $row['wr_status']; ?>';
			if (status > '1') {
				$('input:radio:not(:checked)').attr('disabled', 'disabled');
				$('input').attr('readonly', 'readonly');
				$('textarea').attr('readonly', 'readonly');
				$('.btn-warning').attr('disabled', 'disabled');
			}

			if (status == '10') {
				$('#wr_name').removeAttr('readonly');
				$('#wr_tel').removeAttr('readonly');
				$('#wr_memo').removeAttr('readonly');
			}

			$('#loan_processing').on('click', function(event) {
				var f = document.fprocessing;
				f.wr_name.value = document.fwrite.wr_name.value;
				f.wr_tel.value = document.fwrite.wr_tel.value;
				f.wr_memo.value = document.fwrite.wr_memo.value;
				if (!f.wr_tel.value) {
					alert('대출자 연락처를 입력해주세요.');
				} else {
					f.submit();
				}
			});

			$('#loan_cancel').on('click', function(event) {
				var f = document.fprocessing;
				f.w.value = 'pc'; // 진행취소
				f.submit();
			});

			$("input[name='wr_ca']").on('change', function() {
				var wr_ca = $("input[name='wr_ca']:checked").val();
				// console.log(wr_ca);
				if (wr_ca != 'E') {
					$('#address1').attr("readonly", "readonly");
				} else {
					$('#address1').removeAttr("readonly");
				}
			});

		});

		function fsubmit(f) {

			if (!f.wr_ca.value) {
				alert("담보구분을 선택해주세요");
				return false;
			}

			<?php if ($w == '') { ?>
				if (!f.address1.value) {
					alert("주소검색으로 담보주소를 입력해주세요");
					return false;
				}
				if (f.wr_ca.value != 'E' && f.schpost_chk.value != '1') {
					alert("담보주소는 주소검색을 한뒤 입력해주세요");
					return false;
				}
			<?php } ?>

			return true;

		}
	</script>


	<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
	<script>
		function execDaumPostcode() {
			new daum.Postcode({
				oncomplete: function(data) {
					var roadAddr = data.roadAddress; // 도로명 주소 변수
					var extraRoadAddr = ''; // 참고 항목 변수

					if (data.bname !== '' && /[동|로|가]$/g.test(data.bname)) {
						extraRoadAddr += data.bname;
					}
					// 건물명이 있고, 공동주택일 경우 추가한다.
					if (data.buildingName !== '' && data.apartment === 'Y') {
						extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
					}
					// 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
					if (extraRoadAddr !== '') {
						extraRoadAddr = ' (' + extraRoadAddr + ')';
					}

					// 우편번호와 주소 정보를 해당 필드에 넣는다.
					//document.getElementById('postcode').value = data.zonecode;
					//document.getElementById("roadAddress").value = roadAddr;
					document.getElementById("address1").value = data.jibunAddress;
					document.getElementById("schpost_chk").value = "1"; // 검색체크 항목을 1 로 설정
					//document.getElementById("address1").value = data.address;

					// 참고항목 문자열이 있을 경우 해당 필드에 넣는다.
					var addr2TextBox = document.getElementById("address2");
					if (roadAddr !== '') {
						document.getElementById("address2").value = extraRoadAddr;
						addr2TextBox.style.display = 'block';
					} else {
						document.getElementById("address2").value = '';
						addr2TextBox.style.display = 'none';
					}

					var guideTextBox = document.getElementById("guide");
					// 사용자가 '선택 안함'을 클릭한 경우, 예상 주소라는 표시를 해준다.
					if (data.autoRoadAddress) {
						var expRoadAddr = data.autoRoadAddress + extraRoadAddr;
						guideTextBox.innerHTML = '(예상 도로명 주소 : ' + expRoadAddr + ')';
						guideTextBox.style.display = 'block';

					} else if (data.autoJibunAddress) {
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