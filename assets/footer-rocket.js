// Footer signoff: a small rocket that loops around the big "Drawlead" word.
// It flies a closed spline through and around the letters, passing behind
// some of them (back canvas, under the word) and in front of others (front
// canvas), with a little physics: it speeds up diving, slows climbing, banks
// smoothly into turns and leaves a fading smoke-and-spark trail.
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
  var s = 0, v = 240, ang = 0, size = 18, parts = [], last = 0, running = false, raf = 0;

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

  function drawRocket(ctx, x, y, a, sc, t) {
    var u = size * sc;
    ctx.save();
    ctx.translate(x, y);
    ctx.rotate(a);
    // flame (flickers)
    var fl = u * (.9 + .35 * Math.sin(t * 40) + .2 * Math.random());
    ctx.fillStyle = '#FF8A1F';
    ctx.beginPath(); ctx.moveTo(-u * .55, -u * .22); ctx.quadraticCurveTo(-u * .55 - fl, 0, -u * .55, u * .22); ctx.closePath(); ctx.fill();
    ctx.fillStyle = '#FFD166';
    ctx.beginPath(); ctx.moveTo(-u * .55, -u * .11); ctx.quadraticCurveTo(-u * .55 - fl * .55, 0, -u * .55, u * .11); ctx.closePath(); ctx.fill();
    // fins
    ctx.fillStyle = '#1d7a49';
    ctx.beginPath(); ctx.moveTo(-u * .35, -u * .26); ctx.lineTo(-u * .7, -u * .55); ctx.lineTo(-u * .58, -u * .2); ctx.closePath(); ctx.fill();
    ctx.beginPath(); ctx.moveTo(-u * .35, u * .26); ctx.lineTo(-u * .7, u * .55); ctx.lineTo(-u * .58, u * .2); ctx.closePath(); ctx.fill();
    // body
    ctx.fillStyle = '#ffffff';
    ctx.strokeStyle = '#0f3a24';
    ctx.lineWidth = Math.max(1.2, u * .07);
    ctx.beginPath();
    ctx.moveTo(-u * .6, -u * .27);
    ctx.lineTo(u * .25, -u * .27);
    ctx.quadraticCurveTo(u * .75, -u * .2, u * .95, 0);
    ctx.quadraticCurveTo(u * .75, u * .2, u * .25, u * .27);
    ctx.lineTo(-u * .6, u * .27);
    ctx.closePath();
    ctx.fill(); ctx.stroke();
    // nose band and window
    ctx.fillStyle = '#1d7a49';
    ctx.beginPath(); ctx.moveTo(u * .55, -u * .22); ctx.quadraticCurveTo(u * .8, -u * .14, u * .95, 0); ctx.quadraticCurveTo(u * .8, u * .14, u * .55, u * .22); ctx.closePath(); ctx.fill();
    ctx.fillStyle = '#DAF1E5';
    ctx.beginPath(); ctx.arc(u * .12, 0, u * .13, 0, Math.PI * 2); ctx.fill(); ctx.stroke();
    ctx.restore();
  }

  function frame(now) {
    if (!running) return;
    var dt = Math.min(.05, (now - (last || now)) / 1000);
    last = now;
    var p = at(s);
    // gravity feel: faster diving (sin > 0 means moving down the screen), slower climbing
    var target = (W / 4.2) * (1 + .55 * Math.sin(p[2]));
    v += (target - v) * Math.min(1, dt * 2.4);
    s += v * dt;
    var q = at(s), frac = (((s % total) + total) % total) / total;
    // bank smoothly towards the path direction
    var da = Math.atan2(Math.sin(q[2] - ang), Math.cos(q[2] - ang));
    ang += da * Math.min(1, dt * 7);
    var behind = isBehind(frac);
    // exhaust: smoke puffs and a few sparks from the tail
    var tx = q[0] - Math.cos(ang) * size * .6, ty = q[1] - Math.sin(ang) * size * .6;
    for (var i = 0; i < 2; i++) {
      parts.push({
        x: tx, y: ty,
        vx: -Math.cos(ang) * (30 + Math.random() * 40) + (Math.random() - .5) * 30,
        vy: -Math.sin(ang) * (30 + Math.random() * 40) + (Math.random() - .5) * 30,
        r: size * (.12 + Math.random() * .1), life: 0, max: .9 + Math.random() * .6,
        spark: Math.random() < .25, back: behind
      });
    }
    bctx.clearRect(0, 0, W, H);
    fctx.clearRect(0, 0, W, H);
    for (var j = parts.length - 1; j >= 0; j--) {
      var pt = parts[j];
      pt.life += dt;
      if (pt.life > pt.max) { parts.splice(j, 1); continue; }
      pt.x += pt.vx * dt; pt.y += pt.vy * dt;
      pt.vx *= .96; pt.vy = pt.vy * .96 - 6 * dt; // smoke drifts up a touch
      var k = pt.life / pt.max, ctx = pt.back ? bctx : fctx;
      ctx.beginPath();
      if (pt.spark) {
        ctx.fillStyle = 'rgba(255,138,31,' + (1 - k) * .9 + ')';
        ctx.arc(pt.x, pt.y, Math.max(.6, pt.r * .35 * (1 - k)), 0, Math.PI * 2);
      } else {
        ctx.fillStyle = 'rgba(100,116,139,' + (1 - k) * .22 + ')';
        ctx.arc(pt.x, pt.y, pt.r * (1 + k * 2.2), 0, Math.PI * 2);
      }
      ctx.fill();
    }
    drawRocket(behind ? bctx : fctx, q[0], q[1], ang, behind ? .86 : 1, now / 1000);
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
