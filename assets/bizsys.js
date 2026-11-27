/* Business Systems page (templates/business-systems-body.php), after home10.js.
   home10.js already runs the scroll reveals, the heading word reveal, the
   count-up and the progress bar. This adds the four things particular to the
   page; each one is optional, and the page reads in full without it. */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var hasIO = 'IntersectionObserver' in window;

  /* ---------- 01 the fingerprint: ridges draw in once; the dot rides the workflow ridge ---------- */
  var print = $('.bz-print');
  if (print) {
    var svg = $('svg', print);
    var canPause = svg && typeof svg.pauseAnimations === 'function';
    if (reduce) {
      if (canPause) svg.pauseAnimations();
      print.classList.add('is-drawn');
    } else {
      requestAnimationFrame(function () { requestAnimationFrame(function () { print.classList.add('is-drawn'); }); });
      // the dot only moves while the hero is on screen
      if (canPause && hasIO) {
        new IntersectionObserver(function (es) {
          if (es[0].isIntersecting) svg.unpauseAnimations(); else svg.pauseAnimations();
        }).observe(print);
      }
    }
  }

  /* ---------- 03 the app window: the capability list is a set of tabs ---------- */
  var tour = $('[data-bz-tour]');
  if (tour) {
    var tabs = $$('[role="tab"]', tour);
    var select = function (tab, focus) {
      tabs.forEach(function (t) {
        var on = t === tab;
        t.setAttribute('aria-selected', on ? 'true' : 'false');
        t.tabIndex = on ? 0 : -1;
        var panel = document.getElementById(t.getAttribute('aria-controls'));
        if (panel) panel.classList.toggle('is-on', on);
      });
      if (focus) tab.focus();
      // on phones the list scrolls sideways: keep the chosen one in view
      if (tab.parentNode.scrollWidth > tab.parentNode.clientWidth) {
        tab.parentNode.scrollTo({ left: tab.offsetLeft - 16, behavior: reduce ? 'auto' : 'smooth' });
      }
    };
    tabs.forEach(function (t, i) {
      t.addEventListener('click', function () { select(t); });
      t.addEventListener('keydown', function (e) {
        var to = { ArrowDown: i + 1, ArrowRight: i + 1, ArrowUp: i - 1, ArrowLeft: i - 1, Home: 0, End: tabs.length - 1 }[e.key];
        if (to === undefined) return;
        e.preventDefault();
        select(tabs[(to + tabs.length) % tabs.length], true);
      });
    });
  }

  /* ---------- 04 the department ring: point at one to light what it shares; click keeps it ---------- */
  var ring = $('[data-bz-ring]');
  if (ring) {
    var depts = $$('[data-dept]', ring);
    var chords = $$('.bz-ring__chord', ring);
    var pts = $$('.bz-ring__pt', ring);
    var cap = $('[data-bz-cap]', ring);
    var base = cap ? cap.textContent : '';
    var pinned = null;
    var show = function (btn) {
      var key = btn ? btn.getAttribute('data-dept') : '';
      var rel = btn ? btn.getAttribute('data-rel').split(' ') : [];
      ring.classList.toggle('is-active', !!btn);
      depts.forEach(function (b) {
        var k = b.getAttribute('data-dept');
        b.classList.toggle('is-on', k === key);
        b.classList.toggle('is-rel', rel.indexOf(k) > -1);
        b.setAttribute('aria-pressed', b === pinned ? 'true' : 'false');
      });
      chords.forEach(function (c) {
        c.classList.toggle('is-on', !!key && (c.getAttribute('data-a') === key || c.getAttribute('data-b') === key));
      });
      pts.forEach(function (p) {
        var k = p.getAttribute('data-k');
        p.classList.toggle('is-on', k === key);
        p.classList.toggle('is-rel', rel.indexOf(k) > -1);
      });
      if (cap) cap.textContent = btn ? btn.getAttribute('data-cap') : base;
    };
    depts.forEach(function (b) {
      b.addEventListener('mouseenter', function () { show(b); });
      b.addEventListener('focus', function () { show(b); });
      b.addEventListener('mouseleave', function () { show(pinned); });
      b.addEventListener('blur', function () { show(pinned); });
      b.addEventListener('click', function () {
        pinned = pinned === b ? null : b;
        show(pinned || b);
      });
    });
  }

  /* ---------- 08 composer: a prompt fills the message; the chip itself opens the booking calendar ---------- */
  var msg = $('[data-bz-msg]');
  if (msg) {
    var first = msg.textContent;
    var fill = function (text) {
      msg.classList.add('is-swap');
      setTimeout(function () {
        msg.textContent = text ? 'Hi Drawlead, ' + text.charAt(0).toLowerCase() + text.slice(1) + '.' : first;
        msg.classList.remove('is-swap');
      }, reduce ? 0 : 150);
    };
    $$('.bz-compose__chip').forEach(function (c) {
      c.addEventListener('mouseenter', function () { fill(c.getAttribute('data-prompt')); });
      c.addEventListener('focus', function () { fill(c.getAttribute('data-prompt')); });
    });
  }
})();
