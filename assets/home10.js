/* Drawlead Home 10 — interaction layer (vanilla, no dependencies).
   Ported from drawlead-website/js/main.js for templates/home10-body.php. */
(() => {
  'use strict';
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
  const lerp = (a, b, t) => a + (b - a) * t;
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const hasIO = 'IntersectionObserver' in window;
  // Admin → Pages → Page Content preview: leave text nodes as rendered, the
  // editor's markers are in them.
  const preview = new URLSearchParams(location.search).has('pc_preview');

  /* ---------- links to sections of the previous home page ---------- */
  const moved = { functions: 'solutions', tech: 'solutions', cases: 'case-studies', dashboards: 'data', cta: 'contact' };
  const jumpMoved = () => {
    const to = document.getElementById(moved[location.hash.slice(1)] || '');
    if (to) to.scrollIntoView({ behavior: 'instant' });
  };
  addEventListener('hashchange', jumpMoved);
  jumpMoved();

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
    // the rendered number, not data-count: it may have been edited in admin
    const end = parseInt(el.textContent, 10);
    if (reduce || !hasIO || preview || isNaN(end)) return;
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

  /* ---------- problems heading: Tools ⇄ Complexity flip ----------
     The red box takes the width of the word showing, sized in em so it
     scales with the heading. The last word repeats the first, so after
     sliding onto it we snap back to the start without a visible jump. */
  const flipBox = $('[data-flip]');
  if (flipBox && !reduce) {
    const col = $('.pb-flip', flipBox);
    const words = $$('span', col);
    let i = 0, widths = [];
    const measure = () => {
      const fs = parseFloat(getComputedStyle(flipBox).fontSize);
      widths = words.map((w) => w.getBoundingClientRect().width / fs);
      flipBox.style.width = widths[i] + 'em';
    };
    const show = (n, snap) => {
      i = n;
      col.classList.toggle('is-snap', snap); flipBox.classList.toggle('is-snap', snap);
      col.style.transform = `translateY(${-1.14 * n}em)`;
      flipBox.style.width = widths[n] + 'em';
    };
    const step = () => {
      show(i + 1, false);
      if (i === words.length - 1) setTimeout(() => { show(0, true); void col.offsetWidth; }, 750);
    };
    (document.fonts ? document.fonts.ready : Promise.resolve()).then(() => {
      measure();
      setInterval(() => { if (!document.hidden) step(); }, 2800);
      addEventListener('resize', measure);
    });
  }

  /* ---------- capabilities: sticky list + cards scrolling in ---------- */
  const cxWrap = $('.cx-panels');
  const cxTabs = $$('.cx-tab');
  const cxPanels = cxWrap ? $$('.cx-panel', cxWrap) : [];
  if (cxWrap && cxPanels.length) {
    const cxTop = () => parseFloat(getComputedStyle(cxWrap.parentElement).getPropertyValue('--cx-top')) || 112;
    cxTabs.forEach((t, i) => t.addEventListener('click', () => {
      const y = cxPanels[i].getBoundingClientRect().top + scrollY - cxTop();
      scrollTo({ top: y, behavior: reduce ? 'auto' : 'smooth' });
    }));
    if (hasIO && !reduce) {
      const pio = new IntersectionObserver((es) => es.forEach((e) => {
        if (e.isIntersecting) { e.target.classList.add('is-in'); pio.unobserve(e.target); }
      }), { rootMargin: '0px 0px -12% 0px' });
      cxPanels.forEach((p) => pio.observe(p));
    } else cxPanels.forEach((p) => p.classList.add('is-in'));
    let cxRaf = 0;
    const cxUpdate = () => {
      cxRaf = 0;
      const line = innerHeight * .45;
      let act = 0;
      cxPanels.forEach((p, i) => { if (p.getBoundingClientRect().top <= line) act = i; });
      cxTabs.forEach((t, i) => t.setAttribute('aria-current', i === act ? 'true' : 'false'));
    };
    const cxQueue = () => { if (!cxRaf) cxRaf = requestAnimationFrame(cxUpdate); };
    addEventListener('scroll', cxQueue, { passive: true });
    addEventListener('resize', cxQueue);
    cxUpdate();

    if (!reduce && matchMedia('(hover:hover) and (pointer:fine)').matches) {
      const tilt = (p, x, y) => {
        p.style.setProperty('--ry', (x * 5).toFixed(2) + 'deg');
        p.style.setProperty('--rx', (-y * 4).toFixed(2) + 'deg');
        p.style.setProperty('--gx', (50 + x * 60).toFixed(1) + '%');
      };
      cxPanels.forEach((p) => {
        p.addEventListener('pointermove', (e) => {
          const r = p.getBoundingClientRect();
          tilt(p, (e.clientX - r.left) / r.width - .5, (e.clientY - r.top) / r.height - .5);
        });
        p.addEventListener('pointerleave', () => tilt(p, 0, 0));
      });
    }
  }

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
  const bar = $('.progress');
  let ticking = false;
  const onScroll = () => {
    ticking = false;
    const y = scrollY, vh = innerHeight;
    const max = document.documentElement.scrollHeight - vh;
    bar.style.setProperty('--sp', max > 0 ? (y / max).toFixed(4) : 0);
    if (flow) {
      const r = flow.getBoundingClientRect();
      const p = clamp((vh * 0.72 - r.top) / (r.height * 0.75));
      flow.style.setProperty('--fp', p.toFixed(3));
      const rail = $('.flow__rail', flow);
      if (rail && rail.offsetParent !== null) steps.forEach((s, i) => s.classList.toggle('is-on', p >= i / 3 - 0.03 && p > 0.02));
    }
  };
  const req = () => { if (!ticking) { ticking = true; requestAnimationFrame(onScroll); } };
  addEventListener('scroll', req, { passive: true });
  addEventListener('resize', req);
  onScroll();

  /* ---------- selected work: colour panels ---------- */
  const swx = $('[data-swx]');
  if (swx) {
    const ps = $$('.swx-p', swx), btns = ps.map((p) => $('.swx-tab', p));
    const stacked = matchMedia('(max-width:1240px)');
    const openAt = () => ps.findIndex((p) => p.classList.contains('is-open'));
    const select = (i) => {
      if (ps[i].classList.contains('is-open')) return;
      ps.forEach((p, k) => {
        p.classList.toggle('is-open', k === i);
        btns[k].setAttribute('aria-expanded', k === i);
      });
      // stacked: the panel above has just closed, so bring this one's top back into view
      if (stacked.matches) setTimeout(() => {
        const top = ps[i].getBoundingClientRect().top;
        if (top < 90 || top > innerHeight * .6) scrollTo({ top: top + scrollY - 100, behavior: reduce ? 'auto' : 'smooth' });
      }, reduce ? 0 : 580);
    };
    const step = (d) => (openAt() + d + ps.length) % ps.length;
    btns.forEach((b, i) => {
      b.addEventListener('click', () => select(i));
      // side by side: left and right arrow keys move between panels
      b.addEventListener('keydown', (e) => {
        const d = { ArrowLeft: -1, ArrowRight: 1 }[e.key];
        if (!d || stacked.matches) return;
        e.preventDefault();
        const to = (i + d + ps.length) % ps.length;
        select(to);
        btns[to].focus({ preventScroll: true });
      });
    });
    $$('[data-swx-step]', swx.parentElement).forEach((a) => a.addEventListener('click', () => select(step(+a.dataset.swxStep))));
  }

  /* ---------- 8-week program: week cards scroll, the calendar flips 1 → 8 ---------- */
  const pg = $('[data-pg]');
  if (pg) {
    const weeks = $$('.pg-week', pg);
    const stage = $('.pg-stage', pg), pad = $('.pg-cal__pad', pg);
    const num = $('[data-cal-n]', pg), lab = $('[data-cal-l]', pg);
    const capK = $('[data-cal-k]', pg), capT = $('[data-cal-t]', pg), dots = $$('.pg-dots li', pg);
    let shown = 0, target = 0, busy = false;
    const paint = (i) => {
      num.textContent = i + 1;
      lab.textContent = i ? 'Weeks' : 'Week';
      stage.style.setProperty('--t', getComputedStyle(weeks[i]).getPropertyValue('--t'));
      capK.textContent = 'Week ' + String(i + 1).padStart(2, '0');
      capT.textContent = weeks[i].dataset.stage;
      dots.forEach((d, k) => { d.classList.toggle('is-on', k <= i); d.classList.toggle('is-cur', k === i); });
      weeks.forEach((w, k) => w.classList.toggle('is-on', k === i));
    };
    const step = () => {
      if (shown === target) { busy = false; return; }
      busy = true;
      const fwd = target > shown, next = shown + (fwd ? 1 : -1);
      if (reduce) { shown = target; paint(shown); busy = false; return; }
      const leaf = document.createElement('div');
      leaf.className = 'pg-cal__leaf' + (fwd ? '' : ' is-down');
      leaf.style.animationDuration = (Math.abs(target - shown) > 1 ? 340 : 640) + 'ms';
      leaf.innerHTML = '<div class="pg-cal__page"><b>' + ((fwd ? shown : next) + 1) + '</b></div><div class="pg-cal__page pg-face--back"></div>';
      pad.appendChild(leaf);
      if (fwd) paint(next); // the new page sits underneath while the old one flips away
      leaf.addEventListener('animationend', () => {
        if (!fwd) paint(next);
        leaf.remove();
        shown = next;
        step();
      }, { once: true });
    };
    let pgRaf = 0;
    const pgUpdate = () => {
      pgRaf = 0;
      const line = innerHeight * .55;
      let act = 0;
      weeks.forEach((w, i) => { if (w.getBoundingClientRect().top <= line) act = i; });
      if (act !== target) { target = act; if (!busy) step(); }
    };
    const pgQueue = () => { if (!pgRaf) pgRaf = requestAnimationFrame(pgUpdate); };
    addEventListener('scroll', pgQueue, { passive: true });
    addEventListener('resize', pgQueue);
    paint(0);
    pgUpdate();
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
