<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/data.inc.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//  error_reporting( E_ALL );
//  ini_set( "display_errors", 1 );


//print_r($_POST);

$loan_table = "loan_write";

$partners = get_partnerlist();

// search
$where = array();

if($status != '') {
	$where[] = " wr_status = '{$status}' ";
}

if($searchtxt != '') {
	$where[] = " ( wr_subject like '%$searchtxt%' or  wr_addr1 like '%$searchtxt%' or  wr_addr2 like '%$searchtxt%'  ) ";
}

// 전체내역을 엑셀로 저장하면 메모리 초과 오류가 발생하므로, 날짜 입력이 없을시 해당월 자료만 저장
if($regdate == '') {
	$regdate = date("Y-m");
}

if($regdate != '') {
	$where[] = " wr_datetime like '{$regdate}%' ";
}

if(!isset($_POST['page'])) {
	$page = 1;	
}

if(count($where) > 0) {
	$where_sql = " where ".implode(" and ",$where)."";
} else {
	$where_sql = "";
}

if($sortName) {
	if($sortName == 'no') $orderby = " order by wr_id ";
	else $orderby = " order by ".$sortName." ";
	
	if($sortASC == 'false') {
		$orderby .= " desc ";
	} else {
		$orderby .= " asc ";
	}
	
} else {
	$orderby = " order by wr_id desc ";
}

$sql = " select count(*) as cnt from {$loan_table} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

$sql = " select * from {$loan_table} {$where_sql} {$orderby} ";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = 1;
while($row=sql_fetch_array($result)){
	$row['data_no'] = $row['wr_id'];
	$row['no'] = $no;
	
	switch($row['wr_ca']) {
		case "A" : $row['wr_ca'] = "아파트"; break;
		case "B" : $row['wr_ca'] = "빌라"; break;
		case "E" : $row['wr_ca'] = "기타"; break;
		default : $row['wr_ca'] = "기타"; 
	}
	
	if($row['wr_part'] == 'P' || $row['wr_part'] == 'PE') {
		$row['wr_ca'] = $row['wr_ca']."[지분]";
	}
	
	$row['address'] = $row['wr_addr1']." \n".$row['wr_addr3']." ".$row['wr_addr2'];
	$row['wdate'] = substr($row['wr_datetime'],0,16);
	$row['status'] = $status_arr[$row['wr_status']];
	$row['mb_bizname'] = $partners[$row['pt_idx']]['mb_bizname'];
	$row['mb_name'] = $partners[$row['pt_idx']]['mb_name'];
	
	$pjfile = get_writefile($row['wr_id']);
	$row['filecnt'] = number_format($pjfile['count']);
	
	$data[] = $row;
	$no++;
}

//$res['success'] = true;
$res['recordsTotal'] = intval($total_count);
$res['recordsFiltered'] = intval($total_count);
//$res['page'] = $page;
$res['search'] = $search;
$res['data'] = $data;
$res['total']['cnt'] = number_format($total_count);

///////////////////////////////////////////////

require_once '../../vendor/Excel/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);

$sheet = $objPHPExcel->getActiveSheet();

