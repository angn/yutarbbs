<?= h2(forum_name($thread->fid)) ?>

<form method="post" action="<?= u(threads, update) ?>" enctype="multipart/form-data" class="message">
	<p class="subject"><input type="text" name="subject" size="30" value="<?= h($thread->subject) ?>"></p>
	<p><textarea name="message" cols="40" rows="20"><?= h($thread->message) ?></textarea><br>
		<small>&lt;tex&gt;\code&lt;/tex&gt;</small></p>
	<p class="attachment"><input type="file" name="attachment" size="40"></p>
	<p class="submit"><input type="submit" accesskey="w" value="수정&rarr;"></p>
	<input type="hidden" name="tid" value="<?= $thread->tid ?>">
</form>

<script>
document.forms[0].elements[0].focus()
</script>
