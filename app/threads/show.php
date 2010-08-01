<?php $this->js(u(js, thread) . '.js') ?>
<?php/*<link type="text/css" rel="stylesheet" href="/dp/Styles/SyntaxHighlighter.css"></link>*/?>

<?= h2(forum_name($thread->fid)) ?>

<h3><?= h($thread->subject) ?></h3>

<div class="message">
	<dl>
		<dt>글쓴이</dt><dd class="author"><small class="year"><?= $thread->year ?></small><?= h($thread->name) ?></dd>
		<dt>작성일</dt><dd class="created"><span class="created"><?= format_time($thread->created) ?></span></dd>
	</dl>
	<?php if ($thread->uid == $my->uid): ?><ul class=cmd>
		<li><a href="<?= u(threads, edit, $thread->tid) ?>" accesskey="e">수정</a></li>
		<li><a href="<?= u(threads, delete, $thread->tid) ?>" onclick="return confirm('아 정말요?')">삭제</a></li>
		<li><a href="<?= u(threads, announce, $thread->tid) ?>">공지</a></li>
	</ul><?php endif ?>
	<?php if ($attachment): $attachment = (object)$attachment ?>
		<div class="attachment">
		<?php if ($attachment->img_attr): ?>
			<p class="info"><a href="<?= u(threads, attachment, $thread->tid, $thread->attachment) ?>"><?= h($thread->attachment) ?></a> <small>(<?= number_format($attachment->img_width) ?> &times <?= number_format($attachment->img_height) ?> / <?= number_format($attachment->size >> 10) ?>KB)</small></p>
			<p><a href="<?= u(threads, attachment, $thread->tid, $thread->attachment) ?>"><img src="<?= u(threads, attachment, $thread->tid, $thread->attachment) ?>" <?= $attachment->img_attr ?> alt="<?= h($thread->attachment) ?>"></a></p>
		<?php /* elseif ($attachment->zip_entries): ?>
			<p class="info"><a href="<?= u(threads, attachment, $thread->tid, $thread->attachment) ?>"><?= h($thread->attachment) ?></a> <small>(<?= number_format($attachment->size >> 10) ?>KB)</small><br>
			<?php foreach ($attachment->zip_entries as $e): ?>
				<?= h($e->name) ?> <small>(<?= number_format($e->size >> 10) ?>KB)</small><br>
			<?php endforeach ?></p>
		<?php */ elseif (isset($attachment->html)): ?>
			<p class="info"><a href="<?= u(threads, attachment, $thread->tid, $thread->attachment) ?>"><?= h($thread->attachment) ?></a> <small>(<?= number_format($attachment->size >> 10) ?>KB)</small></p>
			<pre id=code name=code class=html><?= $attachment->html ?></pre>
			<?php/*
			<script src="/dp/Scripts/shCore.js"></script>
			<script src="/dp/Scripts/shBrushCpp.js"></script>
			<script src="/dp/Scripts/shBrushXml.js"></script>
			<script>
			dp.SyntaxHighlighter.HighlightAll('code')
			</script>
			<p class=zoom><input type=button onclick="zoom()" value="코드 펼치기"></p>
			<p id=unzoom style="display: none"><input type=button onclick="unzoom()" value="코드 접기"></p>
			*/?>
		<?php else: ?>
			<p class="info"><a href="<?= u(threads, attachment, $thread->tid, $thread->attachment) ?>"><?= h($thread->attachment) ?></a> <small>(<?= number_format($attachment->size >> 10) ?>KB)</small></p>
		<?php endif ?>
		</div>
	<?php endif ?>
	<div class="text"><?= format_text($thread->message) ?></div>
	<p class="remark"><?= $thread->year ?><strong><?= h($thread->name) ?></strong><br>
		<?php if ($thread->email): ?><a href="mailto:<?= h($thread->email) ?>"><?= h($thread->email) ?></a><br><?php endif ?>
		<?php if ($thread->website): ?><a target="_blank" href="<?= h($thread->website) ?>"><?= h($thread->website) ?></a><br><?php endif ?>
		<?= nl2br(h($thread->remark)) ?></p>
</div>

<?php $n = count($messages); foreach ($messages as $i => $m): $a ^= 1 ?><hr><div class="message <?php $a and print 'a' ?>">
	<dl>
		<dt>글쓴이</dt><dd class="author">
		    <a href="#" title="댓글1" onclick="return add1s(<?php echo $n - $i ?>)">
                <small class="year"><?= $m->year ?></small><?= h($m->name) ?></a></dd>
		<dt>작성일</dt><dd class="created"><small class="created"><a href="<?= u(messages, delete, $m->mid) ?>" title="삭제" onclick="if (!confirm('아 정말요?')) return false; this.href += '?y'"><?= format_time($m->created) ?></a></small></dd>
	</dl>
	<div class="text">
        <a class="repost" href="#" title="Repost!" onclick="return repost(<?php echo $m->mid ?>)">RE</a>
        <?= replace_emoticons(format_text($m->message)) ?>
	</div>
</div><?php endforeach ?>

<hr>
<form name="comment" method="post" action="<?= u(messages, create) ?>">
	<input type="hidden" name="tid" value="<?= $thread->tid ?>">
	<div class="message <?php $a or print 'a' ?>">
		<dl>
			<dt>글쓴이</dt><dd class="author"><small class="year"><?= $my->year ?></small><?= h($my->name) ?></dd>
		</dl>
		<div class="text">
			<p><textarea name="message" cols="40" rows="5" accesskey="m"></textarea></p>
			<p><small>&lt;tex&gt;\code&lt;/tex&gt;</small> <input type="submit" accesskey="w" value="작성&rarr;"></p>
		</div>
	</div>
</form>

<form name="dup" method="post" action="<?= u(messages, duplicate) ?>">
<input type="hidden" name="mid">
</form>