// 스타일 정의
$sheet->getStyle('A1:Z500')->getAlignment()->setWrapText(true);
$sheet->getStyle('A1:Z500')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('A1:Z500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:Z500')->getFont()->setSize(10);
$sheet->getStyle('A1:S1')->getFont()->setBold(true)->setSize(10);
$sheet->getStyle('A1:S1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
$sheet->getStyle('A1:S1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$sheet->getStyle('C2:D500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('F2:F500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//$sheet->getStyle('K2:K500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('T1')->getFont()->setSize(10);	// 마지막 열에 커서 

$sheet->getColumnDimension('A')->setWidth(10);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(50);
$sheet->getColumnDimension('D')->setWidth(50);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(30);
$sheet->getColumnDimension('G')->setWidth(8);
$sheet->getColumnDimension('H')->setWidth(15);
$sheet->getColumnDimension('I')->setWidth(20);
$sheet->getColumnDimension('J')->setWidth(20);
$sheet->getColumnDimension('K')->setWidth(15);
$sheet->getColumnDimension('L')->setWidth(15);
$sheet->getColumnDimension('M')->setWidth(40);

$sheet->getColumnDimension('N')->setWidth(15);
$sheet->getColumnDimension('O')->setWidth(20);
$sheet->getColumnDimension('P')->setWidth(20);
$sheet->getColumnDimension('Q')->setWidth(40);

/*
$sheet->getColumnDimension('N')->setWidth(15);
$sheet->getColumnDimension('O')->setWidth(20);

$sheet->getColumnDimension('P')->setWidth(15);
$sheet->getColumnDimension('Q')->setWidth(15);
$sheet->getColumnDimension('R')->setWidth(15);
$sheet->getColumnDimension('S')->setWidth(40);
*/

$rowCount = 1;
$pos = 1;
$columnPos = 0;
$columnName = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T'];
//$columnTitle = ['', '', ''];

// $activeSheet->fromArray($data, NULL, 'A2');
// $objPHPExcel->getActiveSheet()->SetCellValue('A1', $data[5]['code']);

// 제목 설정
$sheet
->SetCellValue($columnName[$columnPos++].$rowCount, 'NO')
->SetCellValue($columnName[$columnPos++].$rowCount, '구분')
->SetCellValue($columnName[$columnPos++].$rowCount, '제목')
->SetCellValue($columnName[$columnPos++].$rowCount, '담보주소')
->SetCellValue($columnName[$columnPos++].$rowCount, '전용면적(㎡)')
->SetCellValue($columnName[$columnPos++].$rowCount, '물건참고')
->SetCellValue($columnName[$columnPos++].$rowCount, '첨부')
->SetCellValue($columnName[$columnPos++].$rowCount, '진행상태')
->SetCellValue($columnName[$columnPos++].$rowCount, '등록업체')
->SetCellValue($columnName[$columnPos++].$rowCount, '등록일')
->SetCellValue($columnName[$columnPos++].$rowCount, '승인한도(만원)')
->SetCellValue($columnName[$columnPos++].$rowCount, '승인금리(%)')
->SetCellValue($columnName[$columnPos++].$rowCount, '부대조건')
/*
->SetCellValue($columnName[$columnPos++].$rowCount, '차주명')
->SetCellValue($columnName[$columnPos++].$rowCount, '연락처')
*/
->SetCellValue($columnName[$columnPos++].$rowCount, '담보가산정')
->SetCellValue($columnName[$columnPos++].$rowCount, '선순위원금')
->SetCellValue($columnName[$columnPos++].$rowCount, '선순위설정액')
->SetCellValue($columnName[$columnPos++].$rowCount, '검토메모');

$rowCount++;
$columnPos = 0;

foreach ($data as $row) {

	$sheet
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['no'])
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['wr_ca'])
	//->setCellValueExplicit($columnName[$columnPos++].$rowCount, $row['Phone'])    // 전화번호
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['wr_subject'])
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['address'])
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['wr_m2'])
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['wr_addr_ext1'])
	->setCellValueExplicit($columnName[$columnPos++].$rowCount, $row['filecnt'])
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['status'])
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['mb_bizname'])
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['wdate'])
	->setCellValueExplicit($columnName[$columnPos++].$rowCount, $row['jd_amount'])
	->setCellValueExplicit($columnName[$columnPos++].$rowCount, $row['jd_interest'])
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['jd_condition'])
	/*
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['wr_name'])
	->setCellValueExplicit($columnName[$columnPos++].$rowCount, $row['wr_tel'])
	*/
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['rf_first3'])
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['rf_first1'])
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['rf_first2'])
	->SetCellValue($columnName[$columnPos++].$rowCount, $row['jd_memo']);
	$rowCount++;
	$columnPos = 0;
	// $pos++;
}

$excelFileName = 'loaninfo_list_'.date("YmdHis").'.xls';

header('Content-Type: application/vnd.ms-excel;charset=utf-8');
header('Content-type: application/x-msexcel;charset=utf-8');
header('Content-Disposition: attachment;filename="'.$excelFileName.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

$objWriter->save('php://output');
//$objWriter->save('export/'.$excelFileName);

exit;
?>