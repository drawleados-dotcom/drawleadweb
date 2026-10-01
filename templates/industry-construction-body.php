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

/* Small inline marks that sit inside the card headline, as in the reference.
   Keyed so a card names its icon rather than carrying 400 bytes of SVG. */
$cxIcons = [
 'eye'    => '<path d="M1.8 12S5.4 5.2 12 5.2 22.2 12 22.2 12 18.6 18.8 12 18.8 1.8 12 1.8 12z"/><circle cx="12" cy="12" r="3.1"/>',
 'trend'  => '<path d="M3 16.4l5.4-5.4 3.4 3.4L21 5.6"/><path d="M15.2 5.6H21v5.8"/>',
 'box'    => '<path d="M12 2.8 21 7v10l-9 4.2L3 17V7z"/><path d="M3 7l9 4.2L21 7"/><path d="M12 11.2v10"/>',
 'users'  => '<circle cx="9.2" cy="8.4" r="3.4"/><path d="M2.6 19.6c0-3.4 2.9-5.6 6.6-5.6s6.6 2.2 6.6 5.6"/><path d="M17.2 7.6a3 3 0 0 1 0 5.6"/><path d="M18.4 19.6c0-2.2-.8-3.8-2.2-4.8"/>',
 'inbox'  => '<path d="M3.2 13.4 5.6 5.2A2 2 0 0 1 7.5 3.8h9a2 2 0 0 1 1.9 1.4l2.4 8.2"/><path d="M3.2 13.4h4.6l1.2 2.6h6l1.2-2.6h4.6v4.8a2 2 0 0 1-2 2H5.2a2 2 0 0 1-2-2z"/>',
 'rupee'  => '<path d="M7.6 4.6h8.8M7.6 8.8h8.8M14.4 4.6c2 0 3.2 1.4 3.2 3.2s-1.2 3.4-3.6 3.4H7.6l7.4 8.2"/>',
 'file'   => '<path d="M13.6 2.8H7a2 2 0 0 0-2 2v14.4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8.2z"/><path d="M13.6 2.8V8.2H19"/><path d="M8.6 13.2h6.8M8.6 16.8h4.4"/>',
 'chat'   => '<path d="M20.4 14.2a2 2 0 0 1-2 2H8.2L4 20.2V6a2 2 0 0 1 2-2h12.4a2 2 0 0 1 2 2z"/><path d="M8.4 9.4h7.6M8.4 12.6h5"/>',
 'pulse'  => '<path d="M2.8 12.4h4l2.4-6.2 3.6 12 2.6-5.8h5.8"/>',
 'wallet' => '<path d="M3.4 7.4a2 2 0 0 1 2-2h11.2a1.4 1.4 0 0 0 0-2.8H5.6"/><path d="M3.4 7.4h16.2a1.4 1.4 0 0 1 1.4 1.4v9.4a2 2 0 0 1-2 2H5.4a2 2 0 0 1-2-2z"/><circle cx="16.8" cy="13.4" r="1.2"/>',
 'target' => '<circle cx="12" cy="12" r="8.4"/><circle cx="12" cy="12" r="4.2"/><circle cx="12" cy="12" r="1" fill="currentColor" stroke="none"/>',
 'shield' => '<path d="M12 3 19 5.6v5.6c0 4.4-3 8-7 9.2-4-1.2-7-4.8-7-9.2V5.6z"/><path d="M9.2 12.2l2 2 3.6-3.8"/>',
];

/* Each card: highlighted headline (the <em> is the accent, as in the reference),
   the icon that sits inline after it, and one supporting line. */
$cxProblems = [
 ['Limited <em>project visibility</em>',        'eye',    'No single view of where each site actually stands today.'],
 ['Silent <em>budget overruns</em>',            'trend',  'Costs drift past estimates before anyone sees the trend.'],
 ['Material <em>shortages and wastage</em>',    'box',    'Stock runs out mid-pour, or sits unused across sites.'],
 ['Poor <em>team coordination</em>',            'users',  'Engineers, contractors and office staff work off different numbers.'],
 ['Missed <em>leads and follow-ups</em>',       'inbox',  'Enquiries sit in inboxes and WhatsApp until the buyer moves on.'],
 ['Payment and <em>cash flow issues</em>',      'rupee',  'Invoices, retentions and receivables tracked in scattered sheets.'],
 ['Unorganized <em>data and reports</em>',      'file',   'Every report is rebuilt by hand from files nobody trusts.'],
 ['Customer <em>communication gaps</em>',       'chat',   'Buyers chase updates that should reach them automatically.'],
];

