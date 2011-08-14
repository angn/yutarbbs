<?php
if (requirelogin())
    return;

list($mid) = $args;
if ($_GET['y'] == '1')
    delete('messages', array('mid = ? AND uid = ?', $mid, $my->uid), 1);
redirect(null);

