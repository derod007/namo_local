<?php
//error_reporting(E_ALL);
ini_set("display_errors", 0);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

$partners = get_partnerlist();

$w = $_GET['w'];

if ($w == 'u') {
	$wr_id = $_GET['wr_id'];
	if (!$wr_id) {
		alert("잘못된 접근입니다.");
	}
	$sql = "select * from loan_write where wr_id = '{$wr_id}' limit 1";
	$row = sql_fetch($sql);

	$sql_calcul_auto = "SELECT * FROM loan_calcul WHERE wr_id = '{$wr_id}' AND lc_type='auto'";
	$row_calcul_auto = sql_fetch($sql_calcul_auto);

	$sql_calcul_manual = "SELECT * FROM loan_calcul WHERE wr_id = '{$wr_id}' AND lc_type='manual' AND lc_use='1'";
	$row_calcul_manual = sql_fetch($sql_calcul_manual);

	if (!$row['wr_id']) {
		alert('해당되는 데이터가 없습니다');
	}
	$btntxt = "수정";
	$btnclass = "file_action_save_btn";
	$duppop = check_duplicate($row['wr_addr1'], $row['address2'], $row['wr_id']);
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

$address = $row["wr_addr1"];
$region_mapping = [
	"서울" => "서울특별시",
	"인천" => "인천광역시",
	"경기" => "경기도",
	"제주" => "제주특별자치도",
	"강원" => "강원특별자치도",
	"전북" => "전라북도",
	"전남" => "전라남도",
	"부산" => "부산광역시",
	"울산" => "울산광역시",
	"대구" => "대구광역시",
	"대전" => "대전광역시",
	"경북" => "경상북도",
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

// 중복 제거

// echo "추출된 주소: " . implode(' ', $add2);



// $add2 = array_unique($add2);
// $add2 = implode(' ', $add2);

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
				if (preg_match('/^양주시\s|[^남]양주시\s/', $address_condition)) {
                    $repay_amt = 2500;
                }else{
					$repay_amt = $row2['rp_repay_amt'];
				}
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
// echo "소액임차보증금 : ".$repay_amt." / 주소 : ".$address;
?>
<!-- 아파트 실거래가 시세 -->
<form name="fnewwin_real" id="fnewwin_real" method="GET">
	<!-- <input type="hidden" name="addr1" value="<?php echo isset($row["wr_addr1"]) && !empty($row["wr_addr1"]) ? htmlspecialchars(trim($row["wr_addr1"])) : htmlspecialchars(trim($new_addr1)); ?>"> -->
	<input type="hidden" name="addr1" value="<?php echo isset($address) ? trim($address) : trim($row["wr_addr1"]); ?>">
	<input type="hidden" name="py" value="<?php echo isset($row["wr_m2"]) && !empty($row["wr_m2"]) ? htmlspecialchars(trim($row["wr_m2"])) : htmlspecialchars(trim($area[0])); ?>">
</form>

<script>
	function addCommas(number) {
		return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	function removeCommas_s(number) {
		if (typeof number === 'string' && number !== "0") {
			return number.replace(/,/g, "");
		} else {
			return 0;
		}
	}

	$(function() {
		// var params = $("#fnewwin_real").serialize();
		var addr1 = $("input[name='addr1']").val();
		var py = $("input[name='py']").val();
		// console.log(addr1);
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
				// console.log(json);
				if (json.data.ave_price) {
					var real_price = addCommas(json.data.ave_price);
					if (!$('#auto_price').val()) {
						$("#auto_price").val(real_price);
					}
					// $("input[name='price']").val(real_price);
					// $("#span_price").html(real_price);
				}
			}
		});
	});
</script>

<?php
// park 선순위 최고액 산출

$wr_cont3_lines = explode("\n", $row['wr_cont3']);

$best_entry = null;
foreach ($wr_cont3_lines as $wr_cont3_line) {

	if (strpos($wr_cont3_line, '해제') !== false) {
		continue;
	}

	$parts = explode(' / ', trim($wr_cont3_line));

	if (count($parts) < 4) {
		continue;
	}

	preg_match('/(\d{4})년(\d{1,2})월(\d{1,2})일/', $parts[2], $matches);
	if ($matches) {
		$year = $matches[1];
		$month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
		$day = str_pad($matches[3], 2, '0', STR_PAD_LEFT);
		$date = $year . $month . $day;

		$amount = intval(str_replace(',', '', $parts[3]));

		// 가장 이른 날짜이거나, 같은 날짜면 금액이 더 높은 것을 선택
		// if ($best_entry === null || $date < $best_entry['date'] || ($date === $best_entry['date'] && $amount > $best_entry['amount'])) {
		// 	$best_entry = [
		// 		'date' => $date,
		// 		'amount' => $amount
		// 	];
		// }

		// 그냥 모든 유지금액 합친거
		$best_entry['amount'] += $amount;
	}
}
if (!empty($best_entry['amount'])) {
	$best_entry['amount'] = substr($best_entry['amount'], 0, -4);
}

// LTV 기본 선택
$selected_value = '75';

if (strpos($row['wr_subject'], '토지') !== false) {
	$selected_value = '40';
} else if (strpos($row['wr_subject'], '공장') !== false || strpos($row['wr_subject'], '상가') !== false) {
	$selected_value = '50';
} else if (strpos($row['wr_subject'], '단독주택') !== false) {
	$selected_value = '60';
} else if (strpos($row['wr_subject'], '공동주택') !== false || strpos($row['wr_subject'], '근린생활시설') !== false || strpos($row['wr_subject'], '연립주택') !== false || strpos($row['wr_subject'], '다세대주택') !== false || strpos($row['wr_subject'], '도시형생활주택') !== false) {
	$selected_value = '70';
} else if (strpos($row['wr_subject'], '아파트') !== false) {
	$selected_value = '75';
	if (strpos($address, '서울') !== false || strpos($address, '경기') !== false || strpos($address, '인천') !== false) {
        $selected_value = '80';
    }
}


?>
<input type="hidden" id="repay_amt" value="<?php echo htmlspecialchars($repay_amt, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" id="best_loan" value="<?php echo htmlspecialchars($best_entry['amount'], ENT_QUOTES, 'UTF-8'); ?>">



