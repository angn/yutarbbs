(function() {
    var N = -1;

    function map(a, f) {
        for (var i = 0, n = a.length; i < n; i++)
            f(a[i], i);
    }

    function offset(m) {
        var a = [0, 0];
        do {
            a[0] += m.offsetLeft;
            a[1] += m.offsetTop;
        } while (m = m.offsetParent);
        return a;
    }

    function move_cursor_by(d) {
        var a = document.getElementById('threads').getElementsByTagName('TR');
        if (0 <= N && N < a.length)
            var m = a[N];
        if (m) m.className = m.className.replace(/\bfc\b/g, '');
        a[N = Math.max(0, Math.min(a.length - 1, N + d))].className += ' fc';
    }

    KEY_FUNC.j = function() {
        move_cursor_by(1);
    }

    KEY_FUNC.k = function() {
        move_cursor_by(-1);
    }

    KEY_FUNC[';'] = function() {
        var a = document.getElementById('threads').getElementsByTagName('TR');
        var m = a[N];
        if (m) location = m.getElementsByTagName('A')[0].href;
    }

    function rowClick(e) {
        e = e || event;
        //if (e.srcElement.tagName != 'A')
        if ((e.target || e.srcElement).tagName != 'A')
            location = this.getElementsByTagName('A')[0].href;
    }
            
    function rowMouseOver(e) {
        this.className += ' fc';
    }

    function rowMouseOut(e) {
        this.className = this.className.replace(/\bfc\b/g, '');
    }

    window.onload = function(e) {
        map(document.getElementById('threads').getElementsByTagName('TR'), function(m) {
            m.onclick = rowClick;
            m.onmouseenter = rowMouseOver;
            m.onmouseleave = rowMouseOut;
        });
    };
})();
