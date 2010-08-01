<?= h2(이모티콘) ?>

<p class="notice">이모티콘 사용법: 댓글에서 @(이모티콘이름)@</p>

<div style="display: none"><span><img name="emoticon" src="" alt="이모티콘"></span></div>

<ul id="emoticons" class="emoticons">
<?php foreach ($emoticons as $p => $a): ?>
	<li><a href="<?= u(emo, $p) ?>" src="<?= u(emo, $p) ?>.jpg"><?= h($p) ?></a>
		<a class="delete" href="<?= u(emoticons, delete, $p) ?>">&times;</a>
<?php endforeach ?>
</ul>

<form method="post" action="<?= u(emoticons, create) ?>" enctype="multipart/form-data">
	<p><label for="name">이름&nbsp;</label><input type="text" id="name" name="name" size="35"></p>
	<p><input type="file" name="emoticon" size="30"></p>
	<p class="submit"><input type="submit" value="&uarr;"></p>
	<p><em>(!)</em> GIF, JPEG, PNG 형식의 100KB 미만의 그림만 받아요.</p>
</form>

<script>
(function() {
	var a = document.getElementById('emoticons').getElementsByTagName('A')
	for (var i = 0, n = a.length; i < n; i++) {
		var o = a[i]
		switch (o.className) {
		case '':
			o.onmouseover = function() {
				var m = document.images.emoticon
				m.style.display = ''
				m.src = this.getAttribute('src')
				m.alt = this.textContent || this.innerText
				this.appendChild(m.parentNode).style.display = ''
			}
			o.onmouseout = function() {
				document.images.emoticon.parentNode.style.display = 'none'
			}
			break
		case 'delete':
			o.onclick = function() { return confirm('아 정말요?') }
			break
		}
	}
})()
</script>
