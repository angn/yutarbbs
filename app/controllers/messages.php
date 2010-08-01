<?php

class Messages
{
	function Messages()
	{
		require_login();
	}
	
	function create()
	{
		insert(messages, array(created_at => (object)'NOW()', uid => $_SESSION['uid']) + $_POST);
		redirect_back();
	}
	
	function duplicate()
	{
		$mid = intval($_POST['mid']);
		$m = fetch_one('tid, message FROM messages WHERE mid = ? LIMIT 1', $mid);
		insert(messages, array(created_at => (object)'NOW()', uid => $_SESSION['uid']) + (array)$m);
		redirect_back();
	}
	
	function delete($mid)
	{
		// isset($_GET['y']) and update(messages, array(message => ''), array('mid = ? AND uid = ?', $mid, $_SESSION['uid']), 1);
		isset($_GET['y']) and delete(messages, array('mid = ? AND uid = ?', $mid, $_SESSION['uid']), 1);
		redirect_back();
	}
}
