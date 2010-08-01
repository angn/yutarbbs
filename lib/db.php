<?php

function quote_into($s)
{
	$a = func_get_args();
	$a = array_map(quotes, array_slice($a, 1));
	return preg_replace('/\?/e', 'array_shift($a)', $s, count($a));
}

function quotes($a)
{
	if (is_int($a) || is_real($a))
		return (string)$a;
	elseif (is_string($a))
		return '"' . addslashes($a) . '"';
	elseif (is_array($a))
		return '(' . implode(',', array_map(quotes, $a)) . ')';
	elseif (is_object($a))
		return (string)$a->scalar;
	return 'NULL';
}

function query($q)
{
	static $C;
	$C or $C = mysql_select_db(DATABASE);
	// mysql_query('SET NAMES utf8');
	$r = mysql_unbuffered_query($q) or trigger_error(mysql_error() . " (SQL: $q)", E_USER_ERROR);
	return $r;
}

function fetch_one($q)
{
	$a = func_get_args();
	return mysql_fetch_object(query('SELECT ' . call_user_func_array(quote_into, $a)));
}

function fetch_all($q)
{
	$a = func_get_args();
	$s = query('SELECT ' . call_user_func_array(quote_into, $a));
	while ($r = mysql_fetch_object($s))
		$t[] = $r;
	return (array)$t;
}

function insert($t, $v)
{
	is_array(reset($v)) or $v = array($v);
	$k = implode(',', array_keys($v[0]));
	$v = implode('),(', map('implode(",",array_map(quotes,$_[0]))', $v));
	query("INSERT INTO $t ($k) VALUES ($v)");
	return mysql_insert_id();
}

function update($t, $s, $w, $n = null)
{
	is_array($s) and $s = implode(',', map('"$_[0]=$_[1]"', array_keys($s), array_map(quotes, array_values($s))));
	is_array($w) and $w = call_user_func_array(quote_into, $w);
	$q = "UPDATE $t SET $s WHERE $w";
	$n and $q .= " LIMIT $n";
	query($q);
	return mysql_affected_rows();
}

function delete($t, $w, $n = null)
{
	is_array($w) and $w = call_user_func_array(quote_into, $w);
	$q = "DELETE FROM $t WHERE $w";
	$n and $q .= " LIMIT $n";
	query($q);
	return mysql_affected_rows();
}

function explain_select($q)
{
	$a = func_get_args();
	$s = query('EXPLAIN SELECT ' . call_user_func_array(quote_into, $a));
	while ($r = mysql_fetch_object($s))
		$t[] = $r;
	return (array)$t;
}

