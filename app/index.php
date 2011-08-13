<?php
$notice = fetchone('tid, subject, message FROM threads WHERE fid = 1 ORDER BY created_at DESC LIMIT 1');
if ($my)
    redirect('thread', $notice->tid);
?>
<?php if (!$my): ?>
<form name=login action="<?= u('gateway') ?>" method=post class=login><p>
<label><span>아이디</span> <input type=text name=userid autofocus placeholder="아이디"></label>
<label><span>암호</span> <input type=password name=passwd placeholder="암호"></label>
<input type="submit" value="로그인&raquo;">
<label><input type=checkbox name=persistent>로그인 유지</label>
</p></form>
<?php endif ?>

<h2>공지사항</h2>

<?php if ($notice): ?>
<div class=thread>
<h3><?= h($notice->subject) ?></h3>
<div class=article><?= formattext($notice->message) ?></div>
</div>
<?php endif ?>

