// Footer signoff: a small paper plane that loops around the big "Drawlead" word.
// It flies a closed spline through and around the letters, passing behind
// some of them (back canvas, under the word) and in front of others (front
// canvas), with a little physics: it speeds up diving, slows climbing, banks
// smoothly into turns, porpoises gently (phugoid glide) and leaves a fading
// dashed trail with sketchy speed lines. Each letter fills with green while
// the plane flies through it and fades back once it has left (`.is-lit`,
// styled in footer-signoff.css).
// Paused when off screen or the tab is hidden; still for reduced motion.
(function () {
  var word = document.querySelector('.dl-sign__w');
  if (!word) return;
  var back = word.querySelector('.dl-rk--back');
  var front = word.querySelector('.dl-rk--front');
  if (!back || !front || !back.getContext) return;
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduce) { back.hidden = true; front.hidden = true; return; }

  var bctx = back.getContext('2d'), fctx = front.getContext('2d');
  // closed loop, as fractions of the canvas box (which overhangs the word)
  var P = [[.03,.52],[.08,.2],[.18,.16],[.27,.42],[.36,.68],[.46,.44],[.55,.24],[.64,.5],[.73,.72],[.83,.4],[.92,.16],[.98,.42],[.94,.78],[.78,.94],[.55,.9],[.32,.95],[.12,.9]];
  // stretches of the loop (0..1) where the rocket slips behind the letters
  var BEHIND = [[.13,.26],[.36,.47],[.6,.7]];

  var W = 0, H = 0, dpr = 1, pts = [], lens = [], total = 0;
  var letters = [].slice.call(word.querySelectorAll('span')), boxes = [], lit = [];
  var probe = document.createElement('canvas').getContext('2d');
  var s = 0, v = 240, ang = 0, size = 18, parts = [], last = 0, running = false, raf = 0, trailGap = 0;

  function cr(p0, p1, p2, p3, t) {
    var t2 = t * t, t3 = t2 * t;
    return [
      .5 * (2 * p1[0] + (-p0[0] + p2[0]) * t + (2 * p0[0] - 5 * p1[0] + 4 * p2[0] - p3[0]) * t2 + (-p0[0] + 3 * p1[0] - 3 * p2[0] + p3[0]) * t3),
      .5 * (2 * p1[1] + (-p0[1] + p2[1]) * t + (2 * p0[1] - 5 * p1[1] + 4 * p2[1] - p3[1]) * t2 + (-p0[1] + 3 * p1[1] - 3 * p2[1] + p3[1]) * t3)
    ];
  }

  function build() {
    var r = back.getBoundingClientRect();
    W = r.width; H = r.height;
    if (!W || !H) return;
    dpr = Math.min(window.devicePixelRatio || 1, 2);
    [back, front].forEach(function (c) { c.width = Math.round(W * dpr); c.height = Math.round(H * dpr); });
    bctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    fctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    size = Math.max(12, Math.min(26, H * .075));
    // sample the closed spline densely, keeping running arc length
    pts = []; lens = []; total = 0;
    var n = P.length, steps = 40, prev = null;
    for (var i = 0; i < n; i++) {
      var p0 = P[(i - 1 + n) % n], p1 = P[i], p2 = P[(i + 1) % n], p3 = P[(i + 2) % n];
      for (var k = 0; k < steps; k++) {
        var q = cr(p0, p1, p2, p3, k / steps), x = q[0] * W, y = q[1] * H;
        if (prev) total += Math.hypot(x - prev[0], y - prev[1]);
        pts.push([x, y]); lens.push(total); prev = [x, y];
      }
    }
    total += Math.hypot(pts[0][0] - prev[0], pts[0][1] - prev[1]);
    measureLetters(r);
  }

  // Each letter's ink box in canvas coordinates. The span's box is padded and
  // overlaps its neighbours, so it is narrowed to the glyph's own extents
  // (measureText); that way a lowercase letter only lights when the plane is
  // over the letter itself, not over the empty space above it.
  function measureLetters(cr) {
    boxes = letters.map(function (el) {
      var r = el.getBoundingClientRect(), cs = getComputedStyle(el);
      var fs = parseFloat(cs.fontSize) || 16, padX = parseFloat(cs.paddingLeft) || 0, padY = parseFloat(cs.paddingTop) || 0;
      var x0 = r.left + padX - cr.left, top = r.top + padY - cr.top, lh = r.height - 2 * padY;
      var box = { l: x0, r: r.right - padX - cr.left, t: r.top - cr.top, b: r.bottom - cr.top };
      probe.font = cs.fontWeight + ' ' + fs + 'px ' + cs.fontFamily;
      var m = probe.measureText(el.textContent);
      if (m.fontBoundingBoxAscent != null && m.actualBoundingBoxAscent != null) {
        var base = top + (lh - (m.fontBoundingBoxAscent + m.fontBoundingBoxDescent)) / 2 + m.fontBoundingBoxAscent;
        box = { l: x0 - m.actualBoundingBoxLeft, r: x0 + m.actualBoundingBoxRight, t: base - m.actualBoundingBoxAscent, b: base + m.actualBoundingBoxDescent };
      }
      return box;
    });
  }

  // light the letter (or two, where they touch) the plane is flying through
  function lightLetters(x, y) {
    for (var i = 0; i < boxes.length; i++) {
      var b = boxes[i], on = x > b.l && x < b.r && y > b.t && y < b.b;
      if (on !== !!lit[i]) { lit[i] = on; letters[i].classList.toggle('is-lit', on); }
    }
  }

  function at(dist) {
    dist = ((dist % total) + total) % total;
    var lo = 0, hi = lens.length - 1;
    while (lo < hi) { var mid = (lo + hi + 1) >> 1; if (lens[mid] <= dist) lo = mid; else hi = mid - 1; }
    var a = pts[lo], b = pts[(lo + 1) % pts.length];
    var seg = (lo + 1 < lens.length ? lens[lo + 1] : total) - lens[lo] || 1;
    var f = (dist - lens[lo]) / seg;
    return [a[0] + (b[0] - a[0]) * f, a[1] + (b[1] - a[1]) * f, Math.atan2(b[1] - a[1], b[0] - a[0])];
  }

  function isBehind(frac) {
    for (var i = 0; i < BEHIND.length; i++) if (frac > BEHIND[i][0] && frac < BEHIND[i][1]) return true;
    return false;
  }

  // newsprint: off-white paper with a headline bar, two columns of tiny
  // "text" lines and a gutter, built once as a repeating canvas pattern
  var newsTile = null, newsPat = [];
  // print at roughly 60% scale so the plane shows several lines of copy
  function fine(p) { if (p && p.setTransform && window.DOMMatrix) p.setTransform(new DOMMatrix().scale(.6)); }
  function newsprint(ctx) {
    for (var n = 0; n < newsPat.length; n++) if (newsPat[n][0] === ctx) return newsPat[n][1];
    if (newsTile) { var pp = ctx.createPattern(newsTile, 'repeat'); fine(pp); newsPat.push([ctx, pp]); return pp; }
    var c = document.createElement('canvas');
    c.width = 72; c.height = 72;
    var g = c.getContext('2d');
    g.fillStyle = '#eeeae0';
    g.fillRect(0, 0, 72, 72);
    g.fillStyle = 'rgba(30,30,28,.62)';
    g.fillRect(3, 3, 52, 4);                      // headline
    g.fillStyle = 'rgba(30,30,28,.4)';
    g.fillRect(3, 9, 30, 2);                      // standfirst
    g.fillStyle = 'rgba(30,30,28,.3)';
    for (var row = 14; row < 70; row += 3) {     // body copy, two columns
      var w1 = 20 + ((row * 7) % 11), w2 = 22 + ((row * 5) % 10);
      g.fillRect(3, row, w1, 1);
      g.fillRect(37, row, w2, 1);
    }
    g.fillStyle = 'rgba(30,30,28,.18)';
    g.fillRect(34, 13, 1, 57);                    // column rule
    newsTile = c;
    var pat = ctx.createPattern(c, 'repeat');
    fine(pat);
    newsPat.push([ctx, pat]);
    return pat;
  }

  // A folded newspaper dart, nose along +x, drawn as flat faces like a real
  // fold: the far wing peeking out below, the keel fold, and the big near wing
  // on top. Only grey shading, no colour. `pitch` (phugoid) tilts the wings.
  function drawPlane(ctx, x, y, a, sc, pitch) {
    var u = size * sc * 1.35;
    var lift = 1 + pitch * 1.4;
    var paper = newsprint(ctx);
    function face(pts, shade) {
      ctx.beginPath();
      ctx.moveTo(pts[0][0], pts[0][1]);
      for (var i = 1; i < pts.length; i++) ctx.lineTo(pts[i][0], pts[i][1]);
      ctx.closePath();
      ctx.fillStyle = paper; ctx.fill();
      if (shade) { ctx.fillStyle = shade; ctx.fill(); }
      ctx.stroke();
    }
    ctx.save();
    ctx.translate(x, y);
    ctx.rotate(a);
    ctx.lineJoin = 'round';
    ctx.lineCap = 'round';
    ctx.lineWidth = Math.max(.9, u * .035);
    ctx.strokeStyle = 'rgba(45,45,42,.75)';
    face([[u, 0], [-u * .55, u * .05], [-u * .78, u * .3 * lift]], 'rgba(20,20,18,.2)');     // far wing
    face([[u * .92, u * .01], [-u * .55, u * .05], [-u * .5, u * .28]], 'rgba(20,20,18,.36)'); // keel
    face([[u, 0], [-u * .9, -u * .52 * lift], [-u * .55, u * .05]], null);                   // near wing
    // soft light along the leading fold of the near wing
    ctx.fillStyle = 'rgba(255,255,255,.35)';
    ctx.beginPath(); ctx.moveTo(u, 0); ctx.lineTo(-u * .9, -u * .52 * lift); ctx.lineTo(-u * .7, -u * .34 * lift); ctx.closePath(); ctx.fill();
    // centre crease
    ctx.strokeStyle = 'rgba(45,45,42,.6)';
    ctx.lineWidth = Math.max(.8, u * .028);
    ctx.beginPath(); ctx.moveTo(u * .96, 0); ctx.lineTo(-u * .55, u * .05); ctx.stroke();
    ctx.restore();
  }

  // a few sketchy speed lines behind the wing, longer when it is going fast
  function drawSpeed(ctx, x, y, a, sc, spd) {
    var u = size * sc;
    var len = u * (.6 + Math.min(1.6, spd / (W / 5.5)) * .9);
    ctx.save();
    ctx.translate(x, y);
    ctx.rotate(a);
    ctx.strokeStyle = 'rgba(60,60,56,.35)';
    ctx.lineWidth = Math.max(1, u * .05);
    ctx.lineCap = 'round';
    [[-1.5, -.55], [-1.7, -.2], [-1.45, .2]].forEach(function (o, i) {
      var j = (Math.random() - .5) * u * .15;
      ctx.beginPath();
      ctx.moveTo(u * o[0], u * o[1] + j);
      ctx.lineTo(u * o[0] - len * (1 - i * .2), u * o[1] + j);
      ctx.stroke();
    });
    ctx.restore();
  }

  var phase = 0;
  function frame(now) {
    if (!running) return;
    var dt = Math.min(.05, (now - (last || now)) / 1000);
    last = now;
    var p = at(s);
    // glide: faster when diving (sin > 0 is moving down the screen), slower climbing
    var target = (W / 5.5) * (1 + .6 * Math.sin(p[2]));
    v += (target - v) * Math.min(1, dt * 2.4);
    s += v * dt;
    var q = at(s), frac = (((s % total) + total) % total) / total;
    // bank smoothly towards the path direction
    var da = Math.atan2(Math.sin(q[2] - ang), Math.cos(q[2] - ang));
    ang += da * Math.min(1, dt * 6);
    // phugoid: the glide porpoises gently either side of the path, and the
    // nose pitches up and down a quarter-cycle ahead of the height change
    phase += dt * 2.3;
    var amp = size * .45, off = Math.sin(phase) * amp, pitch = Math.cos(phase) * .12;
    var nx = -Math.sin(q[2]), ny = Math.cos(q[2]);
    var px = q[0] + nx * off, py = q[1] + ny * off;
    var behind = isBehind(frac);
    lightLetters(px, py);
    // dashed flight trail behind the tail
    var tx = px - Math.cos(ang) * size * .7, ty = py - Math.sin(ang) * size * .7;
    trailGap += v * dt;
    if (trailGap > size * .5) {
      trailGap = 0;
      parts.push({ x: tx, y: ty, a: ang, life: 0, max: 1.8, back: behind });
    }
    bctx.clearRect(0, 0, W, H);
    fctx.clearRect(0, 0, W, H);
    for (var j = parts.length - 1; j >= 0; j--) {
      var pt = parts[j];
      pt.life += dt;
      if (pt.life > pt.max) { parts.splice(j, 1); continue; }
      var k = pt.life / pt.max, ctx = pt.back ? bctx : fctx, d = size * .22;
      ctx.strokeStyle = 'rgba(60,60,56,' + (1 - k) * .45 + ')';
      ctx.lineWidth = Math.max(1, size * .06);
      ctx.lineCap = 'round';
      ctx.beginPath();
      ctx.moveTo(pt.x - Math.cos(pt.a) * d, pt.y - Math.sin(pt.a) * d);
      ctx.lineTo(pt.x + Math.cos(pt.a) * d, pt.y + Math.sin(pt.a) * d);
      ctx.stroke();
    }
    var lay = behind ? bctx : fctx, sc = behind ? .86 : 1, heading = ang - pitch;
    drawSpeed(lay, px, py, heading, sc, v);
    drawPlane(lay, px, py, heading, sc, pitch);
    raf = requestAnimationFrame(frame);
  }

  function start() { if (running || !total) return; running = true; last = 0; raf = requestAnimationFrame(frame); }
  function stop() { running = false; cancelAnimationFrame(raf); }

  build();
  var inView = false;
  if ('IntersectionObserver' in window) {
    new IntersectionObserver(function (es) {
      inView = es[0].isIntersecting;
      if (inView && !document.hidden) start(); else stop();
    }, { rootMargin: '120px 0px' }).observe(word);
  } else { inView = true; start(); }
  document.addEventListener('visibilitychange', function () { if (document.hidden) stop(); else if (inView) start(); });
  var rt;
  window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(function () { build(); }, 150); });
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(build);
})();
