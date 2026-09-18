<?php
/**
 * Construction & Real Estate — bespoke industry page.
 *
 * templates/industry-body.php renders the other 19 industries from
 * includes/industries.php; it hands off to this file when the slug is
 * 'construction', so this page can have its own structure without the DB's
 * pages.template value changing and without touching the shared renderer.
 *
 * Nav, footer, typography, spacing and the .rv scroll reveal all come from the
 * site system. Everything specific to this page is .cx-* in
 * assets/industry-construction.css, which no other page loads.
 *
 * @var array $page      set by index.php
 * @var array $industry  set by industry-body.php before the hand-off
 */
$activePage = 'industry-construction';
include __DIR__ . '/partials/nav.php';

/* Photography is optional: until the file exists a labelled plate holds the space,
   so the layout never collapses and no broken image ships. */
$cxHero = is_file(__DIR__ . '/../assets/img/cx-hero.webp');
$cxCta  = is_file(__DIR__ . '/../assets/img/cx-cta.webp');

$cxProblems = [
 ['Limited Project Visibility',        'No single view of where each site actually stands today.'],
 ['Budget Overruns',                   'Costs drift past estimates before anyone sees the trend.'],
 ['Material Shortages and Wastage',    'Stock runs out mid-pour, or sits unused across sites.'],
 ['Poor Team Coordination',            'Engineers, contractors and office staff work off different numbers.'],
 ['Missed Leads and Follow-Ups',       'Enquiries sit in inboxes and WhatsApp until the buyer moves on.'],
 ['Payment and Cash Flow Issues',      'Invoices, retentions and receivables tracked in scattered sheets.'],
 ['Unorganized Data and Reports',      'Every report is rebuilt by hand from files nobody trusts.'],
 ['Customer Communication Gaps',       'Buyers chase updates that should reach them automatically.'],
];

$cxFeatures = [
 ['Real-Time Project Tracking',        'Live status, milestones and delays across every site.'],
 ['Budget and Expense Management',     'Estimates against actuals, with approvals before spend.'],
 ['Material and Inventory Management', 'Stock, indents and transfers tracked site by site.'],
 ['Team and Contractor Management',    'Assignments, attendance and contractor bills in one place.'],
 ['Lead and Sales Management',         'Every enquiry captured, assigned and followed up on time.'],
 ['Payment and Cash Flow Tracking',    'Receivables, payables and retentions visible as they move.'],
 ['Centralized Data and Reports',      'One source of truth, with reports generated not assembled.'],
 ['Better Customer Communication',     'Buyers kept updated automatically at every stage.'],
];

$cxOutcomes = [
 ['01', 'Accurate Estimates',   'Create reliable BOQs based on actual project costs.'],
 ['02', 'Controlled Approvals', 'Reduce unauthorized purchases and expenses.'],
 ['03', 'Secure Access',        'Protect business data with role-based permissions.'],
 ['04', 'Clear Audit History',  'Track every update, approval, and transaction.'],
];

$cxWhy = [
 ['Industry-Focused Solutions',   'Built around construction and real estate workflows, not a generic template.'],
 ['Custom Development',           'Your process drives the system, rather than the other way round.'],
 ['Experienced Development Team', 'Engineers who have shipped ERP and CRM for operations like yours.'],
 ['Latest Technologies',          'A modern, maintainable stack that will still be supportable in five years.'],
 ['Ongoing Support',              'A team that stays after go-live, through every change and new site.'],
 ['Flexible Pricing',             'Scoped in phases, so the spend follows the value delivered.'],
];

