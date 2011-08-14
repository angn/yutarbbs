var KEY = {};

document.onkeypress = function(e) {
	e = e || event;
	if (!(/INPUT|TEXTAREA|SELECT/.test((e.target || e.srcElement).tagName) ||
			e.shiftKey || e.altKey || e.ctrlKey || e.metaKey)) {
		var c = String.fromCharCode(e.keyCode || e.which);
		var a = document.links;
		for (var i = 0, n = a.length; i < n; i++) {				
			if (a[i].accessKey == c) {
				location.hash = '#'; // Webkit bug
				location = a[i].href;
				return;
			}
		}
		KEY[c] && KEY[c]();
	}
};

var G;

KEY.g = function f() {
    if (G)
        location.href = '/gateway';
    else
        G = 1, setTimeout(function() { G = 0; }, 250);
};

var N = -1;

function cursorby(d) {
    var c = document.getElementById('list');
    if (c) {
        var a = c.getElementsByTagName('a');
        N = Math.max(0, Math.min(a.length - 1, N + d));
        a[N].focus();
    } else {
        scrollBy(0, d * 60);
    }
}

KEY.j = function() { cursorby(1); };
KEY.k = function() { cursorby(-1); };

KEY.o = function() {
    if (document.activeElement.href)
        location.href = document.activeElement.href;
};

KEY.u = function() { history.back(); };

KEY.m = function() {
    document.forms.comment.elements.message.focus();
};

function add1s(n) {
	var f = document.forms.comment.elements.message;
	f.value = Array(n + 1).join('1') + ' ';
	f.select();
	f.focus();
    f.scrollIntoView();
	return false;
}

function follow1s(n, s) {
	var x = n - s.length;
    location.hash = '#comment' + x;
    return false;
}

function validate(t) {
    if (confirm('아 정말요?'))
        location.href = t.href + '?y=1';
    return false;
}
