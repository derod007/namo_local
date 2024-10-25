<?php
//error_reporting(E_ALL);
ini_set("display_errors", 0);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

$kbno = $_GET['data_no'];
if(strpos($kbno, ":") !== false) {
	$kbno = strstr($kbno, ":", true);
}

if(!$kbno) {
	alert('잘못된 접근입니다.');
	die();
}

$sql = "SELECT *,  (select dong from {$jsb['regioncode_table']} where code=regioncode limit 1) as dong FROM `kbland_danzi` WHERE kbno='{$kbno}' limit 1";
$data = sql_fetch($sql);

$kblink = "https://kbland.kr/c/".$data['kbno']."?ctype=".$data['mmjong']."&xy=".$data['lat'].",".$data['lng'].",16";
$kblink_href = "<a href='{$kblink}' target='_blank'>KB바로가기</a>";
?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>단지 상세정보</h1>
</div>

<?php
//print_r2($data);
?>
<h4>KB 단지 정보</h4>
<table class="table table-striped table-bordered jsb-table">
	<tr>
		<th class="col-sm-2">단지명 </th>
		<td class="col-sm-6" colspan="3"><?php echo $data['danzi'];?></td>
		<th class="col-sm-2">구분</th>
		<td class="col-sm-2"><?php echo $data['mmgubun'];?></td>
	</tr>
	<tr>
		<th class="col-sm-2">일련번호</th>
		<td class="col-sm-2"><?php echo $data['kbno']?></td>
		<th class="col-sm-2">물건식별자</th>
		<td class="col-sm-2"><?php echo $data['kbcode'];?></td>
		<th class="col-sm-2">재건축여부</th>
		<td class="col-sm-2"><?php echo $data['rebuild'];?></td>
	</tr>
	<tr>
		<th class="col-sm-2">법정동</th>
		<td class="col-sm-6" colspan="3"><?php echo $data['dong'];?></td>
		<th class="col-sm-2">법정동코드</th>
		<td class="col-sm-2"><?php echo $data['regioncode'];?></td>
	</tr>
	<tr>
		<th class="col-sm-2">위/경도</th>
		<td class="col-sm-4" colspan="2"><?php echo $data['lat'].' / '.$data['lng'];?></td>
		<th class="col-sm-2"><?php echo $kblink_href;?></th>
		<th class="col-sm-2">등록일</th>
		<td class="col-sm-2"><?php echo $data['lastdate'];?></td>
	</tr>
</table>

<h4>평형정보</h4>
<table class="table table-striped table-bordered jsb-table">
	<tr>
		<th>번호</th>
		<th>공급면적</th>
		<!--th>계약면적</th -->
		<th>전용면적</th>
		<th>KB시세</th>
		<th>KB상한가</th>
		<th>KB하한가</th>
		<th>타입</th>
		<th>방수</th>
		<th>세대수</th>
		<th>평면도</th>
	</tr>
<?php

$sql = "SELECT * FROM `kbland_danzi_py` WHERE kbno='{$kbno}' order by room asc, area_cr asc";
$result = sql_query($sql);
$i=0;
$housholds = 0;
while($row=sql_fetch_array($result)){
	$i++;
	
	if($row['pyno'] == '0') {
?>
	<tr>
		<td colspan="12">No Data!</td>
	</tr>
<?php
	} else {
?>
	<tr>
		<td><?php echo $i?></td>
		<td><?php echo $row['area_sp']?>㎡ / <?php echo number_format($row['area_sp']/3.3058,0); ?>평</td>
		<!--td><?php echo $row['area_cr']?>㎡ / <?php echo number_format($row['area_cr']/3.3058,0); ?>평</td -->
		<td><?php echo $row['area_de']?>㎡ / <?php echo number_format($row['area_de']/3.3058,0); ?>평</td>
		<td class="basic-<?php echo $row['pyno']?>"><?php echo $row['kbprice_basic']?></td>
		<td class="high-<?php echo $row['pyno']?>"><?php echo $row['kbprice_high']?></td>
		<td class="low-<?php echo $row['pyno']?>"><?php echo $row['kbprice_low']?></td>
		<td><?php echo $row['house_type']?></td>
		<td><?php echo $row['room']?></td>
		<td><?php echo $row['households'];?></td>
		<td><?php if(!empty($row['naverfloor_url'])) { ?><a href="<?php echo $row['naverfloor_url'];?>" target="_blank">보기</a><?php } ?></td>
	</tr>
<?php
		$housholds += $row['households'];
	}
}
?>
	<tr>
		<td>합계</td>
		<td colspan="2">총 <?php echo $i?>종</td>
		<td colspan="3"><span id='kbpricedate'></span></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><?php echo number_format($housholds);?>세대</td>
		<td>&nbsp;</td>
	</tr>