$cxFaqs = [
 ['What is construction and real estate ERP software?',
  'It is one system that runs the operational side of the business — projects, sites, materials, contractors, budgets, billing and customers — instead of separate spreadsheets and tools for each. Everyone works from the same live numbers.'],
 ['How does CRM software help real estate businesses?',
  'It captures every enquiry from every channel, assigns it to the right person, and keeps the follow-up on schedule. You can see which sources produce buyers, what stage each deal is at, and what is likely to close this month.'],
 ['Can Drawlead customize the software for our business?',
  'Yes. We build around your existing process rather than asking you to change it to fit the software. Approval flows, cost heads, billing stages and report formats are all shaped to how your team already works.'],
 ['Can the system manage multiple projects and locations?',
  'Yes. Every project and site is tracked separately, with a combined view across all of them. Budgets, materials, teams and payments roll up to company level while staying accurate per site.'],
 ['Can employees access the software from project sites?',
  'Yes. It runs in the browser on phones and tablets, so site engineers can update progress, raise indents and record attendance from the site itself.'],
 ['Can the software integrate with our existing tools?',
  'In most cases, yes — accounting software, payment gateways, WhatsApp and email are the common ones. We confirm what is possible for your specific tools during the consultation.'],
 ['Do you provide training and technical support?',
  'Yes. Training is included at handover for each role, and support continues afterwards for questions, changes and new requirements as the business grows.'],
];
?>
<link rel="stylesheet" href="<?= asset_url('/assets/industry-construction.css') ?>">

<!-- ═════════ 01 · HERO ═════════ -->
<section id="cx-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="cx-hero-grid">
  <div class="cx-hero-copy">
   <div class="eyebrow rv" style="justify-content:flex-start"><span class="eyebrow-text">Industry · Projects · Sites · Billing</span></div>
   <h1 class="cx-h1 rv">One Dashboard for Every Site, Every Project</h1>
   <p class="cx-lead rv">Run multi-site construction and real estate operations from a single system, instead of a different spreadsheet for every project.</p>
   <div class="cx-hero-cta rv">
    <button type="button" data-book class="btn btn-black">Get a Free Consultation</button>
    <button type="button" data-book class="btn btn-outline2">Request a Demo</button>
   </div>
  </div>

  <div class="cx-hero-visual rv">
   <div class="cx-shot<?= $cxHero ? '' : ' cx-shot-empty' ?>">
<?php if ($cxHero): ?>
    <img src="<?= asset_url('/assets/img/cx-hero.webp') ?>" alt="Construction site managed through Drawlead" decoding="async" fetchpriority="high">
<?php else: ?>
    <span class="cx-plate" aria-hidden="true">Site photography</span>
<?php endif; ?>
   </div>
   <!-- floating readouts: the system speaking, not decoration -->
   <div class="cx-float cx-float-a" aria-hidden="true">
    <div class="cx-float-k">Active Projects</div>
    <div class="cx-float-v">12</div>
    <div class="cx-float-bar"><i style="width:74%"></i></div>
   </div>
   <div class="cx-float cx-float-b" aria-hidden="true">
    <div class="cx-float-k">Site Progress</div>
    <div class="cx-float-v">68<span>%</span></div>
    <div class="cx-float-sub">Tower B · Slab 9</div>
   </div>
   <div class="cx-float cx-float-c" aria-hidden="true">
    <div class="cx-float-k">Expenses vs Budget</div>
    <div class="cx-float-v">₹4.2<span>Cr</span></div>
    <div class="cx-float-sub cx-ok">On budget</div>
   </div>
  </div>
 </div>
</section>

<!-- ═════════ 02 · THE PROBLEM ═════════ -->
<section id="cx-problem">
 <div class="eyebrow rv"><span class="eyebrow-text">The Problem</span></div>
 <h2 class="sec-h rv">Where Construction &amp; Real Estate Teams Get Stuck</h2>
 <p class="sec-sub rv">Construction and real estate businesses must manage projects, teams, expenses, materials, leads, payments, and customers. Without a connected system, daily operations can become difficult to control.</p>
 <div class="cx-prob-grid">
<?php foreach ($cxProblems as $n => $prob): ?>
  <article class="cx-prob rv" style="--d:<?= $n % 2 ?>">
   <span class="cx-prob-n"><?= str_pad((string) ($n + 1), 2, '0', STR_PAD_LEFT) ?></span>
   <span class="cx-prob-i" aria-hidden="true">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8.6v5"/><path d="M12 16.6h.01"/><path d="M10.6 3.9 2.5 18a1.6 1.6 0 0 0 1.4 2.4h16.2a1.6 1.6 0 0 0 1.4-2.4L13.4 3.9a1.6 1.6 0 0 0-2.8 0z"/></svg>
   </span>
   <h3 class="cx-prob-t"><?= $prob[0] ?></h3>
   <p class="cx-prob-d"><?= $prob[1] ?></p>
  </article>
