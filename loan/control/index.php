<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/inc/common.php');

if (!empty($member['mb_id'])) {
    goto_url('/app/main.php');
}
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NAMO funding - Loan Manager System</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
        crossorigin="anonymous">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp"
        crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
	<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/iamks-basic.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="/sample/vendor/jquery/1.12.4/jquery.min.js"><\/script>')</script>
    <script src="/vendor/jquery-cookie/1.4.1/jquery.cookie.js"></script>

    <script src="/vendor/jquery-validation/1.17.0/jquery.validate.min.js"></script>
    <script src="/vendor/jquery-validation/1.17.0/additional-methods.min.js"></script>
    <script src="/vendor/jquery-validation/1.17.0/localization/messages_ko.min.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
	
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
        <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>	
    <script src="/assets/js/main.js"></script>
</head>

<body>

<style>
body {
  padding-top: 50px;
  padding-bottom: 40px;
  background-color: #f5f5f5;
}
.form-group {
    margin-bottom: 5px;
}
</style>

<!-- CONTENT START -->

<div style="max-width: 400px;padding:15px;margin:auto;">
	<form id="flogin" name="flogin" action="/app/module/login_check.php" method="post" class="jsb-form">
      <div class="align-center bs-padding20">
        <h2>NAMO Loan Manager</h2>
      </div>
      <div class="form-group">
        <input type="text" id="login_id" name="login_id" class="form-control" placeholder="Login ID" autofocus>
      </div>
      <div class="form-group">
        <input type="password" id="login_pw" name="login_pw" class="form-control" placeholder="Password">
      </div>
      <button class="btn btn-primary btn-block" type="submit">Sign in</button>
	  
      <p class="align-center bs-padding10">Copyright &copy; <strong>NAMO funding</strong> All rights reserved.</p>
    </form>
    </div>

<script>

$('#flogin').validate({
	rules: {
		login_id: { maxlength: 20, required: true },
		login_pw: { maxlength: 20, required: true },
	},
	submitHandler: function (form) {
		//console.log($(form).serialize());
		form.submit();
	}
});

</script>

<!-- CONTENT END -->
<?php
include_once './footer.php';
?>