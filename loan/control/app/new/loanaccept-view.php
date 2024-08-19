<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

$wr_id = $_GET['wr_id'];
if(!$wr_id) {
	alert("잘못된 접근입니다.");
}
$sql = "select * from loan_write where wr_id = '{$wr_id}' limit 1";
$row = sql_fetch($sql);

if(!$row['wr_id']) {
	alert('해당되는 데이터가 없습니다');
}
?>
<!-- CONTENT START -->

<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<!-- a class="btn btn-success btn-sm" href="">등록</a --></div>
	<h1>대출실행 상세</h1>
</div>

<!-- CONTENT START -->

<div style="padding:15px;margin:auto; max-width:1000px;">
	<form id="fwrite" name="fwrite" action="/app/loaninfo-act.php" method="post" class="jsb-form">
	 <input type="hidden" name="w" value="<?php echo $w; ?>">
	 <input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
	   <div class="form-group" >
	   
			<div class="row"><label class="col-sm-2 control-label">진행상태</label>
				<div class="col-sm-10"><span style="font-size:1.5em; color:#0033ff; font-weight:800; letter-spacing: 1px;"><?php echo $status_arr[$row['wr_status']]; ?></span>
				</div>
			</div>

			<div class="row" style="border:1px solid #ccc; padding-bottom:10px;"><label class="col-sm-2 control-label">심사결과</label>
				<div class="col-sm-10">
					<table class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%">
						<tr>
							<th class="col-sm-2 text-center">한도(만원)</th>
							<th class="col-sm-2 text-center">금리(%)</th>
							<th class="col-sm-8 text-center">부대조건</th>
						</tr>
						<tr>
							<td class="text-center"><?php echo ($row["jd_amount"])?number_format($row["jd_amount"])."만원":""; ?></td>
							<td class="text-center"><?php echo ($row["jd_interest"])?$row["jd_interest"]."%":""; ?></td>
							<td><?php echo $row["jd_condition"]; ?></td>
						</tr>
					</table>
					
					<div class="row"><label class="col-sm-2 control-label">차주명</label>
						<div class="col-sm-4"><span style="font-size:1.5em; color:#0033ff; font-weight:800; letter-spacing: 1px;"><?php echo $row["wr_name"]; ?></span></div>
						<label class="col-sm-1 control-label">연락처</label>
						<div class="col-sm-5"><span style="font-size:1.5em; color:#0033ff; font-weight:800; letter-spacing: 1px;"><?php echo $row["wr_tel"]; ?></span></div>
					</div>
					
				</div>
			</div>

			<div class="row"><label class="col-sm-2 control-label">담보구분</label>
			  <div class="col-sm-10 bs-padding10">
					<?php 
					
						switch($row['wr_ca']) {
							case "A" : $ca_view = "아파트"; break;
							case "B" : $ca_view = "빌라"; break;
							case "E" : $ca_view = "기타"; break;
							default : $ca_view = "기타"; 
						}

						echo "<h4>{$ca_view}</h4>";
					?>
			  </div>
			</div>
		  
			<div class="row"><label class="col-sm-2 control-label">제목</label>
				<div class="col-sm-10"><?php echo $row["wr_subject"]; ?></div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">대출자정보</label>
				<div class="col-sm-10"><?php echo nl2br($row["wr_cont1"]); ?></div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">담보주소</label>
				<div class="col-sm-10">
					<?php echo $row["wr_addr1"]; ?><br/>
					<?php echo $row["wr_addr3"]; ?><br/>
					<?php echo $row["wr_addr2"]; ?><br/>
					추가정보 : <?php echo $row["wr_addr_ext1"]; ?>
				</div>
			</div>
			<div class="row"><label class="col-sm-2 control-label">전용면적</label>
				<div class="col-sm-10"><?php echo $row["wr_m2"]; ?> ㎡ (제곱미터)</div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">담보정보</label>
				<div class="col-sm-10"><?php echo nl2br($row["wr_cont2"]); ?></div>
			</div>
			
			<div class="row"><label class="col-sm-2 control-label">희망금액(만원)</label>
				<div class="col-sm-10"><?php echo $row["wr_amount"]; ?></div>
			</div>
			
			<div class="row"><hr/></div>
			
			<div class="row"><label class="col-sm-2 control-label">참고링크#1<br/>(KB시세 URL)</label>
				<div class="col-sm-10">
					<input type="text" id="wr_link1" name="wr_link1" value="<?php echo $row["wr_link1"]; ?>" class="form-control" placeholder="https://링크URL">
					<input type="text" id="wr_link1_subj" name="wr_link1_subj" value="<?php echo $row["wr_link1_subj"]; ?>" class="form-control" placeholder="링크제목">
