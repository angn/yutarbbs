<?php
if (requirelogin())
    return;

list($tid, $mod) = $args;

switch ($mod) {
case 'next':
    $thread = fetchone('b.tid FROM threads a, threads b WHERE a.tid=? AND b.fid=a.fid AND b.tid<a.tid ORDER BY b.tid DESC LIMIT 1', $tid) or notfound();
    if ($thread)
        redirect('thread', $thread->tid);
    nocontent();
case 'prev':
    $thread = fetchone('b.tid FROM threads a, threads b WHERE a.tid=? AND b.fid=a.fid AND b.tid>a.tid ORDER BY b.tid LIMIT 1', $tid) or notfound();
    if ($thread)
        redirect('thread', $thread->tid);
    nocontent();
}

if ($_COOKIE['lasttid'] != $tid) {
    update('threads', 'hits = hits + 1', array('tid = ? AND uid != ?', $tid, $my->uid), 1);
    setcookie('lasttid', $tid);
}

$thread = fetchone('tid, fid, subject, t.uid, year, name, phone, email, website, remark, message, UNIX_TIMESTAMP(created_at) created, attachment FROM threads t INNER JOIN users USING (uid) WHERE tid = ? LIMIT 1', $tid) or notfound();
$messages = fetchall('mid, message, uid, year, name, UNIX_TIMESTAMP(created_at) created FROM messages INNER JOIN users USING (uid) WHERE tid = ? ORDER BY created_at', $tid);
if ($thread->attachment) {
    $image = preg_match('/[.](jpe?g|gif|png|bmp)$/i', $thread->attachment);
    $size = @filesize(ROOT . "/www/attachments/$thread->tid-$thread->attachment");
}
?>
<script src="<?= u('thread') ?>.js"></script>

<h2><?= $FORUM_NAME[$thread->fid] ?></h2>

<div class=thread>
    <p class=nav>
    <a href="<?= u('forum', $thread->fid) ?>" accesskey=u>목록</a>
    <a href="<?= u('thread', $thread->tid, 'prev') ?>" accesskey=p>이전</a>
    <a href="<?= u('thread', $thread->tid, 'next') ?>" accesskey=n>다음</a>
    </p>

    <h3><?= h($thread->subject) ?></h3>

	<p class=info>
		<span class=author><small><?= $thread->year ?></small><?= h($thread->name) ?></span>
		<span class=date><?= formattime($thread->created) ?></span>
        <?php if ($thread->uid == $my->uid): ?> &mdash;
            <a href="<?= u('edit_thread', $thread->tid) ?>" accesskey="e">수정</a> ·
            <a href="<?= u('delete_thread', $thread->tid) ?>" onclick="return confirm('아 정말요?')">삭제</a>
        <?php endif ?>
	</p>
	<?php if ($thread->attachment): ?>
		<blockquote class=attachment>
			<a href="<?= u('attachment', $thread->tid, $thread->attachment) ?>"><?= h($thread->attachment) ?></a> <small>(<?= number_format($size >> 10) ?>KB)</small><br>
            <?php if ($image): ?>
                <img src="<?= u('attachment', $thread->tid, $thread->attachment) ?>" alt="<?= h($thread->attachment) ?>">
            <?php endif ?>
		</blockquote>
	<?php endif ?>
	<div class="article"><?= formattext($thread->message) ?></div>
	<p class="remark"><strong><?= $thread->year ?><?= h($thread->name) ?></strong>
		<?php if ($thread->phone): ?>☎ <?= formatphone($thread->phone) ?><br><?php endif ?>
		<?php if ($thread->email): ?><a href="mailto:<?= h($thread->email) ?>"><?= h($thread->email) ?></a><br><?php endif ?>
		<?php if ($thread->website): ?><a target="_blank" href="<?= h($thread->website) ?>"><?= h($thread->website) ?></a><br><?php endif ?>
		<?= h($thread->remark) ?></p>

<h4>“...!”</h4>
<dl>
<?php $n = count($messages); foreach ($messages as $i => $m):
    $m->message = preg_replace('/^(1{3,})(?=\s)/s',
            '<a href="#" onclick="return follow1s(' . $i . ',\'$1\')">$1</a>',
            $m->message);
?>
<dt class=info>
    <span class=author><small><?= $m->year ?></small><?= h($m->name) ?></span>
    <span class=date><?= formattime($m->created) ?></span>
    <?php if ($my->uid == $m->uid): ?>
    <a class=hid href="<?= u('delete_message', $m->mid) ?>" onclick="if (!confirm('아 정말요?')) return false; this.href += '?y'"><em>삭제</em></a>
    <?php else: ?>
    <a class=hid href=# onclick="return add1s(<?php echo $n - $i ?>)">답글1</a>
    <?php endif ?>
</dt>
<dd id="comment<?php echo $i ?>" class=article><?= replace_emoticons(formattext($m->message)) ?></dd>
<?php endforeach ?>

<dt class=info>
    <span class=author><small><?= $my->year ?></small><?= h($my->name) ?></span>
    <span class=date>지금</span>
<dd>
    <form name=comment method=post action="<?= u('message') ?>">
        <input type=hidden name=tid value="<?= $thread->tid ?>">
        <p><textarea name=message cols=40 rows=5 accesskey=m></textarea></p>
        <p><small>&lt;tex&gt;\code&lt;/tex&gt;</small></p>
        <p><input type=submit accesskey=s value="쓰기"></p>
    </form>
</dl>

</div>
