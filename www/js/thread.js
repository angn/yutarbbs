function slideBy(dx, dy) {
	var f = arguments.callee
	clearTimeout(f.t || 0)
	if (dx || dy) {
		f.dx = (f.dx || 0) + dx
		f.dy = (f.dy || 0) + dy
	}
	if (f.dx || f.dy) {
		scrollBy(f.dx - (f.dx >> 1),
		         f.dy - (f.dy >> 1))
		f.dx >>= 1
		f.dy >>= 1
		f.t = setTimeout(f, 33)
	}
}

KEY_FUNC.j    = function() { slideBy(0,  256) }
KEY_FUNC.k    = function() { slideBy(0, -256) }
KEY_FUNC[';'] = function() { history.back() }
KEY_FUNC.m    = function() { document.forms.comment.elements.message.focus() }

function $(s) { return document.getElementById(s) }

function zoom() {
	$('code').previousSibling.className += ' zoom'
	$('unzoom').style.display = ''
}

function unzoom() {
	var e = $('code').previousSibling
	e.className = e.className.replace(/ zoom/g, '')
	$('unzoom').style.display = 'none'
}

function add1s(n) {
	var a = new Array(n);
	while (n--)
		a[n] = '1';
	var f = document.forms.comment.elements.message;
	f.value = a.join('') + ' ';
	f.select();
	f.focus();
	return false;
}

function repost(n) {
	var m = document.forms.dup;
	m.elements.mid.value = n;
	m.submit();
	return false;
}
