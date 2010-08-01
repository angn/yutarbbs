<?php

class Users
{
	function Users($action)
	{
		$action == login or require_login();
	}

	function index()
	{
		$this->users = fetch_all('year, name, email, phone, website, remark FROM users ORDER BY year DESC, name');
	}

	function edit()
	{
		$this->user = fetch_one('uid, userid, year, name, email, phone, website, remark, UNIX_TIMESTAMP(updated_on) updated FROM users WHERE uid = ?', $_SESSION[uid]);
	}

	function update()
	{
		update(users, array(updated_on => (object)'NOW()') + $_POST, array('uid = ?', $_POST[uid]), 1);
		redirect_back();
	}

	function update_passwd()
	{
		$passwd = $_POST[passwd];
		if ($passwd && $passwd == $_POST[passwd2]) {
			$passwd = $this->hash_passwd($passwd);
			flash(update(users, compact(passwd), array('uid = ?', $_POST[uid]), 1) ?
				  '변경됨.' : '변경 실패!');
		} else {
			flash('암호 이상해!');
		}
		redirect_back();
	}

	function hash_passwd($s)
	{
		return md5("gkfd{$s}gkfd");
	}

	function login()
	{
		if ($_POST) return $this->_login();
	}

	function _login()
	{
		$user = fetch_one('uid, year, name, userid, updated_on + INTERVAL 3 MONTH < NOW() outdated FROM users WHERE userid = ? AND passwd = ? LIMIT 1', $_POST[userid], $this->hash_passwd($_POST[passwd]));
		$_POST[persistent] and setcookie(persistent, 1, 0, '/');
		if ($user) {
			foreach ((array)$user as $k => $v)
				$k != outdated and $_SESSION[$k] = $v;
			sess_write();
			$user->outdated and redirect_to(url(users, edit));
			$_POST[forward_to] and redirect_to($_POST[forward_to]);
		}
		redirect_back();
	}

	function logout()
	{
		sess_destroy();
		setcookie(persistent, 1, time(), '/');
		redirect_to(url());
	}
	
	function elsewhere()
	{
		/*
		$this->users = fetch_all('year, name, website FROM users WHERE website != ""');
		function_exists(curl_init) or trigger_error('No CURL support.', E_USER_ERROR);
		$curl = curl_init();
		curl_setopt(CURLOPT_HEADER, false);
		curl_setopt(CURLOPT_RETURNTRANSFER, true);
		foreach ($this->users as $u) {
			strncmp($u->website, 'http://', 7) and $u->website = "http://$u->website";
			curl_setopt(CURLOPT_URL, $u->website);
			if (preg_match('{<link[^>]+type="application/rss\+xml"[^>]+>}i', curl_exec(), $m)) {
				if (preg_match('{(?<=href=")[^"]+(?=")}', $m[0], $t)) {
					$u->rss = $t[0];
				}
			}
		}
		*/
		$this->users = fetch_all('year, name, rss FROM users WHERE rss != ""');
		ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ko; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		foreach ($this->users as $k => $u) {
			$xml = new SimpleXMLElement(file_get_contents($u->rss));
			$this->users[$k]->title = $xml->channel->title;
			$this->users[$k]->item = $xml->channel->item;
		}
	}
}
