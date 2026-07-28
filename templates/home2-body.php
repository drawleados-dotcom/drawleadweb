<?php
/**
 * Home 2.0 — same core content as the main homepage (templates/home-body.php),
 * rebuilt as a card-based SaaS/CRO-style layout: bento grid, stat cards,
 * step timeline, pill cloud. Styles are scoped under .h2 so they never
 * leak into the rest of the site's shared stylesheet.
 */
$activePage = 'home2';
include __DIR__ . '/partials/nav.php';

$h2Cases = $pdo->query(
    "SELECT * FROM case_studies WHERE status = 'published' ORDER BY created_at DESC LIMIT 3"
)->fetchAll();
?>
<style>
.h2{--h2-radius:20px}
.h2 section{padding:6rem 3.5rem}

/* ── Hero ── */
#h2-hero{padding-top:9.5rem;padding-bottom:5rem;overflow:hidden}
.h2-kicker{display:inline-flex;align-items:center;gap:8px;padding:7px 16px;border-radius:999px;background:var(--white);border:1px solid var(--border);font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--violet);margin-bottom:1.6rem}
.h2-kicker::before{content:'';width:7px;height:7px;border-radius:50%;background:var(--grad);flex-shrink:0}
.h2-hero-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:3.5rem;align-items:center;max-width:1320px;margin:0 auto}
.h2-hero-h{font-size:clamp(34px,4.4vw,56px);font-weight:800;letter-spacing:-.03em;line-height:1.06;margin-bottom:1.3rem}
.h2-hero-p{font-size:16px;color:var(--g500);line-height:1.65;max-width:520px;margin-bottom:2rem}
.h2-hero-btns{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:2.6rem}
.h2-hero-stats{display:flex;gap:1.8rem;flex-wrap:wrap}
.h2-hstat-v{font-size:20px;font-weight:800}
.h2-hstat-l{font-size:10.5px;color:var(--g400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:2px}

.h2-hero-visual{position:relative;display:flex;flex-direction:column;gap:16px;padding:1rem}
.h2-hero-visual::before{content:'';position:absolute;inset:-40px;background:radial-gradient(circle at 60% 40%,rgba(50,180,111,.2),transparent 65%);z-index:-1;filter:blur(6px)}
.h2-float-card{background:var(--white);border:1px solid var(--border);border-radius:var(--h2-radius);padding:1.2rem 1.4rem;box-shadow:0 1px 2px rgba(17,17,18,.04),0 20px 40px -20px rgba(17,17,18,.25)}
.h2-float-card:nth-child(2){margin-left:2.4rem}
.h2-float-card:nth-child(3){margin-left:4.6rem}
.h2-fc-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--g400);margin-bottom:6px}
.h2-fc-value{font-size:24px;font-weight:800}
.h2-fc-row{display:flex;align-items:center;justify-content:space-between;gap:1rem}
.h2-fc-bars{display:flex;align-items:flex-end;gap:4px;height:38px;margin-top:12px}
.h2-fc-bars span{flex:1;background:rgba(50,180,111,.35);border-radius:2px;display:block}
.h2-fc-bars span:last-child{background:var(--grad)}
@media(max-width:960px){.h2-hero-grid{grid-template-columns:1fr}.h2-hero-visual{margin-top:1rem;max-width:440px}}
@media(max-width:560px){.h2-float-card:nth-child(2),.h2-float-card:nth-child(3){margin-left:0}}

/* ── Trust strip ── */
#h2-trust{padding:2.2rem 3.5rem;background:var(--white)}
.h2-trust-row{display:flex;align-items:center;justify-content:center;gap:.7rem;flex-wrap:wrap;max-width:1100px;margin:0 auto}
.h2-trust-label{font-size:11.5px;font-weight:700;color:var(--g400);text-transform:uppercase;letter-spacing:.06em;margin-right:.5rem}
.h2-chip{font-size:12.5px;font-weight:600;color:var(--g600);background:var(--bg);border:1px solid var(--border);border-radius:999px;padding:7px 15px}

