<?php

class Emoticons
{
	function Emoticons()
	{
		require_login();
	}

	function path($s)
	{
		return "./www/emo/$s";
	}

	function index()
	{
		$a = glob($this->path('*'));
		$this->emoticons = $a ?
			array_combine(map('substr($a,0,-4)', array_map(filename, $a)), map('$a[3]', array_map(getimagesize, $a))) :
			$a;
	}

	function create()
	{
		clear_cache(emoticon_list);
		if ($tmp_name = $_FILES[emoticon][tmp_name]) {
			$EXTS = array(IMAGETYPE_GIF => 'gif', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png');
			$ext = $EXTS[nth(2, getimagesize($tmp_name))]
				and filesize($tmp_name) <= 100 << 10
				and move_uploaded_file($tmp_name, $this->path("{$_POST[name]}.$ext"));
		}
		redirect_back();
	}

	function delete($name)
	{
		array_sum(array_map(unlink, glob($this->path("$name.*"))))
			and redirect_back()
			or not_found();
	}
}
