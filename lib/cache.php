<?php

function cache($s, $a = null)
{
	$p = '/tmp/cache.' . encode_filename($s) . '.dat';
	if (is_null($a))
		return @unserialize(file_get_contents($p));
	if ($h = fopen($p, 'w')) {
		fwrite($h, serialize($a));
		fclose($h);
	}
	@chmod($p, 0666);
}

function clear_cache($s)
{
	unlink('/tmp/cache.' . encode_filename($s) . '.dat');
}