<?php
if(!empty($row["wr_link1"])) {
	if(!empty(trim($row["wr_link1_subj"]))) {
		echo "<div><a href='{$row['wr_link1']}' target='_blank'>".$row["wr_link1_subj"]."</a></div>";
	} else {
		echo "<div><a href='{$row['wr_link1']}' target='_blank'>새창링크</a></div>";
	}	
}
?>					
				</div>
			</div>
<?php
if(!empty($row["wr_link2"])) {
?>
			<div class="row"><label class="col-sm-2 control-label">참고링크#2<br/>(추가정보 URL)</label>
				<div class="col-sm-10">
					<input type="text" id="wr_link2" name="wr_link2" value="<?php echo $row["wr_link2"]; ?>" class="form-control" placeholder="https://링크URL">
					<input type="text" id="wr_link2_subj" name="wr_link2_subj" value="<?php echo $row["wr_link2_subj"]; ?>" class="form-control" placeholder="링크제목">
<?php
if(!empty($row["wr_link2"])) {
	if(!empty(trim($row["wr_link2_subj"]))) {
		echo "<div><a href='{$row['wr_link2']}' target='_blank'>".$row["wr_link2_subj"]."</a></div>";
	} else {
		echo "<div><a href='{$row['wr_link2']}' target='_blank'>새창링크</a></div>";
	}	
}
?>	
				</div>
			</div>
<?php
}
?>
		</div>
			
<?php
$pjfile = get_writefile($wr_id);
$filecnt = number_format($pjfile['count']);
?>
			<div class="row"><label class="col-sm-2 control-label">첨부파일 <?php echo "(".$filecnt .")";?></label>
				<div class="col-sm-10">
		<?php
			$cnt = $pjfile['count'];
			if ($cnt) {
				?>
				<!-- 첨부파일 시작 { -->
				<div id="project_v_file">
					<table class="table">
					<?php // 가변 파일
						//print_r2($pjfile);
					foreach ($pjfile as $i => $file) {
						if (isset($file['source']) && $file['source']) {
					?>
						<tr style="border-bottom: 1px solid #ddd">
							<td style="padding-left: 10px;padding-right:10px;">
								<?php echo "[" . $file['category'] . "] "; ?>
							</td>
							<td style="padding-left: 10px;padding-right:10px;">
								<a href="<?php echo $file['href']; ?>" class="view_file_download">
									<strong>
										<?php echo $file['source']; ?></strong>
									( <?php echo $file['size']; ?> ) <i class="fa fa-download" aria-hidden="true"></i></a>
									<?php echo $file['memo']; ?>	</td>
							<td style="padding-left: 10px;padding-right:10px;"><span class="project_v_file_date">
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

			<div class="row"><hr/></div>
			
		<div class="row">
			<div class="col-sm-4"><button class="btn btn-default btn-block col-sm-4" type="button" onclick="history.back();">돌아가기</button></div>
		</div>
    </form>

</div>

<script>
$(function () {
	commonjs.selectNav("navbar", "newloanaccept");
	
	var status = '<?php echo $row['wr_status'];?>';
	if(status > '1') {
		$('input:radio:not(:checked)').attr('disabled', 'disabled');
		$('input').attr('readonly', 'readonly');
		$('textarea').attr('readonly', 'readonly');
		$('.btn-warning').attr('disabled', 'disabled');
	}
	
	if(status == '10') {
		$('#wr_name').removeAttr('readonly');
		$('#wr_tel').removeAttr('readonly');
	}
	
});

</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>