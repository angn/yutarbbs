<?= h2(유타인RSS) ?>

<?php foreach ($users as $u): foreach ($u->item as $i): ?><div class="item">
<h3><a href="<?= h($i->link) ?>" target="_blank"><?= h($i->title) ?></a> | <?= h($u->title) ?></h3>
<p><?= format_date(strtotime($i->pubDate)) ?></p>
<div class="text"><?= format_text($i->description) ?></div>
</div><?php endforeach; endforeach ?>