<script>

</script>

<style>
	.ui-widget {
		font-family: font-family: "Pretendard Variable", Pretendard, -apple-system, BlinkMacSystemFont, system-ui, Roboto, "Helvetica Neue", "Segoe UI", "Apple SD Gothic Neo", "Noto Sans KR", "Malgun Gothic", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", sans-serif;
	}

	.info_window {
		display: none;
	}

	.info_window p {
		margin: 0;
		font-size: 0.9em;
	}

	.btn_infowin {
		display: inline-block;
		width: 20px;
		height: 20px;
		background: #ff0000;
		border-radius: 20%;
		font-size: 15px;
		color: #fff;
		cursor: pointer;
		text-align: center;
		vertical-align: middle;
	}

	.winreal {
		color: #337ab7;
		cursor: pointer;
	}

	.ui-widget-content a {
		color: #337ab7;
	}

	.ptlist {
		display: flex;
		flex-wrap: wrap;
		margin: 0;
		padding: 0;
		list-style: none;
		margin-bottom: 10px;
	}

	.ptlist li {
		display: inline-block;
		position: relative;
		letter-spacing: 1px;
		font-size: 14px;
		padding: 0 5px;
		word-break: keep-all;
	}

	.highlighted {
		color: red;
		text-decoration: line-through;
	}
</style>

<!-- CONTENT START -->
<div class="manager_wrap">

	<!-- 접수및 상태확인 시작 -->
	<div class="page_headerline">
		<!-- <div class="btn-div"> -->
		<!-- <a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a> -->
		<!-- a class="btn btn-success btn-sm" href="">등록</a -->
		<!-- </div> -->
		<div class="page_headerline_container">
			<div class="page_headerline_wrap">
				<div class="headerline_item">
					<label class="">등록자</label>
					<div class="">
						<?php
						if ($w == '') {
							echo get_partner_select("pt_idx", '');
						} else {
							/* echo "<h4>" . $partners[$row['pt_idx']]['mb_bizname'] . "</h4>"; */
							echo "<div class='headerline_item_name'>" . $partners[$row['pt_idx']]['mb_bizname'] . "</div>";
						}
						?>
					</div>
				</div>
				<div class="headerline_item">
					<label class="">진행상태</label>
					<div class=""><span class="manager_result_status_<?php echo $row['wr_status']; ?>"><?php echo ($row['wr_status']) ? $status_arr[$row['wr_status']] : "등록"; ?></span></div>
				</div>
			</div>
			<?php
			if ($row['wr_status'] >= 30) {

				$process_date = "";
				$sql = "SELECT reg_date FROM `log_action` WHERE `wr_id` = '{$row['wr_id']}' and next_status='30' order by log_id desc limit 1";
				$row_date = sql_fetch($sql);
				if ($row_date['reg_date']) {
					$process_date = "\n\n" . $row_date['reg_date'];
				}

			?>
				<!-- <div class="row">
					<label class="col-sm-2 control-label">차주명</label>
					<div class="col-sm-4"><input type="text" id="wr_name" name="wr_name" value="<?php echo $row["wr_name"]; ?>" class="form-control" placeholder="차주명"></div>
					<label class="col-sm-1 control-label">연락처</label>
					<div class="col-sm-5"><span style="font-size:1.5em; color:#0033ff; font-weight:800; letter-spacing: 1px;"><?php echo $row["wr_tel"]; ?></span></div>
				</div>
				<div class="row">
					<label class="col-sm-2 control-label">진행메모</label>
					<div class="col-sm-10"><span style="font-size:1.0em; color:#111;"><?php echo nl2br($row["wr_memo"]); ?><?php echo $process_date; ?></span></div>
				</div> -->
				<!-- <hr /> -->
				<div class="page_headerline_wrap02">
					<div class="manager_info_wrap">
						<div class="manager_info_enter">
							<label class="">차주명</label>
							<input type="text" id="wr_name" name="wr_name" value="<?php echo $row["wr_name"]; ?>" class="form-control info_name" placeholder="">
						</div>
						<div class="manager_info_enter m_t_20">
							<label class="">연락처</label>
							<input type="text" id="wr_tel" name="wr_tel" value="<?php echo $row["wr_tel"]; ?>" class="form-control info_tel">
						</div>
					</div>
					<div class="manager_info_wrap">
						<div class="manager_info_enter">
							<label class="">진행메모</label>
							<textarea id="wr_memo"
								name="wr_memo" class="form-control info_memo" style="height:88px"
								placeholder="진행요청 메모"><?php echo $row["wr_memo"] . $process_date; ?>
                    </textarea>
						</div>
					</div>
				</div>

			<?php
			}
			?>
		</div>
	</div>
</div>
<!-- 접수및 상태확인 끝 -->


