<?php

class View
{
	var $layout = 'default';
	var $title = 'yutar.net';
	var $heads = array();

	function View($controller, $action)
	{
		$this->controller = $controller;
		$this->action     = $action;
	}

	function render()
	{
		$_SESSION and $my = (object)$_SESSION;
		extract(func_get_arg(0));
		ob_start();
		header('Content-Type: text/html');
		(@include "./app/$this->controller/$this->action.php") or $this->dump_and_die(func_get_arg(0));
		$_ = ob_get_clean();
		$this->layout and (require "./app/layouts/$this->layout.php") or print $_;
	}

	function dump_and_die($a)
	{
		header('Not Found', true, 404);
		header('Content-Type: text/plain');
		echo "Not Found\n";
		foreach ($a as $k => $v) {
			echo "\n\$$k : ";
			var_export($v);
		}
		die(1);
	}

	function meta($s = null)
	{
		static $a = '';
		if (!$s)
			return $a;
		$a .= $s;
	}

	function js($u)
	{
		$this->meta('<script src="' . h($u) . '"></script>');
	}

	function rss($u)
	{
		$this->meta('<link rel="alternate" type="application/rss+xml" href="' . h($u) . '">');
	}
}
