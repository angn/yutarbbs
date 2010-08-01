<?php

class Main
{
	function index()
	{
		$this->notice = fetch_one('tid, subject, message FROM threads WHERE fid = 1 ORDER BY created_at DESC LIMIT 1');
		$_SESSION and redirect_to(url(threads, show, $this->notice->tid));
	}
}
