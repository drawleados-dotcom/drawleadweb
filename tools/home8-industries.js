 <script>
 // Industries stack: vertical page scroll drives which panel is open.
 //
 // Widths are continuous, not stepped. Progress p runs 0..n-1 across the runway and
 // every panel gets a weight w = smoothstep(1 - |p - i|), so at any point between
 // panel i and i+1 exactly two panels are part-open. smoothstep has the property
 // ss(x) + ss(1-x) === 1, so those two weights always sum to one and the stack's
 // total width never changes — no reflow of the row as it animates, and no jump
 // when one panel finishes handing over to the next.
 //
 // EXP is solved from the container rather than guessed, so the open panel always
 // lands exactly flush: sum(widths) - (n-1)*OVERLAP === container width.
 (function(){
  const outer  = document.getElementById('isOuter');
  const sticky = document.getElementById('isSticky');
  const stack  = document.getElementById('isStack');
  if(!outer || !sticky || !stack) return;

  const panels = Array.from(stack.children);
  const n = panels.length;
  if(n < 2) return;

  const narrow = window.matchMedia('(max-width:900px)');
  const still  = window.matchMedia('(prefers-reduced-motion: reduce)');

  const OVERLAP = 24;
  let runway = 0, stickyTop = 0, live = false;

  const ss = x => x * x * (3 - 2 * x);                 // smoothstep
  const clamp01 = x => x < 0 ? 0 : x > 1 ? 1 : x;

  function teardown(){
   live = false;
   outer.style.height = '';
   outer.classList.remove('is-live');
   panels.forEach(p => { p.style.removeProperty('--w'); p.style.removeProperty('--k'); p.style.zIndex = ''; });
  }

  function measure(){
   if(narrow.matches || still.matches){ teardown(); return; }
   live = true;
   outer.classList.add('is-live');

   const nav  = document.querySelector('nav');
   const navH = nav ? nav.offsetHeight : 0;
   stickyTop  = navH + Math.max(0, (window.innerHeight - navH - sticky.offsetHeight) / 2);
   outer.style.setProperty('--is-top', stickyTop + 'px');

   // one viewport-ish of scroll per handover
   runway = (n - 1) * Math.max(320, Math.round(window.innerHeight * 0.62));
   outer.style.height = (sticky.offsetHeight + runway) + 'px';
  }

  function paint(){
   if(!live) return;
   const W = stack.clientWidth;
   if(W <= 0) return;

   // GUT keeps the stack off the container's edges; the panels lay out inside
   // it, so the outermost ones can never touch or overflow the edge.
   const GUT = Math.min(40, Math.max(12, Math.round(W * 0.032)));
   const IW  = W - GUT * 2;
   const COL = Math.min(96, Math.max(58, Math.round(IW * 0.062)));
   // Solved against the inner width, then capped: past ~880px the open panel is
   // wider than its content needs and the copy strands in the middle of it.
   // Under the cap the run is narrower than IW, and the stack centres the slack
   // evenly, which is what balances the composition left to right.
   const EXP = Math.min(IW + (n - 1) * (OVERLAP - COL), 880);
   if(EXP <= COL){ teardown(); return; }      // too cramped to open a panel
   stack.style.setProperty('--gut', GUT + 'px');
   stack.style.setProperty('--exp', EXP.toFixed(2) + 'px');
   stack.style.setProperty('--col', COL + 'px');

   const rect = outer.getBoundingClientRect();
   const p = clamp01((stickyTop - rect.top) / runway) * (n - 1);

   let top = 0, topZ = 0;
   panels.forEach((panel, i) => {
    const w = ss(clamp01(1 - Math.abs(p - i)));
    panel.style.setProperty('--w', (COL + w * (EXP - COL)).toFixed(2) + 'px');
    panel.style.setProperty('--k', w.toFixed(4));     // CSS reads this for fade and scale
    // The stack has to fan OUTWARD from whichever panel is open, or the one
    // directly after it gets covered on its left by the open panel and on its
    // right by its own neighbour, leaving a 24px sliver instead of a readable
    // spine. Ramping z down with distance means a panel is only ever covered on
    // the side facing the open one.
    const z = Math.max(1, Math.round(100 - Math.abs(p - i) * 14));
    panel.style.zIndex = z;
    // and the spine centres on the half that is actually showing
    const dir = i < p ? -1 : i > p ? 1 : 0;
    panel.style.setProperty('--sh', (dir * (OVERLAP / 2) * (1 - w)).toFixed(2) + 'px');
    if(z > topZ){ topZ = z; top = i; }
   });
   panels.forEach((panel, i) => panel.classList.toggle('is-on', i === top));
  }

  let ticking = false;
  function onScroll(){
   if(ticking) return;
   ticking = true;
   requestAnimationFrame(() => { ticking = false; paint(); });
  }

  function reset(){ measure(); paint(); }

  reset();
  window.addEventListener('resize', reset);
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('load', reset);
  if(document.fonts && document.fonts.ready) document.fonts.ready.then(reset);
  if(narrow.addEventListener) narrow.addEventListener('change', reset);
  if(still.addEventListener)  still.addEventListener('change', reset);
 })();
 </script>
