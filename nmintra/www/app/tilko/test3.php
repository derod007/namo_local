<?php
ini_set("display_errors", 1); // 오류 메시지를 출력하도록 설정
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

// SQL 쿼리 실행 및 결과 가져오기
$sql = "SELECT * FROM tilkoapi_risuretrieve WHERE idx = '2389'";
$result = sql_query($sql);
$row = sql_fetch_array($result);

$test = $row['Result'];

// 태그 값을 추출하는 일반적인 함수
function extractTagValues($xmlString, $tagName) {
    $pattern = '/<' . preg_quote($tagName, '/') . '>\s*<!\[CDATA\[(.*?)\]\]>\s*<\/' . preg_quote($tagName, '/') . '>/s';
    preg_match_all($pattern, $xmlString, $matches);
    return $matches[1]; // 추출된 값 배열 반환
}

// 태그와 설명을 매핑
$tagDescriptions = [
    'a301pin' => '고유번호',
    'wksbi_address' => '주소',
    'wksbw_indi_no' => '표기번호',
    'wksbw_receve' => '접수',
    'wksbw_buld_cont' => '건물 내역',
    'wksbw_caus_and_etc' => '등기원인 및 기타사항',
    'wksbx_real_indi_no' => '대지권의 목적인 토지의 표시 - 표시번호',
    'wksbx_real_indi_cont' => '대지권의 목적인 토지의 표시 - 소재지번',
    'wksbx_land_type' => '대지권의 목적인 토지의 표시 - 지목',
    'wksbx_area' => '대지권의 목적인 토지의 표시 - 면적',
    'wksbx_caus_and_etc' => '대지권의 목적인 토지의 표시 - 등기원인 및 기타사항',
    'wksby_indi_no' => '표제부 - 표시번호',
    'wksby_receve' => '표제부 - 접수',
    'wksby_build_no' => '표제부 - 건물번호',
    'wksby_buld_cont' => '표제부 - 건물내역',
    'wksby_caus_and_etc' => '표제부 - 등기원인 및 기타사항',
    'wksbz_indi_no' => '표제부(대지권) - 표시번호',
    'wksbz_lot_type' => '표제부(대지권) - 대지권종류',
    'wksbz_lot_num' => '표제부(대지권) - 대지권비율',
    'wksbz_caus_and_etc' => '표제부(대지권) - 등기원인 및 기타사항',
    'wksbk_kap_rank_no' => '갑구 - 순위번호',
    'wksbk_rgs_aim_cont' => '갑구 - 등기목적',
    'wksbk_receve' => '갑구 - 접수',
    'wksbk_rgs_caus' => '갑구 - 등기원인',
    'wksbk_nomprs_and_etc' => '갑구 - 권리자 및 기타사항',
    'wksbe_eul_rank_no' => '을구 - 순위번호',
    'wksbe_rgs_aim_cont' => '을구 - 등기목적',
    'wksbe_receve' => '을구 - 접수',
    'wksbe_rgs_caus' => '을구 - 등기원인',
    'wksbe_nomprs_and_etc' => '을구 - 권리자 및 기타사항'
];

// 태그와 값을 저장할 배열
$allValues = [];
foreach ($tagDescriptions as $tag => $description) {
    $values = extractTagValues($test, $tag);
    foreach ($values as $value) {
        $allValues[$description][] = htmlspecialchars($value); // HTML로 안전하게 변환
    }
}

// 저장된 값을 JSON으로 인코딩하여 JavaScript에서 사용
$allValuesJson = json_encode($allValues);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>등기부테스트</title>
    <style>
        .red-text {
            color: red;
            text-decoration: line-through;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <h2>등기부등본</h2>
    <button onclick="showAll()">전체보기</button>
    <button onclick="showBlackOnly()">활성화 보기</button>
    <div id="values-container">
        <?php
        foreach ($allValues as $description => $values) {
            echo "<h3>$description:</h3>";
            foreach ($values as $value) {
                // &로 감싸진 값은 빨간색으로 표시
                if (strpos($value, '&') !== false) {
                    $value=str_replace('&','',$value);
                    $value=str_replace('amp;','',$value);
                    echo "<div class='value red-text' data-description='$description'>$value</div>";
                } else {
                    echo "<div class='value' data-description='$description'>$value</div>";
                }
            }
            echo "<br/>";
        }
        ?>
    </div>

    <script>
        const allValues = <?php echo $allValuesJson; ?>;
        
        function showAll() {
            document.querySelectorAll('.value').forEach(el => el.classList.remove('hidden'));
        }

        function showBlackOnly() {
            document.querySelectorAll('.value').forEach(el => {
                if (el.classList.contains('red-text')) {
                    el.classList.add('hidden');
                    
                } else {
                    el.classList.remove('hidden');
                }
            });
        }
    </script>
</body>
</html>
