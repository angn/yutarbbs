<?php
if (requirelogin())
    return;

if (isset($_POST['tid'])) {
    insert('messages', array('created_at' => now(), 'uid' => $my->uid) + $_POST);
    redirect(null);

/*
} elseif (isset($_POST['mid'])) {
    $mid = intval($_POST['mid']);
    $m = fetchone('tid, message FROM messages WHERE mid = ? LIMIT 1', $mid);
    insert('messages', array('created_at' => now(), 'uid' => $my->uid) + (array)$m);
    redirect(null);
*/
}

notfound();

