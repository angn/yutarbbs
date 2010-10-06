<?php

function format_date($n)
{
	static $A;
	if (!$A) {
		$A = array(
            0                           => array(0, date, "'y n/j"),
            strtotime('0:0 1/1 -1year') => array(0, date, '작년 n/j' ),
            mktime(0, 0, 0, 1, 1)       => array(0, date, 'n/j' ),
            strtotime('0:0 -2day')      => array(0, id, '그제'),
            strtotime('0:0 -1day')      => array(0, id, '어제'),
            mktime(0, 0, 0)             => array(1, id, '오늘'),
            strtotime('-1min')          => array(1, id, '방금'),
        );
        krsort($A, SORT_NUMERIC);
    }
	foreach ($A as $k => $v) {
		if ($n >= $k) {
            // echo "<!-- DEBUG: $n $k "; var_dump($v); echo " -->";
			$s = @$v[1]($v[2], $n);
			return $v[0] ? "<em>$s</em>" : $s;
		}
	}
	return '';
}

function format_time($n)
{
	return format_date($n) . date(' H:i', $n);
}

function format_phonenumber($n)
{
	if (preg_match('/^(01[01679])(\d{3,4})(\d{4})$/', $n, $m))
		return "$m[1]-$m[2]-$m[3]";
	return $n;
}
function mathtex($m)
{
	return '<img src="http://www.forkosh.dreamhost.com/mathtex.cgi?' .
		rawurlencode($m[1]) . '">';
}

function format_text($s)
{
    $a = preg_split('#(<[A-Z]+[^>]*>|</[A-Z]+>)#is', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
    array_walk($a, 'format_text_each');
    $s = implode('', $a);
    $s = preg_replace_callback('[<tex>(.+?)</tex>]s', 'mathtex', $s);
    if ($s == '')
        $s = '&nbsp;';
	return "<p>$s</p>";
}

function format_text_each(&$s) {
    if (preg_match('#^(?:<[A-Z]+[^>]*>|</[A-Z]+>)$#is', $s))
    	return;
	$s = htmlspecialchars($s);
	$s = str_replace(
		array("\r\n", "\n",   '  ',      "\t"),
		array('<br>', '<br>', ' &nbsp;', ' &nbsp; &nbsp;'),
	   	$s);
	$s = preg_replace('{\bhttps?://[^\s<]+}', '<a target="_blank" href="$0">$0</a>', $s);
	//$s = "<p>$s</p>";
}

function is_whitespace($s)
{
	return ! preg_match('/\S/u', $s);
}

function encode_filename($s)
{
	return strtr(base64_encode($s), '/', '_');
}

function decode_filename($s)
{
	return base64_decode(strtr($s, '_', '/'));
}
