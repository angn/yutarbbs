<?php

error_reporting(E_ALL ^ E_NOTICE);

if ($_REQUEST['x'])
    ini_set('display_errors', 1);

define('ROOT', dirname(__FILE__));

setlocale(LC_ALL, 'ko_KR.utf8', 'en_US.utf8', '');

define('PLAIN', '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz+/=');

require_once ROOT . '/conf.php';

function sess_write($data)
{
    global $conf;
	setcookie('sess',
		strtr(base64_encode(serialize($data)), PLAIN, $conf['CODED']),
		$data ? time() + ($data->persistent ? 1209600 : 1800) : 1,
		'/');
}

function sess_init()
{
    global $conf;
    if (isset($_COOKIE['sess'])) {
        $data = @unserialize(
                base64_decode(strtr($_COOKIE['sess'], $conf['CODED'], PLAIN)));
        sess_write($data);
        return $data;
    }
    return false;
}

function alert($s)
{
	echo '<script>alert(' . json_encode($s) . ')</script>';
}

function db()
{
    global $conf;
	static $c;
    if (!$c)
        $c = new PDO($conf['DSN'], $conf['DBUSER'], $conf['DBPASS']);
    return $c;
}

function fetchone($q)
{
	$a = func_get_args();
	$rs = db()->prepare("SELECT $q");
    return $rs->execute(array_slice($a, 1)) ? $rs->fetchObject() : null;
}

function fetchall($q)
{
	$a = func_get_args();
	$rs = db()->prepare("SELECT $q");
    return $rs->execute(array_slice($a, 1)) ?
            $rs->fetchAll(PDO::FETCH_OBJ) : array();
}

function now()
{
    return date('YmdHis');
}

function insert($t, $v)
{
	$k = implode(',', array_keys($v));
    $a = array_values($v);
	$v = implode(',', array_fill(0, count($v), '?'));
	$rs = db()->prepare("INSERT INTO $t ($k) VALUES ($v)");
    return $rs->execute($a) ? db()->lastInsertId() : 0;
}

function update($t, $s, $w, $n = null)
{
    $a = array();
	if (is_array($s)) {
        $a = array_values($s);
        $s = implode('=?,', array_keys($s)) . '=?';
    }
	if (is_array($w)) {
        $a = array_merge($a, array_slice($w, 1));
        $w = $w[0];
    }
	$q = "UPDATE $t SET $s WHERE $w";
	if ($n)
        $q .= " LIMIT $n";
	$rs = db()->prepare($q);
    return $rs->execute($a) ? $rs->rowCount() : 0;
}

function delete($t, $w, $n = null)
{
    $a = array();
	if (is_array($w)) {
        $a = array_slice($w, 1);
        $w = $w[0];
    }
	$q = "DELETE FROM $t WHERE $w";
	if ($n)
        $q .= " LIMIT $n";
	$rs = db()->prepare($q);
    return $rs->execute($a) ? $rs->rowCount() : 0;
}

function h($s)
{
	return htmlspecialchars($s);
}

function url()
{
	$a = func_get_args();
	return "http://{$_SERVER['HTTP_HOST']}/" .
            implode('/', array_map('rawurlencode', $a));
}

function u()
{
	$a = func_get_args();
	return '/' . implode('/', array_map('rawurlencode', $a));
}

function notfound()
{
	header('Status: 404 Not Found', true, 404);
	header('Content-Type: text/plain');
	echo 'Not Found';
	exit(1);
}

function nocontent()
{
	header('Status: 204 No Content', true, 204);
	exit(0);
}

function redirect()
{
    $a = func_get_args();
    if ($a[0] === null)
        $url = $_SERVER['HTTP_REFERER'];
    else
        $url = call_user_func_array('u', $a);
    if (!$url)
        nocontent();
    header('Status: 303 See Other', true, 303);
	header("Location: $url");
	exit(0);
}

function hashpasswd($s)
{
    return md5("gkfd{$s}gkfd");
}

