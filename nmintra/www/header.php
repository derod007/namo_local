<?php
ini_set("display_errors", 0);
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

$ip_addr = $_SERVER["REMOTE_ADDR"];
if($pathinfo['dirname'] != '/' && $pathinfo['dirname'] != '/app/module' && $pathinfo['dirname'] != '/api') {
	
	if(!in_array($ip_addr, $allow_ips)) {

		session_unset(); // 모든 세션변수를 언레지스터 시켜줌
		session_destroy(); // 세션해제함
		
		alert('로그인후 이용해주세요. RETURN ', '/');
	}
	
}

$pathinfo = pathinfo($_SERVER["PHP_SELF"]);
if(!$member['mb_id'] && $pathinfo['dirname'] != '/' && $pathinfo['dirname'] != '/app/module' && $pathinfo['dirname'] != '/api') {
	alert('로그인후 이용해주세요.', '/');
}
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NAMO funding - Work Support System</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	 <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
	 <link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css" />
    <link rel="stylesheet" href="/assets/css/select2-bootstrap.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/iamks-basic.css">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?php echo JS_VERSION; ?>">
	
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
	
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
   <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>	
   <script src="/assets/js/main.js?v=<?php echo JS_VERSION; ?>"></script>
   
    <!-- Datatables File Export JavaScript -->
   <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
   <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
   <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
   <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
   <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
   <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
   <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
   
</head>

<body>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div id="nav_container" class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false"
                    aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">NAMO Funding</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">업무지원<span
									class="caret"></span></a>
							<ul class="dropdown-menu">
								<li id="publicofficial"><a href="/app/p2p/publicofficial.php">공직자윤리위원회</a></li>
							</ul>
						</li>
						
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">등기부등본조회<span class="caret"></span></a>
                        <ul class="dropdown-menu">
								 <li id="iros_saved_list"><a href="/app/tilko/iros_saved_list.php">등기물건주소 저장목록</a></li>
								 <li id="iros_register"><a href="/app/tilko/iros_register.php">등기물건주소 조회</a></li>
								 <li id="iros_risuretrieve_history"><a href="/app/tilko/iros_risuretrieve_history.php">등기부등본 조회목록</a></li>
								 <li id="iros_revtwelcomeevtc"><a href="/app/tilko/iros_revtwelcomeevtc.php">등기신청사건 조회</a></li>
								<li role="separator" class="divider"></li>
								 <li id="iros_managed_list"><a href="/app/tilko/iros_managed_list.php">등기관리목록</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">KB시세<span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li id="kbland_dong"><a href="/app/kbapt/kbland_dong.php">법정동목록</a></li>
								<li id="kbland_danzi"><a href="/app/kbapt/kbland_danzi.php">KB아파트 단지 목록</a></li>
								<li role="separator" class="divider"></li>
								<li id="kbland_statics"><a href="/app/kbapt/kbland_statics.php">KB데이터 현황</a></li>
								<?php
									if($member['mb_id'] == "admin") {
								?>
								<li id="kbland_crawl"><a href="/app/kbapt/kbland_crawl_history.php"><i class="fas fa-user-cog"></i> 크롤링현황</a></li>
								<?php 
									} 
								?>
								<!--
								<li id="kbapt_3"><a href="#">#</a></li>
								<li id="kbapt_4"><a href="#">#</a></li>
								<li role="separator" class="divider"></li>
								-->
							</ul>
						</li>						
                    <li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">기타<span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li id="pdsupload"><a href="#">작업중</a></li>
								<?php 
									if($member['mb_id'] == "admin") {
								?>
									<li><a href="/app/editor/private.php?code=private">개인정보처리방침</a></li>
									<li><a href="/app/editor/private.php?code=infocredit">신용정보활용체제</a></li>
									<li><a href="/app/editor/private.php?code=private_sum">개인(신용)정보수집동의(요약)</a></li>
									<li><a href="/app/editor/private.php?code=private_3rd">개인(신용)정보 제3자 제공 동의</a></li>
									<li><a href="/app/editor/private.php?code=unique_sum">고유식별정보 처리동의</a></li>
									<li><a href="/app/editor/private.php?code=marketing_sum">마케팅목적 개인(신용)정보 이용 동의</a></li>
									<li><a href="/app/editor/private.php?code=notice">공지사항</a></li>									
										
								<?php 
									}
								?>
								
							</ul>
						</li>
                </ul>
				
                <ul class="nav navbar-nav navbar-right">
                   
				<?php if($member['mb_id']) { ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $member['mb_name']." 님"; ?>
							<span class="caret"></span></a>
                        <ul class="dropdown-menu">
									<!--
                            <li role="separator" class="divider"></li>
                            <li id="btnToggleContainer"><a href="#">VIEW MODE</a></li>
							-->
                            <li role="separator" class="divider"></li>
                            <li><a href="/app/module/logout.php">LogOut</a></li>
                        </ul>
                    </li>
				<?php } ?>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </nav>
    <div id="wrap-content" class="container">
