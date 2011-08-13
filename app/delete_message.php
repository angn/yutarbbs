<?php
if (requirelogin())
    return;

$mid = $_POST['mid'];
if (isset($_GET['y']))
    delete('messages', array('mid = ? AND uid = ?', $mid, $my->uid), 1);
redirect(null);

