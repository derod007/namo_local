<?php
include_once '../../header.php';
?>
<!-- CONTENT START -->

<div class="page-header">
  <h1>비밀번호 변경 <small></small></h1>
</div>

<div>
<form name="fchgpw" id="fchgpw" method="post" class="form-horizontal" action="./password-act.php">
    <div class="form-group"><label class="col-sm-3 control-label">기존 비밀번호</label>
        <div class="col-sm-5"><input type="password" id="old_password" name="old_password" required value="" class="form-control"></div>
    </div>
    <div class="form-group"><label class="col-sm-3 control-label">변경 비밀번호</label>
        <div class="col-sm-5"><input type="password" id="new_password" name="new_password" required value="" class="form-control"></div>
    </div>
    <div class="form-group"><label class="col-sm-3 control-label">변경 비밀번호 확인</label>
        <div class="col-sm-5"><input type="password" id="confirm_password" name="confirm_password" required value="" class="form-control"></div>
    </div>
    <div class="hr-line-dashed"></div>
    <div class="form-group">
        <div class="col-sm-12 col-sm-offset-4">
            <button class="btn btn-primary" type="submit">비밀번호 변경하기</button>
        </div>
    </div>
</form>
<p class="help-block"></p>
</div>

<script>
$(function () {
    commonjs.selectNav("navbar", "password_change");
})

$('#fchgpw').validate({
	rules: {
		old_password: { maxlength: 20, required: true },
		new_password: { minlength: 4, maxlength: 20, required: true },
		confirm_password: { equalTo:"#new_password", maxlength: 20, required: true }
	},
	messages: {
		confirm_password: {
		  equalTo: "비밀번호 확인값이 일치하지 않습니다."
		}
	},
	submitHandler: function (form) {
		//console.log($(form).serialize());
		form.submit();
	}
});

</script>

<!-- CONTENT END -->
<?php
include_once '../../footer.php';
?>