/* ── Bento ── */
.h2-bento{display:grid;grid-template-columns:repeat(4,1fr);grid-auto-rows:152px;gap:14px;grid-auto-flow:dense;max-width:1320px;margin:0 auto}
.h2-bento-card{background:var(--white);border:1px solid var(--border);border-radius:var(--h2-radius);padding:1.4rem;display:flex;flex-direction:column;transition:transform .2s,box-shadow .2s}
.h2-bento-card:hover{transform:translateY(-3px);box-shadow:0 20px 40px -20px rgba(17,17,18,.22)}
.h2-bento-hero{grid-column:span 2;grid-row:span 2;background:#0a1310;color:#fff;border-color:#0a1310;justify-content:space-between}
.h2-bento-cta{background:var(--grad);color:#fff;border:none;justify-content:center}
.h2-bento-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:.8rem;flex-shrink:0}
.h2-bento-icon svg{width:18px;height:18px}
.h2-bento-name{font-size:13.5px;font-weight:800;margin-bottom:.35rem}
.h2-bento-desc{font-size:11px;color:var(--g500);line-height:1.5;flex:1}
@media(max-width:960px){.h2-bento{grid-template-columns:repeat(2,1fr)}.h2-bento-hero{grid-column:span 2;grid-row:span 2}}
@media(max-width:560px){.h2-bento{grid-template-columns:1fr;grid-auto-rows:auto}.h2-bento-card{min-height:130px}.h2-bento-hero{grid-column:span 1;grid-row:span 1}}

/* How it works (step timeline) — shared .h2-steps component now lives in
   templates/partials/style.php since Analyze reuses it too. */

/* ── Solutions (3-card) ── */
.h2-sol-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.6rem;max-width:1320px;margin:0 auto}
.h2-sol-card{background:var(--white);border:1px solid var(--border);border-radius:var(--h2-radius);padding:2rem;display:flex;flex-direction:column}
.h2-sol-num{font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--blue);margin-bottom:1rem}
.h2-sol-name{font-size:19px;font-weight:800;margin-bottom:.6rem;letter-spacing:-.01em}
.h2-sol-link{font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--black);text-decoration:none;display:inline-flex;align-items:center;gap:6px}
@media(max-width:900px){.h2-sol-grid{grid-template-columns:1fr}}