$cxFeatures = [
 ['Real-time <em>project tracking</em>',        'pulse',  'Live status, milestones and delays across every site.'],
 ['Budget and <em>expense management</em>',     'trend',  'Estimates against actuals, with approvals before spend.'],
 ['Material and <em>inventory control</em>',    'box',    'Stock, indents and transfers tracked site by site.'],
 ['Team and <em>contractor management</em>',    'users',  'Assignments, attendance and contractor bills in one place.'],
 ['Lead and <em>sales management</em>',         'target', 'Every enquiry captured, assigned and followed up on time.'],
 ['Payment and <em>cash flow tracking</em>',    'wallet', 'Receivables, payables and retentions visible as they move.'],
 ['Centralized <em>data and reports</em>',      'file',   'One source of truth, with reports generated not assembled.'],
 ['Better <em>customer communication</em>',     'chat',   'Buyers kept updated automatically at every stage.'],
];

/* The four outcomes that radiate from the go-live tile. Each: title, the line
   under it, and its mark. The marks are two-tone — the outline is ink, the tick
   is the brand green — so the <path class="g"> parts are coloured separately. */
$cxOutcomes = [
 ['Accurate Estimates', 'Create reliable BOQs based on actual project costs.',
  '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M9 13.4h6"/><path d="M9 17h3.2"/>'],
 ['Controlled Approvals', 'Reduce unauthorized purchases and expenses.',
  '<circle cx="9.8" cy="7.2" r="3.6"/><path d="M3.2 20.2c0-3.7 3-6.2 6.6-6.2 1 0 2 .2 2.9.6"/><path d="M14.2 17.4l2.4 2.4 4.4-4.9" class="g"/>'],
 ['Secure Access', 'Protect business data with role-based permissions.',
  '<path d="M12 2.6 20 5.6v5.9c0 4.7-3.3 8.7-8 9.9-4.7-1.2-8-5.2-8-9.9V5.6z"/><path d="M8.5 12.1l2.5 2.5 4.5-4.9" class="g"/>'],
 ['Clear Audit History', 'Track every update, approval, and transaction.',
  '<path d="M3.6 12a8.4 8.4 0 1 0 2.5-6"/><path d="M3.2 4.4v4.3h4.3"/><path d="M12 7.6V12l3.2 2" class="g"/>'],
];

/* Why Drawlead: title, the line under it, the mark, and the two labels that sit
   at the foot of the card. */