<?php endforeach; ?>
 </div>
</section>

<!-- ═════════ 03 · UNIFIED ERP SOLUTION ═════════ -->
<section id="cx-erp">
 <div class="eyebrow rv"><span class="eyebrow-text">Unified ERP Solution</span></div>
 <h2 class="sec-h rv">Built for How Construction &amp; Real Estate Actually Works</h2>
 <p class="sec-sub rv">Drawlead provides custom ERP and CRM software that connects projects, teams, materials, finances, sales, and customers in one system.</p>

 <div class="cx-erp-wrap">
  <div class="cx-erp-col">
<?php foreach (array_slice($cxFeatures, 0, 4) as $f): ?>
   <article class="cx-feat rv"><h3><?= $f[0] ?></h3><p><?= $f[1] ?></p></article>
<?php endforeach; ?>
  </div>

  <!-- the product, built rather than photographed, so it stays sharp and on-brand -->
  <div class="cx-dash rv" aria-hidden="true">
   <div class="cx-dash-top">
    <span class="cx-dot"></span><span class="cx-dot"></span><span class="cx-dot"></span>
    <span class="cx-dash-title">Drawlead ERP · Projects</span>
   </div>
   <div class="cx-dash-body">
    <div class="cx-dash-kpis">
     <div class="cx-kpi"><span>Projects</span><b>12</b></div>
     <div class="cx-kpi"><span>On Track</span><b class="cx-ok">9</b></div>
     <div class="cx-kpi"><span>Delayed</span><b class="cx-warn">3</b></div>
    </div>
    <div class="cx-dash-rows">
     <div class="cx-row"><span>Skyline Tower B</span><i><em style="width:82%"></em></i><b>82%</b></div>
     <div class="cx-row"><span>Green Acres Villas</span><i><em style="width:64%"></em></i><b>64%</b></div>
     <div class="cx-row"><span>Harbour Offices</span><i><em style="width:41%" class="cx-bar-warn"></em></i><b>41%</b></div>
     <div class="cx-row"><span>Lakeview Phase 2</span><i><em style="width:23%"></em></i><b>23%</b></div>
    </div>
    <div class="cx-dash-foot">
     <div class="cx-chip"><span>Materials</span><b>Indent #418 approved</b></div>
     <div class="cx-chip"><span>Payments</span><b>₹62L received</b></div>
     <div class="cx-chip"><span>Leads</span><b>34 new this week</b></div>
    </div>
   </div>
  </div>

  <div class="cx-erp-col">
<?php foreach (array_slice($cxFeatures, 4) as $f): ?>
   <article class="cx-feat rv"><h3><?= $f[0] ?></h3><p><?= $f[1] ?></p></article>
<?php endforeach; ?>
  </div>
 </div>
</section>

<!-- ═════════ 04 · EXPECTED OUTCOMES ═════════ -->
<section id="cx-outcomes">
 <div class="eyebrow rv"><span class="eyebrow-text">Expected Outcomes</span></div>
 <h2 class="sec-h rv">What Changes After Go-Live</h2>
 <p class="sec-sub rv">See how your operations improve with better control, security, accuracy, and transparency.</p>
 <div class="cx-track-outer" id="cxTrackOuter">
  <div class="cx-track-pin" id="cxTrackPin">
   <div class="cx-track" id="cxTrack">
<?php foreach ($cxOutcomes as $o): ?>
    <article class="cx-out">
     <span class="cx-out-n"><?= $o[0] ?></span>
     <h3 class="cx-out-t"><?= $o[1] ?></h3>
     <p class="cx-out-d"><?= $o[2] ?></p>
    </article>
