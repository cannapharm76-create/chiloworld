/* Cannaverse front - small vanilla helpers replacing the original React runtime */
(function () {
    var root = document.documentElement;
    var SITE = window.SITE || {root: '/', assets: '/'};

    /* viewport css vars used by the compiled stylesheet */
    function setVars() {
        root.style.setProperty('--vw', document.documentElement.clientWidth + 'px');
        root.style.setProperty('--vhfix', window.innerHeight + 'px');
        root.style.setProperty('--fithvr', '2px');
    }
    setVars();
    root.style.setProperty('--vhfixonce', window.innerHeight + 'px');
    window.addEventListener('resize', setVars);

    function store(k, v) {
        try { if (v === undefined) return localStorage.getItem(k); localStorage.setItem(k, v); } catch (e) { return null; }
    }

    /* active nav item */
    var here = location.pathname.replace(/\/+$/, '');
    document.querySelectorAll('.cv-toolbar__link, .cv-toolbar__claim, .cv-drawer a').forEach(function (a) {
        var p = (a.pathname || '').replace(/\/+$/, '');
        if (p && p === here) a.classList.add('is-selected');
    });

    /* cookie notice */
    var cookie = document.querySelector('aside[aria-label="Cookie acknowledgement"]');
    if (cookie) {
        if (store('cv_cookie_ok')) cookie.remove();
        else cookie.querySelector('button').addEventListener('click', function () {
            store('cv_cookie_ok', '1');
            cookie.remove();
        });
    }

    /* mobile drawer (hamburger) */
    var links = [
        ['Home', ''], ['Products', 'products'], ['Brands', 'brands'], ['Strain index', 'strain-index'], ['Cannabis map', 'cannabis-map'], ['Our method', '#method'], ['Reviews', 'reviews'],
        ['Shop', 'shop'], ['Leaderboard', 'leaderboard'], ['About', 'about'],
        ['Claim Rewards', 'redeem'], ['Log in', 'login'], ['Sign up', 'signup']
    ];
    var drawer = document.createElement('div');
    drawer.className = 'cv-drawer';
    drawer.setAttribute('role', 'dialog');
    drawer.setAttribute('aria-label', 'Menu');
    drawer.innerHTML = '<button type="button" class="cv-drawer__close" aria-label="Close menu">&times;</button><nav>' +
        links.map(function (l) { return '<a href="' + SITE.root + l[1] + '">' + l[0] + '</a>'; }).join('') + '</nav>';
    document.body.appendChild(drawer);
    function toggle(open) {
        drawer.classList.toggle('is-open', open);
        document.querySelectorAll('.cv-toolbar__hamburger').forEach(function (b) { b.setAttribute('aria-expanded', open); });
    }
    document.querySelectorAll('.cv-toolbar__hamburger').forEach(function (b) {
        b.addEventListener('click', function () { toggle(!drawer.classList.contains('is-open')); });
    });
    drawer.querySelector('.cv-drawer__close').addEventListener('click', function () { toggle(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') toggle(false); });

    /* notifications button -> login */
    document.querySelectorAll('button[aria-label="Notifications"]').forEach(function (b) {
        b.addEventListener('click', function () { location.href = SITE.root + 'login'; });
    });

    /* forms: post to the Laravel route of the current page */
    var token = (document.querySelector('meta[name="csrf-token"]') || {}).content;
    document.querySelectorAll('form:not([data-local])').forEach(function (f) {
        f.method = 'post';
        f.action = location.pathname;
        var t = document.createElement('input');
        t.type = 'hidden'; t.name = '_token'; t.value = token;
        f.appendChild(t);
        f.querySelectorAll('input:not([name]), textarea:not([name]), select:not([name])').forEach(function (i, n) {
            i.name = i.id || i.getAttribute('autocomplete') || i.type || ('field' + n);
        });
        f.querySelectorAll('[disabled]').forEach(function (el) { if (el.type === 'submit' || el.tagName === 'BUTTON') el.disabled = false; });
    });

    /* videos captured from React lose the muted property -> restore autoplay */
    document.querySelectorAll('video').forEach(function (v) {
        v.muted = true;
        v.playsInline = true;
        if (v.hasAttribute('autoplay')) { var p = v.play(); if (p && p.catch) p.catch(function () {}); }
    });

    /* simple tab groups: role=tablist buttons toggle aria-selected */
    document.querySelectorAll('[role="tablist"]').forEach(function (list) {
        var tabs = list.querySelectorAll('[role="tab"]');
        tabs.forEach(function (t) {
            t.addEventListener('click', function () {
                tabs.forEach(function (o) { o.setAttribute('aria-selected', o === t ? 'true' : 'false'); });
            });
        });
    });

    /* flash message auto hide */
    var flash = document.querySelector('.cv-flash');
    if (flash) setTimeout(function () { flash.classList.add('is-hidden'); }, 5000);
})();
