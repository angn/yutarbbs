<?php
if (requirelogin())
    return;

list($tid) = $args;

$thread = fetchone('fid FROM threads WHERE tid = ? LIMIT 1', $tid);
if (delete('threads', array('tid = ? AND uid = ?', $tid, $my->uid), 1))
    redirect('forum', $thread->fid);
nocontent();
