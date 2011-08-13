<?php
if (requirelogin())
    return;

list($fid, $page) = $args;

isset($FORUM_NAME[$fid]) or notfound();

$page = max(1, intval($page));
$r = fetchone('GREATEST(1, CEILING(COUNT(*) / 15)) n FROM threads WHERE fid = ? LIMIT 1', $fid);
$max_page = $r->n;
if ($keyword = $_POST['keyword']) {
    $threads = fetchall('tid, subject, year, name, UNIX_TIMESTAMP(created_at) created, hits, attachment FROM threads INNER JOIN users USING (uid) WHERE fid = ? AND (UPPER(name) = UPPER(?) OR INSTR(UPPER(subject), UPPER(?)) OR INSTR(UPPER(message), UPPER(?))) ORDER BY created_at DESC', $fid, $keyword, $keyword, $keyword);
} else {
    $threads = fetchall('tid, subject, year, name, UNIX_TIMESTAMP(created_at) created, hits, attachment FROM threads INNER JOIN users USING (uid) WHERE fid = ? ORDER BY created_at DESC LIMIT ' . ($page * 15 - 15) . ', 15', $fid);
}

$info = array();
if ($threads) {
    $tids = array();
    foreach ($threads as $e)
        $tids[] = intval($e->tid);
    $tids = implode(',', $tids);
    $rs = fetchall("tid, COUNT(tid) replies, MAX(created_at) + INTERVAL 1 DAY > NOW() updated FROM messages WHERE tid IN ($tids) GROUP BY tid");
    foreach ($rs as $r)
        $info[$r->tid] = $r;
}
?>
<h2><?= $FORUM_NAME[$fid] ?></h2>
<div class=forum>

<form method="post" class="search"><p>
	<input id="search" type="search" name="keyword" placeholder="이 게시판에서 검색" accesskey="f" value="<?= h($keyword) ?>"><input type="submit" value="검색">
</p></form>

<h3>
    <span class=subject>주제</span>
    <span class=author>글쓴이</span>
    <span class=date>날짜</span>
    <span class=hits>조회</span>
</h3>
<ol id=list>
<?php if ($threads): foreach ($threads as $i => $t):
$t->replies = $info[$t->tid]->replies;
$t->updated = $info[$t->tid]->updated;
?><li class="<?php $i & 1 and print 'a' ?>"><a href="<?= u(thread, $t->tid) ?>">
    <span class=subject><?= h($t->subject) ?><?php if ($t->replies): ?> <small class=replies><?= number_format($t->replies) ?></small><?php endif ?> <?php $t->updated and print '<em>*</em>' ?> <?php if ($t->attachment): ?><span class=attachment>첨부</span><?php endif ?></span>
    <span class=author><small class=year><?= $t->year ?></small><?= h($t->name) ?></span>
    <span class=date><?= formatdate($t->created) ?></span>
    <span class=hits><?= number_format($t->hits) ?></span>
</a></li><?php endforeach; endif ?>
</ol>

<form>
<p class=pages>

<?php if (empty($keyword)): ?>

<?php if ($page > 1): ?>
<a href="<?= u('forum', $fid, $page - 1) ?>" accesskey=p>&laquo;이전</a>
<?php else: ?>
<del>&laquo;이전</del>
<?php endif ?>
·
<input size=2 value="<?= h($page) ?>"> / <?= number_format($max_page) ?>
 ·
<?php if ($page < $max_page): ?>
<a href="<?= u('forum', $fid, $page + 1) ?>" accesskey=n>다음&raquo;</a>
<?php else: ?>
<del>다음&raquo;</del>
<?php endif ?>

<?php else: ?>
&nbsp;
<?php endif ?>

</p>
</form>

<p class=command><a href="<?= u('edit_thread', 'forum', $fid) ?>" accesskey=w>새 글</a></p>

</div>