<!-- CONTENT START -->
<div class="manager_container">
	<div class="form-group" style="width: 56%;">
		<form id="flogin" name="flogin" action="/app/new/loan-act.php" method="post" class="jsb-form">
			<input type="hidden" name="w" value="<?php echo $w; ?>">
			<input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
			<input type="hidden" name="prev_status" value="<?php echo $row['wr_status']; ?>">

			<!-- 접수내용(왼쪽) 화면 시작 -->
			<div class="form-group" style="background-color: #eeeeee; padding:20px; border:1px solid #bcbcbc">



				<!-- 담보정보 -->
				<div class="">
					<div class="h1_title">담보 정보</div>
					<div class="section collateral-info">
						<div class="form-content">
							<div class="form_field"><label class="w_20 field_title">제목<span class="p_l_2" style="color: #BF1212;">*</span></label>
								<div class="w_80"><input type="text" id="wr_subject" name="wr_subject" value="<?php echo $row["wr_subject"]; ?>" class="form-control" placeholder="홍길동 / 담보종류 / 자금용도 (확인된 사항만 기재)"></div>
							</div>
							<div class="form_field02 bg_gr"><label class="w_20 field_title">대출종류<span class="p_l_2" style="color: #BF1212;">*</span></label>
								<div class="w_80">
									<input type="radio" id="wr_type_01" name="wr_type" value="A" required <?php echo ($row['wr_type'] != 'B') ? "checked" : ""; ?>>
									<label for="wr_type_01">일반 &nbsp;</label>
									<input type="radio" id="wr_type_02" name="wr_type" value="B" required <?php echo ($row['wr_type'] == 'B') ? "checked" : ""; ?>>
									<label for="wr_type_02">매매/경락잔금 (선택시 일부 정보는 등록되지 않습니다) &nbsp;</label>
								</div>
							</div>
							<div class="form_field"><label class="w_20 field_title">담보주소<span class="p_l_2" style="color: #BF1212;">*</span>&nbsp; <span class="winreal"><a href="#" onclick="copyAddress()"><i class="fas fa-regular fa-copy"></i></a></span>
									<div><?php echo $duppop; ?></div>
								</label>
								<div class="w_80">
									<!-- <label><a onclick="execDaumPostcode();">☞주소검색</a></label> -->
									<input type="text" name="address1" id="address1" value="<?php echo $row["wr_addr1"]; ?>" class="form-control" placeholder="기본주소(시/군/구/동) - 주소검색시 자동입력">
									<span id="guide" style="color:#999;display:block"></span>
									<!-- <input type="text" name="address2" id="address2" value="<?php echo $row["wr_addr2"]; ?>" class="form-control" readonly="readonly" style="display:none"> -->
									<input type="text" name="address3" id="address3" value="<?php echo $row["wr_addr3"]; ?>" class="form-control" placeholder="상세주소(동/호,건물명)">
									<!-- <input type="text" name="address_ext" id="address_ext" value="<?php echo $row["wr_addr_ext1"]; ?>" class="form-control" placeholder="추가정보(세대수/층)"> -->
								</div>
							</div>
							<div class="form_field bg_gr"><label class="w_20 field_title">담보구분<span class="p_l_2" style="color: #BF1212;">*</span></label>
								<div class="w_80">
									<input type="radio" id="control_01" name="wr_ca" value="A" required <?php echo ($row['wr_ca'] == 'A') ? "checked" : ""; ?>>
									<label for="control_01">아파트 &nbsp;</label>
									<input type="radio" id="control_02" name="wr_ca" value="B" required <?php echo ($row['wr_ca'] == 'B') ? "checked" : ""; ?>>
									<label for="control_02">빌라 &nbsp;</label>
									<input type="radio" id="control_03" name="wr_ca" value="E" required <?php echo ($row['wr_ca'] == 'E') ? "checked" : ""; ?>>
									<label for="control_03">기타 &nbsp;</label>
								</div>
							</div>
							<div class="form_field">
								<label class="w_20 field_title">지분여부<span class="p_l_2" style="color: #BF1212;">*</span></label>
								<div class="w_80">
									<select id="wr_part_select" name="wr_part" required>
										<option value="A" <?php echo ($row['wr_part'] == 'A' || $owner_percent == '100') ? 'selected' : ''; ?>>단독소유</option>
										<option value="PE" <?php echo ($row['wr_part'] == 'PE') ? 'selected' : ''; ?>>지분소유(기타)</option>
									</select>
									<input type="number" id="control_05" name="wr_part_percent" value="<?php echo $row['wr_part_percent']; ?>" placeholder="30" style="width:100px;"><span class="unit_text"> %</span>
								</div>
							</div>
							<div class="form_field bg_gr"><label class="w_20 field_title">전용면적<span class="p_l_2" style="color: #BF1212;">*</span> &nbsp; <span id="win_real" class="winreal"><i class="fas fa-chart-bar"></i></span></label>
								<div class="w_80"><input type="text" name="wr_m2" id="wr_m2" value="<?php echo $row["wr_m2"]; ?>" class="form-control" style="display:inline-block; width:74%;" placeholder="000.00"><span class="unit_text"> ㎡ (제곱미터)</span></div>
							</div>
						</div>
					</div>
				</div>

				<!-- 담보정보 끝-->



				<!-- 신청정보 시작 -->
				<div class="m_t_30">
					<div class="h1_title">신청 정보</div>
					<div class="section application-info">
						<div class="form-content">
							<div class="form_field"><label class="w_20 field_title">희망금액</label>
								<div class="field_area">
									<input type="text" id="wr_amount" name="wr_amount" style="display:inline-block; width:392px;" value="<?php echo $row["wr_amount"]; ?>" class="form-control">
									<input type="checkbox" id="maxamount" name="maxamount" value="1" <?php if (strpos($row['wr_amount'], '최대요청') === false) echo 'checked'; ?>><label for="maxamount"></label>
								</div>
							</div>
							<!-- park 임대차보증금 -->
							<div class="form_field"><label class="w_20 field_title">임대차보증금</label>
								<div class=""><input type="text" id="wr_rental_deposit" name="wr_rental_deposit" style="display:inline-block; width:392px;" placeholder="있을경우 작성 / 단위 만원" value="<?php echo $row["wr_rental_deposit"]; ?>" class="form-control"> 원</div>
							</div>
							<!-- park 임시 담보정보 (추후 삭제 예정) -->
							<div class="form_field"><label class="w_20 field_title">기타 정보</label>
								<div class="w_80"><textarea id="wr_cont2" name="wr_cont2" class="form-control" style="height:200px;" placeholder="자유양식 작성"><?php echo $row["wr_cont2"]; ?></textarea></div>
							</div>
							<div class="form_field"><label class="w_20 field_title">참고링크<br><span style="font-size: 12px; color:#6e6e6e;">(KB시세 URL)</span></label>
								<div style="display:flex; justify-content: space-between; width:80%">
									<input type="text" id="wr_link1" name="wr_link1" value="<?php echo $row["wr_link1"]; ?>" class="form-control" placeholder="https://링크URL" style="width:343px; margin-right:4px">
									<input type="text" id="wr_link1_subj" name="wr_link1_subj" value="<?php echo $row["wr_link1_subj"]; ?>" class="form-control" placeholder="링크제목" style="width:110px">
									<?php
									if (!empty($row["wr_link1"])) {
										if (!empty(trim($row["wr_link1_subj"]))) {
											echo "<div class='input_link_copy'><a href='{$row['wr_link1']}' target='_blank'><i class='fas fa-solid fa-link'></i></a></div>";
										} else {
											echo "<div><a href='{$row['wr_link1']}' target='_blank'>바로가기</a></div>";
										}
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- 신청정보 끝 -->


				<!-- 채권정보 시작 -->
				<div class="bond-section m_t_30">
					<div class="h1_title">채권 정보</div>
					<div class="section">
						<div class="form_field">
							<label class="field_title" style="width: 134px;">소유지분현황<span class="p_l_2" style="color: #BF1212;">*</span></label>
							<textarea id="wr_cont4" name="wr_cont4" class="form-control" style="height:100px;" placeholder="자유양식 작성"><?php echo $row["wr_cont4"]; ?></textarea>
						</div>
						<div class="form_field bg_gr output-container">
							<label class="field_title03" style="width: 128px;">(근)저당권<br>및<br>전세권 등<span class="p_l_2" style="color: #BF1212;">*</span></label>
							<div class="form-control" style="height:100%">
								<?php
								if ($row["wr_datetime"] < '2024-08-21 00:00:00') {
								?>
									<?php echo isset($row["wr_cont3"]) && !empty($row["wr_cont3"]) ? htmlspecialchars(trim($row["wr_cont3"])) : '<br/><br/>'; ?>
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

											if (strpos($lastTwoChars, '해제') !== false) {
												$spanClass = "strikethrough";
											} else {
												$spanClass = "";
												// $line = str_replace('유지','',$line);
											}
											$view_line = str_replace(['해제', '유지'], '', $line); // '해제' 또는 '유지' 제거

											echo "<div class='row $class' id='row_$i' style='width:100%; margin:5px 0 5px 0; border-bottom: 1px solid #ccc;'>";
											echo "<span style='display:none'>" . $line . "</span>";
											echo "<span class='line-text $spanClass' title='" . $view_line . "'>"
												. (mb_strlen($view_line, 'UTF-8') > 47 ? mb_substr($view_line, 0, 47, 'UTF-8') . "..." : $view_line)
												. "</span>";
											echo "</div>";
										}
										echo '<textarea id="wr_cont3" name="wr_cont3" class="form-control" style="display:none;"></textarea>';
									}
								}
								?>
							</div>
						</div>
					</div>
				</div>


				<!-- 첨부파일 eunjin수정 241121 -->

				<?php
				$pjfile = get_writefile($wr_id);
				$filecnt = number_format($pjfile['count']);
				?>
				<div class="upload_files m_t_30">
					<label class="h1_title">첨부파일 <?php echo "(" . $filecnt . ")"; ?><a href="./loan-file.php?wr_id=<?php echo $wr_id;?>" style="float:right; padding-left:10px;">관리 &gt;&gt;</a></label><br /></label>
					<div class="section">
						<?php
						$cnt = $pjfile['count'];
						if ($cnt) {
						?>
							<!-- 첨부파일 시작 { -->
							<div id="project_v_file">
								<table class="file_download">
									<?php
									foreach ($pjfile as $i => $file) {
										if (isset($file['source']) && $file['source']) {
									?>
											<tr style="border-bottom: 1px solid #ddd">
												<td>
													<a href="<?php echo $file['href']; ?>" class="view_file_download">
														<strong>
															<?php echo $file['source']; ?></strong>
														( <?php echo $file['size']; ?> ) <i class="fa fa-download" aria-hidden="true"></i></a>
													<?php echo $file['memo']; ?>
												</td>
												<td><span class="project_v_file_date">
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

				<!-- <div class="row">
				<hr />
			    </div> -->

				<!-- 첨부파일 항목 안보이기 -->
				<!-- <div class="m_t_20">
					<?php if ($w == '') { ?><div class=""> ※ 첨부파일을 추가하시려면 저장후 첨부파일 버튼을 눌러 업로드해주세요. </div>
					<?php } else { ?><div class=""> ※ 첨부파일을 추가 또는 삭제하시려면 첨부파일 버튼을 눌러 업로드해주세요. </div><?php } ?>
				</div> -->
				<div class="file_action_btn m_t_20">
					<div style="width: 49%;">
						<button class="file_action_save_btn <?php echo $btnclass; ?> btn-block col-sm-4" type="submit"><?php echo $btntxt; ?></button>
					</div>
					<!-- <?php if ($wr_id) { ?>
						<div class="col-sm-4">
							<button class="btn btn-info btn-block col-sm-4" type="button" onclick="document.location.href='./loan-file.php?wr_id=<?php echo $wr_id; ?>';">첨부파일<?php echo "(" . $filecnt . ")"; ?></button>
						</div>
					<?php } ?> -->
					<div style="width: 49%;">
						<button class="file_action_list_btn btn-block col-sm-4" type="button" onclick="document.location.href='./loan-list.php';">목록으로</button>
					</div>
				</div>

			</div>





			<!-- 기존 첨부파일 영역 -->

			<!-- <div class="row">
				<hr />
			</div> -->
			<!-- 첨부파일 시작 -->
			<!-- <?php
					$pjfile = get_writefile($wr_id);
					$filecnt = number_format($pjfile['count']);
					?>
			<div class="upload_files m_t_30">
				<label class="h1_title">첨부파일 <?php echo "(" . $filecnt . ")"; ?><br /></label>
				<div class="section">
					<?php
					$cnt = $pjfile['count'];
					if ($cnt) {
					?> -->
			<!-- 첨부파일 시작 { -->
			<!-- <div id="project_v_file">
							<table class="table">
								<?php
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
										</tr>
								<?php
									}
								}
								?>
							</table>
						</div> -->
			<!-- } 첨부파일 끝 -->
			<!-- <?php
					} else {
						echo "<span style='color:gray'>등록된 첨부파일이 없습니다.</span>";
					}
					?> -->
			<!-- </div> -->
			<!-- </div> -->

			<!-- <div class="row">
				<hr />
			</div> -->

			<!-- 첨부파일 항목 안보이기 -->
			<!-- <div class="row">
				<?php if ($w == '') { ?><div class="col-sm-12 blue"> ※ 첨부파일을 추가하시려면 저장후 첨부파일 버튼을 눌러 업로드해주세요. </div>
				<?php } else { ?><div class="col-sm-12 blue"> ※ 첨부파일을 추가 또는 삭제하시려면 첨부파일 버튼을 눌러 업로드해주세요. </div><?php } ?>
			</div>
			<div class="row">
				<div class="col-sm-4"><button class="btn <?php echo $btnclass; ?> btn-block col-sm-4" type="submit"><?php echo $btntxt; ?></button></div>
				<?php if ($wr_id) { ?><div class="col-sm-4"><button class="btn btn-info btn-block col-sm-4" type="button" onclick="document.location.href='./loan-file.php?wr_id=<?php echo $wr_id; ?>';">첨부파일<?php echo "(" . $filecnt . ")"; ?></button></div><?php } ?>
				<div class="col-sm-4"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="document.location.href='./loan-list.php';">목록으로</button></div>
			</div> -->
		</form>

	</div>


	<!-- 관리자영역 시작 - jin_241120-->

	<div class="form-group" style="width: 42%;">
		<form id="fjudge" name="fjudge" action="/app/new/loan-act.php" method="post" class="jsb-form">
			<input type="hidden" name="w" value="pr">
			<input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
			<input type="hidden" name="prev_status" value="<?php echo $row['wr_status']; ?>">
			<input type="hidden" name="next_status" value="">

			<!-- park 산식계산 -->
			<div class="calculation-section" id="calculation-section">
				<div class="calculation_headline">
					<div>
						한도계산
					</div>
					<div style="font-size: 12px;">
						(단위:만원)
					</div>
				</div>
				<div class="limit_calc_zone">
					<div class="calc_top_zone">
						<div class="auto-calculation">
							<div class="mgr_form_field">
								<label class="field_title">시세</label>
								<input type="text" id="auto_price" name="auto_price" placeholder="시세" readonly value="<?php echo isset($row_calcul_auto['lc_price']) ? $row_calcul_auto['lc_price'] : ''; ?>" class="form-control">
							</div>
							<div class="mgr_form_field">
								<label class="field_title">지분율</label>
								<input type="text" id="auto_part_percent" name="auto_part_percent" placeholder="지분율" readonly value="<?php echo isset($row_calcul_auto['lc_part_percent']) ? $row_calcul_auto['lc_part_percent'] : $row['wr_part_percent']; ?>" class="form-control">
							</div>
							<div class="mgr_form_field">
								<label class="field_title">LTV</label>
								<input type="text" id="auto_ltv" placeholder="LTV" name="auto_ltv" readonly value="<?php echo $selected_value; ?>" class="form-control">
							</div>
							<div class="mgr_form_field">
								<label class="field_title">소액보증금</label>
								<input type="text" id="auto_small_deposit" name="auto_small_deposit" placeholder="소액보증금" readonly value="<?php echo isset($row_calcul_auto['lc_small_deposit']) ? number_format($row_calcul_auto['lc_small_deposit']) : number_format($repay_amt); ?>" class="form-control">
							</div>
							<div class="mgr_form_field">
								<label class="field_title">임대차보증금</label>
								<input type="text" id="auto_rental_deposit" name="auto_rental_deposit" placeholder="임대차보증금" readonly value="<?php echo isset($row_calcul_auto['lc_rental_deposit']) ? number_format($row_calcul_auto['lc_rental_deposit']) : number_format($row['wr_rental_deposit']); ?>" class="form-control">
							</div>
							<div class="mgr_form_field">
								<label class="field_title">선순위최고액</label>
								<input type="text" id="auto_senior_loan" name="auto_senior_loan" placeholder="선순위최고액" readonly value="<?php echo isset($row_calcul_auto['lc_senior_loan']) ? number_format($row_calcul_auto['lc_senior_loan']) : number_format($best_entry['amount']); ?>" class="form-control">
							</div>

							<div class="mgr_result_title"><label for="auto_selected_option">한도(만원)</label></div>
							<div class="mgr_result_choice">
								<input type="radio" id="auto_selected_option" name="selected_option" value="auto"> <label for="auto_selected_option" class="mgr_result_radio"><span id="auto_amount" name="auto_amount"></span></label>
							</div>
						</div>

						<div class="manual-calculation">
							<div class="mgr_form_field">
								<label class="field_title">시세</label>
								<input type="text" id="manual_price" name="manual_price" placeholder="시세" class="form-control">
							</div>
							<div class="mgr_form_field">
								<label class="field_title">지분율</label>
								<input type="text" id="manual_part_percent" name="manual_part_percent" placeholder="지분율" class="form-control">
							</div>
							<div class="mgr_form_field">
								<label class="field_title">LTV</label>
								<input type="text" id="manual_ltv" name="manual_ltv" placeholder="LTV" class="form-control" style="width:75px" >
								<div class="ltv-button">
									<button type="button" id="ltv-increase" class="btn-style">+</button>
									<button type="button" id="ltv-decrease" class="btn-style">-</button>
								</div>
							</div>
							<div class="mgr_form_field">
								<label class="field_title">소액보증금</label>
								<input type="text" id="manual_small_deposit" name="manual_small_deposit" placeholder="소액보증금" class="form-control">
							</div>
							<!--
							<button type="button" id="multiply-btn" class="btn">×2</button>
							<button type="button" id="divide-btn" class="btn">÷2</button>
							-->
							<div class="mgr_form_field">
								<label class="field_title">임대차보증금</label>
								<input type="text" id="manual_rental_deposit" name="manual_rental_deposit" placeholder="임대차보증금" class="form-control">
							</div>
							<div class="mgr_form_field">
								<label class="field_title">선순위최고액</label>
								<input type="text" id="manual_senior_loan" name="manual_senior_loan" placeholder="선순위최고액" class="form-control">
							</div>
							<div class="mgr_result_title02"><label for="manual_selected_option">한도(만원)</label></div>
							<div class="mgr_result_choice">
								<input type="radio" id="manual_selected_option" name="selected_option" value="manual"> <label for="manual_selected_option" class="mgr_result_radio"><span id="manual_amount" name="auto_amount"></span></label>
							</div>
						</div>
					</div>
					<button type="button" id="calculateBtn" class="w_100 mgr_calc_action_btn">계산하기</button>
				</div>
			</div>

			<div class="result_feedback m_t_20">
				<div class="feedback_headline">심사의견<?php if ($row['jd_autoid']) { ?> (<a href='javascript:autojudgeModalPopup(<?php echo $row['jd_autoid']; ?>);' data-jdid='<?php echo $row['jd_autoid']; ?>'><i class='fas fa-balance-scale'></i>자동한도</a>)<?php } ?></div>
				<div class="feedback_zone">
				담보가산정 <input type="text" id="rf_first3" name="rf_first3" value="<?php echo $row["rf_first3"]; ?>"  class="form-control">
				<br/>
					<div class="num_zone">
						<div class="">
							<label class="field_title" for="jd_amount">한도(만원)</label>
							<input type="text" id="jd_amount" name="jd_amount" value="<?php echo $row["jd_amount"]; ?>" class="form-control" placeholder="숫자만 입력">
						</div>
						<div class="">
							<label class="field_title" for="jd_interest">금리(%)</label>
							<input type="text" id="jd_interest" name="jd_interest" value="<?php echo $row["jd_interest"]; ?>" class="form-control" placeholder="연 10%">
						</div>
					</div>
					<div class="m_t_20 memo_zone">
						<div class="">
							<label class="field_title">부대조건</label>
							<textarea id="jd_condition" name="jd_condition" class="form-control" style="height:60px;" placeholder="자유양식"><?php echo $row["jd_condition"]; ?></textarea>
						</div>
					</div>
					<div class="m_t_6 log_zone">
						<?php
						$sql = "select * from log_judge where wr_id = '{$wr_id}' order by jd_id desc limit 3";
						$res = sql_query($sql);
						while ($jd = sql_fetch_array($res)) {
							echo '<div class="">' . $jd['manage_id'] . " / " . $jd['jd_amount'] . " / " . $jd['jd_interest'] . " : " . $jd['reg_date'] . '</div>';
						}
						?>
					</div>
					<div>
						<div class="m_t_20">
							<label class="field_title">검토메모(내부전용)</label>
							<textarea id="jd_memo" name="jd_memo" class="form-control" style="height:80px;" placeholder="제휴사 노출안되는 내부기록"><?php echo $row["jd_memo"]; ?></textarea>
						</div>
					</div>
				</div>
			</div>

			<!-- <h3>심사의견<?php if ($row['jd_autoid']) { ?> (<a href='javascript:autojudgeModalPopup(<?php echo $row['jd_autoid']; ?>);' data-jdid='<?php echo $row['jd_autoid']; ?>'><i class='fas fa-balance-scale'></i>자동한도</a>)<?php } ?></h3>
			<hr /> -->


			<!-- <div class="row">
				<div class="col-sm-6"><label class="control-label" for="jd_amount">한도(만원)</label><input type="text" id="jd_amount" name="jd_amount" value="<?php echo $row["jd_amount"]; ?>" class="form-control" placeholder="숫자만 입력"></div>
				<div class="col-sm-6"><label class="control-label" for="jd_interest">금리(%)</label><input type="text" id="jd_interest" name="jd_interest" value="<?php echo $row["jd_interest"]; ?>" class="form-control" placeholder="연 10%"></div>
			</div> -->
			<!-- <div class="row">
				<div class="col-sm-12"><label class="control-label">부대조건</label><textarea id="jd_condition" name="jd_condition" class="form-control" style="height:60px;" placeholder="자유양식"><?php echo $row["jd_condition"]; ?></textarea></div>
			</div> -->
			<!-- <div class="row"><?php
									$sql = "select * from log_judge where wr_id = '{$wr_id}' order by jd_id desc limit 3";
									$res = sql_query($sql);
									while ($jd = sql_fetch_array($res)) {
										echo '<div class="col-sm-12">' . $jd['manage_id'] . " / " . $jd['jd_amount'] . " / " . $jd['jd_interest'] . " : " . $jd['reg_date'] . '</div>';
									}
									?></div>

			<hr /> -->
			<!-- <div class="row">
				<div class="col-sm-12"><label class="control-label">검토메모(내부전용)</label><textarea id="jd_memo" name="jd_memo" class="form-control" style="height:80px;" placeholder="제휴사 노출안되는 내부기록"><?php echo $row["jd_memo"]; ?></textarea></div>
			</div> -->
			<!-- <div class="row">
				<div class="col-sm-12"><label class="control-label">담보가 산정(만원 / 내부)</label><input type="text" id="rf_first3" name="rf_first3" value="<?php echo $row["rf_first3"]; ?>" class="form-control" placeholder="자유양식"></div>
			</div>
			<div class="row">
				<div class="col-sm-12"><label class="control-label">선순위 원금(만원 / 내부)</label><input type="text" id="rf_first1" name="rf_first1" value="<?php echo $row["rf_first1"]; ?>" class="form-control" placeholder="자유양식"></div>
			</div>
			<div class="row">
				<div class="col-sm-12"><label class="control-label">선순위 설정액(만원 / 내부)</label><input type="text" id="rf_first2" name="rf_first2" value="<?php echo $row["rf_first2"]; ?>" class="form-control" placeholder="자유양식"></div>
			</div> -->

			<br class="clear" />
			<?php if ($row['wr_status'] != 60 && $row['wr_status'] != 99) { ?>
				<div class="feedback_action_btn_wrap">
					<div class="feedback_action_btn"><button class="feedback_btn_save btn-block" type="button" onclick="judge_save()">저장</button></div>
					<?php if ($row['wr_status'] >= 30) { ?>
						<div class="feedback_action_btn"><button class="feedback_btn_okay btn-block" type="button" onclick="judge2_ok()">대출실행</button></div>
						<div class="feedback_action_btn"><button class="feedback_btn_cancel btn-block" type="button" onclick="judge2_deny()">진행취소</button></div>
					<?php } else { ?>
						<div class="feedback_action_btn"><button class="feedback_btn_okay btn-block" type="button" onclick="judge_ok()">가승인</button></div>
						<div class="feedback_action_btn"><button class="feedback_btn_cancel btn-block" type="button" onclick="judge_deny()">부결</button></div>
						<div class="" style="width:157px"><button class="feedback_btn_duplication btn-block" type="button" onclick="judge_dupl()">중복</button></div>
					<?php } ?>
				</div>
			<?php } ?>
			<div class="row">
				<?php if ($row['wr_status'] != 60 && $row['wr_status'] != 99) { ?>
					<?php if ($row['wr_status'] >= 30) { ?>
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
</div>
</div>

<!-- <br class="clear" /> -->

<!-- </div> -->

<div id="autojudgeModalPopup"></div>

<script>
	document.getElementById("calculateBtn").addEventListener("click", function() {
		// 자동 계산된 값을 표시하는 부분
		const autoAmount = calculateAmount(
			document.getElementById("auto_price").value,
			document.getElementById("auto_part_percent").value,
			document.getElementById("auto_ltv").value,
			document.getElementById("auto_small_deposit").value,
			document.getElementById("auto_rental_deposit").value,
			document.getElementById("auto_senior_loan").value
		);
		document.getElementById("auto_amount").textContent = autoAmount;

		// 수동 계산된 값을 표시하는 부분
		const manualAmount = calculateAmount(
			document.getElementById("manual_price").value,
			document.getElementById("manual_part_percent").value,
			document.getElementById("manual_ltv").value,
			document.getElementById("manual_small_deposit").value,
			document.getElementById("manual_rental_deposit").value,
			document.getElementById("manual_senior_loan").value
		);
		document.getElementById("manual_amount").textContent = manualAmount;

	});

	function calculateAmount(price, partPercent, ltv, smallDeposit, rentalDeposit, seniorLoan) {
		const calculatedAmount = ((removeCommas_s(price) * partPercent / 100) * (ltv / 100)) -
			((removeCommas_s(smallDeposit)* partPercent / 100)) - ((removeCommas_s(rentalDeposit)* partPercent / 100)) - 
			(removeCommas_s(seniorLoan) * partPercent / 100);
		return addCommas(calculatedAmount.toFixed(0));
	}


	document.addEventListener("DOMContentLoaded", function() {
		const manualData = <?php echo json_encode($row_calcul_manual); ?>;
		if (manualData && manualData.wr_id) {
			// 수동 계산 데이터가 있을 때
			document.getElementById("manual_price").value = manualData.lc_price || '';
			document.getElementById("manual_part_percent").value = manualData.lc_part_percent || '';
			document.getElementById("manual_ltv").value = manualData.lc_ltv || '';
			document.getElementById("manual_small_deposit").value = manualData.lc_small_deposit || '';
			document.getElementById("manual_rental_deposit").value = manualData.lc_rental_deposit || '';
			document.getElementById("manual_senior_loan").value = manualData.lc_senior_loan || '';
		} else {
			// 수동 계산 데이터가 없을 때 (기본값 설정)
			setTimeout(function() {
				document.getElementById("manual_price").value = document.getElementById("auto_price").value;
				document.getElementById("manual_part_percent").value = document.getElementById("auto_part_percent").value;
				document.getElementById("manual_ltv").value = document.getElementById("auto_ltv").value;
				document.getElementById("manual_small_deposit").value = document.getElementById("auto_small_deposit").value;
				document.getElementById("manual_rental_deposit").value = document.getElementById("auto_rental_deposit").value;
				document.getElementById("manual_senior_loan").value = document.getElementById("auto_senior_loan").value;
			}, 500);
		}
		
		/*
		// 소액임대차 곱하기, 나누기
		const depositField = document.getElementById('manual_small_deposit');
		const multiplyBtn = document.getElementById('multiply-btn');
		const divideBtn = document.getElementById('divide-btn');

		// 곱하기 버튼 클릭 이벤트
		multiplyBtn.addEventListener('click', function () {
			const currentValue = removeCommas_s(depositField.value); // 콤마 제거 후 숫자로 변환
			const newValue = currentValue * 2; // 값 ×2
			depositField.value = addCommas(newValue); // 결과에 콤마 추가
		});

		// 나누기 버튼 클릭 이벤트
		divideBtn.addEventListener('click', function () {
			const currentValue = removeCommas_s(depositField.value); // 콤마 제거 후 숫자로 변환
			const newValue = currentValue / 2; // 값 ÷2
			depositField.value = addCommas(newValue); // 결과에 콤마 추가
		});
		*/

		// LTV 플러스 마이너스
		const ltvField = document.getElementById('manual_ltv');
		const ltvIncrease = document.getElementById('ltv-increase');
		const ltvDecrease = document.getElementById('ltv-decrease');

		function updateLTV(amount) {
			const currentValue = parseFloat(ltvField.value) || 0;
			const newValue = currentValue + amount;
			ltvField.value = Math.max(0, newValue);
		}

		ltvIncrease.addEventListener('click', function () {
			updateLTV(5);
		});

		ltvDecrease.addEventListener('click', function () {
			updateLTV(-5);
		});

		// 자동계산 실행, 클릭이벤트와 안겹치도록
		$('#calculateBtn').on('calculateOnly', function() {
			const autoAmount = calculateAmount(
				document.getElementById("auto_price").value,
				document.getElementById("auto_part_percent").value,
				document.getElementById("auto_ltv").value,
				document.getElementById("auto_small_deposit").value,
				document.getElementById("auto_rental_deposit").value,
				document.getElementById("auto_senior_loan").value
			);
			document.getElementById("auto_amount").textContent = autoAmount;

			const manualAmount = calculateAmount(
				document.getElementById("manual_price").value,
				document.getElementById("manual_part_percent").value,
				document.getElementById("manual_ltv").value,
				document.getElementById("manual_small_deposit").value,
				document.getElementById("manual_rental_deposit").value,
				document.getElementById("manual_senior_loan").value
			);
			document.getElementById("manual_amount").textContent = manualAmount;
		});

		setTimeout(function() {
			$('#calculateBtn').trigger('calculateOnly');
		}, 700);

		// 계산 클릭시
		$('#calculateBtn').on('click', function() {
			$('#calculateBtn').trigger('calculateOnly');
			var amount = removeCommas_s($('#manual_amount').text()); // 수동 계산 한도 값 가져오기
			var price = removeCommas_s($('#manual_price').val()); // 계산기 시세값 가져오기
			
			amount = parseInt(amount.toString().slice(0, -3) + '000', 10);
			if(amount < 0) amount=0;
			$('#jd_amount').val(amount);
			$('#rf_first3').val(price);
			$('input[type="radio"][name="selected_option"][value="manual"]').prop('checked', true).focus();

			setTimeout(function() {
				$('input[type="radio"][name="selected_option"][value="manual"]').blur(); // 포커스 해제
			}, 100);
		});

		// 계산식 엔터 허용
		$('#calculation-section').on('keydown', function(e) {
			if (e.key === 'Enter') {
				e.preventDefault(); // 엔터 키로 폼이 제출되는 것을 막음
				$('#calculateBtn').click();
			}
		});

		// 선택 라디오버튼 클릭시
		$('[name="selected_option"]').on('click', function() {
			const selectedOption = $(this).val();
			let amount;
			var price;

			if (selectedOption === 'auto') {
				amount = removeCommas_s($('#auto_amount').text()); // 자동 계산 한도 값 가져오기
				price = removeCommas_s($('#auto_price').val()); // 계산기 시세값 가져오기
			} else if (selectedOption === 'manual') {
				amount = removeCommas_s($('#manual_amount').text()); // 수동 계산 한도 값 가져오기
				price = removeCommas_s($('#manual_price').val()); // 계산기 시세값 가져오기
			}
			amount = parseInt(amount.toString().slice(0, -3) + '000', 10);
			if(amount < 0) amount=0;
			$('#rf_first3').val(price);
			$('#jd_amount').val(amount);
		});

		// 자동 콤마
		$('#auto_price, #auto_small_deposit, #auto_rental_deposit, #auto_senior_loan, #manual_price, #manual_small_deposit, #manual_rental_deposit, #manual_senior_loan').on('input', function() {
			let value = $(this).val();
			value = value.replace(/[^0-9]/g, '');
			if (value) {
				value = Number(value).toLocaleString();
			}
			$(this).val(value);
		});
	});

	function autojudgeModalPopup(jdid) {
		$.ajax({
			url: "./modal.autojudge.php",
			data: {
				jd_id: jdid
			},
			dataType: "html",
			success: function(data) {
				//console.log(data);	// .load(data)
				//$('#autojudgeModalPopup').html(data);
				$("#autojudgeModalPopup").html(data).dialog({
					title: "자동심사 결과",
					height: "auto",
					width: 450,
					modal: true,
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


	$(function() {
		commonjs.selectNav("navbar", "newloan");

		/*
		var status = '<?php echo $row['wr_status']; ?>';
		if(status > '1') {
			$('input:radio:not(:checked)').attr('disabled', 'disabled');
			$('input').attr('readonly', 'readonly');
			$('textarea').attr('readonly', 'readonly');
			$('.btn-warning').attr('disabled', 'disabled');
		}
		*/
	});

	// 중복 클릭시 해당 윈도우 보이기
	$(document).ready(function() {
		$('.btn_infowin').on('click', function() {
			//console.log( this);
			var winsn = $(this).data("winsn");
			//$("#"+winsn).toggle();
			$("#" + winsn).dialog({
				title: "검색결과 최대 6개",
				resizable: false,
				height: "auto",
				width: 450,
				modal: true,
				open: function() {
					$('.ui-widget-overlay').off('click');
					$('.ui-widget-overlay').on('click', function() {
						$("#" + winsn).dialog('close');
					})
				}
			});
		});

		// 실거래가 조회
		$('#win_real').on('click', function() {
			var addr1 = $("#address1").val();
			var py = $("#wr_m2").val();
			var url = '/app/real/newwin_real.php?addr1=' + addr1 + '&py=' + py;
			window.open(url, 'newwin_real', 'scrollbars=yes,width=650,height=600,top=10,left=100');
		});
	});

	function copyAddress() {
		var addressInput = document.getElementById("address1");

		addressInput.select();
		addressInput.setSelectionRange(0, 99999); // For mobile devices

		document.execCommand("copy");
	}

	function judge_save() {
		var f = document.fjudge;
		f.submit();
	}

	function judge_ok() {
		var f = document.fjudge;
		f.next_status.value = '10'; // 승인
		f.submit();
	}

	function judge_deny() {
		var f = document.fjudge;
		f.next_status.value = '20'; // 부결
		f.submit();
	}

	function judge_dupl() {
		var f = document.fjudge;
		f.next_status.value = '90'; // 중복
		f.submit();
	}

	function judge2_ok() {
		var f = document.fjudge;
		f.next_status.value = '60'; // 대출실행
		f.submit();
	}

	function judge2_deny() {
		var f = document.fjudge;
		f.next_status.value = '99'; // 진행취소
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
				// 도로명 주소의 노출 규칙에 따라 주소를 표시한다.
				// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
				var roadAddr = data.roadAddress; // 도로명 주소 변수
				var extraRoadAddr = ''; // 참고 항목 변수

				// 법정동명이 있을 경우 추가한다. (법정리는 제외)
				// 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
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

<script>
	// 수동계산 클릭 이벤트
	document.addEventListener('click', (event) => {
		const calculationElements = document.querySelectorAll('.manual-calculation');

		calculationElements.forEach((element) => {
			if (element.contains(event.target)) {
				element.style.borderColor = '#4466ff';
			} else {
				element.style.borderColor = '#BCBCBC';
			}
		});
	});

		//중복값 처리 이벤트
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('duplicate-data-btn')) {
            
			
			const rfFirst3 = e.target.getAttribute('data-rf-first3');
            const jdAmount = e.target.getAttribute('data-jd-amount');
            const jdInterest = e.target.getAttribute('data-jd-interest');
            const jdCondition = e.target.getAttribute('data-jd-condition');

			document.getElementById('rf_first3').value = rfFirst3;
			document.getElementById('jd_amount').value = jdAmount;
			document.getElementById('jd_interest').value = jdInterest;
			document.getElementById('jd_condition').value = jdCondition;
        }
    });
});
</script>


<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>