$cxWhy = [
 ['Industry-Focused Solutions', 'Built around construction and real estate workflows, not a generic template.',
  '<path d="M3.4 21h17.2"/><path d="M5.6 21V5.2a2 2 0 0 1 2-2h4.8a2 2 0 0 1 2 2V21"/><path d="M14.4 21V10.4h3.2a2 2 0 0 1 2 2V21"/><path d="M8.4 7.2h3M8.4 10.8h3M8.4 14.4h3"/>',
  ['Construction', 'Real Estate']],
 ['Custom Development', 'Your process drives the system, rather than the other way round.',
  '<path d="M9 7.4 4.4 12 9 16.6"/><path d="M15 7.4 19.6 12 15 16.6"/><path d="M13.2 4.8 10.8 19.2"/>',
  ['Tailored', 'Scalable']],
 ['Experienced Development Team', 'Engineers who have shipped ERP and CRM for operations like yours.',
  '<circle cx="9" cy="9.2" r="2.8"/><circle cx="16.8" cy="9.6" r="2.2"/><path d="M3 18.8c0-2.7 2.7-4.6 6-4.6s6 1.9 6 4.6"/><path d="M16.2 14.4c2.9.2 4.8 1.6 4.8 3.9"/>',
  ['Expert Team', 'Domain Knowledge']],
 ['Latest Technologies', 'A modern, maintainable stack that will still be supportable in five years.',
  '<ellipse cx="12" cy="6.2" rx="7.4" ry="3.2"/><path d="M4.6 6.2v5.6c0 1.8 3.3 3.2 7.4 3.2s7.4-1.4 7.4-3.2V6.2"/><path d="M4.6 11.8v5.8c0 1.8 3.3 3.2 7.4 3.2s7.4-1.4 7.4-3.2v-5.8"/>',
  ['Modern Stack', 'Future Ready']],
 ['Ongoing Support', 'A team that stays after go-live, through every change and new site.',
  '<path d="M4.6 13.4v-1.8a7.4 7.4 0 0 1 14.8 0v1.8"/><rect x="2.8" y="12.6" width="4" height="6" rx="2"/><rect x="17.2" y="12.6" width="4" height="6" rx="2"/><path d="M19.2 18.6v.5a2.2 2.2 0 0 1-2.2 2.2h-2.1"/>',
  ['Dedicated Support', 'Long-Term']],
 ['Flexible Pricing', 'Scoped in phases, so the spend follows the value delivered.',
  '<rect x="2.6" y="5.2" width="18.8" height="13.6" rx="2.6"/><path d="M2.6 9.8h18.8"/><path d="M6.4 14.8h4.2"/>',
  ['Phased Approach', 'Value Based']],
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
 <!-- Pinned horizontal run. data-hx marks a track for the shared driver at the
      foot of this file; below 900px it degrades to an ordinary swipe row. -->
 <div class="cx-hx" data-hx>
  <div class="cx-hx-pin">
   <div class="cx-hx-track">
<?php foreach ($cxProblems as $n => $prob): ?>
    <article class="cx-card">
     <span class="cx-card-n"><?= str_pad((string) ($n + 1), 2, '0', STR_PAD_LEFT) ?></span>
     <h3 class="cx-card-t"><?= $prob[0] ?><span class="cx-ic" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><?= $cxIcons[$prob[1]] ?></svg></span></h3>
     <p class="cx-card-d"><?= $prob[2] ?></p>
    </article>
<?php endforeach; ?>
   </div>
  </div>
 </div>
</section>

<!-- ═════════ 03 · UNIFIED ERP SOLUTION ═════════ -->
<section id="cx-erp">
 <div class="eyebrow rv"><span class="eyebrow-text">Unified ERP Solution</span></div>
 <h2 class="sec-h rv">Built for How Construction &amp; Real Estate Actually Works</h2>
 <p class="sec-sub rv">Drawlead provides custom ERP and CRM software that connects projects, teams, materials, finances, sales, and customers in one system.</p>

 <div class="cx-hx" data-hx>
  <div class="cx-hx-pin">
   <div class="cx-hx-track">
<?php foreach ($cxFeatures as $n => $f): ?>
    <article class="cx-card cx-card-sol">
     <span class="cx-card-n"><?= str_pad((string) ($n + 1), 2, '0', STR_PAD_LEFT) ?></span>
     <h3 class="cx-card-t"><?= $f[0] ?><span class="cx-ic" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><?= $cxIcons[$f[1]] ?></svg></span></h3>
     <p class="cx-card-d"><?= $f[2] ?></p>
    </article>
<?php endforeach; ?>
   </div>
  </div>
 </div>
</section>

<!-- ═════════ 04 · EXPECTED OUTCOMES ═════════ -->
<section id="cx-outcomes">
 <div class="eyebrow rv"><span class="eyebrow-text">Expected Outcomes</span></div>
 <h2 class="sec-h rv">What Changes After Go-Live</h2>
 <p class="sec-sub rv">See how your operations improve with better control, security, accuracy, and transparency.</p>

 <!-- A hub and its four spokes. The nodes are placed by grid area rather than by
      source order, so the DOM stays in reading order for a screen reader while
      two of them render on the right. The wires are decoration. -->
 <div class="cx-og rv">
  <div class="cx-og-wire cx-og-l" aria-hidden="true">
   <svg viewBox="0 0 100 100" preserveAspectRatio="none" fill="none">
    <path d="M100 44C52 44 48 0 0 0"/>
    <path d="M100 56C52 56 48 100 0 100"/>
   </svg>
   <i></i><i></i><i></i><i></i>
  </div>
  <div class="cx-og-wire cx-og-r" aria-hidden="true">
   <svg viewBox="0 0 100 100" preserveAspectRatio="none" fill="none">
    <path d="M0 44C48 44 52 0 100 0"/>
    <path d="M0 56C48 56 52 100 100 100"/>
   </svg>
   <i></i><i></i><i></i><i></i>
  </div>

<?php foreach ($cxOutcomes as $n => $o): ?>
  <article class="cx-og-n cx-og-n<?= $n + 1 ?>">
   <span class="cx-og-ic" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?= $o[2] ?></svg></span>
   <div class="cx-og-tx">
    <h3 class="cx-og-t"><?= $o[0] ?></h3>
    <p class="cx-og-d"><?= $o[1] ?></p>
   </div>
  </article>
<?php endforeach; ?>

  <div class="cx-og-hub">
   <span class="cx-og-ring" aria-hidden="true"></span>
   <span class="cx-og-ring" aria-hidden="true"></span>
   <div class="cx-og-tile">
    <svg class="cx-og-bars" viewBox="0 0 48 34" aria-hidden="true">
     <rect x="1" y="17" width="11" height="17" rx="3.2"/>
     <rect x="18.5" y="8" width="11" height="26" rx="3.2"/>
     <rect x="36" y="0" width="11" height="34" rx="3.2"/>
    </svg>
    <span class="cx-og-hub-t">Go-Live</span>
    <span class="cx-og-hub-s">Better Operations</span>
   </div>
  </div>
 </div>
</section>

<!-- ═════════ 05 · WHY DRAWLEAD ═════════ -->
<section id="cx-why">
 <!-- the glow and the arc that sweeps out of the bottom-left corner -->
 <span class="cx-why-glow" aria-hidden="true"></span>
 <span class="cx-why-arc" aria-hidden="true"></span>

 <div class="cx-why-grid">
  <div class="cx-why-left">
   <div class="eyebrow rv" style="justify-content:flex-start"><span class="eyebrow-text">Your Trusted Technology Partner</span></div>
   <h2 class="cx-why-h rv">Why Choose <span class="cx-why-g">Drawlead</span></h2>
   <p class="cx-why-p rv">Drawlead develops custom ERP and CRM solutions that match the real needs of construction and real estate businesses.</p>
   <p class="cx-why-statement rv">Technology built around the way your business actually works.</p>
  </div>

  <div class="cx-why-right">
<?php foreach ($cxWhy as $w): ?>
   <article class="cx-why-card rv">
    <span class="cx-why-ic" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><?= $w[2] ?></svg></span>
    <div class="cx-why-body">
     <h3><?= $w[0] ?></h3>
     <p><?= $w[1] ?></p>
     <div class="cx-why-tags">
<?php foreach ($w[3] as $t): ?>
      <span class="cx-why-tag"><?= $t ?></span>
<?php endforeach; ?>
     </div>
    </div>
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
     <span class="cx-faq-t"><span class="cx-faq-num"><?= $n + 1 ?>.</span><?= $faq[0] ?></span>
     <span class="cx-faq-ico" aria-hidden="true">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6.2 8 10.2l4-4"/></svg>
     </span>
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
 <!-- Dark rounded card inset in the page, matching the Home 7 and About Us closing
      CTAs. The grid and the cursor glow live inside the card now, not on the page. -->
 <div class="cx-cta-card">
<?php if ($cxCta): ?>
  <img class="cx-cta-bg" src="<?= asset_url('/assets/img/cx-cta.webp') ?>" alt="" aria-hidden="true" loading="lazy" decoding="async">
<?php endif; ?>
  <div class="cx-cta-grid" aria-hidden="true"></div>
  <div class="cx-glow" id="cxGlow" aria-hidden="true"><span class="cx-glow-blob"></span><span class="cx-glow-lines"></span></div>
  <div class="cx-cta-eyebrow rv">Start Your ERP Journey</div>
  <h2 class="cx-cta-h rv">Ready to Build a Smarter Business?</h2>
  <p class="cx-cta-p rv">Get a custom ERP and CRM solution built around your projects, teams, sales process, and business needs.</p>
  <div class="cx-cta-btn rv"><button type="button" data-book class="cta-btn-w">Get a Free Quote</button></div>
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

/* ── Pinned horizontal runs ───────────────────────────────────────────────
   One driver for every [data-hx] track on the page: the problem cards, the
   solution cards and the outcomes. The wrapper is made as tall as the track's
   horizontal overflow, CSS position:sticky pins the viewport-height box, and
   scroll distance through the wrapper maps 1:1 to translateX.

   Each track measures itself, so cards of different widths per section are fine.
   Below 900px, or with reduced motion, nothing is pinned and the track is an
   ordinary swipe row — the class the CSS keys off is never added. */
(function(){
 const tracks = Array.from(document.querySelectorAll('[data-hx]'));
 if(!tracks.length) return;
 if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
 if(window.matchMedia('(max-width:900px)').matches) return;

 const runs = tracks.map(function(outer){
  return {
   outer: outer,
   pin: outer.querySelector('.cx-hx-pin'),
   track: outer.querySelector('.cx-hx-track'),
   stickyTop: 0,
   overflow: 0
  };
 }).filter(function(r){ return r.pin && r.track; });
 if(!runs.length) return;

 let ticking = false;

 function measure(){
  runs.forEach(function(r){
   r.outer.classList.add('cx-hx-on');
   r.stickyTop = parseFloat(getComputedStyle(r.pin).top) || 0;
   r.overflow = Math.max(0, r.track.scrollWidth - r.pin.clientWidth);
   r.outer.style.height = (r.pin.offsetHeight + r.overflow) + 'px';
  });
 }
 function render(){
  ticking = false;
  runs.forEach(function(r){
   if(r.overflow <= 0){ r.track.style.transform = 'translateX(0)'; return; }
   const rect = r.outer.getBoundingClientRect();
   // only the runs near the viewport are worth touching
   if(rect.bottom < -200 || rect.top > window.innerHeight + 200) return;
   let p = (r.stickyTop - rect.top) / r.overflow;
   p = p < 0 ? 0 : p > 1 ? 1 : p;
   r.track.style.transform = 'translateX(' + (-p * r.overflow).toFixed(1) + 'px)';
  });
 }
 function request(){ if(!ticking){ ticking = true; requestAnimationFrame(render); } }

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
 const host = fx.closest('.cx-cta-card') || fx.closest('section');
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
