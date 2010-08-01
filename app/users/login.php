<?php forbidden() ?>
<?= h2(로그인) ?>

<form name="login" method="post" class="login">
	<h3>자, 일단 로그인부터.</h3>
	<dl>
		<dt><label for="userid">아이디</label></dt>
		<dd><input id="userid" type="text" name="userid"></dd>
		<dt><label for="passwd">암호</label></dt>
		<dd><input id="passwd" type="password" name="passwd"></dd>
	</dl>
	<p><input type="submit" value="로그인&rarr;"></p>
	<p><input type="checkbox" name="persistent" id="persistent"><label for="persistent">로그인유지</label></p>
	<input type="hidden" name="forward_to" value="<?= h($_GET['forward_to']) ?>">
</form>

<script>
document.forms.login.elements.userid.focus()
</script>
