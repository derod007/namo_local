<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');
include_once('./inc_auth.php');

$search = $_POST['search'];

$sigungu = $_POST['sigungu'];
$dong = $_POST['dong'];
$zibun = $_POST['zibun'];
$danzi = $_POST['danzi'];
$py = $_POST['py'];
$year = $_POST['year'];

$py_where = "";
if($py) {
	 
	$s_py = number_format($py * 0.9, 2, '.', '');
	$e_py = number_format($py * 1.1, 2, '.', '');
	$py_where = "and (py between {$s_py} and {$e_py} )";
}

// 국토부 실거래가
//$sql = "SELECT * FROM {$jsb['actualprice_table']} WHERE region_code = '{$apt_info['sigun_code']}' and zibun = '{$apt_info['zibun']}' order by yyyy desc, mm desc, dd desc limit 20";
$sql = "SELECT * FROM {$jsb['actualprice_table']} WHERE yyyy = '{$year}' and sigungu='{$sigungu}' and dong='{$dong}' and zibun = '{$zibun}' and open_yy <= yyyy {$py_where} order by yyyy desc, mm desc, dd desc limit 20";
$result = sql_query($sql);
$apt_real = array();
$i=1;
while($row = sql_fetch_array($result)) {
	// 아파트 오픈년월보다 이후 데이터만 출력
	$ap_opendate = $row['yyyy'].str_pad($row['mm'],2,'0',STR_PAD_LEFT);
	//if($ap_opendate >= $apt_info['opendate']) {
		$row['no'] = $i;
		$row['ymd'] = $row['yyyy'].".".str_pad($row['mm'],2,'0',STR_PAD_LEFT);
		$row['py'] = number_format($row['py'],2);
		$apt_real[] = $row;
		$i++;
	//}
}

$data['apt_real'] = $apt_real;
//print_r2($apt_real);

//die();
//$res['search'] = $search;
$res['total'] = count($apt_real);
$res['data'] = $apt_real;
$res['sql'] = addslashes($sql);

if(isset ($_GET['callback']))
{
    //header("Content-Type: application/json");
    echo $_GET['callback']."(".json_encode($res).")";

} else {
	echo json_encode($res);
}
exit;