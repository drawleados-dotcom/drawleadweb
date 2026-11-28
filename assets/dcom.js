/* Digital Commerce page (templates/digital-commerce-body.php), after home10.js.
   home10.js already runs the scroll reveals (adding .is-in, which starts the
   funnel bars, the order on the rail and the stepper line), the heading word
   reveal and the progress bar. This adds the two interactive pieces; each
   is optional, and the page reads in full without it. */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* tabs: click, arrow keys, Home/End; hover too where asked (the category menu) */
  var tabs = function (box, opts) {
    var list = $$('[role="tab"]', box);
    var select = function (tab, focus) {
      list.forEach(function (t) {
        var on = t === tab;
        t.setAttribute('aria-selected', on ? 'true' : 'false');
        t.tabIndex = on ? 0 : -1;
        var panel = document.getElementById(t.getAttribute('aria-controls'));
        if (panel) panel.classList.toggle('is-on', on);
      });
      if (focus) tab.focus();
      var row = tab.parentNode;
      if (row.scrollWidth > row.clientWidth + 2) {
        row.scrollTo({ left: tab.offsetLeft - 16, behavior: reduce ? 'auto' : 'smooth' });
      }
    };
    list.forEach(function (t, i) {
      t.addEventListener('click', function () { select(t); });
      if (opts && opts.hover) {
        t.addEventListener('mouseenter', function () {
          if (matchMedia('(hover: hover) and (min-width: 981px)').matches && t.getAttribute('aria-selected') !== 'true') select(t);
        });
      }
      t.addEventListener('keydown', function (e) {
        var to = { ArrowDown: i + 1, ArrowRight: i + 1, ArrowUp: i - 1, ArrowLeft: i - 1, Home: 0, End: list.length - 1 }[e.key];
        if (to === undefined) return;
        e.preventDefault();
        select(list[(to + list.length) % list.length], true);
      });
    });
  };

  /* ---------- 03 horizontal sticky scroll: the section pins, the tickets slide past ----------
     Only on wide, tall enough screens and without reduced motion; everywhere else
     the tickets stay in their grid. The section is made as tall as the sideways
     distance plus one screen, so scrolling it down moves the track across. */
  var hs = $('[data-dc-hs]');
  var track = hs && $('[data-dc-track]', hs);
  if (hs && track) {
    var fit = matchMedia('(min-width: 981px) and (min-height: 600px)');
    var count = $('[data-dc-count]', hs);
    var prog = $('[data-dc-prog]', hs);
    var cards = $$('.dc-tks .step', hs);
    var dist = 0;
    var ticking = false;
    var move = function () {
      ticking = false;
      if (!hs.classList.contains('is-hs')) return;
      var span = hs.offsetHeight - window.innerHeight;
      var p = span > 0 ? Math.min(1, Math.max(0, -hs.getBoundingClientRect().top / span)) : 0;
      track.style.transform = 'translate3d(' + (-p * dist).toFixed(1) + 'px,0,0)';
      if (prog) prog.style.transform = 'scaleX(' + p.toFixed(4) + ')';
      if (count && cards.length) {
        // 01 at the start, the last one at the end, in step with the scroll
        var best = Math.round(p * (cards.length - 1));
        count.textContent = (best < 9 ? '0' : '') + (best + 1);
      }
    };
    var onScroll = function () {
      if (!ticking) { ticking = true; requestAnimationFrame(move); }
    };
    var setup = function () {
      var on = fit.matches && !reduce;
      hs.classList.toggle('is-hs', on);
      if (!on) {
        hs.style.height = '';
        track.style.transform = '';
        return;
      }
      track.style.transform = '';
      dist = Math.max(0, track.scrollWidth - window.innerWidth);
      hs.style.height = (dist + window.innerHeight) + 'px';
      move();
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', setup);
    if (fit.addEventListener) fit.addEventListener('change', setup);
    window.addEventListener('load', setup);
    setup();
  }

  /* ---------- 05 the timeline: the line fills as you scroll, each step lights as it's reached ---------- */
  var tl = $('[data-dc-tl]');
  if (tl) {
    var steps = $$('.dc-tl__step', tl);
    var tlTick = false;
    var tlMove = function () {
      tlTick = false;
      var mark = window.innerHeight * 0.55;
      var r = tl.getBoundingClientRect();
      var p = r.height ? Math.min(1, Math.max(0, (mark - r.top) / r.height)) : 0;
      tl.style.setProperty('--p', p.toFixed(4));
      steps.forEach(function (s) {
        var n = $('.dc-tl__node', s).getBoundingClientRect();
        s.classList.toggle('is-on', n.top + n.height / 2 < mark);
      });
    };
    if (reduce) {
      tl.style.setProperty('--p', '1');
      steps.forEach(function (s) { s.classList.add('is-on'); });
    } else {
      window.addEventListener('scroll', function () {
        if (!tlTick) { tlTick = true; requestAnimationFrame(tlMove); }
      }, { passive: true });
      window.addEventListener('resize', tlMove);
      tlMove();
    }
  }

  /* ---------- 06 the category menu: pointing at a category previews it ---------- */
  var cat = $('[data-dc-cat]');
  if (cat) tabs(cat, { hover: true });

  /* ---------- 07 the browser window: the projects are its tabs ---------- */
  var br = $('[data-dc-br]');
  if (br) tabs(br);
})();
