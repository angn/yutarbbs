<?= h2(폭탄) ?>

<p class="notice">이곳의 파일들은 한 주간 보관됩니다.</p>

<ul class="bombs">
<?php foreach ($bombs as $p => $t): $p = filename($p) ?>
	<li><a href="<?= u(bombs, $p) ?>"><?= h($p) ?></a></li>
<?php endforeach ?>
</ul>

<form method="post" action="<?= u(bombs, create) ?>" enctype="multipart/form-data">
	<p><input type="file" name="bomb" size="30"></p>
	<p class="submit"><input type="submit" value="&uarr;"></p>
</form>
