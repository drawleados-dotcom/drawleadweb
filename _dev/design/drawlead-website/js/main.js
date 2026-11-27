/* Drawlead — interaction layer (vanilla, no dependencies) */
(() => {
  'use strict';
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
  const lerp = (a, b, t) => a + (b - a) * t;
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const hasIO = 'IntersectionObserver' in window;

  /* ---------- reveal on scroll ---------- */
  const reveals = $$('.reveal');
  if (hasIO) {
    const io = new IntersectionObserver((es) => es.forEach((e) => {
      if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
    }), { threshold: 0.08, rootMargin: '0px 0px -6% 0px' });
    reveals.forEach((el) => io.observe(el));
  } else reveals.forEach((el) => el.classList.add('is-in'));

  /* ---------- illustrations only animate while on screen ---------- */
  const ils = $$('.il');
  if (hasIO) {
    const lio = new IntersectionObserver((es) => es.forEach((e) => e.target.classList.toggle('is-live', e.isIntersecting)), { rootMargin: '120px' });
    ils.forEach((el) => lio.observe(el));
  } else ils.forEach((el) => el.classList.add('is-live'));

  /* ---------- count-up ---------- */
  $$('[data-count]').forEach((el) => {
    const end = +el.dataset.count;
    if (reduce || !hasIO) return;
    el.textContent = '0';
    const o = new IntersectionObserver((es) => {
      if (!es[0].isIntersecting) return;
      o.disconnect();
      const t0 = performance.now(), d = 1400;
      const tick = (t) => {
        const p = clamp((t - t0) / d);
        el.textContent = Math.round(end * (1 - Math.pow(1 - p, 3)));
        if (p < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    }, { threshold: 0.6 });
    o.observe(el);
  });

  /* ---------- spotlight cards ---------- */
  $$('.spot').forEach((el) => {
    el.addEventListener('pointermove', (e) => {
      const r = el.getBoundingClientRect();
      el.style.setProperty('--mx', (e.clientX - r.left) + 'px');
      el.style.setProperty('--my', (e.clientY - r.top) + 'px');
    }, { passive: true });
  });

  /* ---------- mobile drawer ---------- */
  const burger = $('.nav__burger'), drawer = $('#drawer');
  const setDrawer = (open) => {
    burger.setAttribute('aria-expanded', open);
    burger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    drawer.hidden = !open;
  };
  burger.addEventListener('click', () => setDrawer(burger.getAttribute('aria-expanded') !== 'true'));
  $$('a', drawer).forEach((a) => a.addEventListener('click', () => setDrawer(false)));
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setDrawer(false); });

  /* ---------- capability summary → card ---------- */
  $$('.capsum').forEach((b) => b.addEventListener('click', () => {
    const t = document.getElementById(b.dataset.target);
    if (!t) return;
    t.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'center' });
    t.classList.remove('is-flash'); void t.offsetWidth; t.classList.add('is-flash');
    t.focus({ preventScroll: true });
  }));
  if (hasIO) {
    const sums = $$('.capsum');
    const cio = new IntersectionObserver((es) => es.forEach((e) => {
      const i = +e.target.id.replace('cap-', '') - 1;
      sums[i] && sums[i].classList.toggle('is-active', e.isIntersecting);
    }), { rootMargin: '-35% 0px -35% 0px' });
    $$('.cap').forEach((c) => cio.observe(c));
  }

  /* ---------- problem → solution ---------- */
  const pairs = $$('.pair');
  if (hasIO) {
    const pio = new IntersectionObserver((es) => es.forEach((e) => e.target.classList.toggle('is-resolved', e.isIntersecting || e.boundingClientRect.top < 0)),
      { rootMargin: '-8% 0px -42% 0px' });
    pairs.forEach((p) => pio.observe(p));
  } else pairs.forEach((p) => p.classList.add('is-resolved'));
  pairs.forEach((p) => {  // hover/focus pairing
    $$('.pcard', p).forEach((c) => c.addEventListener('focus', () => p.classList.add('is-resolved')));
  });

  /* morph stage: scattered tools snap into one connected system */
  const morph = $('[data-morph]');
  const tiles = morph ? $$('.mt', morph) : [];
  const stubs = morph ? $$('.stub', morph) : [];
  const ease = (t) => t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
  const setMorph = (p) => {
    morph.style.setProperty('--p', p.toFixed(3));
    const pts = tiles.map((t, i) => {
      const local = ease(clamp(p * 1.35 - i * 0.05));
      const x = lerp(+t.dataset.sx, +t.dataset.ex, local), y = lerp(+t.dataset.sy, +t.dataset.ey, local);
      t.setAttribute('transform', `translate(${x.toFixed(1)} ${y.toFixed(1)}) rotate(${lerp(+t.dataset.sr, 0, local).toFixed(2)} 56 28)`);
      return [x + 56, y + 28];
    });
    stubs.forEach((s) => {
      const a = pts[+s.dataset.a], b = pts[+s.dataset.b];
      s.setAttribute('d', `M${a[0].toFixed(0)} ${a[1].toFixed(0)} L${b[0].toFixed(0)} ${b[1].toFixed(0)}`);
    });
  };
  if (morph) setMorph(reduce ? 1 : 0);

  /* ---------- connected workflow (how we work) ---------- */
  const flow = $('[data-flow]');
  const steps = flow ? $$('.step', flow) : [];
  if (hasIO && steps.length) {
    const sio = new IntersectionObserver((es) => es.forEach((e) => {
      const rail = $('.flow__rail', flow);
      if (rail && rail.offsetParent !== null) return; // desktop uses rail progress
      e.target.classList.toggle('is-on', e.isIntersecting || e.boundingClientRect.top < 0);
    }), { rootMargin: '-30% 0px -30% 0px' });
    steps.forEach((s) => sio.observe(s));
  }

  /* ---------- scroll-linked state (one rAF-throttled handler) ---------- */
  const nav = $('#nav'), bar = $('.progress');
  const pairsEl = $('.pairs');
  const links = $$('[data-nav]');
  const groups = [
    ['#top', 'credibility'].map((id) => id.replace('#', '')),
    ['solutions', 'problems', 'approach', 'program', 'data', 'industries'],
    ['case-studies', 'industry-focus', 'testimonials', 'contact'],
  ];
  const secs = groups.map((g) => g.map((id) => document.getElementById(id)).filter(Boolean));
  let ticking = false;
  const onScroll = () => {
    ticking = false;
    const y = scrollY, vh = innerHeight;
    const max = document.documentElement.scrollHeight - vh;
    bar.style.setProperty('--sp', max > 0 ? (y / max).toFixed(4) : 0);
    nav.classList.toggle('is-scrolled', y > 24);
    if (morph && !reduce && pairsEl) {
      const r = pairsEl.getBoundingClientRect();
      setMorph(clamp((vh * 0.72 - r.top) / (r.height * 0.8)));
    }
    if (flow) {
      const r = flow.getBoundingClientRect();
      const p = clamp((vh * 0.72 - r.top) / (r.height * 0.75));
      flow.style.setProperty('--fp', p.toFixed(3));
      const rail = $('.flow__rail', flow);
      if (rail && rail.offsetParent !== null) steps.forEach((s, i) => s.classList.toggle('is-on', p >= i / 3 - 0.03 && p > 0.02));
    }
    let active = 0;
    secs.forEach((list, gi) => list.forEach((s) => { if (s.getBoundingClientRect().top <= vh * 0.4) active = gi; }));
    links.forEach((a, i) => a.classList.toggle('is-active', i === active));
  };
  const req = () => { if (!ticking) { ticking = true; requestAnimationFrame(onScroll); } };
  addEventListener('scroll', req, { passive: true });
  addEventListener('resize', req);
  onScroll();

  /* ---------- generic accessible tabs ---------- */
  function tabs(list, orientation, onChange) {
    const tabEls = $$('[role="tab"]', list);
    const panels = tabEls.map((t) => document.getElementById(t.getAttribute('aria-controls')));
    const select = (i, focus) => {
      tabEls.forEach((t, k) => { t.setAttribute('aria-selected', k === i); t.tabIndex = k === i ? 0 : -1; });
      panels.forEach((p, k) => { p.hidden = k !== i; });
      if (focus) tabEls[i].focus();
      onChange && onChange(i);
    };
    tabEls.forEach((t, i) => {
      t.addEventListener('click', () => select(i, false));
      t.addEventListener('keydown', (e) => {
        const prev = orientation === 'vertical' ? 'ArrowUp' : 'ArrowLeft', next = orientation === 'vertical' ? 'ArrowDown' : 'ArrowRight';
        let n = null;
        if (e.key === next || (orientation === 'vertical' && e.key === 'ArrowRight')) n = (i + 1) % tabEls.length;
        else if (e.key === prev || (orientation === 'vertical' && e.key === 'ArrowLeft')) n = (i - 1 + tabEls.length) % tabEls.length;
        else if (e.key === 'Home') n = 0;
        else if (e.key === 'End') n = tabEls.length - 1;
        if (n !== null) { e.preventDefault(); select(n, true); }
      });
    });
    return { select, count: tabEls.length, tabEls };
  }

  /* industries */
  const itl = $('.itablist');
  if (itl) tabs(itl, 'vertical');

  /* ---------- 8-week timeline ---------- */
  const tl = $('[data-timeline]');
  if (tl) {
    const rail = $('.wkrail', tl), phases = $$('.phase', tl);
    const playBtn = $('[data-play]', tl);
    let cur = 0, userPaused = reduce, hover = false, inView = false, timer = null;
    const DUR = 7000;
    const T = tabs(rail, 'horizontal', (i) => { cur = i; paint(); schedule(); });
    const paint = () => {
      rail.style.setProperty('--tp', (cur / (T.count - 1)).toFixed(3));
      $('.wkrail__line', rail).style.setProperty('--tp', (cur / (T.count - 1)).toFixed(3));
      T.tabEls.forEach((t, k) => t.classList.toggle('is-done', k < cur));
      phases.forEach((p, k) => p.classList.toggle('is-on', k === (cur < 4 ? 0 : 1)));
    };
    const running = () => !userPaused && !hover && inView;
    const schedule = () => {
      clearTimeout(timer);
      tl.classList.remove('is-playing'); void tl.offsetWidth;
      if (!running()) return;
      tl.style.setProperty('--dur', DUR + 'ms');
      tl.classList.add('is-playing');
      timer = setTimeout(() => T.select((cur + 1) % T.count, false), DUR);
    };
    const manual = (i) => { userPaused = true; syncPlay(); T.select(i, false); };
    const syncPlay = () => {
      playBtn.setAttribute('aria-pressed', userPaused);
      playBtn.setAttribute('aria-label', userPaused ? 'Resume automatic progression' : 'Pause automatic progression');
      playBtn.innerHTML = `<svg class="ic" aria-hidden="true"><use href="#i-${userPaused ? 'play' : 'pause'}"/></svg>`;
      schedule();
    };
    T.tabEls.forEach((t, i) => t.addEventListener('click', () => { userPaused = true; syncPlay(); }));
    $('[data-prev]', tl).addEventListener('click', () => manual((cur - 1 + T.count) % T.count));
    $('[data-next]', tl).addEventListener('click', () => manual((cur + 1) % T.count));
    playBtn.addEventListener('click', () => { userPaused = !userPaused; syncPlay(); });
    tl.addEventListener('pointerenter', () => { hover = true; schedule(); });
    tl.addEventListener('pointerleave', () => { hover = false; schedule(); });
    if (hasIO) new IntersectionObserver((es) => { inView = es[0].isIntersecting; schedule(); }, { threshold: 0.35 }).observe(tl);
    syncPlay(); paint();
  }

  /* ---------- data sources ↔ visualisation ---------- */
  const dgrid = $('.data__grid');
  if (dgrid) {
    const set = (n) => {
      if (n) dgrid.dataset.active = n; else delete dgrid.dataset.active;
      $$('.src, .stream', dgrid).forEach((el) => el.classList.toggle('on', n && (el.dataset.src || el.dataset.stream) === String(n)));
      $$('.dcard', dgrid).forEach((c) => c.classList.toggle('is-on', n && c.dataset.src === String(n)));
    };
    $$('.dcard', dgrid).forEach((c) => {
      const n = c.dataset.src;
      c.addEventListener('pointerenter', () => set(n));
      c.addEventListener('pointerleave', () => set(null));
      c.addEventListener('focus', () => set(n));
      c.addEventListener('blur', () => set(null));
    });
  }
})();
