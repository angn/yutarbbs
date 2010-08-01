<?php

$SESS_TR = array('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz+/=',
                 'EFGHIJKLMmnopqrstuvwxyz-_.NOPQRSTUVWXYZabcdefghijkl0123456789ABCD');

function sess_encode($s)
{
	global $SESS_TR;
	return strtr(base64_encode($s), $SESS_TR[0], $SESS_TR[1]);
}

function sess_decode($s)
{
	global $SESS_TR;
	return base64_decode(strtr($s, $SESS_TR[0], $SESS_TR[1]));
}

function sess_write()
{
	setcookie(sess,
		sess_encode(
			serialize(
				array(timestamp => time() + ($_COOKIE[persistent] ? 1209600 : 1800)) +
				$_SESSION)),
		time() + 1209600,
		'/');
}

function sess_destroy()
{
	setcookie(sess, '', 1, '/');
}

isset($_COOKIE[persistent]) and
	setcookie(persistent, 1, time() + 1209600, '/');

if (isset($_COOKIE[sess])) {
	$_SESSION = (array)@unserialize(sess_decode($_COOKIE[sess]));
	if ($_SESSION[timestamp] >= time())
		sess_write();
	else
		sess_destroy();
}

function flash($s = null)
{
	setcookie(flash, $s, $s ? 0 : 1, '/');
	return (string)$_COOKIE[flash];
}
