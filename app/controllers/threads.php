<?php

class Threads
{
	function Threads($action)
	{
		$action == rss or require_login();
	}

	function issue($fid)
	{
		$this->fid = $fid;
		$this->page = max(1, $_GET['p']);
		$this->max_page = of(n, fetch_one('GREATEST(1, CEILING(COUNT(*) / 15)) n FROM threads WHERE fid = ? LIMIT 1', $fid));
		$this->threads = fetch_all('tid, subject, year, name, UNIX_TIMESTAMP(created_at) created, hits, (SELECT COUNT(*) FROM messages WHERE tid = threads.tid) replies, (SELECT MAX(created_at) + INTERVAL 1 DAY > NOW() FROM messages WHERE tid = threads.tid) updated, (SELECT MAX(created_at) FROM messages WHERE tid = threads.tid) last_reply_at FROM threads INNER JOIN users USING (uid) WHERE fid = ? ORDER BY IFNULL(last_reply_at, created_at) DESC LIMIT ?, 15', $fid, $this->page * 15 - 15);
	}

	function index($fid)
	{
		$this->fid = $fid;
		$this->page = max(1, $_GET['p']);
		$this->max_page = of(n, fetch_one('GREATEST(1, CEILING(COUNT(*) / 15)) n FROM threads WHERE fid = ? LIMIT 1', $fid));
		if ($this->keyword = $_POST['keyword']) {
			$this->threads = fetch_all('tid, subject, year, name, UNIX_TIMESTAMP(created_at) created, hits, attachment FROM threads INNER JOIN users USING (uid) WHERE fid = ? AND (UPPER(name) = UPPER(?) OR INSTR(UPPER(subject), UPPER(?)) OR INSTR(UPPER(message), UPPER(?))) ORDER BY created_at DESC', $fid, $this->keyword, $this->keyword, $this->keyword);
		} else {
			$this->threads = fetch_all('tid, subject, year, name, UNIX_TIMESTAMP(created_at) created, hits, attachment FROM threads INNER JOIN users USING (uid) WHERE fid = ? ORDER BY created_at DESC LIMIT ?, 15', $fid, $this->page * 15 - 15);
		}
		$this->info = array();
		if ($this->threads) {
			$rs = fetch_all('tid, COUNT(tid) replies, MAX(created_at) + INTERVAL 1 DAY > NOW() updated FROM messages WHERE tid IN ? GROUP BY tid', map('$a->tid', $this->threads));
			foreach ($rs as $r)
				$this->info[$r->tid] = $r;
		}
	}

	function add($fid)
	{
		$this->fid = $fid;
		$this->categories = fetch_all('cid, label FROM categories WHERE fid = ?', $fid);
	}

	function get_attachment_path($tid, $name)
	{
		return "./www/attachments/$tid-$name";
	}
	
	function upload($tid, $file)
	{
		return !is_uploaded_file($file[tmp_name]) || @move_uploaded_file($file[tmp_name], $this->get_attachment_path($tid, $file[name]));
	}

	function create()
	{
		if (!is_whitespace($_POST[subject])) {
			$tid = insert(threads, array(created_at => (object)'NOW()', uid => $_SESSION[uid]) + $_POST + array(attachment => strval($_FILES[attachment][name])))
				and $this->upload($tid, $_FILES[attachment])
				and redirect_to(url(threads, show, $tid));
		}
		no_content();
	}

	function inc_hit($tid)
	{
		if ($_COOKIE[lasttid] != $tid) {
			update(threads, 'hits = hits + 1', array('tid = ? AND uid != ?', $tid, $_SESSION[uid]), 1);
			setcookie(lasttid, $tid);
		}
	}

