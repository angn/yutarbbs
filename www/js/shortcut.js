var KEY_FUNC = {
	g: function() {
		var f = arguments.callee
		if (f.x)
			location = '/users/logout'
		else {
			setTimeout(function() { delete f.x }, 250);
			f.x = true
		}
	}
}
document.onkeypress = function(e) {
	e = e || event
	if (!(/INPUT|TEXTAREA|SELECT/.test((e.target || e.srcElement).tagName) ||
			e.shiftKey || e.altKey || e.ctrlKey || e.metaKey)) {
		var c = String.fromCharCode(e.keyCode || e.which)
		var a = document.links
		for (var i = 0, n = a.length; i < n; i++) {				
			if (a[i].accessKey == c) {
				location.hash = '#'; // Webkit bug
				location = a[i].href
				return
			}
		}
		if (KEY_FUNC[c]) {
			KEY_FUNC[c](e)
		}
	}
}
