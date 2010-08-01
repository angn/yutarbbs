<?= h2($user->userid) ?>

<form method="post" action="update">
	<dl>
<?php foreach (array(
	array('이름', name),
	array('학번', year),
) as $a): ?>
		<dt><?= h($a[0]) ?></dt>
		<dd><?= h($user->{$a[1]}) ?></dd>
<?php endforeach ?>
<?php foreach (array(
	array('이메일', email),
	array('전화번호', phone),
	array('웹사이트', website),
) as $i => $a): ?>
		<dt><label for="<?= h($a[1]) ?>"><?= h($a[0]) ?></label></dt>
		<dd><input type="text" id="<?= h($a[1]) ?>" name="<?= h($a[1]) ?>" value="<?= h($user->{$a[1]}) ?>"></dd>
<?php endforeach ?>
		<dt><label for="remark">기타</label></dt>
		<dd><textarea id="remark" name="remark" rows="5"><?= h($user->remark) ?></textarea></dd>
		<dt>최종 수정</dt>
		<dd><?= format_date($user->updated) ?></dd>
	</dl>
	<p><input type="submit" value="저장&rarr;" accesskey="s"></p>
	<input type="hidden" name="uid" value="<?= $user->uid ?>">
</form>

<hr>

<form method="post" action="update_passwd">
	<dl>
		<dt><label for="passwd">암호</label></dt>
		<dd><input type="password" id="passwd" name="passwd"></dd>
		<dt><label for="passwd2">한 번 더</label></dt>
		<dd><input type="password" id="passwd2" name="passwd2"></dd>
	</dl>
	<p><em><?= flash() ?></em></p>
	<p><input type="submit" value="변경&rarr;" accesskey="s"></p>
	<input type="hidden" name="uid" value="<?= $user->uid ?>">
</form>
