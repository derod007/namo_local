<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/food.php
include_once '../header.php';
include_once "../inc/simple_html_dom.php";

$url = 'http://picpanzee.com/'; /*URL*/

$ids_array = array(
	array("id"=>"sirak5656", "title"=>"시락"),
	array("id"=>"illu_cafeteria", "title"=>"일루"),
	array("id"=>"jungsuk_cafeteria", "title"=>"정석"),
);

ob_start();

foreach($ids_array as $ids) {
	
	$target_url = $url.$ids['id'];
	
	
	// curl 리소스를 초기화 
	$ch = curl_init(); 
	
	// url을 설정 
	curl_setopt($ch, CURLOPT_URL, $target_url ); 
	
	// 헤더는 제외하고 content 만 받음 
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	
	// 응답 값을 브라우저에 표시하지 말고 값을 리턴 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	
	// 브라우저처럼 보이기 위해 user agent 사용 
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36'); 
	$content = curl_exec($ch); 
	
	// 리소스 해제를 위해 세션 연결 닫음 
	
	curl_close($ch); 

	
	if($content) {
		$html = str_get_html($content);

		//echo "<textarea style='width:90%; height:500px;'>".addslashes($html)."</textarea>";

		$ch_info = array();

		$ch_info['url'] = $target_url;

		$rc = $html->find('.grid-media');
		$ch_info['photos'] = array();
		$no = 0;
		foreach ($rc as $r) {
			
			//echo "<textarea style='width:90%; height:70px;'>".$r."</textarea>";
			$r2 = str_get_html($r);
			
			$a['img_url'] = $r2->find('.media-detail',0)->find('img',0)->attr['src'];
			$a['img_alt'] = $r2->find('.media-detail',0)->find('img',0)->attr['alt'];
			
			$ch_info['photos'][] = $a;
			$no++;
			if($no > 3) break;
		}
		
		echo "<h2>".$ids['title']."</h2>".$PHP_EOL;
		echo "<div class='food'>".$PHP_EOL;
		foreach($ch_info['photos'] as $r) {
			echo "<dl class='menu'>".$PHP_EOL;
				echo "<dt><img src='".$r['img_url']."'></dt>".$PHP_EOL;
				echo "<dd>".$r['img_alt']."</dd>".$PHP_EOL;
			echo "</dl>".$PHP_EOL;
		}
		echo "</div>".$PHP_EOL;
		//print_r2($ch_info);
	}
}

$output = ob_get_clean();

?>
<style>
h1, h2, h3 { clear:both; }
.food { width:100%; }
.menu { width:280px; float:left; padding:5px; border:1px solid #ff0000; }
.menu dt { }
.menu dt img { width:100%; height:auto;}
.menu dd { word-break: break-all; word-wrap:break-word; }
</style>
	
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>오늘의 메뉴</h1>
</div>

<?php
	// 화면출력
	echo $output;

?>

<script>
	$(function () {
		commonjs.selectNav("navbar", "");
    });
</script>

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>