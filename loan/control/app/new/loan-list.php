<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// http://apitest.ddiablo.net/app/apt_list.php
include_once '../../header.php';

$chk_pt_idx_json = "[]";
$chk_pt_idx = array();
if(isset($_GET['pt_idx'])) {
	$pt_idx = $_GET['pt_idx'];
	
	if(count($pt_idx) > 0) {
		foreach($pt_idx as $v) {
			$chk_pt_idx[] = $v; 
		}
		$chk_pt_idx_json = json_encode($chk_pt_idx, JSON_NUMERIC_CHECK);
		set_session("ss_pt_idx", implode(",",$chk_pt_idx));
	}
} else {
	$ss_pt_idx = get_session("ss_pt_idx");
	if($ss_pt_idx) {
		//echo "ss_pt_idx : ".$ss_pt_idx;
		$chk_pt_idx = explode(",",$ss_pt_idx);
		$chk_pt_idx_json = json_encode($chk_pt_idx, JSON_NUMERIC_CHECK);
	}
}

if(isset($_GET['chk_reset'])) {
	if($_GET['chk_reset'] == '1') {
		set_session("ss_pt_idx", '');
		$chk_pt_idx_json = "[]";
	}
}

?>
<style>
.ui-widget {
    font-family: font-family: "Pretendard Variable", Pretendard, -apple-system, BlinkMacSystemFont, system-ui, Roboto, "Helvetica Neue", "Segoe UI", "Apple SD Gothic Neo", "Noto Sans KR", "Malgun Gothic", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", sans-serif;
}
.info_window {display:none;}
.info_window p {margin:0; font-size:0.9em;}
.btn_infowin {cursor:pointer;}