/* ── Results (3-card, real case studies) ── */
.h2-res-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.4rem;max-width:1320px;margin:0 auto 3rem}
.h2-res-card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:var(--h2-radius);padding:1.8rem;text-decoration:none;color:inherit;display:flex;flex-direction:column;transition:background .2s,transform .2s}
.h2-res-card:hover{background:rgba(255,255,255,.07);transform:translateY(-3px)}
.h2-res-avatar{width:42px;height:42px;border-radius:12px;background:var(--grad);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;margin-bottom:1rem}
.h2-res-title{font-size:15px;font-weight:800;margin-bottom:.5rem}
.h2-res-desc{font-size:12px;color:rgba(255,255,255,.55);line-height:1.6;flex:1;margin-bottom:1rem}
.h2-res-link{font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#34a87c}
@media(max-width:900px){.h2-res-grid{grid-template-columns:1fr}}

/* ── Why (4-card) ── */
.h2-why-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.4rem;max-width:1320px;margin:0 auto}
.h2-why-card{text-align:center;padding:1rem}
.h2-why-icon{width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#32b46f,#14855a);display:flex;align-items:center;justify-content:center;margin:0 auto 1.1rem;box-shadow:0 8px 20px rgba(20,133,90,.25)}
.h2-why-name{font-size:14.5px;font-weight:800;margin-bottom:.5rem}
.h2-why-desc{font-size:12px;color:var(--g500);line-height:1.6}
@media(max-width:900px){.h2-why-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:560px){.h2-why-grid{grid-template-columns:1fr}}

/* ── Industries (pill cloud) ── */
.h2-pill-cloud{display:flex;flex-wrap:wrap;justify-content:center;gap:.6rem;max-width:1100px;margin:0 auto}
.h2-pill{font-size:12.5px;font-weight:600;color:var(--g600);background:var(--bg);border:1px solid var(--border);border-radius:999px;padding:9px 18px;text-decoration:none;transition:all .15s}
.h2-pill:hover{background:var(--grad);color:#fff;border-color:transparent}

/* ── Final CTA trust row ── */
.h2-trustrow{display:flex;justify-content:center;gap:2rem;flex-wrap:wrap;margin-top:2.6rem;position:relative}
.h2-trust-item{display:flex;align-items:center;gap:7px;font-size:11.5px;font-weight:600;color:rgba(255,255,255,.55)}
</style>

<div class="h2">

<!-- ═══════════════════ HERO ═══════════════════ -->
<section id="h2-hero">
 <div class="grid-bg" style="opacity:.4"></div>
 <div class="h2-hero-grid">
  <div>
   <div class="h2-kicker rv">Operating System for Modern Business</div>
   <h1 class="h2-hero-h rv">Run your entire business from <span class="g" style="background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">one dashboard</span>.</h1>
   <p class="h2-hero-p rv">Drawlead unifies ERP, AI automation, analytics, and cloud workflows into a single operating system — built for India's growing businesses.</p>
   <div class="h2-hero-btns rv">
    <button type="button" data-book class="btn btn-black">Get a Free Demo →</button>
    <a href="#h2-platform" class="btn btn-ghost">See How It Works</a>
   </div>
   <div class="h2-hero-stats rv">
    <div><div class="h2-hstat-v" style="color:var(--blue)">7</div><div class="h2-hstat-l">Core functions</div></div>
    <div><div class="h2-hstat-v">20+</div><div class="h2-hstat-l">Industries</div></div>
    <div><div class="h2-hstat-v" style="color:var(--violet)">AI</div><div class="h2-hstat-l">Powered</div></div>
    <div><div class="h2-hstat-v">∞</div><div class="h2-hstat-l">Scalable</div></div>
   </div>
  </div>
  <div class="h2-hero-visual rv">
   <div class="h2-float-card">
    <div class="h2-fc-row">
     <div>
      <div class="h2-fc-label">Revenue Tracked</div>
      <div class="h2-fc-value" style="color:var(--blue)">₹18.4L</div>
     </div>
     <div style="font-size:12px;font-weight:700;color:var(--blue)">↑ 24%</div>
    </div>
    <div class="h2-fc-bars"><span style="height:40%"></span><span style="height:60%"></span><span style="height:48%"></span><span style="height:75%"></span><span style="height:58%"></span><span style="height:92%"></span></div>
   </div>
   <div class="h2-float-card">
    <div class="h2-fc-label">Modules Synced</div>
    <div class="h2-fc-row">
     <div class="h2-fc-value">7/7</div>
     <div style="display:flex;gap:4px">
      <span style="width:8px;height:8px;border-radius:50%;background:var(--blue)"></span>
      <span style="width:8px;height:8px;border-radius:50%;background:var(--violet)"></span>
      <span style="width:8px;height:8px;border-radius:50%;background:#23a065"></span>
     </div>
    </div>
   </div>
   <div class="h2-float-card">
    <div class="h2-fc-label">On-Time Delivery</div>
    <div class="h2-fc-value" style="color:var(--violet)">96%</div>
   </div>
  </div>
 </div>
</section>

<!-- ═══════════════════ TRUST STRIP ═══════════════════ -->
<section id="h2-trust">
 <div class="h2-trust-row rv">
  <span class="h2-trust-label">Everything unified —</span>
  <span class="h2-chip">ERP</span>
  <span class="h2-chip">CRM</span>
  <span class="h2-chip">WhatsApp Automation</span>
  <span class="h2-chip">Analytics</span>
  <span class="h2-chip">Cloud Infra</span>
  <span class="h2-chip">AI Workflows</span>
 </div>
</section>

<!-- ═══════════════════ PLATFORM (BENTO) ═══════════════════ -->
<section id="h2-platform" style="background:var(--bg)">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Core Platform</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">One OS. <span class="g">Seven functions.</span></h2>
 <p class="sec-sub rv">Every core business function, streamlined and intelligently connected through one operating system.</p>

 <div class="h2-bento">
  <div class="h2-bento-card h2-bento-hero rv">
   <div>
    <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.5);margin-bottom:.7rem">Platform Overview</div>
    <div style="font-size:19px;font-weight:800;line-height:1.32;margin-bottom:1.3rem">All 7 functions,<br>synced in real time.</div>
    <div style="display:flex;gap:1.8rem">
     <div><div style="font-size:20px;font-weight:800;color:#32b46f">₹18.4L</div><div style="font-size:9px;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.05em;margin-top:2px">Tracked</div></div>
     <div><div style="font-size:20px;font-weight:800">7/7</div><div style="font-size:9px;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.05em;margin-top:2px">Live</div></div>
    </div>
   </div>
   <a href="#h2-how" style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#fff;text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-top:1.4rem">See how it works →</a>
  </div>

  <?php foreach (platform_modules_ordered() as $i => $entry): $m = $entry['module']; ?>
  <div class="h2-bento-card rv d<?= ($i % 4) + 1 ?>">
   <div class="h2-bento-icon" style="background:linear-gradient(135deg,<?= h($m['color']) ?>,#0a1310)"><?= $m['icon'] ?></div>
   <div class="h2-bento-name"><?= h($m['name']) ?></div>
   <div class="h2-bento-desc"><?= h($m['tagline']) ?></div>
  </div>
  <?php endforeach; ?>

  <div class="h2-bento-card h2-bento-cta rv">
   <div style="font-size:14.5px;font-weight:800;line-height:1.3;margin-bottom:.5rem">See all 7 in one demo</div>
   <div style="font-size:11px;color:rgba(255,255,255,.8);line-height:1.5;margin-bottom:1rem">Book a free walkthrough of the full platform.</div>
   <button type="button" data-book style="background:#fff;color:var(--black);border:none;border-radius:6px;padding:9px 16px;font-family:var(--font);font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;cursor:pointer;align-self:flex-start">Book Demo →</button>
  </div>
 </div>
</section>

<!-- ═══════════════════ HOW IT WORKS ═══════════════════ -->
<section id="h2-how" style="background:var(--white)">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">How It Works</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Stop juggling tools. <span class="g">Start with a plan.</span></h2>
 <p class="sec-sub rv">Most businesses run on a dozen disconnected apps. We audit how you actually work, then build one system around it — in four steps.</p>

 <div class="h2-steps rv">
  <div class="h2-step">
   <div class="h2-step-n">01</div>
   <div class="h2-step-name">Audit</div>
   <div class="h2-step-desc">Map your current workflows, tools, and bottlenecks.</div>
  </div>
  <div class="h2-step">
   <div class="h2-step-n">02</div>
   <div class="h2-step-name">Measure</div>
   <div class="h2-step-desc">Set up KPIs and dashboards so decisions are backed by data.</div>
  </div>
  <div class="h2-step">
   <div class="h2-step-n">03</div>
   <div class="h2-step-name">Automate</div>
   <div class="h2-step-desc">Remove manual approvals, follow-ups, and notifications.</div>
  </div>
  <div class="h2-step">
   <div class="h2-step-n">04</div>
   <div class="h2-step-name">Scale</div>
   <div class="h2-step-desc">Build the ERP, CRM, or automation platform — built to grow.</div>
  </div>
 </div>
</section>

<!-- ═══════════════════ SOLUTIONS ═══════════════════ -->
<section id="h2-solutions" style="background:var(--bg)">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Solutions</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Built for <span class="g">Growth</span> <span class="fade">— Three Ways</span></h2>
 <p class="sec-sub rv">Beyond the core platform, three focused solution tracks that plug straight into your operating system.</p>

 <div class="h2-sol-grid">
  <div class="h2-sol-card rv d1">
   <div class="h2-sol-num" style="color:var(--blue)">Solution 01 · ERP</div>
   <div class="h2-sol-name">Custom Operational Solutions</div>
   <ul class="sol-list">
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Modules mapped to your real workflows</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Role-based access &amp; audit trails</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Migration off spreadsheets &amp; legacy tools</li>
   </ul>
   <a href="/custom-erp-solution" class="h2-sol-link">Explore ERP →</a>
  </div>
  <div class="h2-sol-card rv d2">
   <div class="h2-sol-num" style="color:#32b46f">Solution 02</div>
   <div class="h2-sol-name">Ecommerce Solutions</div>
   <ul class="sol-list">
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Live inventory sync across channels</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Automated order, invoice &amp; GST workflows</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Payments, logistics &amp; returns end to end</li>
   </ul>
   <a href="/ecommerce-solutions" class="h2-sol-link">Explore ecommerce →</a>
  </div>
  <div class="h2-sol-card rv d3">
   <div class="h2-sol-num" style="color:var(--violet)">Solution 03</div>
   <div class="h2-sol-name">Marketing Solutions</div>
   <ul class="sol-list">
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Technical SEO &amp; content engine</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Google, Meta &amp; LinkedIn campaigns</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Instant WhatsApp &amp; email follow-up</li>
   </ul>
   <a href="/marketing-solutions" class="h2-sol-link">Explore marketing →</a>
  </div>
 </div>
</section>

<!-- ═══════════════════ RESULTS ═══════════════════ -->
<section id="h2-results" style="background:#0a1310;color:#fff">
 <div class="grid-bg" style="opacity:.4"></div>
 <div class="eyebrow rv"><div class="eyebrow-line" style="background:#34a87c"></div><span class="eyebrow-text" style="color:#34a87c">Results</span><div class="eyebrow-line" style="background:#34a87c"></div></div>
 <h2 class="sec-h rv" style="color:#fff">Real businesses. <span style="color:rgba(255,255,255,.22)">Real numbers.</span></h2>
 <p class="sec-sub rv" style="color:rgba(255,255,255,.5)">A few of the teams already running on Drawlead.</p>

 <?php if ($h2Cases): ?>
 <div class="h2-res-grid">
  <?php foreach ($h2Cases as $i => $cs): $initial = strtoupper(substr($cs['client_name'] ?: $cs['title'], 0, 1)); ?>
  <a href="/case-studies/<?= h($cs['slug']) ?>" class="h2-res-card rv d<?= $i + 1 ?>">
   <div class="h2-res-avatar"><?= h($initial ?: 'D') ?></div>
   <div class="h2-res-title"><?= h($cs['title']) ?></div>
   <?php if (!empty($cs['description'])): ?><p class="h2-res-desc"><?= h($cs['description']) ?></p><?php endif; ?>
   <span class="h2-res-link">Read the story →</span>
  </a>
  <?php endforeach; ?>
 </div>
 <?php else: ?>
 <p class="rv" style="text-align:center;color:rgba(255,255,255,.5);font-size:13px;margin-bottom:3rem">Case studies coming soon.</p>
 <?php endif; ?>

 <div class="sec-cta rv">
  <button type="button" data-book class="btn btn-black" style="background:#fff;color:#0a1310">Start Your Success Story →</button>
 </div>
</section>

<!-- ═══════════════════ WHY DRAWLEAD ═══════════════════ -->
<section id="h2-why" style="background:var(--white)">
 <div class="eyebrow rv"><div class="eyebrow-line" style="background:#14855a"></div><span class="eyebrow-text" style="color:#14855a">Why Drawlead</span><div class="eyebrow-line" style="background:#14855a"></div></div>
 <h2 class="sec-h rv"><span style="color:#14855a">What Sets</span> Us Apart</h2>
 <p class="sec-sub rv">We're not just software — we're a long-term partner in your digital transformation and growth.</p>

 <div class="h2-why-grid">
  <div class="h2-why-card rv d1">
   <div class="h2-why-icon"><svg width="24" height="24" fill="none" viewBox="0 0 40 40">
     <rect x="3" y="14" width="12" height="12" rx="3" fill="rgba(255,255,255,.9)"/>
     <rect x="25" y="14" width="12" height="12" rx="3" fill="rgba(255,255,255,.9)"/>
     <rect x="14" y="18" width="12" height="4" rx="2" fill="rgba(255,255,255,.7)"/>
     <circle cx="8" cy="8" r="4" fill="rgba(255,255,255,.4)"/>
     <circle cx="32" cy="32" r="4" fill="rgba(255,255,255,.4)"/>
   </svg></div>
   <div class="h2-why-name">Unified Ecosystem</div>
   <div class="h2-why-desc">All 7 core functions in one platform — no more tool-switching or data silos.</div>
  </div>
  <div class="h2-why-card rv d2">
   <div class="h2-why-icon"><svg width="24" height="24" fill="none" viewBox="0 0 40 40">
     <polygon points="22,4 10,22 20,22 18,36 30,18 20,18" fill="rgba(255,255,255,.95)"/>
     <circle cx="32" cy="8" r="3" fill="rgba(255,255,255,.4)"/>
     <circle cx="8" cy="32" r="3" fill="rgba(255,255,255,.4)"/>
   </svg></div>
   <div class="h2-why-name">AI-Driven Efficiency</div>
   <div class="h2-why-desc">Automate repetitive tasks and surface insights without technical expertise.</div>
  </div>
  <div class="h2-why-card rv d3">
   <div class="h2-why-icon"><svg width="24" height="24" fill="none" viewBox="0 0 40 40">
     <polygon points="20,4 28,12 24,12 24,18 16,18 16,12 12,12" fill="rgba(255,255,255,.9)"/>
     <polygon points="20,36 28,28 24,28 24,22 16,22 16,28 12,28" fill="rgba(255,255,255,.9)"/>
     <polygon points="4,20 12,12 12,16 18,16 18,24 12,24 12,28" fill="rgba(255,255,255,.6)"/>
     <polygon points="36,20 28,12 28,16 22,16 22,24 28,24 28,28" fill="rgba(255,255,255,.6)"/>
   </svg></div>
   <div class="h2-why-name">Scalable Architecture</div>
   <div class="h2-why-desc">Built for startups, SMEs, and enterprises — scales exactly as you grow.</div>
  </div>
  <div class="h2-why-card rv d4">
   <div class="h2-why-icon"><svg width="24" height="24" fill="none" viewBox="0 0 40 40">
     <path d="M20 4 L34 10 L34 22 Q34 32 20 37 Q6 32 6 22 L6 10 Z" fill="rgba(255,255,255,.85)"/>
     <polyline points="13,20 18,25 28,15" fill="none" stroke="#14855a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
   </svg></div>
   <div class="h2-why-name">Secure &amp; Reliable</div>
   <div class="h2-why-desc">Enterprise-grade security, 99.9% uptime SLA, end-to-end encryption.</div>
  </div>
 </div>
</section>

<!-- ═══════════════════ INDUSTRIES ═══════════════════ -->
<section id="h2-industries" style="background:var(--bg)">
 <div class="eyebrow rv"><div class="eyebrow-line" style="background:#14855a"></div><span class="eyebrow-text" style="color:#14855a">Industries</span><div class="eyebrow-line" style="background:#14855a"></div></div>
 <h2 class="sec-h rv">Built for <span style="color:#14855a">Your Industry</span></h2>
 <p class="sec-sub rv">20 industries, one adaptable platform. Pick yours.</p>

 <div class="h2-pill-cloud rv">
  <?php foreach (industries_ordered() as $entry): $ind = $entry['industry']; ?>
  <a href="/industry-<?= h($entry['key']) ?>" class="h2-pill"><?= h($ind['name']) ?></a>
  <?php endforeach; ?>
 </div>

 <div class="sec-cta rv" style="margin-top:2.6rem">
  <button type="button" data-book class="btn btn-black">Find Your Industry Solution →</button>
 </div>
</section>

<!-- ═══════════════════ CTA ═══════════════════ -->
<section id="cta">
 <div class="cta-grid-bg"></div>
 <div class="cta-glow"></div>

 <h2 class="cta-h rv">Build your<br><span class="fade">business</span> <span class="gr">ERP</span><br><span class="gr2">OS</span> with <span class="gr3">AI</span></h2>
 <p class="cta-p rv">Digitize, automate, and scale with Drawlead. Start with a free consultation — no commitment needed.</p>
 <div class="cta-btns rv">
  <button type="button" data-book class="cta-btn-w">Schedule Free Consultation →</button>
  <button type="button" data-book class="cta-btn-g">Book a Product Demo</button>
 </div>
 <div class="h2-trustrow rv">
  <span class="h2-trust-item"><svg width="14" height="14" fill="none" stroke="#34a87c" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Free consultation</span>
  <span class="h2-trust-item"><svg width="14" height="14" fill="none" stroke="#34a87c" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Setup in days, not months</span>
  <span class="h2-trust-item"><svg width="14" height="14" fill="none" stroke="#34a87c" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>No lock-in — you own the system</span>
 </div>
</section>

</div><!-- /.h2 -->

<?php include __DIR__ . '/partials/footer.php'; ?>
