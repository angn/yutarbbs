<?php
if (requirelogin())
    return;

if ($_POST) {
    if (preg_match('/\S/', $_POST['subject'])) {
        if (!$_POST['tid']) {
            unset($_POST['tid']);
            $tid = insert('threads', array(
                    'created_at' => now(),
                    'uid' => $my->uid
                ) + $_POST + array('attachment' => strval($_FILES['attachment']['name'])));
        } else {
            update('threads', $_POST, array('tid = ? AND uid = ?', $_POST['tid'], $my->uid), 1); 
            if ($_FILES['attachment']['name']) {
                update('threads', array('attachment' => $_FILES['attachment']['name']),
                    array('tid = ? AND uid = ?', $_POST['tid'], $my->uid), 1);
            }
            $tid = $_POST['tid'];
        }
        if ($tid) {
            if ($file = $_FILES['attachment'])
                @move_uploaded_file($file['tmp_name'], ROOT . "/www/attachments/$tid-{$file['name']}");
            redirect('thread', $tid);
        }
    }
    notfound();
}

list($tid, $fid) = $args;
if ($tid == 'forum') {
    $thread = (object)array('fid' => $fid, 'tid' => 0);
} else {
    $thread = fetchone('tid, fid, subject, message, UNIX_TIMESTAMP(created_at) created FROM threads INNER JOIN users USING (uid) WHERE tid = ? LIMIT 1', $tid) or notfound();
}
?>
<h2><?= $FORUM_NAME[$fid] ?></h2>

<div class=thread>
<form method=post enctype=multipart/form-data>
<h3><input type=text name=subject size=80 value="<?= h($thread->subject) ?>" autofocus></h3>
<div class=article><textarea name=message cols=80 rows=20><?= h($thread->message) ?></textarea>
<small>&lt;tex&gt;\code&lt;/tex&gt;</small>
<input type=file name=attachment size=30>
<input type=submit accesskey=s value="작성">
</div>
<input type=hidden name=fid value="<?= $thread->fid ?>">
<input type=hidden name=tid value="<?= $thread->tid ?>">
</form>
</div>
