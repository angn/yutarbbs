<?= h2(회원명부) ?>

<form onsubmit="return false">
	<p class="user-search"><label for="search">검색&nbsp;</label><input id="search" type="search" accesskey="f" incremental="incremental" placeholder="검색" onsearch="search(this.value)" onkeyup="this.type == 'search' || delayed_search(this.value)"></p>
</form>

<script>
function map(a, f) {
	for (var i = 0, n = a.length; i < n; i++)
		f(a[i], i)
}
function search(s) {
	map(document.getElementById('users').tBodies[0].rows,
		s ? function(r) { r.className = (r.textContent || r.innerText).indexOf(s) >= 0? 'hl' : 'fd' } :
			function(r) { r.className = '' })
}
function delayed_search(s) {
	var f = arguments.callee
	if (f.s != s) {
		f.s = s
		f.t && clearTimeout(f.t)
		f.t = setTimeout(function () { search(s) }, 250)
	}
}
</script>

<table id="users" class="users">
	<col style="font: 92% Tahoma, Arial, sans-serif"> 
	<col>
	<col span="3" style="font: 92% Tahoma, Arial, sans-serif"> 
	<col width="200" style="font-family: 돋움, Dotum, Tahoma, Arial, sans-serif; white-space: nowrap"> 
	<thead><tr>
		<td>학번</td>
		<td>이름</td>
		<td>전화번호</td>
		<td>E-mail</td>
		<td>웹사이트</td>
		<td>기타</td>
	</tr></thead>
	<tbody><?php foreach ($users as $u): ?><tr class="<?php $u->year & 1 and print 'a' ?>">
		<td><?= h($u->year) ?></td>
		<td><?= h($u->name) ?></td>
		<td><?= h(format_phonenumber($u->phone)) ?></td>
		<td class="email"><a href="mailto:<?= h($u->email) ?>"><?= h($u->email) ?></a></td>
		<td class="website"><?php if ($u->website): ?><a href="<?= h(strpos($u->website, '://') !== false ? $u->website : "http://$u->website") ?>"><?= h($u->website) ?></a><?php endif ?></td>
		<td><?php if ($u->remark): ?><?= nl2br(h($u->remark)) ?><?php endif ?></td>
	</tr><?php endforeach ?></tbody>
</table>
