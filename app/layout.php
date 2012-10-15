<?php
$updated = is_forum_updated();
?>
<!DOCTYPE html>
<html>
<head>
<meta name="verify-v1" content="Vk1vAsUQPxHe0RgJW6YfJN3njmjm3VjxLPKADaq9SfA=" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>yutar.net</title>
<link rel=stylesheet type=text/css href="<?= u('master') ?>.css">
<meta name=viewport content="initial-scale=1.0,minimum-scale=1.0">
<script src="<?= u('shortcut') ?>.js"></script>
</head>
<body>
<div class=container>

<h1><a href="<?= u() ?>">
    <big>yutar. the premium.</big>
    <small>Yonsei Informatics Specialists Since 1998</small>
</a></h1>

<?php if ($my): ?>
<p class=identity>
<strong><?= h($my->userid) ?></strong> · 
<a class=minor href="<?= u('me') ?>">수정</a> · 
<a href="<?= u('gateway') ?>">로그아웃</a>
</p>

<p class=menu>
<?php foreach (array('`' => 1, 1 => 2, 8, 10, 9, 6, 3, 4, 7) as $i => $e): ?>
<a accesskey="<?= $i ?>" href="<?= u('forum', $e) ?>"><?= $FORUM_NAME[$e] ?><?= $updated[$e] ?></a> · 
<?php endforeach ?>
<a accesskey=9 class=fade href="<?= u('users') ?>">회원명부</a> · 
<a accesskey=0 class="fade minor" href="<?= u('emoticons') ?>">이모티콘</a> · 
<a class="fade minor" href="ftp://jingyi.yutar.net/">징이</a>
</p>
<?php endif ?>

<?= $body ?>

</div>
<address><a href="http://code.google.com/p/yutarbbs/">http://code.google.com/p/yutarbbs/</a></address>

</body>
</html>