<?php endforeach; ?>
   </div>
  </div>
 </div>
</section>

<!-- ═════════ 05 · WHY DRAWLEAD ═════════ -->
<section id="cx-why">
 <div class="cx-why-grid">
  <div class="cx-why-left">
   <div class="eyebrow rv" style="justify-content:flex-start"><span class="eyebrow-text">Your Trusted Technology Partner</span></div>
   <h2 class="cx-why-h rv">Why Choose Drawlead</h2>
   <p class="cx-why-p rv">Drawlead develops custom ERP and CRM solutions that match the real needs of construction and real estate businesses.</p>
   <p class="cx-why-statement rv">Technology built around the way your business actually works.</p>
  </div>
  <div class="cx-why-right">
<?php foreach ($cxWhy as $n => $w): ?>
   <article class="cx-why-card rv<?= $n % 2 ? ' cx-offset' : '' ?>">
    <h3><?= $w[0] ?></h3><p><?= $w[1] ?></p>
   </article>
<?php endforeach; ?>
  </div>
 </div>
</section>

<!-- ═════════ 06 · FAQ ═════════ -->
<section id="cx-faq">
 <h2 class="sec-h rv">Frequently Asked Questions</h2>
 <div class="cx-faq-list rv">
<?php foreach ($cxFaqs as $n => $faq): $id = 'cxfaq' . $n; ?>
  <div class="cx-faq-item">
   <h3 class="cx-faq-h">
    <button type="button" class="cx-faq-q" aria-expanded="false" aria-controls="<?= $id ?>">
     <span><?= $faq[0] ?></span>
     <span class="cx-faq-ico" aria-hidden="true"></span>
    </button>
   </h3>
   <div class="cx-faq-a" id="<?= $id ?>" role="region" hidden>
    <p><?= $faq[1] ?></p>
   </div>
  </div>
<?php endforeach; ?>
 </div>
</section>

<!-- ═════════ 07 · FINAL CTA ═════════ -->
<section id="cx-cta">
 <div class="grid-bg" style="opacity:.5"></div>
 <div class="cx-glow" id="cxGlow" aria-hidden="true"><span class="cx-glow-blob"></span><span class="cx-glow-lines"></span></div>
 <div class="cx-cta-inner">
  <h2 class="cx-cta-h rv">Ready to Build a Smarter Business?</h2>
  <p class="cx-cta-p rv">Get a custom ERP and CRM solution built around your projects, teams, sales process, and business needs.</p>
  <div class="cx-cta-btn rv"><button type="button" data-book class="btn btn-black">Get a Free Quote</button></div>
  <div class="cx-cta-shot<?= $cxCta ? '' : ' cx-shot-empty' ?> rv">
<?php if ($cxCta): ?>
   <img src="<?= asset_url('/assets/img/cx-cta.webp') ?>" alt="Architectural view of a completed development" loading="lazy" decoding="async">
<?php else: ?>
   <span class="cx-plate" aria-hidden="true">Architectural photography</span>
<?php endif; ?>
  </div>
 </div>
</section>

<script>
// Construction page behaviour: FAQ accordion, the outcomes horizontal run, and the
// cursor glow on the closing CTA. Each piece checks for its own elements, so one
// failing cannot take the others down.

/* ── FAQ ──────────────────────────────────────────────────────────────────
   Height is animated by transitioning grid-template-rows 0fr -> 1fr, which needs
   no measurement and so cannot drift when the copy wraps differently. [hidden] is
   removed on open and restored after the collapse finishes, keeping closed panels
   out of the accessibility tree without blocking the transition. */