function is_forum_updated()
{
    $a = array();
    $rs = fetchall('fid, MAX(created_at) > NOW() - INTERVAL 1 DAY t FROM threads GROUP BY fid');
    foreach ($rs as $r)
        if ($r->t)
            $a[$r->fid] = '<em>*</em>';
    $rs = fetchall('fid, MAX(messages.created_at) > NOW() - INTERVAL 1 DAY t FROM messages INNER JOIN threads USING (tid) GROUP BY fid');
    foreach ($rs as $r)
        if ($r->t)
            $a[$r->fid] = '<em>*</em>';
	return $a;
}

function _replace_emo($m)
{
    static $a = array();
    $e = $m[1];
    if ($a[$e])
        return $a[$e];
    if (glob(ROOT . '/www/emo/' . $e . '.*'))
        return $a[$e] = '<img src="' . u('emo', $e) . '" alt="' . h($e) . '">';
    return $m[0];
}

function replace_emoticons($s)
{
    return preg_replace_callback('#@([^@/.\s]+)@#', '_replace_emo', $s);
}

function requirelogin()
{
    global $my;
	if (!$my || !$my->uid) {
        header('Status: 403 Forbidden', true, 403);
		require ROOT . '/app/auth.php';
        return true;
    }
}

function formatdate($n)
{
	static $A;
	if (!$A) {
		$A = array(
            mktime(0, 0, 0, 1, 1)  => array('date', 'n/j' ),
            strtotime('0:0 -2day') => array('sprintf', '그제'),
            strtotime('0:0 -1day') => array('sprintf', '어제'),
            mktime(0, 0, 0)        => array('sprintf', '<em>오늘</em>'),
            strtotime('-5min')     => array('sprintf', '<em>방금</em>'),
        );
        krsort($A, SORT_NUMERIC);
    }
	foreach ($A as $k => $v)
		if ($n >= $k)
			return $v[0]($v[1], $n);
	return date("'y n/j", $n);
}

function formattime($n)
{
	return formatdate($n) . date(' H:i', $n);
}

function formatphone($n)
{
    return preg_replace('/^(01[01679])(\d{3,4})(\d{4})$/', '$1-$2-$3', $n);
}

function mathtex($m)
{
	return '<img src="http://www.forkosh.dreamhost.com/mathtex.cgi?' .
		rawurlencode($m[1]) . '">';
}

function formattext($s)
{
    $a = preg_split('#(<[A-Z]+[^>]*>|</[A-Z]+[^>]*>)#is', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
    $s = implode(array_map('_formattexteach', $a));
    $s = preg_replace_callback('#<tex>(.+?)</tex>#s', 'mathtex', $s);
	return "<p>$s</p>";
}

function _formattexteach($s) {
    if (preg_match('#^<[A-Z/].*>$#is', $s))
    	return $s;
    return preg_replace_callback('#\bhttps?://[^\s<]+|\b()[\w.]+@[\w.]+\b#',
            '_formattexturl', htmlspecialchars($s));
}

function _formattexturl($m) {
    $url = $m[0];
    $href = ($m[1] === '' ? 'mailto:' : '') . $url;
    $label = preg_replace('/(?<=[?#]).+/', '&hellip;', $m[0]);
    return "<a target=\"_blank\" href=\"$href\" title=\"$url\">$label</a>";
}

$FORUM_NAME = array('', '공지', '자유게시판', '학술', 'PS', '유타닷넷', '운영', '소모임', '질문·토론', '진로', '테크');

$my = sess_init();

$CLI = isset($argv);
$uri = $CLI ? $argv[1] : strtok($_SERVER['REQUEST_URI'], '?');
$args = array_slice(explode('/', rtrim($uri, '/')), 1)
    + array('index');

ob_start();
@include ROOT . '/app/' . array_shift($args) . '.php';
$body = ob_get_clean() or notfound();

if ($CLI)
    echo $body;
else
    require ROOT . '/app/layout.php';

