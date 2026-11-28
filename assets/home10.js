/* Drawlead Home 10 — interaction layer (vanilla, no dependencies).
   Ported from _dev/design/drawlead-website/js/main.js for templates/home10-body.php. */
(() => {
  'use strict';
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
  const lerp = (a, b, t) => a + (b - a) * t;
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const hasIO = 'IntersectionObserver' in window;
  // { on } stays true while el is within `margin` of the screen; cb (if set) runs
  // each time it comes near. Scroll-linked updaters check it before reading
  // positions: sections below the first screen are skipped by the browser
  // (content-visibility, home10.css), and measuring inside one makes it lay the
  // section out early, the very start-up work the skip saves.
  const near = (el, margin = '200px') => {
    const s = { on: !hasIO, cb: null };
    if (hasIO) new IntersectionObserver(([e]) => { s.on = e.isIntersecting; if (s.on && s.cb) s.cb(); }, { rootMargin: margin + ' 0px' }).observe(el);
    return s;
  };
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

  /* ---------- heading text reveal: words rise one by one from behind a mask ----------
     Section headings (not the hero, not the flipping problems heading) are
     split into words client-side, so the HTML and the admin editor keep the
     plain text. Highlighted spans stay intact; their words are split inside. */
  if (!reduce && !preview && hasIO) {
    const heads = $$('section:not(.hero) h2, .ahero h1, .bsh h1').filter((h) => !h.classList.contains('pb-h2') && !h.querySelector('[data-flip]'));
    const wordIO = new IntersectionObserver((es) => es.forEach((e) => {
      if (e.isIntersecting) { e.target.classList.add('is-in'); wordIO.unobserve(e.target); }
    }), { threshold: 0.25, rootMargin: '0px 0px -8% 0px' });
    heads.forEach((h) => {
      let n = 0;
      const word = (text) => {
        const outer = document.createElement('span');
        outer.className = 'tw';
        const inner = document.createElement('span');
        inner.className = 'tw__i';
        inner.style.setProperty('--i', n++);
        inner.textContent = text;
        outer.appendChild(inner);
        return outer;
      };
      const walk = (parent) => {
        Array.from(parent.childNodes).forEach((node) => {
          if (node.nodeType === 3) {
            const frag = document.createDocumentFragment();
            node.textContent.split(/(\s+)/).forEach((part) => {
              if (!part) return;
              frag.appendChild(/^\s+$/.test(part) ? document.createTextNode(part) : word(part));
            });
            parent.replaceChild(frag, node);
          } else if (node.nodeType === 1 && node.tagName !== 'BR' && !node.classList.contains('tw')) {
            walk(node);
          }
        });
      };
      walk(h);
      h.querySelectorAll('.reveal').forEach((el) => { el.classList.remove('reveal'); el.classList.add('is-in'); });
      h.classList.remove('reveal');
      h.classList.add('tr');
      wordIO.observe(h);
    });
  }

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

  /* ---------- every looping animation pauses while its section is off screen ----------
     .is-off sets animation-play-state:paused (home10.css). Running off screen
     they still cost style, layout and paint work on every frame, which
     makes taps wait (INP). */
  if (hasIO) {
    const zio = new IntersectionObserver((es) => es.forEach((e) => {
      e.target.classList.toggle('is-off', !e.isIntersecting);
      // SVG <animateMotion> dots ignore CSS: pause their <svg> directly.
      $$('svg', e.target).forEach((svg) => {
        if (svg.pauseAnimations && svg.querySelector('animate, animateMotion, animateTransform')) {
          if (e.isIntersecting) svg.unpauseAnimations(); else svg.pauseAnimations();
        }
      });
    }), { rootMargin: '150px 0px' });
    $$('main > section, main > div > section, footer').forEach((el) => zio.observe(el));
  }

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
    // measured and flipped only while the heading is near the screen
    const flipNear = near(flipBox);
    let measured = false;
    const ensure = () => { if (!measured) { measured = true; measure(); } };
    (document.fonts ? document.fonts.ready : Promise.resolve()).then(() => {
      flipNear.cb = ensure;
      if (flipNear.on) ensure();
      setInterval(() => { if (!document.hidden && flipNear.on) { ensure(); step(); } }, 2800);
      addEventListener('resize', () => { measured = false; if (flipNear.on) ensure(); });
    });
  }

  /* ---------- capabilities: sticky list + cards scrolling in ---------- */
  const cxWrap = $('.cx-panels');
  const byVisualOrder = (a, b) => Number(a.style.order || 0) - Number(b.style.order || 0);
  const cxTabs = $$('.cx-tab').sort(byVisualOrder);
  const cxPanels = cxWrap ? $$('.cx-panel', cxWrap).sort(byVisualOrder) : [];
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
    const cxNear = near(cxWrap);
    const cxQueue = () => { if (cxNear.on && !cxRaf) cxRaf = requestAnimationFrame(cxUpdate); };
    cxNear.cb = cxQueue; // first run: when the panels come near, not at start-up
    addEventListener('scroll', cxQueue, { passive: true });
    addEventListener('resize', cxQueue);

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
  const flowNear = flow ? near(flow) : null;
  let ticking = false;
  const onScroll = () => {
    ticking = false;
    const y = scrollY, vh = innerHeight;
    const max = document.documentElement.scrollHeight - vh;
    bar.style.setProperty('--sp', max > 0 ? (y / max).toFixed(4) : 0);
    if (flow && flowNear.on) {
      const r = flow.getBoundingClientRect();
      const p = clamp((vh * 0.72 - r.top) / (r.height * 0.75));
      flow.style.setProperty('--fp', p.toFixed(3));
      const rail = $('.flow__rail', flow);
      if (rail && rail.offsetParent !== null) steps.forEach((s, i) => s.classList.toggle('is-on', p >= i / 3 - 0.03 && p > 0.02));
    }
  };
  const req = () => { if (!ticking) { ticking = true; requestAnimationFrame(onScroll); } };
  if (flowNear) flowNear.cb = req;
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

  /* ---------- hero: each heading line is fitted to a width (desktop only) ----------
     the struck line and "Start Building" end at the eyebrow's right edge;
     "Smarter Business Solutions." runs nearly the full width of the hero */
  const fitS = $('[data-fit-strike]'), fitSb = $('.hero__sb'), fitHl = $('.hero__hl');
  const fitEb = $('.hero-eb'), fitBox = $('.hero__copy');
  if (fitS && fitSb && fitEb) {
    const fitTo = (el, measure, target) => {
      const w = measure.getBoundingClientRect().width;
      if (w && target) el.style.fontSize = (parseFloat(getComputedStyle(el).fontSize) * target / w).toFixed(2) + 'px';
    };
    const fitHero = () => {
      [fitS, fitSb, fitHl].forEach((e) => { if (e) e.style.fontSize = ''; });
      if (innerWidth <= 760) return;
      // tablets: only match the struck line to "Start Building" so the two
      // heading lines keep the same size; the eyebrow-width fit is for laptops up
      if (innerWidth < 1100) { fitTo(fitS, $('s', fitS), fitSb.getBoundingClientRect().width); return; }
      const eb = fitEb.offsetWidth;
      fitTo(fitSb, fitSb, eb);
      fitTo(fitS, $('s', fitS), eb);
      if (fitHl && fitBox) fitTo(fitHl, fitHl, fitBox.getBoundingClientRect().width * .94);
    };
    (document.fonts ? document.fonts.ready : Promise.resolve()).then(fitHero);
    addEventListener('resize', fitHero);
  }

  /* ---------- 8-week program: week cards scroll, calendar pages tear off 1 → 8 ---------- */
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
    // a ragged top edge, so the page looks torn along the perforation
    const ragged = () => {
      const pts = ['0% 100%', '0% 0.9em'];
      for (let x = 0; x <= 100; x += 4) pts.push(x + '% calc(0.75em + ' + (Math.random() * 0.32).toFixed(2) + 'em)');
      pts.push('100% 100%');
      return 'polygon(' + pts.join(',') + ')';
    };
    // tear: peel from one corner, then fall under gravity with spin and drift
    const tear = (leaf, speed, done) => {
      const dir = Math.random() < .5 ? -1 : 1;
      const h = leaf.offsetHeight || 200;
      leaf.style.transformOrigin = dir > 0 ? '0% 0%' : '100% 0%';
      const PEEL = 170 / speed;
      let t0 = 0, last = 0, x = 0, y = 0, a = 0;
      let vx = dir * (80 + Math.random() * 120), vy = -40, va = dir * (40 + Math.random() * 50);
      const G = 2400;
      const frame = (now) => {
        if (!t0) { t0 = last = now; }
        const el = now - t0;
        if (el < PEEL) {
          const p = el / PEEL;
          a = dir * 10 * p * p;
          y = -4 * p;
          if (p > .55 && !leaf.style.clipPath) leaf.style.clipPath = ragged();
        } else {
          const dt = Math.min(.034, (now - last) / 1000) * speed;
          vy += G * dt; vx *= .995; va *= .99;
          x += vx * dt; y += vy * dt; a += va * dt;
        }
        last = now;
        leaf.style.transform = `translate(${x.toFixed(1)}px,${y.toFixed(1)}px) rotate(${a.toFixed(2)}deg)`;
        leaf.style.opacity = String(clamp(1 - (y - h * .5) / (h * .9)));
        if (y > h * 1.6 || el > 2200) done(); else requestAnimationFrame(frame);
      };
      requestAnimationFrame(frame);
    };
    const step = () => {
      if (shown === target) { busy = false; return; }
      busy = true;
      const fwd = target > shown, next = shown + (fwd ? 1 : -1);
      if (reduce) { shown = target; paint(shown); busy = false; return; }
      const fast = Math.abs(target - shown) > 1 ? 1.9 : 1;
      const leaf = document.createElement('div');
      leaf.className = 'pg-cal__leaf' + (fwd ? '' : ' is-back');
      const face = pad.querySelector(':scope > .pg-cal__page').cloneNode(true);
      face.querySelector('b').removeAttribute('data-cal-n');
      face.querySelector('b').textContent = (fwd ? shown : next) + 1;
      leaf.appendChild(face);
      pad.appendChild(leaf);
      const finish = () => { leaf.remove(); shown = next; step(); };
      if (fwd) {
        paint(next); // the next page is already underneath while the old one tears away
        tear(leaf, fast, finish);
      } else {
        // scrolling back: the previous page settles back onto the pad
        leaf.style.animationDuration = (420 / fast) + 'ms';
        leaf.addEventListener('animationend', () => { paint(next); finish(); }, { once: true });
      }
    };
    let pgRaf = 0;
    const pgUpdate = () => {
      pgRaf = 0;
      const line = innerHeight * .55;
      let act = 0;
      weeks.forEach((w, i) => { if (w.getBoundingClientRect().top <= line) act = i; });
      if (act !== target) { target = act; if (!busy) step(); }
    };
    const pgNear = near(pg);
    const pgQueue = () => { if (pgNear.on && !pgRaf) pgRaf = requestAnimationFrame(pgUpdate); };
    let pgReady = false;
    pgNear.cb = () => { if (!pgReady) { pgReady = true; paint(0); } pgQueue(); }; // set up when it comes near
    addEventListener('scroll', pgQueue, { passive: true });
    addEventListener('resize', pgQueue);
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