	function show($tid)
	{
		$this->inc_hit($tid);

		$this->thread = fetch_one('tid, fid, subject, t.uid, year, name, email, website, remark, message, UNIX_TIMESTAMP(created_at) created, attachment FROM threads t INNER JOIN users USING (uid) WHERE tid = ? LIMIT 1', $tid) or not_found();
		$this->messages = fetch_all('mid, message, year, name, UNIX_TIMESTAMP(created_at) created FROM messages INNER JOIN users USING (uid) WHERE tid = ? ORDER BY created_at', $tid);
		$this->attachment = array();
		if ($this->thread->attachment) {
			$path = $this->get_attachment_path($this->thread->tid, $this->thread->attachment);
			$this->attachment += array(size => @filesize($path));
			$ext = strtolower(substr($this->thread->attachment, -4));
			if (in_array($ext, array('.jpg', '.gif', '.png'))) {
				if ($info = @getimagesize($path))
					if ($info[0] > 400)
						$info[3] = sprintf('width="400" height="%d"', $info[1] * 400 / $info[0]);
				$this->attachment += array(
					img_width => $info[0],
					img_height => $info[1],
					img_attr => $info[3],
				);
			} /* elseif ($ext == '.zip') {
				if (is_resource($zip = @zip_open($path))) {
					$zip_entries = array();
					while ($entry = zip_read($zip))
						$zip_entries[] = (object)array( name => zip_entry_name($entry), size => zip_entry_filesize($entry) );
					zip_close($zip);
					$this->attachment += compact(zip_entries);
				}
			} */ elseif (preg_match('{^text/(?:x-)?([^;]+)}', mime_type($path), $m)) {
				#$FILTER = array(c => intval, 'c++' => intval);
				$filter = $FILTER[$m[1]] or $filter = h;
				$this->attachment += array(html => $filter(file_get_contents($path)));
			}
		}
	}

	function show_message($tid)
	{
		$this->thread = fetch_one('message FROM threads WHERE tid = ? LIMIT 1', $tid);
	}

	function edit($tid)
	{
		$this->thread = fetch_one('tid, fid, subject, message, UNIX_TIMESTAMP(created_at) created FROM threads INNER JOIN users USING (uid) WHERE tid = ? LIMIT 1', $tid);
	}

	function update()
	{
		update(threads, $_POST, array('tid = ? AND uid = ?', $_POST[tid], $_SESSION['uid']), 1); 
		if ($_FILES[attachment][name]) {
            update(threads, array(attachment => $_FILES[attachment][name]), array('tid = ? AND uid = ?', $_POST[tid], $_SESSION['uid']), 1);
            $this->upload($_POST[tid], $_FILES[attachment]);
        }
        
        /*
        
		update(threads, $_POST + array(attachment => $_FILES[attachment][name]), array('tid = ? AND uid = ?', $_POST[tid], $_SESSION['uid']), 1) and
			$this->upload($_POST[tid], $_FILES[attachment]);
        */
		redirect_to(url(threads, show, $_POST[tid]));
	}

	function delete($tid)
	{
		$thread = fetch_one('fid FROM threads WHERE tid = ? LIMIT 1', $tid);
		if (delete('threads', array('tid = ? AND uid = ?', $tid, $_SESSION['uid']), 1))
			redirect_to(url(threads, index, $thread->fid));
		no_content();
	}

	function announce($tid)
	{
		// @insert(notices, compact(tid));
		update(registry, compact(tid), 1);
		redirect_to(url());
	}

	function conceal($tid)
	{
		// delete(notices, compact(tid), 1);
		redirect_back();
	}

	function rss()
	{
		$this->threads = fetch_all('fid, tid, subject, userid, UNIX_TIMESTAMP(created_at) created, message FROM threads INNER JOIN users USING (uid) ORDER BY created_at DESC LIMIT 20');
		$rs = fetch_all('tid, message, userid, UNIX_TIMESTAMP(created_at) created FROM messages INNER JOIN users USING (uid) WHERE tid IN ? ORDER BY created_at',
		 	map('$a->tid', $this->threads));
		foreach ($rs as $r)
			$this->messages[$r->tid][] = $r;
	}
}
