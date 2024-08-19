<?php
$partners = get_partnerlist();

$sql = "select count(*) as cnt from loan_write where wr_status = '30'";
$prcnt = sql_fetch($sql);

$sql = " select *, (select reg_date from log_action where wr_id = loan_write.wr_id and next_status='30' limit 1 ) as procdate from loan_write where wr_status = '30' order by procdate desc limit 5 ";
$result = sql_query($sql);
$data = array();
$i = 0;
while($row=sql_fetch_array($result)) {
	$data[] = $row;
	$i++;
}
if($i > 0) {
?>
<h4>진행요청 <a href="./loanproc-list.php" target="_blank">(전체 <?php echo $prcnt['cnt']?> 건)</a></h4>
<table id="datalist2" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%">
  <thead>
    <tr role="row">
      <th class="align-center">요청일시</th>
      <th class="align-center">진행상태</th>
      <th class="align-center">등록일시</th>
      <th class="align-center">등록업체</th>
      <th class="align-center">제목</th>
      <th class="align-center">담보주소</th>
      <th class="align-center">한도</th>
      <th class="align-center">금리</th>
      <th class="align-center">부대조건</th>
    </tr>
  </thead>
  <tbody>
<?php
	foreach($data as $k => $v) {
?>  
    <tr role="row">
      <td class=" align-center"><?php echo substr($v['procdate'],5,11);?></td>
      <td class="align-center"><span class='magenta'><B><?php echo $status_arr[$v['wr_status']];?></B></span></td>
      <td class=" align-center"><?php echo substr($v['wr_datetime'],5,11);?></td>
      <td class="align-center"><?php echo $partners[$v['pt_idx']]['mb_bizname'];?></td>
      <td class="align-center"><a href="./loan-write.php?w=u&wr_id=<?php echo $v['wr_id'];?>"><?php echo $v['wr_subject'];?></a></td>
      <td><?php echo $v['wr_addr1']." ".$v['wr_addr3']." ".$v['wr_addr2'];?></td>
      <td><?php echo $v['jd_amount'];?></td>
      <td><?php echo $v['jd_interest'];?></td>
      <td><?php echo $v['jd_condition'];?></td>
    </tr>
<?php
	}
?>	
	</tbody>
</table>
<?php
}
?>
