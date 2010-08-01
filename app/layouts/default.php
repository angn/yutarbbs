<?php $updated = is_forum_updated() ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ko" class="<?= $this->controller ?> <?= strpos($_SERVER[HTTP_USER_AGENT], 'AppleWebKit') ? 'webkit' : '' ?>">
<head>
<meta name="verify-v1" content="Vk1vAsUQPxHe0RgJW6YfJN3njmjm3VjxLPKADaq9SfA=" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?= h($this->title) ?></title>
<link rel="stylesheet" type="text/css" media="screen" href="<?= u(css, master) ?>.css">
<link rel="stylesheet" type="text/css" media="screen" href="<?= u(css, $this->controller, $this->action) ?>.css">
<link rel="stylesheet" type="text/css" media="only screen and (max-device-width: 480px)" href="<?= u(css, iphone) ?>.css">
<meta name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
<script src="<?= u(js, shortcut) ?>.js"></script>
<?= $this->meta() ?>
</head>
<body class="<?= $this->action ?>">

<h1><a href="<?= u() ?>">yutar.net</a></h1>

<div class="user">
<p>사용자
<?php if ($my): ?><ul>
<li><a href="<?= u(users, edit) ?>"><?= h($my->userid) ?></a>
<li><a href="<?= u(users, logout) ?>">로그아웃</a>
</ul><?php endif ?>
</div>

<div class="menu">
<p>메뉴<ol id="menu">
<li><a accesskey="`" href="<?= u(threads, index, 1) ?>">공지<?= $updated[1] ?></a>
<li><a accesskey="1" href="<?= u(threads, index, 2) ?>"><?= h(forum_name(2)) ?><?= $updated[2] ?></a>
<li><a accesskey="2" href="<?= u(threads, index, 8) ?>"><?= h(forum_name(8)) ?><?= $updated[8] ?></a>
<li><a accesskey="3" href="<?= u(threads, index, 3) ?>"><?= h(forum_name(3)) ?><?= $updated[3] ?></a>
<li><a accesskey="4" href="<?= u(threads, index, 10) ?>"><?= h(forum_name(10)) ?><?= $updated[10] ?></a>
<li><a accesskey="5" href="<?= u(threads, index, 4) ?>"><?= h(forum_name(4)) ?><?= $updated[4] ?></a>
<?php/*
<li><a               href="<?= u(threads, index, 5) ?>"><?= h(forum_name(5)) ?><?= $updated[5] ?></a>
*/?>
<li><a accesskey="6" href="<?= u(threads, index, 9) ?>"><?= h(forum_name(9)) ?><?= $updated[9] ?></a>
<li><a accesskey="7" href="<?= u(threads, index, 6) ?>"><?= h(forum_name(6)) ?><?= $updated[6] ?></a>
<li><a accesskey="8" href="<?= u(threads, index, 7) ?>"><?= h(forum_name(7)) ?><?= $updated[7] ?></a>
</ol>
<p>도구<ul>
<li><a accesskey="9" href="<?= u(users) ?>">회원명부</a>
<li><a accesskey="0" href="<?= u(emoticons) ?>">이모티콘</a>
<li><a href="http://ps2009.yutar.net/">PS2009</a>
<li><a href="ftp://jingyi.yutar.net/">징이</a>
<li><a href="http://dev.yutar.net/">DEV</a>
<li><a href="http://wiki.yutar.net/">wiki</a>
</ul>
</div>

<div class="container"><?= $_ ?></div>

<address>연세대학교 제1공학관 311호 연세정보특기자모임 -
☎ (02)2123-4265</address>
</body>
</html>
