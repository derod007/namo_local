<?php
header("Content-Type: application/json; charset=utf-8");
@ini_set("default charset","utf8");


include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

if(!$member['mb_id']) {
    alert('접근권한이 없습니다.');
}

$dong1 = trim($_GET['dong']);

$tmp = explode(" ",$dong1);
if($tmp[0]) $dong = $tmp[0];
else if($tmp[1]) $dong = $tmp[1];
else $dong = $dong1;

// search
$where = array();

if($term)
{
	$where[] = " (a.dong like '%{$term}%' or a.danzi like '%{$term}%' or a.kbcode like '%{$term}%' or a.py like '%{$term}%') ";
}

if($dong)
{
	$where[] = " a.dong like '%{$dong}%' ";
}

if($where) {
	$where_sql = " where ".implode(" and ",$where)."";
} else {
	$where_sql = "";
}

$orderby = " order by idx asc ";

if(!isset($start)) $start = 0;
if(!isset($length)) $length = $config['rows'];

//$sql = " select * from {$jsb['kbapt_table']} {$where_sql} {$orderby} limit 300";

$sql = " select a.*, b.region, b.dong from {$jsb['kbapt_info_table']} as a 
				left join  {$jsb['regioncode_table']} as b on a.rcode = b.code and b.use_yn='존재'
				{$where_sql} {$orderby} limit 300 ";

$result = sql_query($sql);
$res['results'] = array();
$i = 0;
while($row=sql_fetch_array($result)){
	//$row['text'] = $row['si'].' '.$row['bun'].' '.$row['dong'].' '.$row['danzi'].' : '.$row['py'];
	$row['text'] = $row['dong'].' '.$row['danzi'].' '.$row['zibun'].' : '.$row['py_info']; 
	$res['results'][] = $row;
}
$res['query'] = $sql;
echo json_encode($res);

exit;
?>