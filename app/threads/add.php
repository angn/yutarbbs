<?= h2(forum_name($fid)) ?>

<form method="post" action="<?= u(threads, create) ?>" enctype="multipart/form-data" class="message">
	<?php if ($categories): ?><p><select name="cid"><?php foreach ($categories as $c): ?><option value="<?= $c->cid ?>"><?= h($c->label) ?><?php endforeach ?></select></p><?php endif ?>
	<p class="subject"><input type="text" name="subject" size="30"></p>
	<p><textarea name="message" cols="40" rows="20"></textarea><br>
		<small>&lt;tex&gt;\code&lt;/tex&gt;</small></p>
	<p class="attachment"><input type="file" name="attachment" size="30"></p>
	<p class="submit"><input type="submit" accesskey="w" value="작성&rarr;"></p>
	<input type="hidden" name="fid" value="<?= $fid ?>">
</form>

<script>
document.forms[0].elements[0].focus()
</script>
