<?php
$this->layout = false;
//if($my->userid == "") exit();
header('Content-Type: application/rss+xml; charset=utf-8');
echo '<rss version="2.0">',
	'<channel><title>yutar.net</title>',
	'<link>http://www.yutar.net/</link>',
	'<description>yutar.net</description>';
foreach ($threads as $t) {
	echo '<item><title>[', h(forum_name($t->fid)), '] ',
			h($t->subject), '</title>',
		"<link>http://www.yutar.net/threads/show/$t->tid</link>",
		'<description>', h(format_text($t->message)), '&lt;hr&gt;&lt;dl&gt;';
	foreach ((array)$messages[$t->tid] as $m)
		echo '&lt;dt&gt;', h(h($m->userid)), ' &lt;small&gt;(',
			date('r', $m->created), ')&lt;/small&gt;&lt;/dt&gt;&lt;dd&gt;',
			h(format_text($m->message)), '&lt;/dd&gt;';
	echo '&lt;/dl&gt;</description>',
		'<author>', h($t->userid), '</author>',
		'<pubDate>', date('r', $t->created), '</pubDate>',
		'<category>', h(forum_name($t->fid)), '</category></item>';
}
echo '</channel></rss>';
