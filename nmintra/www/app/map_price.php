<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/map_price.php
include_once '../header.php';


?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>부동산가격정보(Map)</h1>
</div>

<div class="search-box max-768-target">
	<form name="fsearch" id="fsearch" method="get">
		<div class="row">
			<div class="col-sm-2">
				<label>조회년월(<?php echo date("Ym");?>)</label>
				<select id="yymm" name="yymm"  class="form-control">
					<option value="">선택</option>
				<?php
					$ym = date('Ym');
					$i = 0;
					while($i < 10) {
						if($i != 0) {
							$ym = date("Ym", strtotime("-$i month"));
						}
						echo option_selected($ym, $yymm, $ym);
						$i++;
					}
				?>	
				</select>
			</div>
			<div class="col-sm-5">
				<label>지역(시군구)</label>
				<?php echo get_regioncode_select("region", $region, ""); ?>
			</div>
			<div class="col-sm-2">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
		</div>
	</form>
</div>

<?php




?>

<script>
	$(function () {
		commonjs.selectNav("navbar", "actual_price");
    });

    $('#search').on('click', function (event) {
		$('#fsearch').submit();
    });

	$(document).ready( function () {
		
	} );
	
</script>

<!-- CONTENT END -->
<?php
include_once '../footer.php';
?>