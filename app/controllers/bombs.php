<?php

class Bombs
{
	function Bombs()
	{
		require_login();
	}

	function path($s)
	{
		return "./www/bombs/$s";
	}

	function index()
	{
		$a = glob($this->path('*'), GLOB_NOSORT);
		/*
		$this->bombs = (array)@array_combine(
			$a, map('('.time().'-$a)/86400', array_map(filemtime, $a)));
		*/
		$this->bombs = (array)@array_combine(
			$a, array_map(filemtime, $a));
		asort($this->bombs);
		array_map(unlink, array_keys(select(time().'-$a>786400', $this->bombs)));
	}

	function create()
	{
		move_uploaded_file($_FILES[bomb][tmp_name], $this->path($_FILES[bomb][name]));
		redirect_back();
	}
}
