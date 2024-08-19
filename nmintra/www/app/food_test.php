<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>V Funding</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	 <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
	 <link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css" />
    <link rel="stylesheet" href="/assets/css/select2-bootstrap.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/iamks-basic.css">
    <link rel="stylesheet" href="/assets/css/style.css?v=20191024">
	
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
	
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
   <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>	
   <script src="/assets/js/main.js?v=20191024"></script>
   
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
                <a class="navbar-brand" href="/">V Funding</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">API TEST<span
                                class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li id="actual_price"><a href="/app/actual_price.php">국토부 실거래가</a></li>
                            <li id="apt_list"><a href="/app/apt_list.php">단지(APT)정보조회</a></li>
								<!--
								<li id="map_price"><a href="/app/map_price.php">부동산가격정보</a></li>
								<li role="separator" class="divider"></li>
								<li id="member_project"><a href="/app/member/project-list.php">참여 프로젝트</a></li>
								<li id="member_outsourcing"><a href="/app/member/outsourcing-list.php">외주작업 실적</a></li>
								<li id="member_vacation"><a href="/app/member/vacation-view.php">연차사용내역</a></li>
								-->
                        </ul>
                    </li>
                    <!-- li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">프로젝트<span
									class="caret"></span></a>
							<ul class="dropdown-menu">
								<li id="project_list"><a href="/app/manager/project-list.php">프로젝트 관리</a></li>
								<li role="separator" class="divider"></li>
								<li id="customer_list"><a href="/app/manager/customer-list.php">거래처 관리</a></li>
								<li id="outsourcing_list"><a href="/app/manager/outsourcing-list.php">외주작업 관리</a></li>
							</ul>
						</li -->
                </ul>
				
                <ul class="nav navbar-nav navbar-right">
                   
				                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </nav>
    <div id="wrap-content" class="container">

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

<h2>시락</h2><dl></dl>
<h2>일루</h2>
<div class='food'>
<dl class='menu'>
<dt class='detail'><img src='https://scontent-lhr3-1.cdninstagram.com/vp/190d214d31d841115a31d704264121e8/5E48DA81/t51.2885-15/e35/73457419_696268844196184_2515384437617871913_n.jpg?_nc_ht=scontent-lhr3-1.cdninstagram.com&_nc_cat=104&se=7&ig_cache_key=MjE2MTM0NTkyOTUzNDY3MTczMg%3D%3D.2' height='150'></dt>
<dd class='description'>illu 2019년 10월 24일 목요일 메뉴입니다.  가을연서  속절없이 흘러가는 야속한 세월  특별한 기억이 있는 것도 아닌데  문득  잊고 지낸 사람이 생각이 나고  보고싶어 지는 건  가을이기 때문입니다.  낙지볶음 🐙 깐풍육 고구마카레라이스  해물완자 비빔냉면 직접발라구운김  과일(제주감귤 🍊) 굴무생채 포기김치  돼지고기김치찌개등  ㅡ일루 퓨전한식뷔페ㅡ(테라타워2 g117호)  #일루한식뷔페#송파구맛집#문정동맛집#점심식사#한끼식사#맛있는집#테라타워#구내식당#sk타워#테라타워2#대명타워#정석#늘솜#시락#한식뷔페#뭐먹을까#오늘의메뉴#현금#카드#단체식사#단체환영#집밥#테라타워2#줄서는집#다이어트실패식당</dd>
</dl>
<dl class='menu'>
<dt class='detail'><img src='https://scontent-lhr3-1.cdninstagram.com/vp/644c84bd81f420a046f5a505d7d3c5b8/5E43471F/t51.2885-15/e35/72620504_512490522865296_8892927901236485845_n.jpg?_nc_ht=scontent-lhr3-1.cdninstagram.com&_nc_cat=100&se=7&ig_cache_key=MjE2MTMyODA1MzEwMDQ5NzQ1NQ%3D%3D.2' height='150'></dt>
<dd class='description'>illu 2019년 10월 23일 수요일 메뉴 손님상차림입니다.  식강,색감 모두 좋습니다. 메뉴판 앞까지 사진 가지고 계신분은 당첨되신 분들  이십니다. 축하드립니다. 오늘 오셔서 사진확인 하시고 무료식사 즐겨주세요.  ㅡ일루 퓨전한식뷔페ㅡ(테라타워2 g117호)  #일루한식뷔페#송파구맛집#문정동맛집#점심식사#한끼식사#맛있는집#테라타워#구내식당#sk타워#테라타워2#대명타워#정석#늘솜#시락#한식뷔페#뭐먹을까#오늘의메뉴#현금#카드#단체식사#단체환영#집밥#테라타워2#줄서는집#다이어트실패식당</dd>
</dl>
<dl class='menu'>
<dt class='detail'><img src='https://scontent-lhr3-1.cdninstagram.com/vp/ae8bf4cc3900b026909fccb0475b92f9/5E493C1C/t51.2885-15/e35/72476251_861922487538456_314536563376444700_n.jpg?_nc_ht=scontent-lhr3-1.cdninstagram.com&_nc_cat=100&se=7&ig_cache_key=MjE2MDcwMjI4NTA4MTI5Njg2NQ%3D%3D.2' height='150'></dt>
<dd class='description'>illu 2019년 10월 23일 메뉴입니다. &quot;무생채&quot; 가 &quot;취나물무침&quot;  으로 변경되었음을 알려드립니다.  ㅡ일루 퓨전한식뷔페ㅡ(테라타워2 g117호)  #일루한식뷔페#송파구맛집#문정동맛집#점심식사#한끼식사#맛있는집#테라타워#구내식당#sk타워#테라타워2#대명타워#정석#늘솜#시락#한식뷔페#뭐먹을까#오늘의메뉴#현금#카드#단체식사#단체환영#집밥#테라타워2#줄서는집#다이어트실패식당</dd>
</dl>
<dl class='menu'>
<dt class='detail'><img src='https://scontent-lhr3-1.cdninstagram.com/vp/ca4fce6082f02ef05ad941b0307a05e3/5E418783/t51.2885-15/e35/74670585_448907209065374_1253486541166353365_n.jpg?_nc_ht=scontent-lhr3-1.cdninstagram.com&_nc_cat=102&se=7&ig_cache_key=MjE2MDYyMTE1NTQ1NTk2NjY2OQ%3D%3D.2' height='150'></dt>
<dd class='description'>illu 2019년 10월 23일 수요일 메뉴입니다.  같이라는 단어의 소중함 함께라는 단어의 감사함. 손을 잡으면 손이 따스해지기 보다는  마음이 따뜻해져 오는 사람이 좋다.  앗싸! 라면데이 🍜 돼지고기주물럭 유자치킨 일루라면 실멸치볶음  고추장오뎅볶음 과일(홍로사과) 무생채  모듬야채쌈 쌈된장 포기김치  칼칼한 맑은홍합탕등  ㅡ일루 퓨전한식뷔페ㅡ(테라타워2 g117호)  #일루한식뷔페#송파구맛집#문정동맛집#점심식사#한끼식사#맛있는집#테라타워#구내식당#sk타워#테라타워2#대명타워#정석#늘솜#시락#한식뷔페#뭐먹을까#오늘의메뉴#현금#카드#단체식사#단체환영#집밥#테라타워2#줄서는집#다이어트실패식당</dd>
</dl>
</div>
<h2>정석</h2>
<dl></dl>

<script>
	$(function () {
		commonjs.selectNav("navbar", "");
    });
</script>

<!-- CONTENT END -->
      </div>
      <div class="bs-padding30"></div>
    </body>

</html>