(function(){
 const items = document.querySelectorAll('#cx-faq .cx-faq-item');
 if(!items.length) return;
 items.forEach(function(item){
  const btn = item.querySelector('.cx-faq-q');
  const panel = item.querySelector('.cx-faq-a');
  if(!btn || !panel) return;
  btn.addEventListener('click', function(){
   const open = btn.getAttribute('aria-expanded') === 'true';
   if(open){
    item.classList.remove('is-open');
    btn.setAttribute('aria-expanded', 'false');
    const done = function(e){
     if(e.propertyName !== 'grid-template-rows') return;
     panel.hidden = true;
     panel.removeEventListener('transitionend', done);
    };
    panel.addEventListener('transitionend', done);
   } else {
    panel.hidden = false;
    // force a reflow so the browser has a closed state to animate away from.
    // rAF would also work, but this is synchronous and cannot be skipped.
    void panel.offsetHeight;
    item.classList.add('is-open');
    btn.setAttribute('aria-expanded', 'true');
   }
  });
 });
})();

/* ── Outcomes: horizontal run while pinned ────────────────────────────────
   Same approach as Home 7's Core Platform row: the wrapper is made as tall as the
   horizontal overflow, the track is pinned by CSS position:sticky, and scroll
   distance maps 1:1 to translateX. Below 900px it is a normal swipe row. */
(function(){
 const outer = document.getElementById('cxTrackOuter');
 const pin   = document.getElementById('cxTrackPin');
 const track = document.getElementById('cxTrack');
 if(!outer || !pin || !track) return;
 if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
 if(window.matchMedia('(max-width:900px)').matches) return;

 let stickyTop = 0, overflow = 0, ticking = false;

 function measure(){
  stickyTop = parseFloat(getComputedStyle(pin).top) || 0;
  overflow = Math.max(0, track.scrollWidth - pin.clientWidth);
  outer.style.height = (pin.offsetHeight + overflow) + 'px';
 }
 function render(){
  ticking = false;
  if(overflow <= 0){ track.style.transform = 'translateX(0)'; return; }
  const rect = outer.getBoundingClientRect();
  let p = (stickyTop - rect.top) / overflow;
  p = p < 0 ? 0 : p > 1 ? 1 : p;
  track.style.transform = 'translateX(' + (-p * overflow).toFixed(1) + 'px)';
 }
 function request(){ if(!ticking){ ticking = true; requestAnimationFrame(render); } }

 outer.classList.add('cx-track-on');
 measure(); render();
 window.addEventListener('scroll', request, { passive: true });
 window.addEventListener('resize', function(){ measure(); render(); });
})();

/* ── Closing CTA: cursor glow ─────────────────────────────────────────────
   Mirrors the About Us hero: the JS writes only --cx-x/--cx-y and CSS derives both
   the light and the grid mask from them, so they cannot drift apart. Hover-capable
   pointers only — on touch there is no hover to fade out of. */
(function(){
 const fx = document.getElementById('cxGlow');
 if(!fx) return;
 const host = fx.closest('section');
 if(!host) return;
 if(!window.matchMedia('(hover:hover) and (pointer:fine)').matches) return;
 const snap = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
 let tx = 0, ty = 0, x = 0, y = 0, raf = 0, seeded = false;

 function paint(){
  fx.style.setProperty('--cx-x', x.toFixed(1) + 'px');
  fx.style.setProperty('--cx-y', y.toFixed(1) + 'px');
 }
 function step(){
  raf = 0;
  const dx = tx - x, dy = ty - y;
  if(Math.abs(dx) < 0.5 && Math.abs(dy) < 0.5){ x = tx; y = ty; paint(); return; }
  x += dx * 0.12; y += dy * 0.12;
  paint();
  raf = requestAnimationFrame(step);
 }
 host.addEventListener('pointermove', function(e){
  const r = host.getBoundingClientRect();
  tx = e.clientX - r.left; ty = e.clientY - r.top;
  if(!seeded){ seeded = true; x = tx; y = ty; paint(); fx.classList.add('is-on'); return; }
  if(snap){ x = tx; y = ty; paint(); return; }
  if(!raf) raf = requestAnimationFrame(step);
 }, { passive: true });
 host.addEventListener('pointerenter', function(){ if(seeded) fx.classList.add('is-on'); }, { passive: true });
 host.addEventListener('pointerleave', function(){
  fx.classList.remove('is-on');
  if(raf){ cancelAnimationFrame(raf); raf = 0; }
 }, { passive: true });
})();
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
