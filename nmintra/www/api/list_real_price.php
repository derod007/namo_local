<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

header("Content-Type: application/json; charset=utf-8");
ini_set("default charset","utf8");

include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

//print_r($_POST);

// search
$where = array();

if($danzi!='') {
	$where[] = " danzi like '%$danzi%' ";
}

if($region!='') {
	$where[] = " region_code = '$region' ";
}

if($yyyy) {
	$where[] = " yyyy = '$yyyy' ";
}

if($mm) {
	$where[] = " mm = '$mm' ";
}

if(!isset($_POST['page'])) {
	$page = 1;	
}

if($where) {
	$where_sql = " where ".implode(" and ",$where)."";
} else {
	$where_sql = "";
}

if($sortName) {
	$orderby = " order by ".$sortName." ";
	if($sortASC == 'false') {
		$orderby .= " desc ";
	} else {
		$orderby .= " asc ";
	}
	
} else {
	$orderby = " order by yyyy desc, mm desc, load_no desc ";
}

$sql = " select count(*) as cnt, sum(py) as sum_py, sum(price) as sum_price from {$jsb['actualprice_table']} {$where_sql} ";
$row = sql_fetch($sql);
$total_count = ($row['cnt'])?$row['cnt']:0;
$total_py = ($row['sum_py'])?$row['sum_py']:0;
$total_price = ($row['sum_price'])?$row['sum_price']:0;

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

//$sql = " select *, (select kbcode from {$jsb['kbapt_link_table']} where ap_region={$jsb['actualprice_table']}.region_code and ap_danzi={$jsb['actualprice_table']}.danzi and ap_zibun={$jsb['actualprice_table']}.zibun limit 1 ) as kbcode from {$jsb['actualprice_table']} {$where_sql} {$orderby} limit {$start}, {$length} ";
$sql = " select *, (select kbcode from {$jsb['kbapt_link_table']} where ap_region={$jsb['actualprice_table']}.region_code and ap_danzi={$jsb['actualprice_table']}.danzi and ap_zibun={$jsb['actualprice_table']}.zibun limit 1 ) as kbcode 
				from {$jsb['actualprice_table']} 
				{$where_sql} {$orderby} limit {$start}, {$length} ";
//echo "<div>".$sql."</div>";
$result = sql_query($sql);
$data = array();
$i = 0;
$no = $total_count - $start;
while($row=sql_fetch_array($result)){
	$row['data_no'] = $row['no'];
	$row['no'] = $no;
	$row['rdate'] = $row['yyyy']."-".$row['mm'];
	$row['region'] = $row['region_code']." ".$row['sigungu']." ".$row['dong'];
	$row['py_h'] = ($row['py']/3.3);
	$row['py_view'] = $row['py']."㎡ /".number_format(($row['py']/3.3),0)."평";
	$row['pyprice'] =  number_format($row['price'] / round(($row['py']/3.3),2));
	$row['price'] = number_format($row['price']);
	$row['wdate'] = substr($row['wdate'], 0, 16);	
	
	$data[] = $row;
	$no--;
}

$res['draw'] = intval($draw);
//$res['success'] = true;
$res['recordsTotal'] = intval($total_count);
$res['recordsFiltered'] = intval($total_count);
//$res['page'] = $page;
$res['search'] = $search;
$res['data'] = $data;
$res['total']['cnt'] = number_format($total_count);
if(!$total_py) {
	$res['total']['pyprice'] = 0;
} else {
	$res['total']['pyprice'] = number_format($total_price/($total_py/3.3),2);
}
$res['total']['price'] = number_format($total_price);

echo json_encode($res);

exit;
?>