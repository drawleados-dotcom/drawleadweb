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

  /* ---------- story: the illustration and ground follow the chapter ---------- */
  const story = $('[data-story]');
  const chs = story ? $$('[data-ch]', story) : [];
  let cur = -1;
  const setChapter = (vh) => {
    let i = 0;
    chs.forEach((c, k) => { if (c.getBoundingClientRect().top < vh * 0.5) i = k; });
    if (i === cur) return;
    cur = i;
    story.dataset.at = i;
    chs.forEach((c, k) => c.classList.toggle('is-at', k === i));
  };

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
    if (story) setChapter(vh);
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
