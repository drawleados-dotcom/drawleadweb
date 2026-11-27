// Footer signoff: a small paper plane that loops around the big "Drawlead" word.
// It flies a closed spline through and around the letters, passing behind
// some of them (back canvas, under the word) and in front of others (front
// canvas), with a little physics: it speeds up diving, slows climbing, banks
// smoothly into turns, porpoises gently (phugoid glide) and leaves a fading
// dashed trail with sketchy speed lines.
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

  // A folded paper dart, nose along +x, drawn as three flat faces like a real
  // fold: the far wing peeking out below, the dark keel fold, and the big lit
  // near wing on top. `pitch` (from the phugoid) tilts the wings a little.
  function drawPlane(ctx, x, y, a, sc, pitch) {
    var u = size * sc * 1.35;
    var lift = 1 + pitch * 1.4;
    ctx.save();
    ctx.translate(x, y);
    ctx.rotate(a);
    ctx.lineJoin = 'round';
    ctx.lineCap = 'round';
    ctx.lineWidth = Math.max(1, u * .045);
    ctx.strokeStyle = '#0b2418';
    // far wing, seen below the body
    ctx.fillStyle = '#1d7a49';
    ctx.beginPath(); ctx.moveTo(u, 0); ctx.lineTo(-u * .55, u * .05); ctx.lineTo(-u * .78, u * .3 * lift); ctx.closePath(); ctx.fill(); ctx.stroke();
    // keel fold hanging under the centre line
    ctx.fillStyle = '#0f3a24';
    ctx.beginPath(); ctx.moveTo(u * .92, u * .01); ctx.lineTo(-u * .55, u * .05); ctx.lineTo(-u * .5, u * .28); ctx.closePath(); ctx.fill(); ctx.stroke();
    // near wing, the big lit face on top
    ctx.fillStyle = '#32B46F';
    ctx.beginPath(); ctx.moveTo(u, 0); ctx.lineTo(-u * .9, -u * .52 * lift); ctx.lineTo(-u * .55, u * .05); ctx.closePath(); ctx.fill(); ctx.stroke();
    // light catching the fold of the near wing
    ctx.fillStyle = '#5fd093';
    ctx.beginPath(); ctx.moveTo(u, 0); ctx.lineTo(-u * .9, -u * .52 * lift); ctx.lineTo(-u * .66, -u * .3 * lift); ctx.closePath(); ctx.fill();
    // centre crease
    ctx.strokeStyle = 'rgba(11,36,24,.55)';
    ctx.lineWidth = Math.max(.8, u * .03);
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
    ctx.strokeStyle = 'rgba(11,36,24,.35)';
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
      ctx.strokeStyle = 'rgba(11,36,24,' + (1 - k) * .45 + ')';
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