.winreal { color: #337ab7;cursor:pointer; }

.ui-widget-content a {
    color: #337ab7;
}
.ptlist {
	display: flex; flex-wrap: wrap; margin: 0; padding: 0; list-style: none; margin-bottom:10px;
}
.ptlist li {
	display:inline-block;
	position: relative;
	letter-spacing: 1px;
   font-size: 14px;
   padding:0 5px;
   word-break: keep-all;
}

.w380el {
  display: block;
  min-width:300px;
  max-width:380px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

#datalist .fa-balance-scale { font-size:1.4em; }
#autojudgeModalPopup td { font-size:0.8em; }

.modal-dialog {
    position: relative;
    width: auto;
    margin: 10px;
}

@media (max-width: 1400px) {
	.container {
		width: 98%;
		max-width: 1400px;
		padding-right: 5px;
		padding-left: 5px;
	}
}

</style>

<!-- CONTENT START -->
<div class="page-header">
<div class="btn-div">
	<a class="btn btn-sm btn-default max-768-toggle"><i class="fas fa-filter"></i> Filter</a>
	<a class="btn btn-success btn-sm" href="./loan-apt.php">아파트 등록</a>
	<a class="btn btn-warning btn-sm" href="./loan-write.php">빌라/토지 등록</a></div>
	<h1>대출신청 목록 <a class="btn btn-default btn-sm" href="./loan-list.php"><i class="fas fa-sync-alt"></i></a></h1>
</div>

<?php
// 진행중 목록
include_once './inc_loanprocess.php';
?>

<div class="search-box">
	<form name="fsearch" id="fsearch" method="get">
	<input type="hidden" name="chk_reset" value="">
		<div class="row">
			<div class="col-sm-12">
				<label>등록업체 설정 <button class="btn btn-primary btn-xs" id="pt_reset" type="button">초기화</button></label>
				<ul class="ptlist">
				<?php
				    $sql = " select * from partner_member where idx !='1' and mb_display='1' and is_sub='0' order by idx asc ";	// 
					$result = sql_query($sql);
					for ($i=0; $row=sql_fetch_array($result); $i++) {
						$row['mb_use_txt'] = ($row['mb_use']!='1')?"(중지)":"";
						if(in_array($row['idx'], $chk_pt_idx)) {
							echo "<li><input type='checkbox' name='pt_idx[]' value='".$row['idx']."' checked> ".$row['mb_bizname'].$row['mb_use_txt']."</li>";
						} else {
							echo "<li><input type='checkbox' name='pt_idx[]' value='".$row['idx']."'> ".$row['mb_bizname'].$row['mb_use_txt']."</li>";
						}
					}
				?>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2">
				<label>진행상태</label>
				<select id="status" name="status" class="form-control">
					<?php
					echo option_selected('', $_GET['status'], '전체');
					foreach($status_arr as $k => $v) {
						echo option_selected($k, $_GET['status'], $v);
					}
					?>
				</select>
			</div>
			<div class="col-sm-4">
				<label>검색어</label>
				<input type="text" id="searchtxt" name="searchtxt" class="form-control" value="<?php echo $searchtxt ?? '';?>" placeholder="제목, 담보주소">
			</div>
			<div class="col-sm-2">
				<label>검토메모 검색</label>
				<input type="text" id="searchmemo" name="searchmemo" class="form-control" value="<?php echo $searchmemo ?? '';?>" placeholder="검토메모">
			</div>
			<div class="col-sm-2">
				<label>일자기준</label>
				<input type="text" id="regdate" name="regdate" class="form-control datepicker" value="<?php echo $regdate ?? '';?>" placeholder="일자검색">
			</div>
			<div class="col-sm-1">
				<label class="hidden-xs" style="width:100%">&nbsp;</label>
				<button class="btn btn-primary btn-block" id="search" type="button">검색</button>
			</div>
			<div class="col-sm-1">
				<label class="hidden-xs" style="width: 100%;">&nbsp;</label>
				<button class="btn btn-success btn-block" id="exceldown" type="button">EXCEL</button>
			</div>
			
		</div>
	</form>
</div>

<table id="datalist" class="table table-striped table-bordered jsb-table1 nowrap" style="width:100%"></table>

<iframe id="hiddenframe" style="display:none;"></iframe>

<div id="autojudgeModalPopup"></div>

<script>
    function autojudgeModalPopup(jdid){
		$.ajax({
			url: "./modal.autojudge.php",
			data: { jd_id : jdid },
			dataType: "html",
			success: function(data) {
				//console.log(data);	// .load(data)
				//$('#autojudgeModalPopup').html(data);
				$("#autojudgeModalPopup").html(data).dialog({
				    title: "자동심사 결과",
					height: "auto",
					width: 450,
					modal:true,
					open: function() {
						$('.ui-widget-overlay').off('click');
						$('.ui-widget-overlay').on('click', function() {
							$("#autojudgeModalPopup").dialog('close');
						})
					}
				}).dialog('open');
			}
		});
    }
</script>


<script>
$(function () {
	commonjs.selectNav("navbar", "newloan");
		
	$(".datepicker").datepicker();
	$("#pt_idx").select2();
	
    var dataTable = $('#datalist').DataTable({
        paging: true,
        searching: false,
        scrollX : true,
        ordering: false,
        "order": [
            [0, 'desc']
        ],
        orderable: false,
        info: true,
        "bProcessing": true,
        "serverSide": true,
        columns: [{
                data: 'no',
                title: 'No',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'wdate',
                title: '등록일',
                className : 'align-center',	
				orderable: false
            },
            {
                data: 'mb_bizname',
                title: '등록업체',
                className : 'align-center font-w600',					
				orderable: false
            },
            //{ data: 'tax_id' },
            {
                data: 'wr_ca',
                title: '구분',
                className : 'align-center',
				orderable: false
            },
			{
                data: 'wr_subject',
                title: '제목',
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					// var btn = $("<a href='./loan-write.php?w=u&wr_id=" + rowData.wr_id + "'>" + rowData.wr_subject + "</a>");
					if(rowData.wr_subject == ''){
                        rowData.wr_subject = "임시 저장 파일입니다";
                    }
                    var mb_id = "<?php echo $member['mb_id']?>"
					var btn = $("<a href='./loan-write.php?w=u&wr_id=" + rowData.wr_id + "'>" + rowData.wr_subject + "</a>");
                    
					$(cell).append(btn);
				}
            },
            // {
            //     data: 'wr_subject',
            //     title: '제목',
			// 	orderable: false,
			// 	"render": function (data, type, row, meta) {
			// 		return '';
			// 	},
			// 	createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
            //         if (rowData.wr_subject == '') {
            //             rowData.wr_subject = "임시 저장 파일입니다";
            //         }

            //         var mb_id = "<?php echo $member['mb_id']?>"

            //         $.ajax({
            //             url: '/app/real/get_realprice4.php',
            //             type: "post",
            //             data: {
            //                 addr1: rowData.address,
            //                 py: rowData.wr_m2
            //             },
            //             contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            //             dataType: "text",
            //             success: function (data) {
            //                 let json = $.parseJSON(data);
            //                 let admit = '';
			// 				// console.log(rowData);

            //                 if (json.data.ave_price) {
            //                     var ave_price = json.data.ave_price * 10000;
								
			// 					// wr_amount = 희망금액, wr_auto_price = 자동한도금액
			// 					// ave_price = 평균시세
			// 					// var wr_amount = parseFloat(rowData.wr_amount) || 0;
            //                     // var wr_auto_price = parseFloat(rowData.wr_auto_price) || 0;
			// 					var wr_amount = rowData.wr_amount;
            //                     var wr_auto_price = rowData.wr_auto_price;
								
			// 					if (wr_amount && ave_price) {
			// 						if (wr_amount <= wr_auto_price || (typeof wr_auto_price === 'string' && wr_amount.includes('최대요청'))) {
			// 							admit = "<span style='color:blue'> 임시 승인</span>";
			// 						} else {
			// 							admit = "<span style='color:blue'> 임시 승인 (금액차)</span>";
			// 						}
			// 						if(wr_auto_price < 0 ){
			// 							admit = "<del style='color:red'> 부결</span>";
			// 						}
			// 					}else{
			// 						admit = "<span style='color:red'> 계산 불가</span>";
			// 					}
            //                 }else{
			// 					admit = "<span style='color:red'> 계산 불가</span>";
			// 				}

            //                 // 최종 버튼 만들기
            //                 var btn;
            //                 if (mb_id == 'testuser') {
            //                     btn = $("<a href='./test.php?w=u&wr_id=" + rowData.wr_id + "'>" + rowData.wr_subject + "</a>" + admit);
            //                 } else {
            //                     btn = $("<a href='./loan-write.php?w=u&wr_id=" + rowData.wr_id + "'>" + rowData.wr_subject + "</a>");
            //                 }

            //                 $(cell).append(btn);
            //             }
            //         });
            //     }
            // },
            {
                data: 'duppop',
                title: '중복',
                className : 'align-center',
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $(rowData.duppop);
					$(cell).append(btn);
				}
            },
            {
                data: 'address',
                title: '담보주소',
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					if(rowData.wr_ca == '아파트' || rowData.wr_ca == '아파트[지분]') {
						var btn =  $('<div>' + rowData.address + ' &nbsp;&nbsp; <span class="winreal" onclick="win_real(\'' + rowData.address + '\',\'' + rowData.wr_m2 + '\');"><i class="fas fa-chart-bar"></i></a></div> ');
					} else {
						var btn =  $('<div>' + rowData.address + ' &nbsp;&nbsp; </div> ');
					}
					$(cell).append(btn);
				}
            },			
            {
                data: 'address2',
                title: '상세주소',
				orderable: false,
				width: '200',
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn =  $("<div class='w380el'>" + rowData.address2 + "</div>");
					$(cell).append(btn);
				}
            },
            {
                data: 'filecnt',
                title: '첨부',
                className : 'align-center',
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<a href='./loan-file.php?wr_id=" + rowData.wr_id + "'>" + rowData.filecnt + "</a>");
					$(cell).append(btn);
				}
            },
            {
                data: 'status',
                title: '진행상태',
                className : 'align-center',	
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					if(rowData.status == '부결') {
						var btn = $("<span class='red'>" + rowData.status + "</span>");
					} else if (rowData.status == '진행취소') {
						var btn = $("<span class='red'><B>" + rowData.status + "</B></span>");
					} else if (rowData.status == '가승인') {
						var btn = $("<span class='blue'>" + rowData.status + "</span>");
					} else if (rowData.status == '진행요청') {
						var btn = $("<span class='magenta'><B>" + rowData.status + "</B></span>");
					} else if (rowData.status == '대출실행') {
						var btn = $("<span class='green'><B>" + rowData.status + "</B></span>");
					} else {
						var btn = $("<span>" + rowData.status + "</span>");
					}
					$(cell).append(btn);
				}
				
            },
            {
                data: 'jd_autoid',
                title: '자동심사',
                className : 'align-center',
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					//if(rowData.jd_autoid !='0' && rowData.jd_autoid !=null) {
					if(rowData.jd_autoid > 0) {	
						var btn = $("<a href='javascript:autojudgeModalPopup(" + rowData.jd_autoid + ");' data-jdid='" + rowData.jd_autoid + "'><i class='fas fa-balance-scale'></i></a>");
						$(cell).append(btn);
					}
				}
				
            },
            {
                data: 'jd_amount',
                title: '한도(만원)',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'jd_interest',
                title: '금리(%)',
                className : 'align-center',
				orderable: false
            },
            {
                data: 'jd_condition',
                title: '부대조건',
                className : 'align-left',
				orderable: false,
				width: '200',
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn =  $("<div class='w380el'>" + rowData.jd_condition + "</div>");
					$(cell).append(btn);
				}
            },
            {
                data: 'wr_id',
                title: '기능',
                className : 'align-center',	
				orderable: false,
				"render": function (data, type, row, meta) {
					return '';
				},
				createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
					var btn = $("<a href='./loan-write.php?w=u&wr_id=" + rowData.wr_id + "'>수정</a>");
					$(cell).append(btn);
				}
            },
        ],
        // AJAX
        ajax: function (data, callback, settings) {
            // data = dt가 만들어주는 원래 파라미터 원래 날아오는 부분
            //console.log(data);
            // 새로 params를 정의 재정의
            var params = {};

            params.start = data.start;
            params.length = data.length;
            params.searchtxt = $('#searchtxt').val();
            params.searchmemo = $('#searchmemo').val();
            params.status = $('#status').val();
            params.pt_idx = <?php echo $chk_pt_idx_json;?>;
            params.regdate = $('#regdate').val();

            // sorting 관련
            if (data.order && data.order.length > 0) {
                var order = data.order[0];

                // 컬럼에 지정한 속성, sortName을 별도로 지정할 경우 해당 지정한 필드로 연결
                params.sortName = data.columns[order.column].data;
                params.sortASC = order.dir === 'asc' ? true : false;

            }

            return $.ajax({
                url: '/api/v2/list_loaninfo.php',
                type: "post",
                data: params,
                dataType: "json",
                success: function (json) {

                    var result = {
                        draw: data.draw,
                        data: json.data,
                        recordsFiltered: json.recordsFiltered,
                        recordsTotal: json.recordsTotal
                    };
					$('#total_count').html(json.total);
					//$('#total_dist').html(json.total.dist_cnt);

                    callback(result);
                },error: function (xhr, status, error) {
					console.log('AJAX Error:',xhr, status, error); // AJAX 요청 오류를 콘솔에 출력
				}
            });
        }
		
    });
	
});

