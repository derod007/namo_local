<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/inc/common.php');

// IP 접속제한
/*
$ip_addr = $_SERVER["REMOTE_ADDR"];
if($pathinfo['dirname'] != '/' && $pathinfo['dirname'] != '/app/module' && $pathinfo['dirname'] != '/api') {
	if(!in_array($ip_addr, $allow_ips)) {

		session_unset(); // 모든 세션변수를 언레지스터 시켜줌
		session_destroy(); // 세션해제함
		
		alert('로그인후 이용해주세요. RETURN ', '/');
	}
}
*/

$pathinfo = pathinfo($_SERVER["PHP_SELF"]);
if(!$member['mb_id'] && $pathinfo['dirname'] != '/' && $pathinfo['dirname'] != '/app/module' && $pathinfo['dirname'] != '/api') {
	//print_r($member);
	alert('로그인후 이용해주세요.', '/');
}
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NAMO - 대출정보 전산시스템</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	 <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
	 <link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css" />
    <link rel="stylesheet" href="/assets/css/select2-bootstrap.css">
	 <link rel="stylesheet" as="style" crossorigin href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.6/dist/web/static/pretendard.css" />
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
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">NAMO Loan Partner</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">대출정보<span	class="caret"></span></a>
							<ul class="dropdown-menu">
								<li id="loaninfo"><a href="/app/loan/loan-list.php">대출신청 목록</a></li>
								<!-- li id="loanaccept"><a href="/app/loanaccept-list.php">대출승인 목록</a></li -->
							</ul>
						</li>
                    <li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">공지사항<span	class="caret"></span></a>
							<ul class="dropdown-menu">
								<li id="loaninfo"><a href="/app/bbs/notice-list.php">공지사항</a></li>
							</ul>
						</li>
						
                </ul>
				
                <ul class="nav navbar-nav navbar-right">
                   
				<?php if($member['mb_id']) { ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $member['mb_name']." 님"; ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li id="mypage_info"><a href="/app/mypage/myinfo.php">사용자 정보</a></li>
                            <li id="password_change"><a href="/app/mypage/password-edit.php">비밀번호 변경</a></li>
                            <li role="separator" class="divider"></li>
							<!--
                            <li id="btnToggleContainer"><a href="#">VIEW MODE</a></li>
                            <li role="separator" class="divider"></li>
							-->
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
