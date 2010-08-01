<?= h2(공지사항) ?>

<?php if ($notice): ?>
<div class="box">
	<h3><?= h($notice->subject) ?></h3>
	<div class="text"><?= format_text($notice->message) ?></div>
	<div class="past"><p><a href="<?= u(threads, index, 1) ?>">지나간 공지사항</a></p></div>
</div>
<?php endif ?>

<?php if (!$my): ?>
<form name="login" action="<?= u(users, login) ?>" method="post" class="login">
	<p><label for="userid" class="hidden">아이디</label><input id="userid" type="text" name="userid" class="field" accesskey="l">
		<label for="passwd" class="hidden">암호</label><input id="passwd" type="password" name="passwd" class="field">
		<input type="submit" value="로그인&rarr;">
		<input type="checkbox" name="persistent" id="persistent"><label for="persistent">로그인유지</label></p>
</form>

<script>
document.forms.login.elements.userid.focus()
</script>
<?php endif ?>