</table>
<div id="kbprice_reload" style="display:none;">
	<iframe id="kbprice_recall" name="kbprice_recall"></iframe>
</div>

<?php 
if($data['lat'] && $data['lng']) {
?>
<p><a href="https://map.kakao.com/link/map/<?php echo $data['lat'];?>,<?php echo $data['lng'];?>" target="_blank">카카오 지도로 보기(새창)</a></p>
<div id="map" style="width:100%;height:500px;"></div>
<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=4c8d1191d562bbe25a3709eae3a55f7a"></script>
<script>
	var container = document.getElementById('map');
	var options = {
		center: new kakao.maps.LatLng(<?php echo $data['lat'];?>, <?php echo $data['lng'];?>),
		level: 3
	};

	var map = new kakao.maps.Map(container, options);
		
	// 마커가 표시될 위치입니다 
	var markerPosition  = new kakao.maps.LatLng(<?php echo $data['lat'];?>, <?php echo $data['lng'];?>); 

	// 마커를 생성합니다
	var marker = new kakao.maps.Marker({
		position: markerPosition
	});

	// 마커가 지도 위에 표시되도록 설정합니다
	marker.setMap(map);

	// 일반 지도와 스카이뷰로 지도 타입을 전환할 수 있는 지도타입 컨트롤을 생성합니다
	var mapTypeControl = new kakao.maps.MapTypeControl();

	// 지도에 컨트롤을 추가해야 지도위에 표시됩니다
	// kakao.maps.ControlPosition은 컨트롤이 표시될 위치를 정의하는데 TOPRIGHT는 오른쪽 위를 의미합니다
	map.addControl(mapTypeControl, kakao.maps.ControlPosition.TOPRIGHT);

	// 지도 확대 축소를 제어할 수 있는  줌 컨트롤을 생성합니다
	var zoomControl = new kakao.maps.ZoomControl();
	map.addControl(zoomControl, kakao.maps.ControlPosition.RIGHT);	
	
</script>
<?php
}
?>

<script>

$(function () {
		
    commonjs.selectNav("navbar", "kbland_danzi");

	$.ajax({
		type: 'post',
		url: '/api/get_kbland_detail_kbprice.php',
		data: {kbno:'<?php echo $kbno;?>'},
		dataType: 'json',
		success:function (data) {
			//console.log(data);
			var items = data.data;
			if(items.length !== 0) {
				if(data.kbpricedate) {
					$('#kbpricedate').append("시세조회일 : " + data.kbpricedate);
				}
				$.each(items, function(k, v) {
					if(v.is_kbprice == 0) {
						$(".basic-"+k).append('비대상');
						$(".high-"+k).append('-');
						$(".low-"+k).append('-');
					} else {
						$(".basic-"+k).append(v.kbprice_basic);
						$(".high-"+k).append(v.kbprice_high);
						$(".low-"+k).append(v.kbprice_low);
					}
					//console.log(k);
				});
			}
			//$('#kbpricedate').append("&nbsp; <a href='./wget_kbland_price.php?kbno=<?php echo $kbno;?>' target='_blank'>[KB시세갱신]</a> ");
			$('#kbpricedate').append("&nbsp; <a id='kbprice_recall_btn' style='cursor:pointer;'>[KB시세갱신]</a> ");
					
			$("#kbprice_recall_btn").on("click",function() {
				console.log("recall");
				$("#kbprice_reload").css("display","block");
				$("#kbprice_recall").css("width","100%");
				$("#kbprice_recall").css("height","200px");
				//$("#kbprice_recall").attr("src","./wget_kbland_price.php?kbno=<?php echo $kbno;?>");
				$("#kbprice_reload").load("./wget_kbland_price.php?kbno=<?php echo $kbno;?>", function() {
					alert("시세데이터가 갱신되었습니다. 페이지가 새로고침 됩니다.");
					location.reload();
				});
			});
			
		}
	});
	
})


	
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>

