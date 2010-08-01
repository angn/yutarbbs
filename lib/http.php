<?php

function is_get()
{
	return $_SERVER[REQUEST_METHOD] == 'GET';
}

function not_found()
{
	header('Not Found', true, 404);
	header('Content-Type: text/plain');
	echo 'Not Found';
	die(0);
}

function no_content()
{
	header('No Content', true, 204);
	die(0);
}

function redirect_to($url, $queries = null)
{
	$url or no_content();
	$queries and $url .= '?' . http_build_query($queries);
	header("Location: $url");
	die(0);
}

function redirect_back()
{
	redirect_to($_SERVER[HTTP_REFERER]);
}

function forbidden()
{
	header('Forbidden', true, 403);
}

function last_modified($t)
{
	is_string($t) and $t = strtotime($t);
	header('Last-Modified: ' . gmdate('r', $t));
}

function cache_control_max_age($t)
{
	header('Cache-Control: max-age=' . $t);
}

function mime_type($p)
{
	return shell_exec(sprintf('file -bi %s', escapeshellarg($p)));
}
