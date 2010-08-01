<?php

function forum_name($n)
{
	static $a = array(0, 공지, 자유게시판, 학술, PS, 유타닷넷, 운영, 소모임, 질문·토론, 진로, 테크);
	return $a[$n];
}

function h2($s)
{
	return '<h2>' . h($s) . '</h2>';
}

function is_forum_updated()
{
	/*
	$a = cache(is_forum_updated);
	if (rand() & 0xFF == 0) {
		*/
		$a = array();
		$rs = fetch_all('fid, MAX(created_at) > NOW() - INTERVAL 1 DAY t FROM threads GROUP BY fid');
		foreach ($rs as $r)
			if ($r->t)
				$a[$r->fid] = '<em>*</em>';
		$rs = fetch_all('fid, MAX(messages.created_at) > NOW() - INTERVAL 1 DAY t FROM messages INNER JOIN threads USING (tid) GROUP BY fid');
		foreach ($rs as $r)
			if ($r->t)
				$a[$r->fid] = '<em>*</em>';
		/*
		cache(is_forum_updated, $a);
	}
	*/
	return $a;
}

function replace_emoticons($s)
{
	$a = cache(emoticon_list);
	if ($a === false) {
		$a = array();
		foreach (glob('./www/emo/?*.?*', GLOB_NOSORT) as $p) {
			list( , , , $img_attr) = @getimagesize($p);
            if ($img_attr) {
                $f = preg_replace('#^.*/#', '', $p);
                $n = preg_replace('/\.[^.]*$/', '', $f);
                $a["@$n@"] = sprintf('<img src="%s" %s alt="%s">', u(emo, $f), $img_attr, h($n));
            }
		}
		cache(emoticon_list, $a);
	}
	return strtr($s, $a);
}

function require_login()
{
	if (!$_SESSION[uid])
		redirect_to(url(users, login), array(forward_to => $_SERVER[REQUEST_URI]));
}

