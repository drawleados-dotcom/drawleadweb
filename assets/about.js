/* About Us: the few behaviours this page adds to assets/home10.js (reveals,
   progress bar, illustrations). Vanilla, no dependencies. */
(() => {
  'use strict';
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const hasIO = 'IntersectionObserver' in window;
  // Admin → Pages → Page Content preview: leave text nodes as rendered, the
  // editor's markers are in them.
  const preview = new URLSearchParams(location.search).has('pc_preview');

  /* ---------- story: the newspaper travels from 2022 to 2026 ---------- */
  const story = $('[data-story]');
  const trip = story && (() => {
    const run = $('[data-run]', story), stage = $('[data-stage]', story), track = $('[data-track]', story);
    const route = $('[data-route]', story), plane = $('.route__plane', route), yrs = $$('[data-yr]', story);
    const wide = matchMedia('(min-width:1100px) and (min-height:640px)');
    const last = yrs.length - 1;
    const PACE = 1.3;                 // px of paper per px of scroll
    const NS = 'http://www.w3.org/2000/svg';
    let on = false, far = 0, stops = [], pos = 0, aim = 0, raf = 0, geo = null, at = -1, top0 = 360;

    // the rail at the bottom: one hop per year, each year a link
    const nav = document.createElement('nav');
    nav.className = 'route__nav';
    nav.setAttribute('aria-label', 'Our story, year by year');
    const svg = document.createElementNS(NS, 'svg');
    svg.setAttribute('aria-hidden', 'true');
    const [way, done] = ['route__way', 'route__done'].map((c) => {
      const el = document.createElementNS(NS, 'path');
      el.setAttribute('class', c);
      svg.append(el);
      return el;
    });
    done.setAttribute('pathLength', last);
    nav.append(svg);
    const links = yrs.map((y, i) => {
      const l = document.createElement('a');
      l.href = '#' + y.id;
      l.textContent = $('.yr', y).textContent.trim();
      l.style.left = (i / last) * 100 + '%';
      l.addEventListener('click', (e) => {
        if (!on) return;
        e.preventDefault();
        scrollTo({ top: run.getBoundingClientRect().top + scrollY + stops[i] / PACE, behavior: 'smooth' });
      });
      nav.append(l);
      return l;
    });
    route.removeAttribute('aria-hidden');
    route.prepend(nav);

    const setAt = (i) => {
      if (i === at) return;
      at = i;
      story.dataset.at = i;
      links.forEach((l, k) => {
        l.classList.toggle('is-past', k <= i);
        if (k === i) l.setAttribute('aria-current', 'step'); else l.removeAttribute('aria-current');
      });
    };
    const draw = () => {
      track.style.transform = `translate3d(${-pos.toFixed(2)}px,0,0)`;
      let i = 0;
      while (i < last - 1 && pos >= stops[i + 1]) i++;
      const t = clamp((pos - stops[i]) / Math.max(1, stops[i + 1] - stops[i]));
      const q = i + t;
      const x = q * geo.dx, y = geo.y - 2 * t * (1 - t) * geo.lift;
      const tilt = Math.atan2(-(2 - 4 * t) * geo.lift, geo.dx) * 57.3 * clamp(t * (1 - t) * 9);
      plane.style.transform = `translate(${(geo.left + x - geo.pw * .56).toFixed(1)}px,${(geo.top + y - geo.ph - 6).toFixed(1)}px) rotate(${tilt.toFixed(1)}deg)`;
      done.style.strokeDasharray = `${q.toFixed(3)} ${last + 1}`;
      setAt(Math.round(q));
    };
    const tick = () => {
      pos += (aim - pos) * .2;
      if (Math.abs(aim - pos) < .5) pos = aim;
      draw();
      raf = pos === aim ? 0 : requestAnimationFrame(tick);
    };
    const scroll = (now) => {
      const r = run.getBoundingClientRect();
      if (on) {
        aim = clamp(-r.top * PACE, 0, far);
        if (now === true) { pos = aim; draw(); } else if (aim !== pos && !raf) raf = requestAnimationFrame(tick);
        return;
      }
      const vh = innerHeight;
      let i = 0;
      yrs.forEach((y, k) => { if (y.getBoundingClientRect().top < vh * .55) i = k; });
      setAt(i);
      route.style.setProperty('--rp', clamp((vh * .47 - r.top - top0) / (r.height - top0 - 90)).toFixed(4));
    };
    const measure = () => {
      on = wide.matches && !reduce && !preview;
      story.classList.toggle('is-travel', on);
      run.style.height = '';
      if (!on) {
        cancelAnimationFrame(raf);
        raf = 0;
        track.style.transform = plane.style.transform = '';
        top0 = yrs[0].offsetTop + 40;
        route.style.setProperty('--rt', top0 + 'px');
        scroll();
        return;
      }
      const w = stage.clientWidth;
      far = Math.max(1, track.offsetWidth - w);
      run.style.height = stage.offsetHeight + far / PACE + 'px';
      stops = yrs.map((y, i) => (i === 0 ? 0 : i === last ? far : clamp(y.offsetLeft + y.offsetWidth / 2 - w / 2, 1, far - 1)));
      const nw = nav.offsetWidth, dx = nw / last, y = 44, lift = 48;
      geo = { dx, y, lift, left: (w - nw) / 2, top: nav.offsetTop, pw: plane.offsetWidth, ph: plane.offsetHeight || 32 };
      let d = `M0 ${y}`;
      for (let i = 0; i < last; i++) d += `Q${(i + .5) * dx} ${y - lift} ${(i + 1) * dx} ${y}`;
      way.setAttribute('d', d);
      done.setAttribute('d', d);
      scroll(true);
    };
    measure();
    addEventListener('resize', measure);
    addEventListener('load', measure);
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(measure);
    return { scroll };
  })();

  /* ---------- AI: islands join up once the belief is being read ---------- */
  const ai = $('[data-aiflow]');
  const belief = ai && $('[data-belief]', ai);

  /* ---------- one line that fills in as it is read ---------- */
  const fills = [];
  if (!reduce && !preview) {
    $$('[data-fill]').forEach((el) => {
      const words = el.textContent.trim().split(/\s+/);
      el.textContent = '';
      words.forEach((w, i) => {
        const s = document.createElement('span');
        s.className = 'w';
        s.textContent = w;
        el.append(s, i < words.length - 1 ? ' ' : '');
      });
      fills.push({ el, words: $$('.w', el) });
    });
  }

  /* ---------- way of working: the rail fills, steps light up ---------- */
  const way = $('[data-way]');
  const steps = way ? $$('.step', way) : [];
  const rail = way && $('.flow__rail', way);

  /* ---------- slight drift on large images ---------- */
  const drifts = reduce ? [] : $$('[data-drift]').map((el) => ({ el, box: el.parentElement, on: false }));
  if (hasIO && drifts.length) {
    const dio = new IntersectionObserver((es) => es.forEach((e) => {
      const d = drifts.find((x) => x.box === e.target);
      if (d) d.on = e.isIntersecting;
    }), { rootMargin: '80px' });
    drifts.forEach((d) => dio.observe(d.box));
  }
  const fits = (d) => getComputedStyle(d.box).overflow !== 'visible';
  drifts.forEach((d) => { d.clip = fits(d); });

  /* ---------- one rAF-throttled scroll handler ---------- */
  let ticking = false;
  const run = () => {
    ticking = false;
    const vh = innerHeight;
    if (trip) trip.scroll();
    if (belief) ai.classList.toggle('is-linked', belief.getBoundingClientRect().top < vh * 0.66);
    fills.forEach(({ el, words }) => {
      const r = el.getBoundingClientRect();
      const p = clamp((vh * 0.86 - r.top) / (vh * 0.42 + r.height * 0.5));
      const n = Math.round(p * words.length);
      words.forEach((w, i) => w.classList.toggle('on', i < n));
    });
    if (way) {
      const r = way.getBoundingClientRect();
      const p = clamp((vh * 0.78 - r.top) / (r.height * 0.8));
      way.style.setProperty('--fp', p.toFixed(3));
      const wide = rail && rail.offsetParent !== null;
      steps.forEach((s, i) => s.classList.toggle('is-on', wide
        ? p > 0.02 && p >= i / (steps.length - 1) - 0.03
        : s.getBoundingClientRect().top < vh * 0.68));
    }
    drifts.forEach((d) => {
      if (!d.on) return;
      const r = d.box.getBoundingClientRect();
      const t = clamp((r.top + r.height / 2 - vh / 2) / vh, -1, 1);
      d.el.style.transform = d.clip ? `translateY(${(t * -14).toFixed(1)}px) scale(1.07)` : `translateY(${(t * -14).toFixed(1)}px)`;
    });
  };
  const req = () => { if (!ticking) { ticking = true; requestAnimationFrame(run); } };
  addEventListener('scroll', req, { passive: true });
  addEventListener('resize', req);
  run();
})();
