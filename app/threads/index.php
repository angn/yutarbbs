<?php $this->rss(u(threads, rss)) ?> 
<?php $this->js(u(js, forum) . '.js') ?>
<?= h2(forum_name($fid)) ?>

<form method="post"><p class="search">
	<input id="search" type="search" name="keyword" placeholder="이 게시판에서 검색" accesskey="f" value="<?= h($keyword) ?>"><input type="submit" value="검색">
</p></form>

<table class="threads">
	<!--[if lt IE 7]>
	<col width="45" align="center" style="font: 92% Tahoma, Arial, sans-serif">
	<col>
	<colgroup align="center">
		<col span="2" width="65">
		<col width="45" style="font: 92% Tahoma, Arial, sans-serif">
	</colgroup>
	<![endif]-->
	<thead><tr>
		<td>번호</td>
		<td>주제</td>
		<td>글쓴이</td>
		<td>날짜</td>
		<td>조회</td>
	</tr></thead>
	<tbody id="threads">
<?php if ($threads): foreach ($threads as $i => $t):
	$t->replies = $info[$t->tid]->replies;
	$t->updated = $info[$t->tid]->updated;
	?><tr class="<?php $i & 1 and print 'a' ?>">
		<td><?= $t->tid ?></td>
		<td><a href="<?= u(threads, show, $t->tid) ?>"><?= h($t->subject) ?><?php if ($t->replies): ?> <small class="replies"><?= number_format($t->replies) ?></small><?php endif ?></a> <?php $t->updated and print '<em>*</em>' ?> <?php if ($t->attachment): ?><img src="<?= u(img) ?>/attachment.gif" width="16" height="16" alt="첨부"><?php endif ?></td>
		<td><small class="year"><?= $t->year ?></small><?= h($t->name) ?></td>
		<td><?= format_date($t->created) ?></td>
		<td><?= number_format($t->hits) ?></td>
	</tr><?php endforeach; else: ?><tr>
		<td></td>
	 	<td colspan="4">해당 글 없음</td>
	</tr><?php endif ?>
	</tbody>
</table>

<div class="commands">

<form>
<p class="pages">

<?php if (empty($keyword)): ?>

<?php if ($page > 1): ?>
<a href="?p=<?= $page - 1 ?>" accesskey="p">&laquo;이전</a>
<?php else: ?>
<del>&laquo;이전</del>
<?php endif ?>

<select name="p" onchange="this.form.submit()">
<?php foreach (range(1, $max_page) as $p): ?>
<?php if ($p == $page): ?>
<option selected><?= $p ?> / <?= $max_page ?>
<?php else: ?>
<option><?= $p ?>
<?php endif ?>
<?php endforeach ?>
</select>

<?php if ($page < $max_page): ?>
<a href="?p=<?= $page + 1 ?>" accesskey="n">다음&raquo;</a>
<?php else: ?>
<del>다음&raquo;</del>
<?php endif ?>

<?php else: ?>
&nbsp;
<?php endif ?>

</p>
</form>

<form action="<?= u(threads, add, $fid) ?>">
	<p class="add"><input type="submit" value="새 글" accesskey="w"></p>
</form>

<!--
<form action="<?= u(threads, issue, $fid) ?>">
	<p class="issue"><input type="submit" value="덧글 달린 순"></p>
</form>
-->

</div>
