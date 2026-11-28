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

  /* ---------- 06 the category menu: pointing at a category previews it ---------- */
  var cat = $('[data-dc-cat]');
  if (cat) tabs(cat, { hover: true });

  /* ---------- 07 the browser window: the projects are its tabs ---------- */
  var br = $('[data-dc-br]');
  if (br) tabs(br);
})();
