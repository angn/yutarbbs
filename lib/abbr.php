<?php

function f($c)
{
	return create_function('$a=null,$b=null', "\$_=func_get_args();return $c;");
}

function h($s)
{
	return htmlspecialchars($s);
}

function map($c)
{
	$a = func_get_args();
	return call_user_func_array(array_map, array(f($c)) + $a);
}

function url()
{
	$a = func_get_args();
	return http . ($_SERVER[HTTPS] ? s : '') . '://' . $_SERVER[HTTP_HOST] . '/' . implode('/', array_map(rawurlencode, $a));
}

function u()
{
	$a = func_get_args();
	return '/' . implode('/', array_map(rawurlencode, $a));
}

function select($c, $a)
{
	return array_filter($a, f($c));
}

function inject($c, $a, $n)
{
	foreach ($a as $v)
		$n = $c($n, $v);
	return $n;
}

function nth($n, $a)
{
	return $a[$n];
}

function id($a)
{
	return $a;
}

function filename($p)
{
	return substr(strrchr($p, '/'), 1);
}

function of($k, $o)
{
	return $o->$k;
}
