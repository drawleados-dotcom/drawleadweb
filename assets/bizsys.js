// Business Systems landing page (templates/business-systems-body.php).
// Runs alongside /assets/home10.js (reveals, word-by-word headings, booking
// buttons, footer). Each block below drives one section; all of them leave
// the page complete without JavaScript and stand still for reduced motion.
(function () {
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var hasIO = 'IntersectionObserver' in window;
  var onView = function (el, cb, opts) {
    if (!el) return;
    if (!hasIO) { cb(true); return; }
    new IntersectionObserver(function (es) { cb(es[0].isIntersecting); }, opts || { threshold: .25 }).observe(el);
  };
  var once = function (el, cb, opts) {
    if (!el) return;
    if (!hasIO) { cb(); return; }
    var io = new IntersectionObserver(function (es) { if (es[0].isIntersecting) { io.disconnect(); cb(); } }, opts || { threshold: .3 });
    io.observe(el);
  };
  var rafScroll = function (fn) {
    var raf = 0;
    var q = function () { if (!raf) raf = requestAnimationFrame(function () { raf = 0; fn(); }); };
    addEventListener('scroll', q, { passive: true });
    addEventListener('resize', q);
    fn();
  };

  // hero marker: if the heading was not split into words (preview, no IO), show it anyway
  setTimeout(function () { var h = $('.bsh__h1'); if (h && !h.classList.contains('tr')) h.classList.add('is-in'); }, 400);

  /* ---------- 01 console: boots module by module, then a live ticker ---------- */
  var cs = $('[data-bs-console]');
  if (cs && !reduce) {
    var mods = $$('.cs__mods li', cs), boot = $('[data-bs-boot]', cs), prog = $('.cs__prog', cs);
    var feed = $('[data-bs-feed]', cs), ticks = $$('[data-bs-tick]', cs);
    cs.classList.add('is-booting');
    if (prog) prog.style.setProperty('--bp', 0);
    if (boot) boot.textContent = 'Connecting modules…';
    var lines = ['New lead synced to CRM · Sales', 'Purchase order approved · Procurement', 'Site update logged · Projects', 'Invoice paid · Finance', 'Attendance marked · HR', 'Low stock alert raised · Inventory', 'Follow-up scheduled · CRM', 'Weekly report ready · Management'];
    var li = 0, timer = null;
    var tick = function () {
      if (feed) {
        feed.classList.add('is-swap');
        setTimeout(function () { li = (li + 1) % lines.length; feed.textContent = lines[li]; feed.classList.remove('is-swap'); }, 350);
      }
      if (ticks.length && Math.random() < .7) {
        var el = ticks[Math.floor(Math.random() * ticks.length)], n = parseInt(el.textContent, 10);
        if (!isNaN(n)) el.textContent = n + 1;
      }
    };
    once(cs, function () {
      mods.forEach(function (m, i) {
        setTimeout(function () {
          m.classList.add('is-up');
          if (prog) prog.style.setProperty('--bp', ((i + 1) / mods.length).toFixed(2));
          if (boot) boot.textContent = (i + 1) + ' of ' + mods.length + ' modules connected';
          if (i === mods.length - 1) {
            onView(cs, function (vis) {
              if (vis && !document.hidden) { if (!timer) timer = setInterval(tick, 2600); }
              else { clearInterval(timer); timer = null; }
            }, { threshold: .2 });
          }
        }, 300 + i * 180);
      });
    }, { threshold: .3 });
  }

  /* ---------- 02 chaos to clarity: each problem shakes its tile, then all snap into order ---------- */
  var chaos = $('[data-bs-chaos]');
  if (chaos) {
    var items = $$('.bsp__item', chaos), tiles = $$('.bsp__tile', chaos), stage = $('.bsp__stage', chaos);
    if (reduce) {
      stage.classList.add('is-solved');
    } else {
      var lastCur = -1;
      rafScroll(function () {
        var mid = innerHeight * .5, cur = -1;
        items.forEach(function (it, i) { if (it.getBoundingClientRect().top < mid) cur = i; });
        var lastBox = items[items.length - 1].getBoundingClientRect();
        var solved = lastBox.bottom < mid;
        stage.classList.toggle('is-solved', solved);
        items.forEach(function (it, i) { it.classList.toggle('is-cur', i === cur && !solved); });
        if (cur !== lastCur) {
          tiles.forEach(function (t, i) { t.classList.toggle('is-hit', !solved && i <= cur && cur >= 0); });
          lastCur = cur;
        }
      });
    }
  }

  /* ---------- 03 bento: the AI preview flows while on screen ---------- */
  $$('.bento__card').forEach(function (c) {
    if (!reduce && $('.pv--ai', c)) onView(c, function (v) { c.classList.toggle('is-live', v); });
  });

  /* ---------- 04 department map: pick one to light what it shares ---------- */
  var map = $('[data-bs-map]');
  if (map) {
    var depts = $$('.map__d', map), cap = $('[data-bs-cap]', map), capDefault = cap ? cap.textContent : '';
    var pick = function (btn) {
      var rel = btn ? (btn.dataset.rel || '').split(' ') : [];
      map.classList.toggle('has-pick', !!btn);
      depts.forEach(function (d) {
        d.classList.toggle('is-pick', d === btn);
        d.classList.toggle('is-rel', !!btn && rel.indexOf(d.dataset.dept) > -1);
        d.setAttribute('aria-pressed', d === btn ? 'true' : 'false');
      });
      if (cap) cap.textContent = btn ? btn.dataset.cap : capDefault;
    };
    var hoverable = window.matchMedia && matchMedia('(hover:hover)').matches;
    depts.forEach(function (d) {
      d.setAttribute('aria-pressed', 'false');
      d.addEventListener('click', function () { pick(d.classList.contains('is-pick') ? null : d); });
      d.addEventListener('focus', function () { pick(d); });
      if (hoverable) d.addEventListener('mouseenter', function () { pick(d); });
    });
    if (hoverable) map.addEventListener('mouseleave', function () { pick(null); });
  }

  /* ---------- 05 ticket track: pinned and horizontal on wide, tall screens; stubs tear off ---------- */
  var track = $('[data-bs-track]');
  if (track) {
    var row = $('.tkts__row', track), tkts = $$('.tkt', track), bar = $('.tkts__bar', track);
    var wide = window.matchMedia && matchMedia('(min-width:1100px) and (min-height:640px)');
    var pinned = false;
    var setPin = function () {
      var on = !reduce && wide && wide.matches;
      if (on === pinned) return;
      pinned = on;
      track.classList.toggle('is-pinned', on);
      track.style.height = '';
      if (!on) { row.style.removeProperty('--tx'); tkts.forEach(function (t) { t.classList.remove('is-torn', 'is-cur'); }); }
    };
    var update = function () {
      setPin();
      if (!pinned) {
        // stacked: mark the ticket in the middle of the screen as current
        if (reduce) return;
        var mid = innerHeight * .55;
        tkts.forEach(function (t) { var r = t.getBoundingClientRect(); t.classList.toggle('is-cur', r.top < mid && r.bottom > mid); });
        return;
      }
      var dist = Math.max(0, row.scrollWidth - row.parentElement.clientWidth);
      track.style.height = (innerHeight + dist * 1.1) + 'px';
      var r = track.getBoundingClientRect();
      var p = Math.max(0, Math.min(1, -r.top / Math.max(1, r.height - innerHeight)));
      row.style.setProperty('--tx', (-dist * p).toFixed(1) + 'px');
      if (bar) bar.style.setProperty('--p', p.toFixed(3));
      var cur = Math.min(tkts.length - 1, Math.round(p * (tkts.length - 1)));
      tkts.forEach(function (t, i) { t.classList.toggle('is-torn', i < cur); t.classList.toggle('is-cur', i === cur); });
    };
    rafScroll(update);
    if (wide && wide.addEventListener) wide.addEventListener('change', update);
  }

  /* ---------- tabs helper (industries folders, project cases) ---------- */
  var tabs = function (root, tabSel, panelSel, auto) {
    if (!root) return;
    var tbs = $$(tabSel, root), pnls = $$(panelSel, root);
    if (!tbs.length) return;
    var cur = 0, timer = null, stopped = false, visible = false;
    var show = function (n, focus) {
      cur = (n + tbs.length) % tbs.length;
      tbs.forEach(function (t, i) { t.setAttribute('aria-selected', i === cur ? 'true' : 'false'); t.tabIndex = i === cur ? 0 : -1; });
      pnls.forEach(function (p, i) { p.hidden = i !== cur; });
      if (focus) tbs[cur].focus();
    };
    var stop = function () { stopped = true; clearInterval(timer); timer = null; };
    var run = function () {
      if (stopped || reduce || !auto || !visible || document.hidden) { clearInterval(timer); timer = null; return; }
      if (!timer) timer = setInterval(function () { show(cur + 1); }, auto);
    };
    tbs.forEach(function (t, i) {
      t.addEventListener('click', function () { stop(); show(i); });
      t.addEventListener('keydown', function (e) {
        var k = { ArrowDown: 1, ArrowRight: 1, ArrowUp: -1, ArrowLeft: -1 }[e.key];
        var n = k !== undefined ? i + k : e.key === 'Home' ? 0 : e.key === 'End' ? tbs.length - 1 : null;
        if (n === null) return;
        e.preventDefault(); stop(); show(n, true);
      });
    });
    root.addEventListener('pointerdown', stop);
    root.addEventListener('focusin', stop);
    onView(root, function (v) { visible = v; run(); }, { threshold: .35 });
    document.addEventListener('visibilitychange', run);
    show(0);
  };
  tabs($('[data-bs-files]'), '.files__tab', '.file', 6000);
  tabs($('[data-bs-cases]'), '.cases__tab', '.case', 0);

  /* ---------- 07 before/after compare sliders ---------- */
  $$('.cmp').forEach(function (cmp) {
    var range = $('.cmp__range', cmp);
    if (!range) return;
    var set = function () { cmp.style.setProperty('--pos', range.value + '%'); };
    range.addEventListener('input', set);
    set();
    // a gentle hint the first time it is seen: the divider glides once
    if (!reduce) once(cmp, function () {
      var t0 = performance.now();
      var step = function (t) {
        var k = Math.min(1, (t - t0) / 1400), v = 50 + Math.sin(k * Math.PI * 2) * 18 * (1 - k);
        if (document.activeElement !== range) { range.value = v.toFixed(0); set(); }
        if (k < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    }, { threshold: .5 });
  });

  /* ---------- 3D tilt: key surfaces lean gently towards the pointer (mouse/trackpad only) ---------- */
  if (!reduce && window.matchMedia && matchMedia('(hover:hover) and (pointer:fine)').matches) {
    $$('[data-tilt]').forEach(function (el) {
      var max = el.classList.contains('bento__card') ? 5 : 4;
      el.addEventListener('pointermove', function (e) {
        var r = el.getBoundingClientRect();
        var x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
        el.classList.add('is-tilting');
        el.style.setProperty('--ry', (x * max).toFixed(2) + 'deg');
        el.style.setProperty('--rx', (-y * max).toFixed(2) + 'deg');
      });
      el.addEventListener('pointerleave', function () {
        el.classList.remove('is-tilting');
        el.style.setProperty('--ry', '0deg');
        el.style.setProperty('--rx', '0deg');
      });
    });
  }

  /* ---------- 08 composer: a prompt fills the message, then opens the booking calendar ---------- */
  var msg = $('[data-bs-msg]');
  if (msg) {
    var base = msg.textContent;
    var fill = function (text) {
      msg.classList.add('is-swap');
      setTimeout(function () { msg.textContent = text ? 'Hi Drawlead, ' + text.charAt(0).toLowerCase() + text.slice(1) + '.' : base; msg.classList.remove('is-swap'); }, 150);
    };
    $$('.composer__chip').forEach(function (c) {
      c.addEventListener('mouseenter', function () { fill(c.dataset.prompt); });
      c.addEventListener('focus', function () { fill(c.dataset.prompt); });
    });
  }
})();
