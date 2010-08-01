<?php

error_reporting(E_ALL ^ E_NOTICE);

function _error_handler($type, $str, $file, $line, $context)
{
	if (error_reporting() & $type) {
		header('Internal Server Error', true, 500);
		header('Content-Type: text/plain');
		echo "$str\n\n";
		foreach ($context as $k => $v) {
			echo "\$$k : ";
		   	var_export($v);
			echo "\n";
		}
		echo "\n";
		foreach (array_slice(debug_backtrace(), 1) as $f) {
			$f = (object)$f;
			echo "$f->file ($f->line): $f->class$f->type$f->function ( ";
			echo implode(', ', map('is_scalar($_[0])?$_[0]:"*".gettype($_[0])', $f->args));
			echo " )\n";
		}
		die(1);
	}
	return false;
}

set_error_handler('_error_handler');

function dump($a)
{
	if ($h = fsockopen('udp://127.0.0.1', 9999)) {
		fwrite($h, print_r($a, true));
		fclose($h);
	}
}