// 중복 클릭시 해당 윈도우 보이기
$(document).ready(function () {
    var table = $('#datalist').DataTable();
 
    $('#datalist tbody').on('click', '.btn_infowin', function () {
		//console.log( this);
		var winsn = $(this).data("winsn");
		//$("#"+winsn).toggle();
		$("#"+winsn).dialog({
			  title: "검색결과 최대 6개",
			  resizable: false,
			  height: "auto",
			  width: 450,
			  modal: true,
  			  open: function() {
				$('.ui-widget-overlay').off('click');
				$('.ui-widget-overlay').on('click', function() {
					$("#"+winsn).dialog('close');
				})
			}
		});
    });
});

    $('#search').on('click', function (event) {
		$('#fsearch').submit();
    });
	
    $('#pt_reset').on('click', function (event) {
		document.fsearch.chk_reset.value = "1";
		$('#fsearch').submit();
    });
	
	
    //////////////////////////////////////////////////////////////////////////////////////
	
	$('#exceldown').on('click', function (event) {
		var params = $("#fsearch").serialize();		
        var excelform = $('<form id="fexceldown"></form>');
        excelform.attr('action', '/api/v2/excel_loaninfo.php?'+params);
        excelform.attr('method', 'post');
		excelform.attr('target', 'hiddenframe');
		excelform.appendTo('body');
        excelform.submit();
		$('#fexceldown').remove();
    });
	
function win_real(addr1, py) {
	var url = '/app/real/newwin_real.php?addr1=' + addr1 + '&py=' + py;
	window.open(url, 'newwin_real', 'scrollbars=yes,width=650,height=600,top=10,left=100');
}
	
</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>