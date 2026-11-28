/* AI & Automation page (templates/ai-automation-body.php), after home10.js.
   home10.js already runs the scroll reveals (adding .is-in, which starts the
   agent's chat and workflow in the hero), the heading word reveal, the logo
   marquee and the progress bar. This adds three things; each is optional, and
   the page reads in full without it. */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var NS = 'http://www.w3.org/2000/svg';

  /* ---------- 03 the 3D cards lean toward the pointer, like the home page's ---------- */
  if (!reduce && matchMedia('(hover: hover) and (pointer: fine)').matches) {
    $$('[data-ai-tilt]').forEach(function (card) {
      var tilt = function (x, y) {
        card.style.setProperty('--ry', (x * 5).toFixed(2) + 'deg');
        card.style.setProperty('--rx', (-y * 4).toFixed(2) + 'deg');
        card.style.setProperty('--gx', (50 + x * 60).toFixed(1) + '%');
      };
      card.addEventListener('pointermove', function (e) {
        var r = card.getBoundingClientRect();
        tilt((e.clientX - r.left) / r.width - 0.5, (e.clientY - r.top) / r.height - 0.5);
      });
      card.addEventListener('pointerleave', function () { tilt(0, 0); });
    });
  }

  /* ---------- 04 the canvas: wire each node to the next, a packet riding each wire ---------- */
  var canvas = $('[data-ai-canvas]');
  var wires = canvas && $('[data-ai-wires]', canvas);
  if (canvas && wires) {
    var body = wires.parentNode;
    var nodes = $$('.ai-node', canvas);
    var draw = function () {
      while (wires.firstChild) wires.removeChild(wires.firstChild);
      if (getComputedStyle(wires).display === 'none') return;
      var b = body.getBoundingClientRect();
      wires.setAttribute('viewBox', '0 0 ' + b.width.toFixed(0) + ' ' + b.height.toFixed(0));
      for (var i = 0; i < nodes.length - 1; i++) {
        var a = nodes[i].getBoundingClientRect();
        var c = nodes[i + 1].getBoundingClientRect();
        var sx = a.right - b.left, sy = a.top - b.top + 30;
        var ex = c.left - b.left, ey = c.top - b.top + 30;
        var dx = Math.max(24, (ex - sx) * 0.6);
        var d = 'M' + sx + ' ' + sy + 'C' + (sx + dx) + ' ' + sy + ' ' + (ex - dx) + ' ' + ey + ' ' + ex + ' ' + ey;
        var path = document.createElementNS(NS, 'path');
        path.setAttribute('d', d);
        path.setAttribute('id', 'ai-w' + i);
        wires.appendChild(path);
        [[sx, sy], [ex, ey]].forEach(function (p) {
          var dot = document.createElementNS(NS, 'circle');
          dot.setAttribute('cx', p[0]); dot.setAttribute('cy', p[1]); dot.setAttribute('r', 5);
          wires.appendChild(dot);
        });
        if (!reduce) {
          var pk = document.createElementNS(NS, 'circle');
          pk.setAttribute('r', 4);
          pk.setAttribute('class', 'ai-pk');
          var mo = document.createElementNS(NS, 'animateMotion');
          mo.setAttribute('dur', '1.8s');
          mo.setAttribute('begin', (i * 0.45) + 's');
          mo.setAttribute('repeatCount', 'indefinite');
          mo.setAttribute('path', d);
          pk.appendChild(mo);
          wires.appendChild(pk);
        }
      }
    };
    var t;
    window.addEventListener('resize', function () { clearTimeout(t); t = setTimeout(draw, 120); });
    window.addEventListener('load', draw);
    if ('ResizeObserver' in window) new ResizeObserver(function () { clearTimeout(t); t = setTimeout(draw, 60); }).observe(body);
    draw();
  }

  /* ---------- 06 the industry panels: one open at a time; on desktop pointing opens one ---------- */
  var acc = $('[data-ai-acc]');
  if (acc) {
    var panels = $$('.ai-acc__p', acc);
    var open = function (p) {
      panels.forEach(function (q) {
        var on = q === p;
        q.classList.toggle('is-open', on);
        $('.ai-acc__btn', q).setAttribute('aria-expanded', on ? 'true' : 'false');
      });
    };
    var wide = matchMedia('(min-width: 981px)');
    var hover = matchMedia('(hover: hover)');
    panels.forEach(function (p) {
      var btn = $('.ai-acc__btn', p);
      btn.addEventListener('click', function () {
        // on phones a second tap closes it again; side by side, one is always open
        if (!wide.matches && p.classList.contains('is-open')) {
          p.classList.remove('is-open');
          btn.setAttribute('aria-expanded', 'false');
        } else open(p);
      });
      p.addEventListener('mouseenter', function () {
        if (wide.matches && hover.matches && !p.classList.contains('is-open')) open(p);
      });
    });
  }
})();
