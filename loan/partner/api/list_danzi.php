<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

$targeturl = "http://nmapi.xfund.co.kr/get_search_danzi.php";

$search = serialize($_POST);

//print_r($_POST);
//die();
$param_list = array(
	"region_code" => $_POST['region'],
	"searchtxt" => $_POST['searchtxt'],
	"danzi" => $_POST['danzi'],
	"page" => $_POST['page'],
	"start" => $_POST['start'],
	"length" => $_POST['length'],
);
//$params_json = json_encode($param_list);
$post_field_string = http_build_query($param_list, '', '&');

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $targeturl);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field_string);
//curl_setopt($ch, CURLOPT_HEADER, TRUE);
//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

$response = curl_exec($ch);
$curlinfo = curl_getinfo($ch);
curl_close($ch);

$res = array();
//$res['search'] = $search;
$res['data'] = json_decode($response);
//print_r2($res['data']);
//echo json_encode($response);

echo $response;