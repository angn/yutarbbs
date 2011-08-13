<?php

if ($_POST) {
    $user = fetchone('uid, year, name, userid, updated_on + INTERVAL 3 MONTH < NOW() outdated FROM users WHERE userid = ? AND passwd = ? LIMIT 1', $_POST['userid'], hashpasswd($_POST['passwd']));
    if ($user) {
        $my = (object)array();
        foreach ((array)$user as $k => $v)
            $k != 'outdated' and $my->$k = $v;
        $my->persistent = (bool)$_POST['persistent'];
        sess_write($my);
        $user->outdated and redirect('me');
        redirect(null);
    }
}

sess_write(null);
redirect();
