<?php
/**
 * Home 9: Home 8's sections in a different order, served at /home-9 as a
 * DB-managed page (Admin -> Pages).
 *
 * GENERATED FILE — do not edit by hand. It is built from
 * templates/home8-body.php by tools/sync-home9.pl, which tools/sync-home8.pl
 * runs at its end, so a change to Home 7 or Home 8 reaches this page on the
 * next sync. The order and the sections left out (Tech Stack and Home 8's
 * Core Platform, How We Work and Platform Dashboards) are defined in that
 * script; Core Platform is the main Home page's (templates/home-body.php),
 * and How We Work's process row, the horizontal mid-page CTA, the Platform
 * Dashboards hub and the placeholder Testimonials come from
 * tools/home9-process.html, tools/home9-cta-mid.html,
 * tools/home9-dashboards.html and tools/home9-testimonials.html.
 *
 * Styling is Home 8's: /assets/home7.css, /assets/home8-base.css and the dark
 * sheet /assets/home8.css, which the header toggle switches off for light.
 * /assets/home9.css adds only the Testimonials cards and the mid-page CTA.
 */
$activePage = 'home9';
include __DIR__ . '/partials/nav.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset_url('/assets/home7.css') ?>">
<!-- holds in both themes: heading weights, the industries stack, the toggle -->
<link rel="stylesheet" href="<?= asset_url('/assets/home8-base.css') ?>">
<!-- Home 9 only: the Testimonials section and the mid-page CTA's frame -->
<link rel="stylesheet" href="<?= asset_url('/assets/home9.css') ?>">
<!-- the dark theme: colour only, switched off by the header toggle for light -->
<link rel="stylesheet" id="home8Dark" href="<?= asset_url('/assets/home8.css') ?>">
<script>
(function(){
 var t = null;
 try { t = localStorage.getItem('home8-theme'); } catch (e) {}
 if (t === 'light') {
  document.getElementById('home8Dark').media = 'not all';
  document.documentElement.setAttribute('data-theme', 'light');
 }
})();
</script>

<section id="hero">

 <!-- Ambient background layer: technical grid, star dust and green shooting-star
      runners. Purely decorative and inert, aria-hidden, pointer-events:none, and
      pinned behind .hero-grid (which already sits at z-index:2), so no existing hero
      element is touched or obstructed. Animation is CSS-only (transform + opacity). -->
 <!-- Cursor glow, the same effect as the About Us hero: nothing at rest, then a
      soft green light trails the pointer and a green grid lights up beneath it.
      Decorative only — aria-hidden, pointer-events:none, and pinned behind
      .hero-grid, so it never obstructs the copy or the dashboard. -->
 <div class="ah-fx" id="ahFx" aria-hidden="true">
  <span class="ah-blob"></span>
  <span class="ah-lines"></span>
 </div>
 <script>
 // Verbatim copy of the About Us hero driver (templates/about-us-body.php). It
 // writes the pointer position into two custom properties and lets CSS place the
 // light and the grid mask; the element is never re-laid-out, only composited.
 //
 // The light eases toward the pointer instead of snapping to it — that trailing
 // is what makes it read as a soft light rather than a cursor attachment. The
 // rAF loop only runs while the pointer is inside and stops once it catches up.
 (function(){
  const fx = document.getElementById('ahFx');
  if(!fx) return;
  const hero = fx.closest('section');
  if(!hero) return;
  // hover-capable, fine pointers only: on touch there is no hover to fade out of
  if(!window.matchMedia('(hover:hover) and (pointer:fine)').matches) return;

  const snap = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let tx = 0, ty = 0, x = 0, y = 0, raf = 0, seeded = false;

  function paint(){
   fx.style.setProperty('--ah-x', x.toFixed(1) + 'px');
   fx.style.setProperty('--ah-y', y.toFixed(1) + 'px');
  }
  function step(){
   raf = 0;
   const dx = tx - x, dy = ty - y;
   if(Math.abs(dx) < 0.5 && Math.abs(dy) < 0.5){ x = tx; y = ty; paint(); return; }
   x += dx * 0.12;            // ease factor: lower trails further behind
   y += dy * 0.12;
   paint();
   raf = requestAnimationFrame(step);
  }
  function queue(){ if(!raf) raf = requestAnimationFrame(step); }

  hero.addEventListener('pointermove', function(e){
   const r = hero.getBoundingClientRect();
   tx = e.clientX - r.left;
   ty = e.clientY - r.top;
   if(!seeded){ seeded = true; x = tx; y = ty; paint(); fx.classList.add('is-on'); return; }
   if(snap){ x = tx; y = ty; paint(); return; }
   queue();
  }, { passive: true });

  hero.addEventListener('pointerenter', function(){ if(seeded) fx.classList.add('is-on'); }, { passive: true });
  hero.addEventListener('pointerleave', function(){
   fx.classList.remove('is-on');
   if(raf){ cancelAnimationFrame(raf); raf = 0; }
  }, { passive: true });
 })();
 </script>

 <div class="hero-stars" aria-hidden="true">
  <div class="hero-dust"></div>
  <span class="hero-star"></span>
  <span class="hero-star"></span>
  <span class="hero-star"></span>
  <span class="hero-star"></span>
  <span class="hero-star"></span>
  <span class="hero-star"></span>
  <span class="hero-star"></span>
 </div>

 <div class="hero-grid">
  <!-- LEFT, communication & conversion -->
  <div class="hero-left">
   <div class="hero-eyebrow">
    <span class="hero-eicon"></span>
    <span class="hero-etxt">AI-Powered Custom ERP Software</span>
   </div>

   <!-- MAIN HEADLINE, content fixed, browser wraps naturally -->
   <h1 class="hero-h">One Custom <span class="grad-os">OS</span> with <span class="grad-ai">AI</span> for Every Function of Your <span class="grad-os">Business</span></h1>

   <p class="hero-p">Drawlead is one of the leading ERP software companies in Chennai, building custom ERP solutions that connect your sales, finance, inventory, operations, HR, and customer management in one cloud-based platform.</p>

   <div class="hero-btns">
    <button type="button" data-book class="btn btn-primary">Book a Free Consultation</button>
    <a href="#functions" class="btn btn-ghost">
     <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polygon points="10,8 16,12 10,16" fill="currentColor"/></svg>
     Watch 2-Min Demo
    </a>
   </div>
  </div>
  <!-- RIGHT, live industry dashboard, auto-rotating -->
  <div class="hero-right">
   <div class="ind-tabs" id="indTabs">
    <button class="ind-tab active" data-idx="0" onclick="switchDash(0)">Ecommerce</button>
    <button class="ind-tab" data-idx="1" onclick="switchDash(1)">Hospital</button>
    <button class="ind-tab" data-idx="2" onclick="switchDash(2)">Jewellery</button>
    <button class="ind-tab" data-idx="3" onclick="switchDash(3)">Manufacturing</button>
    <button class="ind-tab" data-idx="4" onclick="switchDash(4)">Construction</button>
   </div>

   <div class="dash-window" id="dashWindow">
    <div class="ap-shell" id="dwBody"></div>
   </div>
  </div>
 </div>

 <div class="hero-stats">
    <div class="hstat">
     <div class="hstat-ico"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/></svg></div>
     <div class="hstat-txt"><div class="hstat-n gr">7</div><div class="hstat-l">Core functions</div></div>
    </div>
    <div class="hstat">
     <div class="hstat-ico"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V14M9.33 20V8M14.67 20V11M20 20V4"/></svg></div>
     <div class="hstat-txt"><div class="hstat-n">10+</div><div class="hstat-l">Industries</div></div>
    </div>
    <div class="hstat">
     <div class="hstat-ico"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/></svg></div>
     <div class="hstat-txt"><div class="hstat-n">AI</div><div class="hstat-l">Powered</div></div>
    </div>
    <div class="hstat">
     <div class="hstat-ico"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.178 8c5.096 0 5.096 8 0 8-5.095 0-7.133-8-12.739-8-4.585 0-4.585 8 0 8 5.606 0 7.644-8 12.74-8z"/></svg></div>
     <div class="hstat-txt"><div class="hstat-n">∞</div><div class="hstat-l">Scalable</div></div>
    </div>
 </div>
</section>

<!-- MARQUEE -->
<div class="mq-wrap">
 <div class="mq-track" id="mqt">
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3"/></svg><span>ERP Systems</span></div>
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg><span>AI Automation</span></div>
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>CRM Platforms</span></div>
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg><span>Analytics</span></div>
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z"/></svg><span>Cloud Infra</span></div>
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg><span>Workflow Intelligence</span></div>
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg><span>Predictive Analytics</span></div>
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/></svg><span>Process Automation</span></div>
 <!-- duplicate for seamless loop -->
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3"/></svg><span>ERP Systems</span></div>
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg><span>AI Automation</span></div>
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>CRM Platforms</span></div>
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg><span>Analytics</span></div>
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z"/></svg><span>Cloud Infra</span></div>
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg><span>Workflow Intelligence</span></div>
 <div class="mq-item"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg><span>Predictive Analytics</span></div>
 <div class="mq-item" style="border-right:none"><svg class="mq-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63"/></svg><span>Process Automation</span></div>
 </div>
</div>

<!-- ABOUT DRAWLEAD -->
<section id="about-drawlead" aria-labelledby="about-drawlead-title">
 <div class="about-drawlead-grid">
  <div class="about-drawlead-visual">
   <img class="about-drawlead-main" src="<?= asset_url('/assets/img/case-agency-os.webp') ?>" alt="A team reviewing business performance dashboards together" loading="lazy">
   <div class="about-drawlead-trust"><strong>10+</strong><span>Industries<br>served</span></div>
   <div class="about-drawlead-founder"><img src="<?= asset_url('/assets/img/founder.webp') ?>" alt="Drawlead founder" loading="lazy"></div>
  </div>
  <div class="about-drawlead-copy">
   <div class="eyebrow"><span class="eyebrow-text">About Drawlead</span></div>
   <h2 id="about-drawlead-title" class="sec-h">Building <span class="g">Smarter Systems</span> for Growing Businesses</h2>
   <p class="sec-sub">Drawlead is an ERP software development company helping MSMEs and growing businesses simplify operations, connect departments, and make better decisions. We create custom ERP software, websites, ecommerce solutions, and digital growth systems built around real business needs.</p>
   <a class="btn btn-primary" href="/about-us">Get to know us</a>
  </div>
 </div>
</section>
<!-- SOLUTIONS -->
<section id="solutions">
 <div class="sol-scroll" style="--sol-n:3">
  <div class="sol-layout">
   <div class="sol-side">
    <div class="eyebrow rv"><span class="eyebrow-text">Solutions</span></div>
    <h2 class="sec-h rv">Built for <span class="g">Growth</span> &mdash; Three Connected Solutions</h2>
    <p class="sec-sub rv">From managing core business operations to running ecommerce and generating qualified leads, Drawlead delivers connected digital solutions built around your goals. Our ERP, ecommerce &amp; marketing capabilities reduce manual effort, improve coordination, simplify daily work &amp; create a stronger foundation for sustainable growth.</p>
    <div class="sol-nav">
     <a class="sol-nav-item is-active" href="#sol-erp"><span class="sol-nav-num">01</span><span class="sol-nav-text"><strong>ERP</strong></span></a>
     <a class="sol-nav-item" href="#sol-ecommerce"><span class="sol-nav-num">02</span><span class="sol-nav-text"><strong>Ecommerce</strong></span></a>
     <a class="sol-nav-item" href="#sol-marketing"><span class="sol-nav-num">03</span><span class="sol-nav-text"><strong>Marketing</strong></span></a>
    </div>
   </div>
   <div class="sol-grid">

   <!-- Custom Operational Solutions (ERP) -->
   <div class="sol-card is-active" id="sol-erp">
    <div class="sol-head">
     <div class="sol-icon">
     <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect x="5" y="5" width="13" height="13" rx="2.5" fill="white"/><rect x="22" y="5" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.6)"/><rect x="5" y="22" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.6)"/><rect x="22" y="22" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.85)"/><path d="M18 11.5 L22 11.5 M11.5 18 L11.5 22 M28.5 18 L28.5 22 M18 28.5 L22 28.5" stroke="rgba(255,255,255,.5)" stroke-width="2" stroke-linecap="round"/></svg>
    </div>
     <div class="sol-label">ERP</div>
    </div>
    <div class="sol-name">Custom ERP Development</div>
    <div class="sol-tag">ERP software built around the way your business works.</div>
    <p class="sol-desc">Generic systems often force teams to change proven processes. Our customized ERP software adapts to your workflows, approval levels, business terms, reporting needs, integrations, user roles, and long-term growth plans.</p>
    <a href="/custom-erp-solution" class="sol-arrow">Explore ERP</a>
    <ul class="sol-list">
     <li><span class="sol-check" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></svg></span><span class="sol-benefit"><strong>Custom modules</strong><small>For your business processes</small></span></li>
     <li><span class="sol-check" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3"/><path d="M5 20c.8-3.4 3.2-5 7-5s6.2 1.6 7 5"/><path d="M18.5 8.5h3m-1.5-1.5v3"/></svg></span><span class="sol-benefit"><strong>Role-based access</strong><small>Approvals, permissions, and audit trails built in</small></span></li>
     <li><span class="sol-check" aria-hidden="true"><svg viewBox="0 0 24 24"><ellipse cx="12" cy="5.5" rx="6.5" ry="2.5"/><path d="M5.5 5.5v6c0 1.4 2.9 2.5 6.5 2.5s6.5-1.1 6.5-2.5v-6"/><path d="M5.5 11.5v6c0 1.4 2.9 2.5 6.5 2.5s6.5-1.1 6.5-2.5v-6"/></svg></span><span class="sol-benefit"><strong>Migration support</strong><small>From spreadsheets and legacy systems</small></span></li>
     <li><span class="sol-check" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M8 8v-3m0 0H5m3 0h3M16 16v3m0 0h-3m3 0h3"/><path d="M8 5v7a4 4 0 004 4h7"/><path d="M16 19v-7a4 4 0 00-4-4H5"/></svg></span><span class="sol-benefit"><strong>Seamless integrations</strong><small>Tally, banking, GST, and third-party tools</small></span></li>
    </ul>
    <div class="sol-metrics"><div><strong>100%</strong><span>Custom-Built</span></div><div><strong>1</strong><span>Source of Truth</span></div></div>
   </div>

   <!-- Ecommerce Solutions -->
   <div class="sol-card" id="sol-ecommerce">
    <div class="sol-head">
     <div class="sol-icon">
     <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><path d="M4 10 L10 10 L14 27 L32 27" stroke="rgba(255,255,255,.55)" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M11.5 14 L35 14 L32 24 L13.8 24 Z" fill="white"/><circle cx="16" cy="33" r="3" fill="rgba(255,255,255,.85)"/><circle cx="30" cy="33" r="3" fill="rgba(255,255,255,.85)"/></svg>
    </div>
     <div class="sol-label">Ecommerce</div>
    </div>
    <div class="sol-name">Ecommerce Solutions</div>
    <div class="sol-tag">Connect your online store with every backend operation.</div>
    <p class="sol-desc">Build and scale an ecommerce business where orders, inventory, payments, invoices, shipping &amp; returns stay connected. Automate updates, reduce repeated data entry, prevent order errors &amp; manage every sales channel efficiently.</p>
    <a href="/ecommerce-solutions" class="sol-arrow">Explore Ecommerce</a>
    <ul class="sol-list">
     <li><span class="sol-check" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 9h14l-1 11H6L5 9z"/><path d="M9 9V7a3 3 0 016 0v2"/></svg></span><span class="sol-benefit"><strong>Storefronts that convert</strong><small>Shopify, WooCommerce, and custom storefronts</small></span></li>
     <li><span class="sol-check" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M18 8a7 7 0 00-11.5-2L4 8.5"/><path d="M4 4v4.5h4.5"/><path d="M6 16a7 7 0 0011.5 2l2.5-2.5"/><path d="M20 20v-4.5h-4.5"/></svg></span><span class="sol-benefit"><strong>Live inventory sync</strong><small>Across every sales channel</small></span></li>
     <li><span class="sol-check" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="6" cy="6" r="2"/><circle cx="18" cy="12" r="2"/><circle cx="6" cy="18" r="2"/><path d="M8 6h4a4 4 0 014 4M8 18h4a4 4 0 004-4"/></svg></span><span class="sol-benefit"><strong>Automated workflows</strong><small>Automated orders, invoices, and GST workflows</small></span></li>
     <li><span class="sol-check" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 7h11v9H3zM14 10h3l3 3v3h-6z"/><circle cx="7" cy="18" r="1.5"/><circle cx="17" cy="18" r="1.5"/></svg></span><span class="sol-benefit"><strong>End-to-end fulfilment</strong><small>Payment, shipping, fulfilment, and return integrations</small></span></li>
    </ul>
    <div class="sol-metrics"><div><strong>24/7</strong><span>Online Selling</span></div><div><strong>0</strong><span>Manual Entry</span></div></div>
   </div>

   <!-- Marketing Solutions -->
   <div class="sol-card" id="sol-marketing">
    <div class="sol-head">
     <div class="sol-icon">
     <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><path d="M4 6 L36 6 L24 21 L24 34 L16 30 L16 21 Z" fill="white"/><path d="M16 21 L24 21 L24 27 L16 27 Z" fill="rgba(255,255,255,.55)"/><path d="M4 6 L36 6 L31 12 L9 12 Z" fill="rgba(255,255,255,.6)"/></svg>
    </div>
     <div class="sol-label">Marketing</div>
    </div>
    <div class="sol-name">Marketing Automation</div>
    <div class="sol-tag">Turn customer interest into consistent business growth.</div>
    <p class="sol-desc">Attract the right audience, capture qualified enquiries, and maintain timely communication using SEO, paid campaigns, WhatsApp, email, and automated follow-ups. Track every lead, reduce missed opportunities, and improve conversions.</p>
    <a href="/marketing-solutions" class="sol-arrow">Explore Marketing</a>
    <ul class="sol-list">
     <li><span class="sol-check" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="5.5"/><path d="M15 15l4 4"/><path d="M8 10.5h5M10.5 8v5"/></svg></span><span class="sol-benefit"><strong>Technical SEO</strong><small>Website performance improvements</small></span></li>
     <li><span class="sol-check" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 3h9l3 3v15H6z"/><path d="M15 3v4h4M9 12h6M9 16h6"/></svg></span><span class="sol-benefit"><strong>Content engine</strong><small>Search-focused content and on-page optimization</small></span></li>
     <li><span class="sol-check" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 17l6-6 4 4 6-7"/><path d="M15 8h5v5"/><path d="M4 20h16"/></svg></span><span class="sol-benefit"><strong>Performance campaigns</strong><small>Google, Meta, and LinkedIn advertising campaigns</small></span></li>
     <li><span class="sol-check" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 5h16v11H9l-5 4z"/><path d="M8 10h8"/></svg></span><span class="sol-benefit"><strong>Instant follow-up</strong><small>Automated WhatsApp and email lead follow-ups</small></span></li>
    </ul>
    <div class="sol-metrics"><div><strong>SEO + ADS</strong><span>Growth Channels</span></div><div><strong>24/7</strong><span>Lead Follow-Ups</span></div></div>
   </div>

   </div>
  </div>
 </div>
</section>
 <script>
(function(){
 const sec=document.getElementById('solutions');
 if(!sec)return;
 const tabs=Array.from(sec.querySelectorAll('.sol-nav-item'));
 const cards=tabs.map(tab=>sec.querySelector(tab.getAttribute('href')));
 if(!tabs.length||cards.some(card=>!card))return;
 function activate(index){
  tabs.forEach((tab,i)=>{
   const active=i===index;
   tab.classList.toggle('is-active',active);
   tab.setAttribute('role','tab');
   tab.setAttribute('aria-selected',active?'true':'false');
   tab.setAttribute('tabindex',active?'0':'-1');
   cards[i].classList.toggle('is-active',active);
   cards[i].hidden=!active;
  });
 }
 tabs.forEach((tab,i)=>{
  tab.setAttribute('role','tab');
  tab.addEventListener('click',event=>{event.preventDefault();activate(i)});
  tab.addEventListener('keydown',event=>{
   if(event.key!=='ArrowRight'&&event.key!=='ArrowLeft')return;
   event.preventDefault();
   const next=(i+(event.key==='ArrowRight'?1:tabs.length-1))%tabs.length;
   activate(next);tabs[next].focus();
  });
 });
 sec.querySelector('.sol-nav').setAttribute('role','tablist');
 sec.querySelector('.sol-nav').setAttribute('aria-label','Drawlead solutions');
 activate(0);
})();
</script>

<!-- ═══════════════════ 7 FUNCTIONS ═══════════════════ -->
<section id="functions">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Core Platform</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">The <span class="g">7 Functions</span> of Business <span class="fade">— Unified</span></h2>
 <p class="sec-sub rv">Every core business function, streamlined and intelligently connected through one operating system.</p>

 <div class="fn-grid">
 <!-- 01 Management -->
 <div class="fn-card rv d1">
 <div class="fn-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(0,0,0,.2)">
 <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><rect x="4" y="25" width="7" height="11" rx="1.5" fill="rgba(255,255,255,.5)"/><rect x="14" y="17" width="7" height="19" rx="1.5" fill="rgba(255,255,255,.75)"/><rect x="24" y="9" width="7" height="27" rx="1.5" fill="white"/><polyline points="6,21 17,13 27,5" fill="none" stroke="rgba(255,255,255,.6)" stroke-width="2" stroke-linecap="round"/><polygon points="27,2 33,8 21,8" fill="rgba(255,255,255,.7)"/></svg>
 </div>
 <div class="fn-num">01 — Management</div>
 <div class="fn-name">Management</div>
 <div class="fn-desc">Centralized dashboards and operational visibility for faster, smarter business decisions.</div>
 <div class="fn-tags"><span class="fn-tag">KPI Tracking</span><span class="fn-tag">Analytics</span><span class="fn-tag">Approvals</span></div>
 <button type="button" data-book class="fn-arrow">Explore module →</button>
 </div>
 <!-- 02 Sales -->
 <div class="fn-card rv d2">
 <div class="fn-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(0,0,0,.2)">
 <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><polyline points="4,30 14,18 22,23 36,8" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><polyline points="4,35 14,23 22,28 36,13" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><circle cx="36" cy="13" r="4" fill="white"/></svg>
 </div>
 <div class="fn-num" style="color:#32b46f">02 — Sales</div>
 <div class="fn-name">Sales</div>
 <div class="fn-desc">Manage leads, pipelines, customers, and revenue operations from one unified platform.</div>
 <div class="fn-tags"><span class="fn-tag">CRM</span><span class="fn-tag">Pipeline</span><span class="fn-tag">Invoicing</span></div>
 <button type="button" data-book class="fn-arrow" style="color:#32b46f">Explore module →</button>
 </div>
 <!-- 03 Marketing -->
 <div class="fn-card rv d3">
 <div class="fn-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(0,0,0,.2)">
 <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><path d="M5 14 L5 26 L11 26 L11 14 Z" fill="rgba(255,255,255,.6)"/><path d="M11 14 L30 6 L30 34 L11 26 Z" fill="white"/><path d="M11 18 L11 22 L8 28 L5 28 L5 22" fill="rgba(255,255,255,.4)"/><path d="M32 15 Q38 20 32 25" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="2.5" stroke-linecap="round"/></svg>
 </div>
 <div class="fn-num">03 — Marketing</div>
 <div class="fn-name">Marketing</div>
 <div class="fn-desc">Track campaigns, automate WhatsApp &amp; email, and improve customer engagement at scale.</div>
 <div class="fn-tags"><span class="fn-tag">Campaigns</span><span class="fn-tag">WhatsApp</span><span class="fn-tag">Nurturing</span></div>
 <button type="button" data-book class="fn-arrow">Explore module →</button>
 </div>
 <!-- 04 Operations -->
 <div class="fn-card rv d4">
 <div class="fn-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(0,0,0,.2)">
 <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="7" fill="white"/><circle cx="20" cy="20" r="3.5" fill="rgba(124,45,18,.85)"/><rect x="18" y="3" width="4" height="7" rx="2" fill="rgba(255,255,255,.85)"/><rect x="18" y="30" width="4" height="7" rx="2" fill="rgba(255,255,255,.85)"/><rect x="3" y="18" width="7" height="4" rx="2" fill="rgba(255,255,255,.85)"/><rect x="30" y="18" width="7" height="4" rx="2" fill="rgba(255,255,255,.85)"/><rect x="7.5" y="7.5" width="4" height="7" rx="2" transform="rotate(45 9.5 11)" fill="rgba(255,255,255,.55)"/><rect x="28.5" y="7.5" width="4" height="7" rx="2" transform="rotate(-45 30.5 11)" fill="rgba(255,255,255,.55)"/></svg>
 </div>
 <div class="fn-num" style="color:#14855a">04 — Operations</div>
 <div class="fn-name">Operations</div>
 <div class="fn-desc">Streamline activities, inventory, and vendor management with intelligent process automation.</div>
 <div class="fn-tags"><span class="fn-tag">Workflows</span><span class="fn-tag">Inventory</span><span class="fn-tag">Vendors</span></div>
 <button type="button" data-book class="fn-arrow" style="color:#14855a">Explore module →</button>
 </div>
 <!-- 05 Finance -->
 <div class="fn-card rv d1">
 <div class="fn-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(0,0,0,.2)">
 <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><ellipse cx="20" cy="11" rx="13" ry="5" fill="white"/><path d="M7 11 Q7 18 20 18 Q33 18 33 11" fill="rgba(255,255,255,.75)"/><path d="M7 18 Q7 25 20 25 Q33 25 33 18" fill="rgba(255,255,255,.5)"/><path d="M7 25 Q7 32 20 32 Q33 32 33 25" fill="rgba(255,255,255,.3)"/></svg>
 </div>
 <div class="fn-num" style="color:#32b46f">05 — Finance</div>
 <div class="fn-name">Finance</div>
 <div class="fn-desc">Centralize billing, expenses, financial reporting, and accounting integrations seamlessly.</div>
 <div class="fn-tags"><span class="fn-tag">Billing</span><span class="fn-tag">Expenses</span><span class="fn-tag">Reports</span></div>
 <button type="button" data-book class="fn-arrow" style="color:#32b46f">Explore module →</button>
 </div>
 <!-- 06 HR -->
 <div class="fn-card rv d2">
 <div class="fn-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(0,0,0,.2)">
 <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><circle cx="14" cy="12" r="7" fill="white"/><circle cx="28" cy="14" r="5" fill="rgba(255,255,255,.6)"/><path d="M2 34 C2 25 8 22 14 22 C20 22 26 25 26 34 Z" fill="rgba(255,255,255,.8)"/><path d="M26 28 C26 24 29 22 32 22 C35 22 38 24 38 28 L38 34 L26 34 Z" fill="rgba(255,255,255,.4)"/></svg>
 </div>
 <div class="fn-num" style="color:#14855a">06 — Human Resources</div>
 <div class="fn-name">HR</div>
 <div class="fn-desc">Manage employees, attendance, payroll workflows, and leave management efficiently.</div>
 <div class="fn-tags"><span class="fn-tag">Payroll</span><span class="fn-tag">Attendance</span><span class="fn-tag">Leave</span></div>
 <button type="button" data-book class="fn-arrow" style="color:#14855a">Explore module →</button>
 </div>
 <!-- 07 Inventory Management -->
 <div class="fn-card rv d3">
 <div class="fn-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(0,0,0,.2)">
 <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><rect x="5" y="18" width="13" height="14" rx="1.5" fill="rgba(255,255,255,.6)"/><rect x="21" y="18" width="14" height="14" rx="1.5" fill="white"/><rect x="12" y="6" width="16" height="13" rx="1.5" fill="rgba(255,255,255,.85)"/><path d="M12 12 L28 12" stroke="rgba(20,133,90,.55)" stroke-width="1.6"/><path d="M5 24 L18 24" stroke="rgba(20,133,90,.4)" stroke-width="1.6"/><path d="M21 24 L35 24" stroke="rgba(20,133,90,.4)" stroke-width="1.6"/></svg>
 </div>
 <div class="fn-num" style="color:#23a065">07 — Inventory</div>
 <div class="fn-name">Inventory Management</div>
 <div class="fn-desc">Track stock across every warehouse and channel, with alerts before you run out.</div>
 <div class="fn-tags"><span class="fn-tag">Stock Levels</span><span class="fn-tag">Reorder Alerts</span><span class="fn-tag">Multi-warehouse</span></div>
 <button type="button" data-book class="fn-arrow" style="color:#23a065">Explore module →</button>
 </div>
 <!-- 08 CTA Card -->
 <div class="fn-card fn-cta-card rv d4">
 <div style="font-size:26px;font-weight:800;color:#fff;line-height:1.1;letter-spacing:-.02em">Ready to<br>unify all 7?</div>
 <div style="font-size:12px;color:rgba(255,255,255,.44);line-height:1.65;font-weight:400">Get a personalised walkthrough of all modules working in sync.</div>
 <button type="button" data-book style="background:#fff;color:var(--black);padding:11px 22px;border-radius:6px;font-size:10.5px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;text-decoration:none;display:inline-block;transition:opacity .2s" onmouseover="this.style.opacity='.84'" onmouseout="this.style.opacity='1'">Book Free Demo →</button>
 </div>
 </div>

 <div class="sec-cta rv">
 <button type="button" data-book class="btn btn-black">Schedule a Consultation →</button>
 <a href="#dashboards" class="btn btn-outline2">View Live Dashboards</a>
 </div>
</section>

<!-- CASE STUDIES -->
<section id="cases">
 <div class="cases-scroll" id="casesScroll">
  <div class="cases-pin">
   <div class="cases-head">
 <div class="eyebrow rv"><span class="eyebrow-text">Case Studies</span></div>
 <h2 class="sec-h rv"><span>Real Results</span> for <span class="fade">Real Businesses</span></h2>
 <p class="sec-sub rv">How Drawlead transforms operations across industries with measurable outcomes.</p>
   </div>
   <div class="cases-track" id="casesTrack">
 <div class="case-card" style="--acc:#38B976;--acc2:#9BE3C0;--atm:rgba(56,185,118,.18)">
  <div class="case-screen"><img class="case-img" src="<?= asset_url('/assets/img/case-construction-erp.webp') ?>" alt="Site engineer reviewing drawings on a multi-storey construction site" decoding="async" fetchpriority="low"></div>
  <div class="case-body">
    <span class="case-tag">Construction and Real Estate</span>
    <div class="case-title">Construction ERP Solution</div>
    <ul class="case-list">
      <li>Better operational visibility across all project sites</li>
      <li>Faster reporting workflows and billing automation</li>
      <li>Improved multi-site project management controls</li>
    </ul>
    <button type="button" data-book class="btn btn-outline2 btn-sm" style="margin-top:1.1rem;align-self:flex-start">Read Case Study</button>
  </div>
 </div>
 <div class="case-card" style="--acc:#2FB5AE;--acc2:#A8E6E2;--atm:rgba(47,181,174,.18)">
  <div class="case-screen"><img class="case-img" src="<?= asset_url('/assets/img/case-physiotherapy.webp') ?>" alt="Physiotherapist assessing a patient's shoulder mobility in a clinic" decoding="async" fetchpriority="low"></div>
  <div class="case-body">
    <span class="case-tag">Healthcare and Wellness</span>
    <div class="case-title">Multi-Brand Physiotherapy Management</div>
    <ul class="case-list">
      <li>Streamlined clinic workflows across branches</li>
      <li>Improved scheduling efficiency and capacity</li>
      <li>Centralized billing and cross-branch reporting</li>
    </ul>
    <button type="button" data-book class="btn btn-outline2 btn-sm" style="margin-top:1.1rem;align-self:flex-start">Read Case Study</button>
  </div>
 </div>
 <div class="case-card" style="--acc:#8B5CF6;--acc2:#CDBEFB;--atm:rgba(139,92,246,.16)">
  <div class="case-screen"><img class="case-img" src="<?= asset_url('/assets/img/case-agency-os.webp') ?>" alt="Agency team reviewing campaign dashboards in a meeting room" decoding="async" fetchpriority="low"></div>
  <div class="case-body">
    <span class="case-tag">Marketing Agencies</span>
    <div class="case-title">Agency OS</div>
    <ul class="case-list">
      <li>Improved team collaboration and project delivery</li>
      <li>Better client and pipeline management</li>
      <li>Measurable increase in team productivity</li>
    </ul>
    <button type="button" data-book class="btn btn-outline2 btn-sm" style="margin-top:1.1rem;align-self:flex-start">Read Case Study</button>
  </div>
 </div>
   </div><!-- /cases-track -->
 <div class="sec-cta rv">
 <button type="button" data-book class="btn btn-black">Start Your Success Story</button>
 </div>
  </div>
 </div><!-- /cases-scroll -->
</section>

<!-- HOW WE WORK — process -->
<!-- Home 9's How We Work, in place of Home 8's (which sync-home9.pl leaves
     out): Home 8's eyebrow, heading and description, centred in one column
     over the steps (heading on one line, description on two), and the button
     centred under the steps. The four steps (Audit / Measure / Automate /
     Scale) are set out as a staggered row of cards joined by curved arrows;
     their titles, copy and icons are Home 8's four cards. No right-hand
     panel. The steps animate in one after another, each arrow drawing
     between them (the script at the foot of the section). Edit this file,
     then run
     `perl tools/sync-home9.pl`; templates/home9-body.php is generated.
     Styles: #process in assets/home9.css. -->
<section id="process">
 <div class="pr-head">
  <div class="eyebrow rv"><span class="eyebrow-text">How We Work</span></div>
  <h2 class="sec-h rv">We don't build first. <br><span class="g">We understand first.</span></h2>
  <p class="sec-sub rv">Before a single line of code or campaign goes live, we audit how your business actually runs, so every system we build is measurable, automated, and built to scale.</p>
 </div>

 <!-- The arrows between the cards are drawn by CSS (each card's ::after), so
      the list holds only the four steps. -->
 <ol class="pr-steps">
  <li class="pr-step">
   <span class="pr-num">01</span>
   <span class="pr-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M4.2 4.1A2.1 2.1 0 0 1 6.3 2h7.05L18.4 7.05v3.4a6.6 6.6 0 0 0-2.3-.72V8.5h-3.25a1.5 1.5 0 0 1-1.5-1.5V4.3H6.5v15.4h3.24c.2.83.55 1.6 1.01 2.3H6.3a2.1 2.1 0 0 1-2.1-2.1z"/><path d="M13.65 4.75 16.7 7.8h-3.05z"/><rect x="7.5" y="8.9" width="4.4" height="2" rx="1"/><rect x="7.5" y="12.4" width="3.3" height="2" rx="1"/><circle cx="16.15" cy="15.75" r="4.15"/><path d="M18.95 18.55a1.2 1.2 0 0 1 1.7 0l1.9 1.9a1.2 1.2 0 0 1-1.7 1.7l-1.9-1.9a1.2 1.2 0 0 1 0-1.7"/></svg></span>
   <h3 class="pr-name">Audit</h3>
   <p class="pr-desc">We map your current workflows, tools, and customer journey to find exactly what's slowing growth down.</p>
  </li>
  <li class="pr-step">
   <span class="pr-num">02</span>
   <span class="pr-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="19.3" width="18" height="2.3" rx="1.15"/><rect x="4.6" y="12.4" width="4" height="5.6" rx="1.3"/><rect x="10" y="6.6" width="4" height="11.4" rx="1.3"/><rect x="15.4" y="9.6" width="4" height="8.4" rx="1.3"/></svg></span>
   <h3 class="pr-name">Measure</h3>
   <p class="pr-desc">We set up KPIs, dashboards, and tracking so every decision from here on is backed by real data.</p>
  </li>
  <li class="pr-step">
   <span class="pr-num">03</span>
   <span class="pr-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><rect x="2.6" y="2.9" width="8.2" height="6.4" rx="2"/><rect x="13.2" y="14.7" width="8.2" height="6.4" rx="2"/><path d="M10.8 5h3.05a3.6 3.6 0 0 1 3.6 3.6v6.1h-2.3V8.6a1.3 1.3 0 0 0-1.3-1.3H10.8z"/><path d="M13.2 18.9h-3.05a3.6 3.6 0 0 1-3.6-3.6V9.2h2.3v6.1c0 .72.58 1.3 1.3 1.3h3.05z"/></svg></span>
   <h3 class="pr-name">Automate</h3>
   <p class="pr-desc">We remove repetitive manual work (approvals, follow-ups, notifications) before we build anything new.</p>
  </li>
  <li class="pr-step">
   <span class="pr-num">04</span>
   <span class="pr-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="19.4" width="18" height="2.2" rx="1.1"/><path d="M20.9 4.5a1.15 1.15 0 0 0-.8-.33h-5.15a1.15 1.15 0 0 0 0 2.3h2.37l-4.62 4.62-2.83-2.83a1.15 1.15 0 0 0-1.63 0l-5.1 5.1a1.15 1.15 0 0 0 1.63 1.63l4.28-4.29 2.83 2.83a1.15 1.15 0 0 0 1.63 0l5.44-5.43v2.37a1.15 1.15 0 0 0 2.3 0V5.32a1.15 1.15 0 0 0-.35-.82"/></svg></span>
   <h3 class="pr-name">Scale</h3>
   <p class="pr-desc">Only then do we build the ERP, CRM, website, or automation platform, designed to grow with you.</p>
  </li>
 </ol>

 <div class="pr-actions rv">
  <button type="button" data-book class="btn pr-cta">Start With an Audit <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4"/></svg></button>
  <p class="pr-note"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><circle cx="8" cy="8" r="6.25"/><path d="M8 4.75V8l2.25 1.5"/></svg>14-day turnaround &bull; Full architecture report included</p>
 </div>

 <script>
 // The steps' entrance (styles: assets/home9.css, "Process animation").
 // .pr-anim hides the cards, their contents and the arrows; .is-in brings
 // them in. From 1101px wide the four stand in one row, so the whole list
 // gets .is-in when it is in view and CSS delays play the sequence: card,
 // its arrow drawing, the next card, and so on, then a glow starts travelling
 // along the row. Narrower, the cards stack, so each gets .is-in as it
 // arrives on screen. No observer or reduced motion: nothing is hidden.
 (function(){
  const list = document.querySelector('#process .pr-steps');
  if(!list || !('IntersectionObserver' in window)) return;
  if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  const wide = window.matchMedia('(min-width:1101px)').matches;
  list.classList.add('pr-anim');
  const io = new IntersectionObserver(entries => {
   entries.forEach(e => {
    if(!e.isIntersecting) return;
    e.target.classList.add('is-in');
    io.unobserve(e.target);
   });
  }, { threshold: wide ? 0.3 : 0.15, rootMargin: '0px 0px -6% 0px' });
  if(wide) io.observe(list);
  else Array.from(list.children).forEach(li => io.observe(li));
 })();
 </script>
</section>

<!-- CTA (mid-page) -->
<!-- Home 9's CTA after How We Work: the closing CTA's card laid out across the
     page. Eyebrow and heading on the left, description and buttons on the
     right, the strapline under both. The copy matches the closing CTA but is
     kept here rather than copied from it, because the layout and the line
     breaks differ. Edit this file, then run `perl tools/sync-home9.pl`;
     templates/home9-body.php is generated. Styles: #cta-mid in
     assets/home9.css; the card's own look comes from Home 7's .cta-* rules. -->
<section id="cta-mid">
 <div class="cta-card">
  <div class="cta-grid-bg"></div>
  <div class="cta-glow"></div>

  <div class="ctam-row">
   <div class="ctam-lead">
    <div class="cta-eyebrow rv">Start Your ERP Journey</div>
    <h2 class="cta-h rv">Build your <span class="fade">business</span><br><span class="gr">ERP</span> <span class="gr2">OS</span> with <span class="gr3">AI</span></h2>
   </div>
   <div class="ctam-act">
    <p class="cta-p rv">Digitize, automate, and scale with Drawlead. <br>Start with a free consultation, no commitment needed.</p>
    <div class="cta-btns rv">
     <button type="button" data-book class="cta-btn-w">Schedule Free Consultation</button>
     <button type="button" data-book class="cta-btn-g">Book a Product Demo</button>
    </div>
   </div>
  </div>

  <div class="cta-note rv">செயலை மாற்றும் · Intelligent Operating System · Secure · Scalable · Future-Ready</div>
 </div>
</section>

<!-- APP CHAOS → SMART BOARD -->
<section id="unify">
 <div class="eyebrow rv"><span class="eyebrow-text">The Problem</span></div>
 <h2 class="sec-h rv">Stop running your business from <span class="g">a dozen different tabs</span></h2>
 <p class="sec-sub rv">Google Sheets, CRM, WhatsApp, Notion, billing software, phone calls. Your business data is scattered everywhere. Drawlead brings it all into one Smart Board.</p>

 <!-- Brand treatment: each pill carries a data-brand key that drives its colour in
      home7.css, plus a small inline mark. Where a vendor's real logo is a simple
      geometric form (Trello board, Todoist checks, WhatsApp bubble, the Office
      letter tiles, the Google document marks) the icon follows it in the official
      palette. Where there is no simple public mark to follow (Vyapar, Tally, Bill
      Book, CRM, Calls) it uses a plain neutral glyph rather than an invented logo. -->
 <div class="phys-stage rv" id="physStage">
  <div class="phys-pill" data-brand="notion"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="4" fill="#fff"/><path d="M8 17V8l6.2 8.2V8" stroke="#111" stroke-width="1.9" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Notion</span></div>
  <div class="phys-pill" data-brand="word"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="2.5" width="16" height="19" rx="2" fill="#fff"/><path d="M7.6 8.2l1.6 7 1.6-4.6 1.6 4.6 1.6-7" stroke="#185ABD" stroke-width="1.7" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Microsoft Word</span></div>
  <div class="phys-pill" data-brand="crm"><svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><circle cx="9" cy="11" r="2.1"/><path d="M5.9 16.4c.5-1.5 1.7-2.2 3.1-2.2s2.6.7 3.1 2.2M15 10h4M15 13.4h4"/></svg><span>CRM</span></div>
  <div class="phys-pill" data-brand="vyapar"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z" fill="#fff"/><text x="12" y="14.6" text-anchor="middle" font-size="9.5" font-weight="700" fill="#CE2C24" font-family="system-ui,sans-serif">&#8377;</text></svg><span>Vyapar</span></div>
  <div class="phys-pill" data-brand="calls"><svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6.2 3.5h3l1.5 3.8-2 1.4a12 12 0 006.6 6.6l1.4-2 3.8 1.5v3a2 2 0 01-2.2 2A17.5 17.5 0 014.2 5.7a2 2 0 012-2.2z"/></svg><span>Calls</span></div>
  <div class="phys-pill" data-brand="gcal"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="17" rx="2.5" fill="#fff"/><path d="M5.5 4H9v3H3V6.5A2.5 2.5 0 015.5 4z" fill="#4285F4"/><path d="M9 4h6v3H9z" fill="#EA4335"/><path d="M15 4h3.5A2.5 2.5 0 0121 6.5V7h-6z" fill="#FBBC04"/><path d="M3 17h6v4H5.5A2.5 2.5 0 013 18.5z" fill="#34A853"/><text x="14" y="17.6" text-anchor="middle" font-size="8.5" font-weight="700" fill="#1f1f1f" font-family="system-ui,sans-serif">31</text></svg><span>Google Calendar</span></div>
  <div class="phys-pill" data-brand="gdocs"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2.5h7.5L19 8v13.5H6z" fill="#fff"/><path d="M13.5 2.5L19 8h-5.5z" fill="#A8C7FA"/><path d="M8.6 12h6.8M8.6 15h6.8M8.6 18h4.4" stroke="#1A73E8" stroke-width="1.5" stroke-linecap="round"/></svg><span>Google Docs</span></div>
  <div class="phys-pill" data-brand="ppt"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="2.5" width="16" height="19" rx="2" fill="#fff"/><path d="M9 17.2V8h3.1a2.7 2.7 0 010 5.4H9" stroke="#C43E1C" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg><span>PowerPoint</span></div>
  <div class="phys-pill" data-brand="billbook"><svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4.5A2 2 0 017 2.5h12v19H7a2 2 0 01-2-2z"/><path d="M9 7h6M9 11h6M9 15h4"/></svg><span>Bill Book</span></div>
  <div class="phys-pill" data-brand="whatsapp"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.2a9.6 9.6 0 00-8.3 14.4L2.4 21.8l5.4-1.3A9.6 9.6 0 1012 2.2z" fill="#04331a"/><path d="M9 7.6c-.3 0-.6.1-.8.4-.3.3-.9.9-.9 2.1s.9 2.4 1 2.6c.1.2 1.7 2.8 4.3 3.8 2.1.8 2.6.7 3 .6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-2.2-1.1c-.2-.1-.4-.1-.5.1l-.7.9c-.1.2-.3.2-.5.1a7 7 0 01-2-1.3 7.7 7.7 0 01-1.4-1.8c-.1-.2 0-.4.1-.5l.7-1v-.5l-.7-1.6c-.2-.4-.4-.4-.5-.4z" fill="#25D366"/></svg><span>WhatsApp</span></div>
  <div class="phys-pill" data-brand="outlook"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="9.5" y="5" width="11.5" height="14" rx="1.6" fill="#fff"/><path d="M9.5 7.6l5.8 3.9 5.7-3.9" stroke="#0F6CBD" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/><rect x="2" y="3.5" width="10" height="17" rx="2" fill="#0F6CBD"/><ellipse cx="7" cy="12" rx="2.3" ry="3" fill="none" stroke="#fff" stroke-width="1.7"/></svg><span>Microsoft Outlook</span></div>
  <div class="phys-pill" data-brand="excel"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="2.5" width="16" height="19" rx="2" fill="#fff"/><path d="M9 8l6 9M15 8l-6 9" stroke="#107C41" stroke-width="1.9" stroke-linecap="round"/></svg><span>Excel</span></div>
  <div class="phys-pill" data-brand="tally"><svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10.5M9.3 20V5M14.7 20v-6.5M20 20V8.5"/></svg><span>Tally</span></div>
  <div class="phys-pill" data-brand="gmail"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 18.6V8.1l9 6.2 9-6.2v10.5a1 1 0 01-1 1h-2.6v-7.1L12 16.7l-5.4-4.2v7.1H4a1 1 0 01-1-1z" fill="#EA4335"/><path d="M3 8.1c0-1.7 1.9-2.6 3.2-1.6L12 10.7l5.8-4.2c1.3-1 3.2-.1 3.2 1.6L12 14.3z" fill="#C5221F"/></svg><span>Gmail</span></div>
  <div class="phys-pill" data-brand="gsheets"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2.5h7.5L19 8v13.5H6z" fill="#fff"/><path d="M13.5 2.5L19 8h-5.5z" fill="#B7E1CD"/><path d="M8.6 11.6h7.8v7.4H8.6z" fill="none" stroke="#0B8043" stroke-width="1.4"/><path d="M12.5 11.6V19M8.6 15.3h7.8" stroke="#0B8043" stroke-width="1.4"/></svg><span>Google Sheets</span></div>
  <div class="phys-pill" data-brand="trello"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3" fill="#fff"/><rect x="6" y="6" width="4.6" height="11" rx="1.2" fill="#0C66E4"/><rect x="13.4" y="6" width="4.6" height="6.6" rx="1.2" fill="#0C66E4"/></svg><span>Trello</span></div>
  <div class="phys-pill" data-brand="todoist"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="4.5" fill="#fff"/><path d="M7.5 9.3l1.9 1.5 4.6-2.7M7.5 13l1.9 1.5 4.6-2.7M7.5 16.7l1.9 1.5 4.6-2.7" stroke="#D1453B" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Todoist</span></div>
  <div class="phys-pill" data-brand="calendly"><svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="5" width="17" height="15.5" rx="3"/><path d="M8 3.2v3.4M16 3.2v3.4M3.5 10h17"/><path d="M14.7 13.9a3.1 3.1 0 100 3.2"/></svg><span>Calendly</span></div>
 </div>

 <div class="sec-cta rv">
  <button type="button" data-book class="btn btn-black">Explore the Platform</button>
 </div>
</section>

<!-- PLATFORM DASHBOARDS — hub -->
<!-- Home 9's Platform Dashboards, in place of Home 8's horizontal row of full
     dashboard cards (which sync-home9.pl leaves out). The six modules are
     wired to one central hub: three down each side as compact cards (icon,
     name, line, period and the module's three headline figures, all Home 8's
     copy) and the "Centralized" hub between them, whose network icon has one
     spoke per module. The wires are drawn by the script at the foot of the
     section from where the cards and hub actually sit, with data dots
     travelling in along each; hovering or tapping a module lights its card,
     wire, spoke and the hub's caption, and when nobody is pointing the
     highlight walks round the six on its own. Edit this file, then run
     `perl tools/sync-home9.pl`; templates/home9-body.php is generated.
     Styles: #dashboards in assets/home9.css (section 11). -->
<section id="dashboards">
 <div class="hub-head">
  <div class="eyebrow rv"><span class="eyebrow-text">Platform Dashboards</span></div>
  <h2 class="sec-h rv">Every Module. <span class="fade">One Screen.</span></h2>
  <p class="sec-sub rv">Live ERP dashboards for every function. See exactly what Drawlead looks like in action.</p>
 </div>

 <div class="hub-stage" id="hubStage">
  <svg class="hub-wires" aria-hidden="true" focusable="false"></svg>

  <div class="hub-col hub-col-l">
   <article class="hub-mod" data-i="0">
    <div class="hub-mod-top">
     <span class="hub-mod-ico" aria-hidden="true"><svg viewBox="0 0 36 36" fill="none"><rect x="3" y="22" width="6" height="10" rx="1" fill="rgba(255,255,255,.5)"/><rect x="12" y="14" width="6" height="18" rx="1" fill="rgba(255,255,255,.75)"/><rect x="21" y="6" width="6" height="26" rx="1" fill="white"/><polyline points="6,18 15,10 24,3" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="2" stroke-linecap="round"/><circle cx="24" cy="3" r="3" fill="white"/></svg></span>
     <div class="hub-mod-tt"><div class="hub-mod-name"><h3>Sales Pipeline</h3><span class="hub-mod-when">This Year</span></div><p>Track your revenue and deal progress</p></div>
    </div>
    <dl class="hub-kpis">
     <div><dt>Total Revenue</dt><dd>&#8377;2.4 Cr<em class="up">18%</em></dd></div>
     <div><dt>Growth</dt><dd>+28%<em class="up">6%</em></dd></div>
     <div><dt>Leads</dt><dd>247<em class="up">12%</em></dd></div>
    </dl>
    <span class="hub-port" aria-hidden="true"></span>
   </article>
   <article class="hub-mod" data-i="1">
    <div class="hub-mod-top">
     <span class="hub-mod-ico" aria-hidden="true"><svg viewBox="0 0 36 36" fill="none"><ellipse cx="18" cy="10" rx="12" ry="4.5" fill="white"/><path d="M6 10 Q6 17 18 17 Q30 17 30 10" fill="rgba(255,255,255,.7)"/><path d="M6 17 Q6 24 18 24 Q30 24 30 17" fill="rgba(255,255,255,.45)"/><path d="M6 24 Q6 31 18 31 Q30 31 30 24" fill="rgba(255,255,255,.25)"/></svg></span>
     <div class="hub-mod-tt"><div class="hub-mod-name"><h3>Finance &amp; Billing</h3><span class="hub-mod-when">This Quarter</span></div><p>Monitor invoices, collections and cash flow</p></div>
    </div>
    <dl class="hub-kpis">
     <div><dt>Invoiced</dt><dd>&#8377;86L<em class="up">12%</em></dd></div>
     <div><dt>Collected</dt><dd>&#8377;72L<em class="up">9%</em></dd></div>
     <div><dt>Outstanding</dt><dd>&#8377;14L<em class="down">4%</em></dd></div>
    </dl>
    <span class="hub-port" aria-hidden="true"></span>
   </article>
   <article class="hub-mod" data-i="2">
    <div class="hub-mod-top">
     <span class="hub-mod-ico" aria-hidden="true"><svg viewBox="0 0 36 36" fill="none"><circle cx="18" cy="18" r="6.5" fill="white"/><circle cx="18" cy="18" r="3" fill="rgba(50,180,111,.7)"/><rect x="16" y="2" width="4" height="6" rx="2" fill="rgba(255,255,255,.85)"/><rect x="16" y="28" width="4" height="6" rx="2" fill="rgba(255,255,255,.85)"/><rect x="2" y="16" width="6" height="4" rx="2" fill="rgba(255,255,255,.85)"/><rect x="28" y="16" width="6" height="4" rx="2" fill="rgba(255,255,255,.85)"/></svg></span>
     <div class="hub-mod-tt"><div class="hub-mod-name"><h3>Operations</h3><span class="hub-mod-when">This Month</span></div><p>Track tasks, vendors and delivery timelines</p></div>
    </div>
    <dl class="hub-kpis">
     <div><dt>Total Tasks</dt><dd>1,248<em class="up">6%</em></dd></div>
     <div><dt>On-Time Rate</dt><dd>94%<em class="up">3%</em></dd></div>
     <div><dt>Vendors</dt><dd>38<em class="up">2</em></dd></div>
    </dl>
    <span class="hub-port" aria-hidden="true"></span>
   </article>
  </div>

  <div class="hub-core">
   <div class="hub-box">
    <svg class="hub-ico" viewBox="0 0 80 80" fill="none" aria-hidden="true" focusable="false">
     <!-- one spoke per module, on the side its card stands: 0-2 left (top,
          middle, bottom), 3-5 right -->
     <g class="hub-node" data-i="0"><line x1="35.5" y1="32.2" x2="28" y2="19.2"/><circle cx="25" cy="14" r="6"/></g>
     <g class="hub-node" data-i="1"><line x1="31" y1="40" x2="16" y2="40"/><circle cx="10" cy="40" r="6"/></g>
     <g class="hub-node" data-i="2"><line x1="35.5" y1="47.8" x2="28" y2="60.8"/><circle cx="25" cy="66" r="6"/></g>
     <g class="hub-node" data-i="3"><line x1="44.5" y1="32.2" x2="52" y2="19.2"/><circle cx="55" cy="14" r="6"/></g>
     <g class="hub-node" data-i="4"><line x1="49" y1="40" x2="64" y2="40"/><circle cx="70" cy="40" r="6"/></g>
     <g class="hub-node" data-i="5"><line x1="44.5" y1="47.8" x2="52" y2="60.8"/><circle cx="55" cy="66" r="6"/></g>
     <circle class="hub-ico-core" cx="40" cy="40" r="8.5"/>
    </svg>
    <span class="hub-name">Centralized</span>
    <span class="hub-cap"><i aria-hidden="true"></i><span>6 modules &middot; live sync</span></span>
   </div>
  </div>

  <div class="hub-col hub-col-r">
   <article class="hub-mod" data-i="3">
    <div class="hub-mod-top">
     <span class="hub-mod-ico" aria-hidden="true"><svg viewBox="0 0 36 36" fill="none"><circle cx="13" cy="11" r="6" fill="white"/><circle cx="25" cy="13" r="4.5" fill="rgba(255,255,255,.6)"/><path d="M1 32 C1 23 7 20 13 20 C19 20 25 23 25 32 Z" fill="rgba(255,255,255,.8)"/><path d="M25 26 C25 23 28 21 31 21 C34 21 36 23 36 26 L36 32 L25 32 Z" fill="rgba(255,255,255,.4)"/></svg></span>
     <div class="hub-mod-tt"><div class="hub-mod-name"><h3>HR &amp; Payroll</h3><span class="hub-mod-when">This Month</span></div><p>Manage headcount, attendance and payroll</p></div>
    </div>
    <dl class="hub-kpis">
     <div><dt>Total Staff</dt><dd>248<em class="up">8</em></dd></div>
     <div><dt>Attendance</dt><dd>97.4%<em class="up">1.2%</em></dd></div>
     <div><dt>Payroll Run</dt><dd>&#8377;34L<em class="up">5%</em></dd></div>
    </dl>
    <span class="hub-port" aria-hidden="true"></span>
   </article>
   <article class="hub-mod" data-i="4">
    <div class="hub-mod-top">
     <span class="hub-mod-ico" aria-hidden="true"><svg viewBox="0 0 36 36" fill="none"><path d="M4 13 L4 23 L10 23 L10 13 Z" fill="rgba(255,255,255,.6)"/><path d="M10 13 L28 5 L28 31 L10 23 Z" fill="white"/><path d="M30 14 Q36 18 30 22" fill="none" stroke="rgba(255,255,255,.75)" stroke-width="2.5" stroke-linecap="round"/></svg></span>
     <div class="hub-mod-tt"><div class="hub-mod-name"><h3>Marketing</h3><span class="hub-mod-when">Last 30 Days</span></div><p>Measure campaigns, reach and engagement</p></div>
    </div>
    <dl class="hub-kpis">
     <div><dt>Campaigns</dt><dd>14<em class="up">3</em></dd></div>
     <div><dt>Engagement</dt><dd>+44%<em class="up">12%</em></dd></div>
     <div><dt>Total Reach</dt><dd>8.2K<em class="up">18%</em></dd></div>
    </dl>
    <span class="hub-port" aria-hidden="true"></span>
   </article>
   <article class="hub-mod" data-i="5">
    <div class="hub-mod-top">
     <span class="hub-mod-ico" aria-hidden="true"><svg viewBox="0 0 36 36" fill="none"><rect x="3" y="3" width="13" height="13" rx="2" fill="white" opacity=".9"/><rect x="20" y="3" width="13" height="13" rx="2" fill="rgba(255,255,255,.55)"/><rect x="3" y="20" width="13" height="13" rx="2" fill="rgba(255,255,255,.55)"/><rect x="20" y="20" width="13" height="13" rx="2" fill="rgba(255,255,255,.75)"/><line x1="16" y1="9.5" x2="20" y2="9.5" stroke="rgba(255,255,255,.7)" stroke-width="1.5"/><line x1="9.5" y1="16" x2="9.5" y2="20" stroke="rgba(255,255,255,.7)" stroke-width="1.5"/><line x1="26.5" y1="16" x2="26.5" y2="20" stroke="rgba(255,255,255,.7)" stroke-width="1.5"/><line x1="16" y1="26.5" x2="20" y2="26.5" stroke="rgba(255,255,255,.7)" stroke-width="1.5"/></svg></span>
     <div class="hub-mod-tt"><div class="hub-mod-name"><h3>Management Overview</h3><span class="hub-mod-when">This Year</span></div><p>Company-wide health across every module</p></div>
    </div>
    <dl class="hub-kpis">
     <div><dt>KPI Score</dt><dd>92<em class="up">6</em></dd></div>
     <div><dt>Efficiency</dt><dd>+18%<em class="up">4%</em></dd></div>
     <div><dt>Modules Live</dt><dd>7/7<em class="up">2</em></dd></div>
    </dl>
    <span class="hub-port" aria-hidden="true"></span>
   </article>
  </div>
 </div>

 <script>
 // Platform Dashboards hub (styles: assets/home9.css, section 11).
 // Draws the six wires from each card's port to the hub, from where the two
 // actually sit (redrawn whenever the stage changes size), each a dim dashed
 // line with a dot travelling in along it, plus a bright flowing copy shown
 // on the active module's wire. The active module (card, wire, hub spoke and
 // the hub's caption) follows the pointer; when nobody is pointing it walks
 // round the six clockwise every 2.4s, only while the section is on screen.
 // From 1101px only: narrower, the cards stack under the hub and there are
 // no wires, but the highlight still walks. Reduced motion: no dots, no
 // flow, no walking, no entrance; hover still highlights.
 (function(){
  const stage = document.getElementById('hubStage');
  if(!stage) return;
  const svg = stage.querySelector('.hub-wires');
  const box = stage.querySelector('.hub-box');
  const mods = Array.from(stage.querySelectorAll('.hub-mod'));
  const nodes = Array.from(stage.querySelectorAll('.hub-node'));
  const cap = stage.querySelector('.hub-cap span');
  if(!svg || !box || mods.length !== 6) return;

  const NS = 'http://www.w3.org/2000/svg';
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const wide = window.matchMedia('(min-width:1101px)');
  const names = mods.map(m => (m.querySelector('h3') || {}).textContent || '');
  const idle = cap ? cap.textContent : '';
  const order = [0, 3, 4, 5, 2, 1];            // clockwise round the hub
  const el = (tag, attrs) => {
   const n = document.createElementNS(NS, tag);
   for(const k in attrs) n.setAttribute(k, attrs[k]);
   return n;
  };

  // the wires: base (dashed), flow (bright, active only), travelling dot, hub end
  const stubs = [el('path', { class: 'hub-wire hub-stub' }), el('path', { class: 'hub-wire hub-stub' })];
  stubs.forEach(s => svg.appendChild(s));
  const wires = mods.map((m, i) => {
   const base = el('path', { class: 'hub-wire', id: 'hubWire' + i });
   const flow = el('path', { class: 'hub-flow' });
   const end = el('circle', { class: 'hub-end', r: 4 });
   svg.append(base, flow);
   if(!reduce){
    const dot = el('circle', { class: 'hub-dot', r: 3 });
    const move = el('animateMotion', { dur: '2.8s', repeatCount: 'indefinite', begin: (-i * 0.47).toFixed(2) + 's' });
    const mp = el('mpath', {});
    mp.setAttribute('href', '#hubWire' + i);
    mp.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', '#hubWire' + i);
    move.appendChild(mp);
    dot.appendChild(move);
    svg.appendChild(dot);
   }
   svg.appendChild(end);
   return { base, flow, end };
  });

  // a right-angled route with rounded corners: across to xm, up or down, across
  function route(x1, y1, x2, y2, xm, r){
   const dy = y2 - y1;
   if(Math.abs(dy) < 2) return `M${x1} ${y1}H${x2}`;
   const s = Math.sign(dy), a = Math.sign(xm - x1), b = Math.sign(x2 - xm);
   r = Math.min(r, Math.abs(dy) / 2, Math.abs(xm - x1), Math.abs(x2 - xm));
   return `M${x1} ${y1}H${xm - a * r}Q${xm} ${y1} ${xm} ${y1 + s * r}V${y2 - s * r}Q${xm} ${y2} ${xm + b * r} ${y2}H${x2}`;
  }
  // positions from offsets, which ignore transforms, so the wires land where
  // the cards and hub rest, not where the entrance or a highlight moves them
  function pos(n){
   let x = 0, y = 0;
   while(n && n !== stage){ x += n.offsetLeft; y += n.offsetTop; n = n.offsetParent; }
   return { x, y };
  }
  function layout(){
   if(!wide.matches){ svg.style.display = 'none'; return; }
   svg.style.display = '';
   const W = stage.clientWidth, H = stage.clientHeight;
   svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
   const bp = pos(box), bw = box.offsetWidth, bh = box.offsetHeight;
   const hy = bp.y + bh / 2;
   const offs = [-0.27, 0, 0.27];              // where each wire meets the hub's side
   mods.forEach((m, i) => {
    const left = i < 3;
    const port = m.querySelector('.hub-port');
    const pp = pos(port);
    const x1 = pp.x + port.offsetWidth / 2, y1 = pp.y + port.offsetHeight / 2;
    const x2 = left ? bp.x : bp.x + bw, y2 = hy + offs[i % 3] * bh;
    const xm = x1 + (x2 - x1) * 0.55;
    const d = route(x1, y1, x2, y2, xm, 18);
    wires[i].base.setAttribute('d', d);
    wires[i].flow.setAttribute('d', d);
    wires[i].end.setAttribute('cx', x2);
    wires[i].end.setAttribute('cy', y2);
   });
   // two faint stubs out of the hub's top and bottom, as on a circuit board
   const hx = bp.x + bw / 2;
   stubs[0].setAttribute('d', `M${hx} ${bp.y}V${Math.max(0, bp.y - 90)}`);
   stubs[1].setAttribute('d', `M${hx} ${bp.y + bh}V${Math.min(H, bp.y + bh + 90)}`);
  }

  let active = -1;
  function set(i){
   if(i === active) return;
   active = i;
   mods.forEach((m, k) => m.classList.toggle('is-active', k === i));
   wires.forEach((w, k) => { w.flow.classList.toggle('is-active', k === i); w.end.classList.toggle('is-active', k === i); });
   nodes.forEach(n => n.classList.toggle('is-active', +n.getAttribute('data-i') === i));
   if(cap) cap.textContent = i < 0 ? idle : names[i];   // just the name: the hub is small
  }

  // the walk: paused while pointing, off screen, or under reduced motion
  let step = 0, timer = 0, pointing = false, onScreen = false;
  function tick(){ step = (step + 1) % order.length; set(order[step]); }
  function run(){
   clearInterval(timer); timer = 0;
   if(!reduce && onScreen && !pointing) timer = setInterval(tick, 2400);
  }
  mods.forEach((m, i) => {
   m.addEventListener('mouseenter', () => { pointing = true; set(i); step = order.indexOf(i); run(); });
   m.addEventListener('mouseleave', () => { pointing = false; run(); });
   m.addEventListener('click', () => { set(i); step = order.indexOf(i); run(); });
  });

  // the entrance: cards slide in from their sides, the hub scales up, wires fade in
  const anim = !reduce && 'IntersectionObserver' in window;
  if(anim) stage.classList.add('hub-anim');
  if('IntersectionObserver' in window){
   const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
     onScreen = e.isIntersecting;
     if(onScreen && anim && !stage.classList.contains('is-in')){
      stage.classList.add('is-in');
      setTimeout(() => stage.classList.remove('hub-anim'), 1800);
     }
     run();
    });
   }, { threshold: 0.25 });
   io.observe(stage);
  } else { onScreen = true; run(); }

  set(order[0]);
  layout();
  if('ResizeObserver' in window) new ResizeObserver(layout).observe(stage);
  else window.addEventListener('resize', layout);
  window.addEventListener('load', layout);
  if(document.fonts && document.fonts.ready) document.fonts.ready.then(layout);
  if(wide.addEventListener) wide.addEventListener('change', layout);
 })();
 </script>
</section>

<!-- TESTIMONIALS -->
<!-- PLACEHOLDER CONTENT. These three quotes and their attributions are dummies
     standing in until real client testimonials are supplied. Replace the quote,
     name and role in each card (and remove this note) before the page goes
     live. Edit this file, tools/home9-testimonials.html, then run
     `perl tools/sync-home9.pl`; templates/home9-body.php is generated.
     Styles: #testimonials in assets/home9.css. -->
<section id="testimonials">
 <div class="eyebrow rv"><span class="eyebrow-text">Testimonials</span></div>
 <h2 class="sec-h rv">What Our <span class="g">Clients Say</span></h2>
 <p class="sec-sub rv">Businesses that moved their operations onto Drawlead, in their own words.</p>

 <div class="tm-grid">
  <figure class="tm-card rv">
   <div class="tm-stars" role="img" aria-label="Rated 5 out of 5"><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg></div>
   <blockquote class="tm-quote"><p>Production, inventory and billing used to live in separate spreadsheets. Now the whole floor runs from one dashboard, and month-end is no longer a scramble to reconcile numbers.</p></blockquote>
   <figcaption class="tm-who">
    <span class="tm-avatar" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8.2" r="4.2"/><path d="M3.6 20.6c0-4.4 3.8-7.2 8.4-7.2s8.4 2.8 8.4 7.2z"/></svg></span>
    <span class="tm-id"><b class="tm-name">Client Name</b><span class="tm-role">Managing Director, Manufacturing</span></span>
   </figcaption>
  </figure>

  <figure class="tm-card rv">
   <div class="tm-stars" role="img" aria-label="Rated 5 out of 5"><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg></div>
   <blockquote class="tm-quote"><p>Our store, orders and stock finally talk to each other. The team stopped copying data between tools and started spending that time on customers.</p></blockquote>
   <figcaption class="tm-who">
    <span class="tm-avatar" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8.2" r="4.2"/><path d="M3.6 20.6c0-4.4 3.8-7.2 8.4-7.2s8.4 2.8 8.4 7.2z"/></svg></span>
    <span class="tm-id"><b class="tm-name">Client Name</b><span class="tm-role">Founder, Retail &amp; E-Commerce</span></span>
   </figcaption>
  </figure>

  <figure class="tm-card rv">
   <div class="tm-stars" role="img" aria-label="Rated 5 out of 5"><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8l-5.2 2.8 1-5.8L1.5 7.7l5.9-.9z"/></svg></div>
   <blockquote class="tm-quote"><p>They took the time to understand how our clinics actually work before building anything. The system fits our process instead of forcing us into someone else's.</p></blockquote>
   <figcaption class="tm-who">
    <span class="tm-avatar" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8.2" r="4.2"/><path d="M3.6 20.6c0-4.4 3.8-7.2 8.4-7.2s8.4 2.8 8.4 7.2z"/></svg></span>
    <span class="tm-id"><b class="tm-name">Client Name</b><span class="tm-role">Operations Head, Healthcare</span></span>
   </figcaption>
  </figure>
 </div>
</section>

<!-- INDUSTRIES -->
<?php
// Section 8: industry sticky stack.
// Order is pinned explicitly (the shared data source lists Manufacturing before
// Marketing Agencies; the design calls for the reverse) and each industry carries its
// own gradient pair + deep base colour. Copy itself is untouched, it still comes
// straight from industries_ordered().
$indStackOrder = ['construction','healthcare','agencies','manufacturing','retail','logistics'];
$indByKey = [];
foreach (industries_ordered() as $entry) { $indByKey[$entry['key']] = $entry['industry']; }
?>
<section id="industries">
 <!-- Solid wide cards in a pinned horizontal run, like the 7 Functions row: while the
      row is pinned, page scroll slides it left one card after another (JS below).
      Phones and reduced motion swipe instead. -->
 <!-- Industries as an overlapping stack of tall panels (Home 8 only; Home 7 keeps
      the sliding row). Collapsed panels show only a vertical spine; the active one
      opens to hold the same content the row cards carried. Page scroll drives which
      panel is open — see the driver below. Phones and reduced motion get a plain
      stacked list instead, which is what this markup is without the JS. -->
 <!-- The heading sits outside the pin and scrolls away as the stack locks, so
      what stays on screen for the whole run is the stack with its buttons under
      it. Heading + stack + buttons together are taller than a laptop viewport;
      pinned all at once, the cards were cut off at the bottom and the buttons
      never showed until the stack let go. -->
 <div class="is-head">
  <div class="eyebrow rv"><span class="eyebrow-text">Industries</span></div>
  <h2 class="sec-h rv">Built for <span>Your Industry</span></h2>
  <p class="sec-sub rv">Every industry has unique challenges. Drawlead adapts to your specific workflows, pain points, and compliance requirements, out of the box.</p>
 </div>

 <div class="is-outer" id="isOuter">
  <div class="is-sticky" id="isSticky">
   <div class="is-stack" id="isStack">
<?php $isN = 0; foreach ($indStackOrder as $key):
  if (!isset($indByKey[$key])) { continue; }
  $ind = $indByKey[$key];
  $isN++;
?>
    <article class="is-panel<?= $isN === 1 ? ' is-on' : '' ?>" style="--i:<?= $isN - 1 ?>">
     <!-- the spine is what a collapsed panel shows: number and name, reading up -->
     <span class="is-spine" aria-hidden="true">
      <i class="is-num"><?= str_pad((string) $isN, 2, '0', STR_PAD_LEFT) ?></i>
      <b class="is-name"><?= h($ind['name']) ?></b>
     </span>

     <div class="is-body">
      <div class="is-body-in">
       <div class="is-top">
        <span class="is-ico"><?= $ind['icon'] ?></span>
        <div>
         <h3 class="is-title"><?= h($ind['name']) ?></h3>
         <div class="is-tag"><?= h($ind['tag']) ?></div>
        </div>
       </div>

       <div class="is-cols">
        <div class="is-col">
         <div class="is-col-h"><span class="is-rule is-rule-p"></span>Common Problems</div>
<?php foreach ($ind['problems'] as $problem): ?>
         <div class="is-line"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg><?= h($problem) ?></div>
<?php endforeach; ?>
        </div>
        <div class="is-col">
         <div class="is-col-h"><span class="is-rule is-rule-s"></span>Drawlead Solution</div>
<?php foreach ($ind['solutions'] as $solution): ?>
         <div class="is-line"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg><?= h($solution) ?></div>
<?php endforeach; ?>
        </div>
       </div>

       <a href="/industry-<?= h($key) ?>" class="is-cta">Explore <?= h($ind['name']) ?> OS</a>
      </div>
     </div>
    </article>
<?php endforeach; ?>
   </div><!-- /is-stack -->

   <!-- the buttons ride in the pin, directly under the stack -->
   <div class="sec-cta rv" style="margin-top:3rem">
    <button type="button" data-book class="btn btn-black">Find Your Industry Solution</button>
    <a href="#cases" class="btn btn-outline2">See Case Studies</a>
   </div>
  </div>
 </div><!-- /is-outer -->
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

  // How far each panel tucks behind the next. Raising it does not change how
  // much of a tab you can see — the visible strip is (IW - EXP) / (n - 1), and
  // COL is derived from the same EXP, so the two move together. What it does
  // change is how much of each panel is hidden: at 44 the 26px corner radius
  // and the border on the covered side disappear behind the neighbour, so the
  // tabs read as one deck rather than as separate cards standing in a row.
  const OVERLAP = 44;
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
   // The pinned block is the stack plus its buttons (the heading scrolls away
   // above it), so it fits under the nav and is centred in that space: the
   // stack and the buttons stay fully on screen for the whole run. When a
   // window is too short to hold both, slack is 0 and it pins flush to the nav.
   const slack = Math.max(0, window.innerHeight - navH - sticky.offsetHeight);
   stickyTop  = navH + Math.round(slack / 2);
   outer.style.setProperty('--is-top', stickyTop + 'px');

   // Scroll spent per handover. At 0.62 of a viewport each, six panels wanted
   // ~2500px and the first card sat there for half a screen before anything
   // moved; 0.40 keeps every transition legible while cutting that by a third,
   // so a normal scroll gesture advances the stack instead of idling in it.
   runway = (n - 1) * Math.max(260, Math.round(window.innerHeight * 0.40));
   outer.style.height = (sticky.offsetHeight + runway) + 'px';
  }

  function paint(){
   if(!live) return;
   const W = stack.clientWidth;
   if(W <= 0) return;

   // One gutter, on the left only: the stack bleeds off the right edge, so the
   // run fills from its left inset all the way to the boundary and the last tab
   // is cropped by it rather than stopping short.
   const GUT = Math.min(60, Math.max(12, Math.round(W * 0.047)));
   const IW  = W - GUT;
   // The open panel's size is the fixed quantity — the tabs absorb whatever is
   // left over. Deriving COL from it this way means the run spans exactly IW at
   // every width, so there is never slack to leave a gap on the right.
   const WANT = 880;
   // The ceiling has to clear (IW - WANT) / (n - 1) + OVERLAP, or it binds first
   // and the open panel grows past WANT to take up the slack — which it did at
   // 1920 once OVERLAP went to 44.
   const COL  = Math.min(200, Math.max(58,
                 Math.round((IW - WANT + (n - 1) * OVERLAP) / (n - 1))));
   const EXP  = IW + (n - 1) * (OVERLAP - COL);
   if(EXP <= COL){ teardown(); return; }      // too cramped to open a panel
   stack.style.setProperty('--gut', GUT + 'px');
   // CSS needs the same number for the negative margin; publishing it from here
   // is what stops the two definitions drifting apart.
   stack.style.setProperty('--ov', OVERLAP + 'px');
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
</section>

<!-- WHY Drawlead -->
<section id="why">
 <h2 class="why-h rv">Why Drawlead</h2>

 <p class="why-lead rv">We're not just software. We're a long-term partner in your digital transformation and growth.</p>

 <div class="why-feats">
  <div class="why-feat rv d1">
   <div class="why-fico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="9" width="7" height="7" rx="2"/><rect x="15" y="9" width="7" height="7" rx="2"/><path d="M9 12.5h6"/><circle cx="5.5" cy="4" r="2"/><circle cx="18.5" cy="20" r="2"/></svg></div>
   <div class="why-fname">Unified Ecosystem</div>
   <div class="why-fdesc">All 7 core functions in one platform. No more tool-switching or disconnected data silos.</div>
  </div>
  <div class="why-feat rv d2">
   <div class="why-fico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L5 13h6l-2 9 8-11h-6l2-9z"/></svg></div>
   <div class="why-fname">AI-Driven Efficiency</div>
   <div class="why-fdesc">Automate repetitive tasks and surface intelligent insights without any technical expertise.</div>
  </div>
  <div class="why-feat rv d3">
   <div class="why-fico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v6M12 16v6M2 12h6M16 12h6"/><path d="M12 2l3 3M12 2L9 5M12 22l3-3M12 22l-3-3M2 12l3-3M2 12l3 3M22 12l-3-3M22 12l-3 3"/></svg></div>
   <div class="why-fname">Scalable Architecture</div>
   <div class="why-fdesc">Built for startups, SMEs, and enterprises. Scales exactly as your business grows.</div>
  </div>
  <div class="why-feat rv d4">
   <div class="why-fico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 3.5v6c0 5-3.4 8.6-8 10.5-4.6-1.9-8-5.5-8-10.5v-6L12 2z"/><polyline points="8.5,12 11,14.5 15.5,9.5"/></svg></div>
   <div class="why-fname">Secure and Reliable</div>
   <div class="why-fdesc">Enterprise-grade security, 99.9% uptime SLA, and end-to-end encryption on all data.</div>
  </div>
  <div class="why-feat rv d1">
   <div class="why-fico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 11q4-4 7 0l3 3 3-3q3-4 7 0"/><path d="M2 15q4-4 7 0l3 3 3-3q3-4 7 0"/></svg></div>
   <div class="why-fname">Long-Term Partnership</div>
   <div class="why-fdesc">Continuous support, updates, and future-proofing. We grow and evolve with your needs.</div>
  </div>
 </div>

 <div class="sec-cta rv">
 <button type="button" data-book class="btn btn-black" style="background:#32b46f;color:#04110a">Partner With Us</button>
 <a href="#cases" class="btn btn-outline2" style="color:rgba(255,255,255,.7);border-color:rgba(255,255,255,.2)">See Case Studies</a>
 </div>
</section>

<?php
// CTA INTRO: continuous letter train
// Two copies live here, and only one is ever shown:
//
//  .ci-static-head  the plain 3-line heading. This is the DEFAULT: it is what renders
//                   with no JS, a JS error, missing GSAP, or reduced motion. Nothing
//                   hides it unless the animation has successfully started.
//  .ci-train        the animated copy. For the animation the heading is treated as ONE
//                   continuous character sequence on ONE baseline, the line breaks
//                   belong to the static layout, not to the train. Inside it,
//                   .ci-measure supplies real kerning offsets and .ci-stage holds the
//                   glyphs the JS actually flies.
$ciHeadLines = [
 ['text' => 'Ready to Transform', 'green' => []],
 ['text' => 'Your Business ERP',  'green' => ['ERP']],
 ['text' => 'with AI?',           'green' => ['AI?']],
];
// the same words, joined into the single sequence the train animates
$ciTrainText  = 'Ready to Transform Your Business ERP with AI?';
$ciTrainGreen = ['ERP', 'AI?'];
?>
<section id="cta-intro">

 <!-- default, always-visible heading -->
 <div class="ci-static-head">
  <?php foreach ($ciHeadLines as $line): ?>
  <p class="ci-line"><?php
   $words = explode(' ', $line['text']);
   foreach ($words as $wi => $word) {
       $isGreen = in_array($word, $line['green'], true);
       echo $isGreen ? '<span class="ci-g">' . h($word) . '</span>' : h($word);
       if ($wi < count($words) - 1) { echo ' '; }
   }
  ?></p>
  <?php endforeach; ?>
 </div>

 <!-- animated single-baseline train (shown only once JS confirms it can run) -->
 <div class="ci-train" aria-hidden="true">
  <div class="ci-track">
   <?php
   $cells = [];
   foreach (explode(' ', $ciTrainText) as $wi => $word) {
       $isGreen = in_array($word, $ciTrainGreen, true);
       foreach (preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
           $cells[] = ['ch' => $ch, 'green' => $isGreen];
       }
       $cells[] = ['ch' => ' ', 'green' => false];   // word gap, kept as a real glyph slot
   }
   array_pop($cells);
   ?>
   <div class="ci-measure"><?php
    foreach ($cells as $c) {
        ?><span<?= $c['green'] ? ' class="ci-g"' : '' ?>><?= $c['ch'] === ' ' ? '&nbsp;' : h($c['ch']) ?></span><?php
    }
   ?></div>
   <div class="ci-stage"><?php
    foreach ($cells as $c) {
        ?><span class="ci-ch<?= $c['green'] ? ' ci-g' : '' ?>"><?= $c['ch'] === ' ' ? '&nbsp;' : h($c['ch']) ?></span><?php
    }
   ?></div>
  </div>
 </div>

</section>
<!-- CTA -->
<section id="cta" class="cta-framed">
 <!-- dark rounded container, inset from the page so white space frames it on all sides -->
 <div class="cta-card">
  <div class="cta-grid-bg"></div>
  <div class="cta-glow"></div>

  <div class="cta-eyebrow rv">Start Your ERP Journey</div>
  <h2 class="cta-h rv">Build your<br><span class="fade">business</span> <span class="gr">ERP</span><br><span class="gr2">OS</span> with <span class="gr3">AI</span></h2>
  <p class="cta-p rv">Digitize, automate, and scale with Drawlead. Start with a free consultation, no commitment needed.</p>
  <div class="cta-btns rv">
  <button type="button" data-book class="cta-btn-w">Schedule Free Consultation</button>
  <button type="button" data-book class="cta-btn-g">Book a Product Demo</button>
  </div>
  <div class="cta-note rv">செயலை மாற்றும் · Intelligent Operating System · Secure · Scalable · Future-Ready</div>
 </div>
</section>

<script>
// Home 9: keep GSAP ScrollTrigger's pin (the closing CTA's letter train,
// #cta-intro) where it belongs.
//
// 1. Refresh without smooth scrolling. To measure, ScrollTrigger.refresh()
//    moves the page's scroll position and puts it back; html has
//    scroll-behavior:smooth (home7.css), which turns those jumps into
//    animations, so a refresh run while scrolled down the page measured
//    mid-animation and set the pin's start thousands of pixels early (seen
//    at 6723 and 2523 against a real 16300), laying the train over How We
//    Work and Platform Dashboards. Smooth scrolling is switched off for the
//    length of each refresh (refreshInit .. refresh) and back on after, so
//    in-page links still glide. The
//    switch is read back at once (getComputedStyle), or Chrome keeps the old
//    smooth value for the jumps that follow. Registered as soon as the page has parsed,
//    before the train's ScrollTrigger exists, so every refresh is covered.
// 2. Refresh when the page's height changes. This page's own scripts change
//    section heights after ScrollTrigger has measured (Solutions switching
//    its pinned mode at 640px tall, Case Studies' and Industries' runways
//    following the window, the hero's headline rotating between one height
//    and another), which would leave the pin's positions stale. So the
//    page's height is watched, and when it differs from the height at the
//    last refresh, ScrollTrigger refreshes (debounced). A refresh that
//    changes nothing leaves the height as it was, so this cannot loop.
// tools/sync-home9.pl places this after the last section.
(function(){
 const html = document.documentElement;
 let lastH = -1, timer = 0, started = false;
 function start(){
  const ST = window.ScrollTrigger;
  if(started || !ST) return;
  started = true;
  lastH = html.scrollHeight;
  // Reading the computed value makes the browser apply it at once: without
  // that, Chrome still scrolled smoothly for the jumps that follow.
  ST.addEventListener('refreshInit', function(){ html.style.scrollBehavior = 'auto'; void getComputedStyle(html).scrollBehavior; });
  ST.addEventListener('refresh', function(){ html.style.scrollBehavior = ''; lastH = html.scrollHeight; });
  if(!('ResizeObserver' in window)) return;
  new ResizeObserver(function(){
   if(html.scrollHeight === lastH) return;
   clearTimeout(timer);
   timer = setTimeout(function(){ if(html.scrollHeight !== lastH) ST.refresh(); }, 250);
  }).observe(document.body);
 }
 // GSAP loads further down the page as ordinary scripts, so it is there by
 // DOMContentLoaded; load is the fallback.
 if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
 else start();
 window.addEventListener('load', start);
})();
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>

<!-- Matter.js is vendored locally (assets/), matching the site's no-external-scripts
     convention. Loaded here rather than in the shared layout so only this page pays for it. -->
<script src="<?= asset_url('/assets/matter.min.js') ?>"></script>

<!-- GSAP + ScrollTrigger, vendored locally to match the same convention. Loaded once,
     here, and nowhere else; only this page pays for them.
     No smooth-scroll library: the page uses the browser's native scrolling. Lenis was
     tried here and removed, it takes over document scroll and adds another ticker
     layer on top of GSAP's, which cost more than it bought on this page. -->
<script src="<?= asset_url('/assets/gsap.min.js') ?>"></script>
<script src="<?= asset_url('/assets/ScrollTrigger.min.js') ?>"></script>

<script>
// Industry dashboard preview. One data object per industry, one renderer, five tabs.
// Sidebar, header, KPI cards, chart and AI card come from the same template for every
// industry, so no dashboard markup is duplicated; only the data below differs.
const NAV_ICONS = [
 '<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
 '<path d="M20.6 13.4 12 22l-9-9V4a1 1 0 0 1 1-1h9l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.2"/>',
 '<path d="M8 7V6a4 4 0 0 1 8 0v1"/><rect x="3" y="7" width="18" height="14" rx="2"/>',
 '<circle cx="9" cy="8" r="3"/><path d="M3 20c0-3.2 2.7-5.4 6-5.4s6 2.2 6 5.4"/><circle cx="17.6" cy="9" r="2.2"/><path d="M16.2 20c.2-2.3 1.8-4 4-4.3"/>',
 '<path d="M3 7.5 12 3l9 4.5"/><path d="M3 7.5v9L12 21l9-4.5v-9"/><path d="M12 12v9"/><path d="M3 7.5 12 12l9-4.5"/>',
 '<path d="M4 20V10M9.3 20V5M14.7 20v-7M20 20V8"/>',
 '<path d="M3 10v4a1 1 0 0 0 1 1h2l6 4V5L6 9H4a1 1 0 0 0-1 1z"/><path d="M17 9a4 4 0 0 1 0 6"/>',
 '<circle cx="12" cy="12" r="3"/><path d="M12 2.6v2.6M12 18.8v2.6M21.4 12h-2.6M5.2 12H2.6M18.6 5.4l-1.8 1.8M7.2 16.8l-1.8 1.8M18.6 18.6l-1.8-1.8M7.2 7.2 5.4 5.4"/>'
];
const KPI_ICONS = [
 '<path d="M9 4h6l1.6 3H7.4z"/><path d="M6.5 7h11a4.5 4.5 0 0 1 1 3.4l-.7 6A3 3 0 0 1 14.8 20H9.2a3 3 0 0 1-3-2.6l-.7-6A4.5 4.5 0 0 1 6.5 7z"/>',
 '<circle cx="7.5" cy="7.5" r="2.4"/><circle cx="16.5" cy="16.5" r="2.4"/><path d="M19 5 5 19"/>',
 '<path d="M3 7.5 12 3l9 4.5"/><path d="M3 7.5v9L12 21l9-4.5v-9"/><path d="M12 12v9"/><path d="M3 7.5 12 12l9-4.5"/>',
 '<circle cx="9.5" cy="19.5" r="1.3"/><circle cx="17" cy="19.5" r="1.3"/><path d="M3 4h2.2l2.4 10.4a1.6 1.6 0 0 0 1.6 1.2h7.7a1.6 1.6 0 0 0 1.6-1.2L21 8H6"/>'
];
const TONES = ['blue', 'green', 'peach', 'yellow'];

const industries = [
 {
  name: 'Ecommerce', suite: 'Ecommerce Business Suite', role: 'Store Manager',
  search: 'Search products, orders, customers...',
  nav: ['Dashboard', 'Products', 'Orders', 'Customers', 'Inventory', 'Reports', 'Marketing', 'Settings'],
  title: 'Ecommerce Orders and Revenue Overview',
  kpis: [
   { l: 'Orders Today', v: '842', d: '14%', n: 'vs last month' },
   { l: 'GMV', v: '&#8377;18.4L', d: '11%', n: 'vs last month' },
   { l: 'Conversion', v: '3.8%', d: '0.6%', n: 'vs last month' },
   { l: 'Avg Order', v: '&#8377;2,140', d: '8%', n: 'vs last month' }
  ],
  chart: { label: 'Daily Orders', range: 'Last 6 Days', max: 1000, ticks: [1000, 750, 500, 250, 0],
           vals: [620, 760, 540, 880, 800, 842], labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] },
  ai: 'Cart abandonment is at 68% on mobile checkout. Recovering 240 carts could add &#8377;5.1L this month.'
 },
 {
  name: 'Hospital', suite: 'Hospital Business Suite', role: 'Operations Manager',
  search: 'Search patients, appointments, wards...',
  nav: ['Dashboard', 'Patients', 'Appointments', 'Doctors', 'Wards', 'Reports', 'Billing', 'Settings'],
  title: 'Hospital Operations and Patient Overview',
  kpis: [
   { l: 'Patients Today', v: '486', d: '9%', n: 'vs last month' },
   { l: 'Appointments', v: '128', d: '12%', n: 'vs last month' },
   { l: 'Bed Occupancy', v: '82%', d: '4%', n: 'vs last month' },
   { l: 'Avg Wait Time', v: '18 min', d: '12%', n: 'vs last month', down: true }
  ],
  chart: { label: 'Patient Visits', range: 'Last 6 Days', max: 500, ticks: [500, 375, 250, 125, 0],
           vals: [380, 430, 360, 470, 440, 486], labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] },
  ai: 'OPD demand is expected to increase 18% this week. Consider adding two additional evening consultation slots.'
 },
 {
  name: 'Jewellery', suite: 'Jewellery Business Suite', role: 'Store Manager',
  search: 'Search products, orders, customers...',
  nav: ['Dashboard', 'Products', 'Orders', 'Customers', 'Inventory', 'Reports', 'Marketing', 'Settings'],
  title: 'Jewellery Sales and Stock Overview',
  kpis: [
   { l: 'Monthly Sales', v: '&#8377;3.2Cr', d: '12%', n: 'vs last month' },
   { l: 'YoY Growth', v: '22%', d: '22%', n: 'vs last year' },
   { l: 'SKUs Active', v: '1,840', d: '8%', n: 'vs last month' },
   { l: 'Order Fulfill', v: '96%', d: '4%', n: 'vs last month' }
  ],
  chart: { label: 'Weekly Sales (&#8377; Lakhs)', range: 'Last 6 Weeks', max: 40, ticks: [40, 30, 20, 10, 0],
           vals: [15, 20, 15, 27, 24, 36], labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'] },
  ai: 'Navaratri season predicts a 40% spike in necklace demand over the next 14 days.'
 },
 {
  name: 'Manufacturing', suite: 'Manufacturing Business Suite', role: 'Plant Manager',
  search: 'Search orders, machines, materials...',
  nav: ['Dashboard', 'Production', 'Machines', 'Inventory', 'Quality', 'Reports', 'Maintenance', 'Settings'],
  title: 'Manufacturing Production and Operations Overview',
  kpis: [
   { l: 'Production Today', v: '12,480', d: '9%', n: 'vs last month' },
   { l: 'OEE', v: '87%', d: '5%', n: 'vs last month' },
   { l: 'Active Machines', v: '42', d: '3%', n: 'vs last month' },
   { l: 'On-Time Output', v: '94%', d: '6%', n: 'vs last month' }
  ],
  chart: { label: 'Weekly Production', range: 'Last 6 Weeks', max: 14000, ticks: [14000, 10500, 7000, 3500, 0],
           vals: [9800, 10600, 9200, 11800, 11200, 12480], labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'] },
  ai: 'Machine utilization is expected to reach 91% next week. Preventive maintenance on Line 3 could reduce downtime by 8%.'
 },
 {
  name: 'Construction', suite: 'Construction Business Suite', role: 'Project Manager',
  search: 'Search projects, sites, vendors...',
  nav: ['Dashboard', 'Projects', 'Sites', 'Workforce', 'Materials', 'Reports', 'Vendors', 'Settings'],
  title: 'Construction Projects and Site Overview',
  kpis: [
   { l: 'Active Projects', v: '24', d: '4%', n: 'vs last month' },
   { l: 'Project Progress', v: '68%', d: '7%', n: 'vs last month' },
   { l: 'Site Workforce', v: '486', d: '12%', n: 'vs last month' },
   { l: 'On-Time Projects', v: '91%', d: '5%', n: 'vs last month' }
  ],
  chart: { label: 'Project Progress', range: 'Last 6 Stages', max: 80, ticks: [80, 60, 40, 20, 0],
           vals: [20, 34, 46, 58, 62, 68], labels: ['Stage 1', 'Stage 2', 'Stage 3', 'Stage 4', 'Stage 5', 'Stage 6'] },
  ai: 'Material demand is expected to increase 16% over the next two weeks. Early procurement could prevent delays across 3 active sites.'
 }
];
let currentIdx = 0;
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function icon(paths, cls) {
 return '<svg class="' + cls + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' + paths + '</svg>';
}

// Sidebar: brand, then the industry's own navigation. Item 0 is always the active one.
function buildSidebar(d) {
 return '<div class="ap-side">' +
  '<div class="ap-brand">' +
   '<span class="ap-brand-mark">' + icon('<path d="M12 2.6 21 9l-9 12.4L3 9z"/>', 'ap-brand-svg') + '</span>' +
   '<span><span class="ap-brand-name">DRAWLEAD</span><span class="ap-brand-suite">' + d.suite + '</span></span>' +
  '</div>' +
  '<div class="ap-nav">' +
   d.nav.map(function (label, i) {
    return '<span class="ap-nav-item' + (i === 0 ? ' is-active' : '') + '">' +
      icon(NAV_ICONS[i], 'ap-nav-ico') + '<span>' + label + '</span></span>';
   }).join('') +
  '</div>' +
  '<div class="ap-side-foot">&copy; 2025 Drawlead</div>' +
 '</div>';
}

function buildHeader(d) {
 return '<div class="ap-top">' +
  '<div class="ap-search">' + icon('<circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/>', 'ap-search-ico') +
   '<span>' + d.search + '</span></div>' +
  '<div class="ap-top-right">' +
   '<span class="ap-bell">' + icon('<path d="M18 8a6 6 0 1 0-12 0c0 6-2 7-2 7h16s-2-1-2-7"/><path d="M13.7 20a2 2 0 0 1-3.4 0"/>', 'ap-bell-ico') +
    '<span class="ap-bell-dot"></span></span>' +
   '<span class="ap-user">' +
    '<span class="ap-avatar">AK</span>' +
    '<span class="ap-user-txt"><span class="ap-user-name">Arun Kumar</span><span class="ap-user-role">' + d.role + '</span></span>' +
    icon('<path d="m6 9 6 6 6-6"/>', 'ap-chev') +
   '</span>' +
  '</div>' +
 '</div>';
}

function buildKpis(d) {
 return '<div class="ap-kpis">' + d.kpis.map(function (k, i) {
  return '<div class="ap-kpi ap-t-' + TONES[i] + '">' +
    '<div class="ap-kpi-top">' +
     '<span class="ap-kpi-ico">' + icon(KPI_ICONS[i], 'ap-kpi-svg') + '</span>' +
    '</div>' +
    '<div class="ap-kpi-label">' + k.l + '</div>' +
    '<div class="ap-kpi-value">' + k.v + '</div>' +
    '<div class="ap-kpi-foot">' +
     '<span class="ap-delta">' + (k.down ? '&#8595;' : '&#8593;') + ' ' + k.d + '</span>' +
     '<span class="ap-delta-note">' + k.n + '</span>' +
    '</div>' +
   '</div>';
 }).join('') + '</div>';
}

// Bars are sized against the chart's own axis maximum, so every industry's numbers sit
// correctly under its own Y scale rather than being normalised to a shared percentage.
function buildChart(d) {
 const c = d.chart;
 return '<div class="ap-card ap-chart-card">' +
  '<div class="ap-card-head">' +
   '<h4 class="ap-card-title">' + c.label + '</h4>' +
   '<span class="ap-select">' + c.range + icon('<path d="m6 9 6 6 6-6"/>', 'ap-chev') + '</span>' +
  '</div>' +
  '<div class="ap-plot">' +
   '<div class="ap-yaxis">' + c.ticks.map(function (t) { return '<span>' + t.toLocaleString() + '</span>'; }).join('') + '</div>' +
   '<div class="ap-grid">' +
    c.ticks.map(function () { return '<span class="ap-gridline"></span>'; }).join('') +
    '<div class="ap-bars">' +
     c.vals.map(function (v, i) {
      const pct = Math.round((v / c.max) * 100);
      return '<div class="ap-bar-col"><span class="ap-bar' + (i === c.vals.length - 1 ? ' is-peak' : '') +
             '" style="height:' + pct + '%"></span></div>';
     }).join('') +
    '</div>' +
   '</div>' +
  '</div>' +
  '<div class="ap-xaxis">' + c.labels.map(function (l) { return '<span>' + l + '</span>'; }).join('') + '</div>' +
 '</div>';
}

function buildAi(d) {
 return '<div class="ap-ai">' +
  '<span class="ap-ai-ico">' + icon('<path d="M12 3.2 13.7 9l5.8 1.7-5.8 1.7L12 18.2l-1.7-5.8L4.5 10.7 10.3 9z"/>', 'ap-ai-svg') + '</span>' +
  '<div class="ap-ai-body"><div class="ap-ai-label">AI INSIGHT</div><p class="ap-ai-text">' + d.ai + '</p></div>' +
  '<span class="ap-ai-cta">View Details ' + icon('<path d="M5 12h13M13 6l6 6-6 6"/>', 'ap-ai-arrow') + '</span>' +
 '</div>';
}

function buildDashBody(d) {
 return buildSidebar(d) +
  '<div class="ap-main">' +
   buildHeader(d) +
   '<div class="ap-content">' +
    '<div class="ap-page-head">' +
     '<div>' +
      '<div class="ap-greet">GOOD MORNING, ARUN</div>' +
      '<h3 class="ap-title">' + d.title + '</h3>' +
      '<p class="ap-sub">Here&rsquo;s what&rsquo;s happening with your business today.</p>' +
     '</div>' +
     '<div class="ap-page-meta">' +
      '<span class="ap-live"><i></i>Live</span>' +
      '<span class="ap-select">' + icon('<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 11h18"/>', 'ap-cal') +
       'May 2025' + icon('<path d="m6 9 6 6 6-6"/>', 'ap-chev') + '</span>' +
     '</div>' +
    '</div>' +
    buildKpis(d) +
    buildChart(d) +
   '</div>' +
  '</div>';
}

function renderDash(idx) {
 const d = industries[idx];
 document.getElementById('dwBody').innerHTML = buildDashBody(d);
 document.querySelectorAll('.ind-tab').forEach(function (t, i) { t.classList.toggle('active', i === idx); });
}

function switchDash(idx) {
 if (idx === currentIdx) return;
 currentIdx = idx;
 const win = document.getElementById('dashWindow');
 if (reduceMotion) { renderDash(idx); return; }
 win.classList.add('switching');
 setTimeout(function () {
  renderDash(idx);
  win.classList.remove('switching');
 }, 300);
}

// Auto-cycle through the five industries, one every CYCLE_MS.
const CYCLE_MS = 5500;
if (!reduceMotion) {
 setInterval(function () { switchDash((currentIdx + 1) % industries.length); }, CYCLE_MS);
}
renderDash(0);



// Core Functions: sticky horizontal scroll
// The row is pinned via CSS position:sticky. As the user scrolls down through
// the wrapper, this maps that scroll distance 1:1 to translateX so the row
// reveals left→right, then releases back to normal vertical scroll once done.
// The sticky box is sized to its own natural content height (no 100vh, no
// extra padding) so the scroll distance consumed matches only the actual
// horizontal overflow, not an arbitrarily inflated viewport-sized box.
(function(){
 const outer = document.getElementById('cfScrollOuter');
 const sticky = document.getElementById('cfScrollSticky');
 const row = document.getElementById('cfRow');
 if(!outer || !sticky || !row) return;

 const skipHijack = reduceMotion || window.matchMedia('(max-width:768px)').matches;
 if(skipHijack) return;

 // Read back off the element instead of being duplicated here: the box now pins
 // centred in the viewport, and a hard-coded 0 would put the horizontal progress out
 // of step with where it actually sticks.
 let stickyTop = 0;
 let overflow = 0;

 function measure(){
 // pin the heading + cards + CTA block centred in the screen below the fixed nav
 const nav = document.querySelector('nav');
 const navH = nav ? nav.offsetHeight : 0;
 const top = navH + Math.max(0, (window.innerHeight - navH - sticky.offsetHeight) / 2);
 outer.style.setProperty('--cf-top', top + 'px');
 stickyTop = top;
 overflow = Math.max(0, row.scrollWidth - sticky.clientWidth);
 outer.style.height = (sticky.offsetHeight + overflow) + 'px';
 }

 function onScroll(){
 if(overflow <= 0){ row.style.transform = 'translateX(0)'; return; }
 const rect = outer.getBoundingClientRect();
 const progress = Math.min(1, Math.max(0, (stickyTop - rect.top) / overflow));
 row.style.transform = `translateX(${-progress * overflow}px)`;
 }

 measure();
 onScroll();
 window.addEventListener('load', ()=>{ measure(); onScroll(); });
 if(document.fonts && document.fonts.ready) document.fonts.ready.then(()=>{ measure(); onScroll(); });
 window.addEventListener('resize', ()=>{ measure(); onScroll(); });
 window.addEventListener('scroll', onScroll, { passive: true });
 window.addEventListener('load', ()=>{ measure(); onScroll(); });
})();

// Tech Stack: same sticky horizontal run as Core Functions. Here only the card row
// and CTA are pinned (the heading and integrations image scroll normally above), so
// the box keeps its content height and is centred in the viewport below the nav.
(function(){
 const outer = document.getElementById('techScrollOuter');
 const sticky = document.getElementById('techScrollSticky');
 const row = document.getElementById('techRow');
 if(!outer || !sticky || !row) return;

 const skipHijack = reduceMotion || window.matchMedia('(max-width:768px)').matches;
 if(skipHijack) return;

 let stickyTop = 0;
 let overflow = 0;

 function measure(){
 const nav = document.querySelector('nav');
 const navH = nav ? nav.offsetHeight : 0;
 stickyTop = Math.max(navH, navH + (window.innerHeight - navH - sticky.offsetHeight) / 2);
 outer.style.setProperty('--tech-top', stickyTop + 'px');
 overflow = Math.max(0, row.scrollWidth - sticky.clientWidth);
 outer.style.height = (sticky.offsetHeight + overflow) + 'px';
 }

 function onScroll(){
 if(overflow <= 0){ row.style.transform = 'translateX(0)'; return; }
 const rect = outer.getBoundingClientRect();
 const progress = Math.min(1, Math.max(0, (stickyTop - rect.top) / overflow));
 row.style.transform = `translateX(${-progress * overflow}px)`;
 }

 measure();
 onScroll();
 window.addEventListener('resize', ()=>{ measure(); onScroll(); });
 if(document.fonts && document.fonts.ready) document.fonts.ready.then(()=>{ measure(); onScroll(); });
 window.addEventListener('load', ()=>{ measure(); onScroll(); });
 window.addEventListener('scroll', onScroll, { passive: true });
})();

// Platform Dashboards: one row of cards. While the row is pinned, vertical scroll
// slides it left so the dashboards pass by one after another. Same runway technique
// as the Core Functions row above: the outer box is made exactly as tall as the
// pinned box plus the horizontal overflow, so the run ends on the last card.
// Phones and reduced motion keep a plain swipeable row (see home7.css).
(function(){
 const outer = document.getElementById('dashScrollOuter');
 const sticky = document.getElementById('dashScrollSticky');
 const row = document.getElementById('dashRow');
 if(!outer || !sticky || !row) return;

 const mq = window.matchMedia('(max-width:768px), (prefers-reduced-motion: reduce)');
 let stickyTop = 0;
 let overflow = 0;
 let ticking = false;

 function measure(){
  if(mq.matches){
   outer.style.height = ''; sticky.style.top = ''; row.style.transform = '';
   overflow = 0; return;
  }
  // centre the pinned block (heading + row) in the space below the fixed nav
  const nav = document.querySelector('nav');
  const navH = nav ? nav.offsetHeight : 0;
  stickyTop = navH + Math.max(0, (window.innerHeight - navH - sticky.offsetHeight) / 2);
  sticky.style.top = stickyTop + 'px';
  overflow = Math.max(0, row.scrollWidth - sticky.clientWidth);
  outer.style.height = (sticky.offsetHeight + overflow) + 'px';
 }

 function update(){
  ticking = false;
  if(mq.matches) return;
  if(overflow <= 0){ row.style.transform = 'translate3d(0,0,0)'; return; }
  const rect = outer.getBoundingClientRect();
  const progress = Math.min(1, Math.max(0, (stickyTop - rect.top) / overflow));
  row.style.transform = 'translate3d(' + (-progress * overflow) + 'px,0,0)';
 }

 function onScroll(){ if(!ticking){ ticking = true; requestAnimationFrame(update); } }
 function refresh(){ measure(); update(); }

 refresh();
 window.addEventListener('scroll', onScroll, { passive: true });
 window.addEventListener('resize', refresh);
 window.addEventListener('load', refresh);
 if(mq.addEventListener) mq.addEventListener('change', refresh);
})();

// Sticky card stacks: scroll-linked "overlap → fade → next becomes active"
// Opacity/scale/blur are mapped continuously to scroll position rather than toggled by
// a CSS transition, so the dimming tracks the scroll exactly with no sudden jump.
// A card starts dimming only once the NEXT card begins to cover it, and bottoms out at
// dimTo (never 0) so its top edge stays visible behind the active card.
(function(){
 const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

 function initStickyStack(selector, opts){
  const cards = Array.from(document.querySelectorAll(selector));
  if(cards.length < 2) return;

  const dimTo  = opts.dimTo  !== undefined ? opts.dimTo  : 0.30;
  const shrink = opts.shrink !== undefined ? opts.shrink : 0.04;
  let ticking = false;

  // PERF: stuckTop + card height are cached on resize. Reading getComputedStyle()
  // and offsetHeight per card per frame forced a synchronous reflow every frame,
  // which was a major source of scroll jank across the whole page.
  const geo = [];
  function measure(){
   geo.length = 0;
   for(let i = 0; i < cards.length; i++){
    geo.push({
     top: parseFloat(getComputedStyle(cards[i]).top) || 0,
     h:   cards[i].offsetHeight
    });
   }
  }

  function update(){
   ticking = false;
   let active = 0;

   for(let i = 0; i < cards.length; i++){
    const card = cards[i];
    const next = cards[i + 1];

    // the final card is always the foreground one, never dim it
    if(!next){ card.style.opacity = ''; card.style.transform = ''; card.style.filter = ''; continue; }

    const g        = geo[i] || { top:0, h:0 };
    const nextTop  = next.getBoundingClientRect().top;

    // fade window: from "next card's top edge reaches this card's bottom"
    //              to   "next card has almost fully covered this card"
    const fadeStart = g.top + g.h;
    const fadeEnd   = g.top + 28;

    let p = (fadeStart - nextTop) / (fadeStart - fadeEnd);
    p = p < 0 ? 0 : (p > 1 ? 1 : p);

    card.style.opacity   = String(1 - p * (1 - dimTo));
    card.style.transform = 'scale(' + (1 - p * shrink) + ')';
    // PERF: animated blur() forces a full repaint of the card every frame. The scale
    // + opacity fade already reads as "receding", so the blur is not worth its cost.

    if(p > 0.5) active = i + 1;   // once a card is mostly covered, the next one leads
   }

   for(let i = 0; i < cards.length; i++){
    cards[i].classList.toggle('is-active', i === active);
   }
   if(opts.onStage) opts.onStage(active);
  }

  function onScroll(){ if(!ticking){ ticking = true; requestAnimationFrame(update); } }

  measure();
  update();
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', function(){ measure(); onScroll(); });
 }

 if(reduce) return;

 // Home 9: Solutions is a sidebar and a card column (the script after #solutions), not a sticky stack.


})();

// Industries: sticky horizontal run, same technique as Core Functions / Tech Stack.
// The pinned box is the card row only, centred in the screen below the fixed nav;
// the scroll distance it consumes equals the row's horizontal overflow.
(function(){
 const outer = document.getElementById('indHOuter');
 const sticky = document.getElementById('indHSticky');
 const row = document.getElementById('indRow');
 if(!outer || !sticky || !row) return;
 if(reduceMotion || window.matchMedia('(max-width:768px)').matches) return;

 let stickyTop = 0, overflow = 0;

 function measure(){
  const nav = document.querySelector('nav');
  const navH = nav ? nav.offsetHeight : 0;
  stickyTop = navH + Math.max(0, (window.innerHeight - navH - sticky.offsetHeight) / 2);
  outer.style.setProperty('--ind-top', stickyTop + 'px');
  overflow = Math.max(0, row.scrollWidth - sticky.clientWidth);
  outer.style.height = (sticky.offsetHeight + overflow) + 'px';
 }

 function onScroll(){
  if(overflow <= 0){ row.style.transform = 'translateX(0)'; return; }
  const rect = outer.getBoundingClientRect();
  const progress = Math.min(1, Math.max(0, (stickyTop - rect.top) / overflow));
  row.style.transform = `translateX(${-progress * overflow}px)`;
 }

 measure();
 onScroll();
 window.addEventListener('resize', ()=>{ measure(); onScroll(); });
 if(document.fonts && document.fonts.ready) document.fonts.ready.then(()=>{ measure(); onScroll(); });
 window.addEventListener('load', ()=>{ measure(); onScroll(); });
 window.addEventListener('scroll', onScroll, { passive: true });
})();

// Section 3: physics tag stage (scroll-triggered, runs once)
// Matter.js runs a real rigid-body sim (gravity, restitution, friction, rotation,
// pill-to-pill collisions). The pills stay as DOM nodes so they keep their CSS
// gradients/blur/shadows; each frame we just write transform onto them.
// The sim is built up-front but held frozen until the section scrolls into view, so
// every tag falls together the moment the user arrives, and only ever once.
(function(){
 const stage = document.getElementById('physStage');
 if(!stage || typeof Matter === 'undefined') return;
 if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

 const pills = Array.from(stage.querySelectorAll('.phys-pill'));
 if(!pills.length) return;

 const { Engine, Runner, Composite, Bodies, Body, Events, Vector } = Matter;

 const engine = Engine.create();
 engine.gravity.y = 0.9;

 let W = stage.clientWidth, H = stage.clientHeight;
 const WALL = 200;                       // thick walls so fast bodies can't tunnel through
 let bodies = [], walls = [];

 function makeWalls(){
  Composite.remove(engine.world, walls);
  walls = [
   Bodies.rectangle(W/2, H + WALL/2, W + WALL*2, WALL, { isStatic:true }), // floor
   Bodies.rectangle(W/2, -WALL/2,    W + WALL*2, WALL, { isStatic:true }), // ceiling
   Bodies.rectangle(-WALL/2, H/2, WALL, H + WALL*2,    { isStatic:true }), // left
   Bodies.rectangle(W + WALL/2, H/2, WALL, H + WALL*2, { isStatic:true })  // right
  ];
  Composite.add(engine.world, walls);
 }

 // Spawn in loose rows rather than one long line. With 19 pills a single row would
 // space them ~54px apart while the widest is ~175px, so they would spawn inside one
 // another and Matter would fire them apart on the first step.
 const widest = pills.reduce(function(m, el){ return Math.max(m, el.getBoundingClientRect().width || 120); }, 0);
 const perRow = Math.max(1, Math.min(pills.length, Math.floor(W / (widest + 24))));

 // One body per pill, sized from its rendered box.
 // Spawn points sit INSIDE the stage near the top: the ceiling body occupies y -200..0,
 // so spawning at negative y (as before) dropped pills inside a static wall, which
 // ejected or trapped most of them, that's why only a couple ever reached the floor.
 pills.forEach(function(el, i){
  const r = el.getBoundingClientRect();
  const w = r.width || 120, h = r.height || 44;
  const col = i % perRow, row = Math.floor(i / perRow);
  const x = (W / (perRow + 1)) * (col + 1) + (Math.random() - 0.5) * 26;
  const y = 40 + row * 64 + Math.random() * 22;   // safely below the ceiling wall
  const body = Bodies.rectangle(x, y, w, h, {
   chamfer: { radius: h/2 },                      // capsule shape matches the pill visually
   restitution: 0.52,                             // bounce
   friction: 0.32,                                // surface friction, lets stacks settle
   frictionAir: 0.014,                            // gentle drag so motion feels weighty
   density: 0.0016
  });
  Body.setAngularVelocity(body, (Math.random() - 0.5) * 0.22);
  Body.setVelocity(body, { x:(Math.random() - 0.5) * 2.5, y:0 });
  body.__el = el; body.__w = w; body.__h = h;
  bodies.push(body);
 });

 makeWalls();

 // cursor repulsion
 const mouse = { x:-9999, y:-9999, active:false };
 const R = 130;                                   // influence radius
 stage.addEventListener('mousemove', function(e){
  const r = stage.getBoundingClientRect();
  mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top; mouse.active = true;
 });
 stage.addEventListener('mouseleave', function(){ mouse.active = false; });

 Events.on(engine, 'beforeUpdate', function(){
  for(let i = 0; i < bodies.length; i++){
   const b = bodies[i];

   if(mouse.active){
    const d = Vector.sub(b.position, mouse);
    const dist = Math.hypot(d.x, d.y);
    if(dist < R && dist > 0.1){
     const strength = (1 - dist / R) * 0.055 * b.mass;   // falls off with distance
     Body.applyForce(b, b.position, { x:(d.x/dist) * strength, y:(d.y/dist) * strength });
    }
   }

   // faint random jitter so settled pills still feel alive
   if(Math.random() < 0.03){
    Body.applyForce(b, b.position, {
     x:(Math.random() - 0.5) * 0.0016 * b.mass,
     y:(Math.random() - 0.5) * 0.0011 * b.mass
    });
   }
  }
 });

 // paint DOM from physics state
 function paint(){
  for(let i = 0; i < bodies.length; i++){
   const b = bodies[i];
   b.__el.style.transform =
    'translate(' + (b.position.x - b.__w/2) + 'px,' + (b.position.y - b.__h/2) + 'px)' +
    ' rotate(' + b.angle + 'rad)';
  }
 }
 function loop(){ paint(); requestAnimationFrame(loop); }

 // Seat the pills at their spawn coordinates while still invisible, so the reveal
 // doesn't flash them at the stage's top-left corner for a frame.
 paint();

 // trigger: fire once, when the section actually scrolls into view
 let started = false;
 function start(){
  if(started) return;
  started = true;
  Composite.add(engine.world, bodies);            // bodies only enter the world now
  Runner.run(Runner.create(), engine);
  stage.classList.add('is-running');              // CSS fades every pill in together
  requestAnimationFrame(loop);
 }

 if('IntersectionObserver' in window){
  const io = new IntersectionObserver(function(entries){
   entries.forEach(function(en){
    if(en.isIntersecting){ start(); io.disconnect(); }   // disconnect => never repeats
   });
  }, { threshold: 0.35 });
  io.observe(stage);
 } else {
  start();
 }

 // keep walls in step with a resized stage, and rescue anything left outside
 let rt;
 window.addEventListener('resize', function(){
  clearTimeout(rt);
  rt = setTimeout(function(){
   W = stage.clientWidth; H = stage.clientHeight;
   makeWalls();
   bodies.forEach(function(b){
    if(b.position.x < 0 || b.position.x > W || b.position.y > H){
     Body.setPosition(b, { x: W/2, y: 60 });
     Body.setVelocity(b, { x:0, y:0 });
    }
   });
  }, 180);
 });
})();


// CTA intro: continuous letter train
// The uploaded prototype's mechanic, unchanged, over the whole heading as ONE character
// sequence on ONE baseline. Letters do NOT fly to their own final 3-line coordinates;
// they land on the moving baseline and then travel with it.
//
//   currentTrainX  = landX - progress * (totalWidth + innerWidth * 0.8)
//   letterSlotX    = currentTrainX + offsets[i]
//   distanceToLand = letterSlotX - landX
//
//   distanceToLand > 0  -> STAGE 1: waiting at, or flying from, the ONE shared launch
//                          point. jumpProgress = 1 - distanceToLand / jumpDistance,
//                          arc = pow(jumpProgress, 1.8) interpolating x, y, rotation,
//                          scale and opacity.
//   distanceToLand <= 0 -> STAGE 2: landed. It sits at letterSlotX, which keeps moving
//                          left, so it travels as part of the train and is never frozen.
//
// PERFORMANCE
// The maths above is untouched; only the cost of applying it has been cut.
//  1. onUpdate bails immediately when scroll progress hasn't moved enough to shift the
//     train by a visible amount, so a stalled/settling scrub costs nothing.
//  2. Glyphs whose x is outside the visible band are written once as they leave and
//     then skipped until they come back. Most glyphs are parked off-screen right at the
//     launch point or have exited left, so this removes the bulk of the per-frame work.
//  3. Values are quantised (0.25px / 0.1deg / 0.001 / 0.01) and compared numerically
//     against typed arrays. The transform string is only built when something moved.
//  4. Every measurement is cached in measure(); render() performs ZERO layout reads.
//  5. ScrollTrigger's own tick is the only loop, no extra requestAnimationFrame.
(function(){
 const section = document.getElementById('cta-intro');
 if(!section) return;
 const track = section.querySelector('.ci-track');
 if(!track) return;

 if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
 if(typeof window.gsap === 'undefined' || typeof window.ScrollTrigger === 'undefined') return;

 const ctaCard = document.querySelector('#cta .cta-card');
 if(!ctaCard) return;

 // the single launch coordinate every glyph spawns from
 const LAUNCH_Y = -260, LAUNCH_ROT = 35, LAUNCH_SCALE = 0.6, ARC_POW = 1.8;

 function init(){
  try {
   gsap.registerPlugin(ScrollTrigger);

   const glyphs   = track.querySelectorAll('.ci-stage .ci-ch');
   const measures = track.querySelectorAll('.ci-measure span');
   const n = glyphs.length;
   if(!n || measures.length !== n) throw new Error('glyph/measure mismatch');

   const offsets = new Float64Array(n);
   const pX = new Float64Array(n), pY = new Float64Array(n);
   const pR = new Float64Array(n), pS = new Float64Array(n), pO = new Float64Array(n);
   const wasOut = new Uint8Array(n);          // glyph is parked outside the visible band

   section.classList.add('ci-anim');   // switch to the pinned track before measuring it

   let totalWidth = 0, landX = 0, launchX = 0, launchY = 0,
       jumpDistance = 0, travel = 0, scrollLen = 0,
       xMin = 0, xMax = 0, progEps = 1;

   // The ONLY place layout is read. Re-runs on refresh, so every coordinate is
   // recalculated cleanly on resize.
   function measure(){
    const vw = window.innerWidth;
    let widest = 0;
    for(let i = 0; i < n; i++){
     const m = measures[i];
     offsets[i] = m.offsetLeft;                     // real kerning, straight from layout
     const w = m.offsetWidth;
     if(w > widest) widest = w;
     totalWidth = m.offsetLeft + w;
    }
    landX   = ctaCard.getBoundingClientRect().right - 140;   // CTA card's right boundary
    launchX = vw + 60;                                       // off the top-right
    launchY = LAUNCH_Y;
    // flight window ≈ one average letter advance, so a glyph leaves the launch point as
    // the one before it touches down: letters land one after another, tightly
    jumpDistance = Math.max(40, (totalWidth / n) * 1.30);
    travel    = totalWidth + vw * 0.8;
    scrollLen = totalWidth + vw + 800;
    // visible band, padded by the widest glyph so nothing is skipped while still partly
    // on screen. launchX sits beyond xMax, so parked glyphs cost nothing.
    xMin = -(widest + 24);
    xMax = vw + 24;
    // progress delta that moves the train less than a quarter pixel, below this the
    // whole frame is skipped
    progEps = travel > 0 ? 0.25 / travel : 0;
    pX.fill(NaN); pY.fill(NaN); pR.fill(NaN); pS.fill(NaN); pO.fill(NaN);
    wasOut.fill(0);
   }

   function render(p){
    const currentTrainX = landX - p * travel;

    for(let i = 0; i < n; i++){
     const letterSlotX    = currentTrainX + offsets[i];
     const distanceToLand = letterSlotX - landX;

     let x, y, rot, sc, op;
     if(distanceToLand > 0){
      // STAGE 1: waiting at / flying from the shared launch point
      let jp = 1 - distanceToLand / jumpDistance;
      if(jp <= 0){
       x = launchX; y = launchY; rot = LAUNCH_ROT; sc = LAUNCH_SCALE; op = 0;
      } else {
       if(jp > 1) jp = 1;
       const e = Math.pow(jp, ARC_POW);            // curved flight
       x   = launchX + (landX - launchX) * e;
       y   = launchY + (0 - launchY) * e;
       rot = LAUNCH_ROT * (1 - e);
       sc  = LAUNCH_SCALE + (1 - LAUNCH_SCALE) * e;
       op  = jp * 2; if(op > 1) op = 1;
      }
     } else {
      // STAGE 2: landed on the moving baseline, travelling with the train
      x = letterSlotX; y = 0; rot = 0; sc = 1; op = 1;
     }

     // Off-screen glyphs: write once as they leave, then leave them alone until they
     // come back. Their exact position is invisible, so this changes nothing on screen.
     if(x < xMin || x > xMax){
      if(wasOut[i]) continue;
      wasOut[i] = 1;
     } else if(wasOut[i]){
      wasOut[i] = 0;
     }

     // quantise to below the threshold of visibility, then compare numerically
     x   = Math.round(x   * 4)    / 4;      // 0.25px
     y   = Math.round(y   * 4)    / 4;      // 0.25px
     rot = Math.round(rot * 10)   / 10;     // 0.1deg
     sc  = Math.round(sc  * 1000) / 1000;   // 0.001
     op  = Math.round(op  * 100)  / 100;    // 0.01

     if(x !== pX[i] || y !== pY[i] || rot !== pR[i] || sc !== pS[i] || op !== pO[i]){
      const el = glyphs[i];
      // one transform string, built only because this glyph actually moved
      el.style.transform = 'translate3d(' + x + 'px,' + y + 'px,0) rotate(' + rot +
                           'deg) scale(' + sc + ')';
      if(op !== pO[i]) el.style.opacity = op;
      pX[i] = x; pY[i] = y; pR[i] = rot; pS[i] = sc; pO[i] = op;
     }
    }
   }

   measure();
   render(0);
   let lastP = -1;

   ScrollTrigger.create({
    trigger: section,
    start: 'top top',
    end: function(){ return '+=' + scrollLen; },
    pin: true,
    anticipatePin: 1,
    scrub: 1.2,
    invalidateOnRefresh: true,
    onRefresh: function(self){ measure(); lastP = -1; render(self.progress); lastP = self.progress; },
    onUpdate: function(self){
     const p = self.progress;
     // the train hasn't moved a visible amount, skip the entire frame
     if(p === lastP || (p > lastP ? p - lastP : lastP - p) < progEps) return;
     lastP = p;
     render(p);
    }
   });

  } catch(err){
   section.classList.remove('ci-anim');   // never leave the heading invisible
   if(window.console && console.warn) console.warn('cta-intro animation disabled:', err);
  }
 }

 // Webfonts change every measurement, so measure only once they have loaded.
 if(document.fonts && document.fonts.ready){
  document.fonts.ready.then(init);
 } else {
  window.addEventListener('load', init);
 }
})();

// The 7 Functions, Tech Stack and Industries runways set their own heights from JS once
// layout settles (load / webfonts). GSAP's pins further down (the "Ready to Transform"
// letter train) cache their start positions when they are created, so without this
// they go stale and the pinned train shows up early, behind the Industries cards.
window.addEventListener('load', function(){
 if(window.ScrollTrigger) ScrollTrigger.refresh();
});

// Case studies: carousel track driven entirely by vertical scroll
// PERF NOTES (this section was janky before):
//  * All layout reads (offsetWidth/offsetHeight) are cached and only refreshed on
//    resize. Reading them inside the frame loop forced a synchronous reflow on EVERY
//    frame while we were also writing styles, classic layout thrashing.
//  * Each frame now does ONE cheap read (rect.top) and then only writes.
//  * Writes are dirty-checked, so untouched cards don't get pointless style updates.
//  * An IntersectionObserver stops the loop entirely when the section is off-screen.
(function(){
 const scroller = document.getElementById('casesScroll');
 const track    = document.getElementById('casesTrack');
 const pin      = scroller && scroller.querySelector('.cases-pin');
 if(!scroller || !track || !pin) return;

 const cards = Array.from(track.querySelectorAll('.case-card'));
 if(!cards.length) return;

 const last = cards.length - 1;

 // cached layout (refreshed only on resize)
 let span = 1, step = 1;
 function measure(){
  span = Math.max(1, scroller.offsetHeight - pin.offsetHeight);
  step = cards[0].offsetWidth + 40;
 }

 // remember what we last wrote so we can skip no-op DOM writes
 const state = cards.map(function(){ return { x:null, o:null, z:null, v:null }; });

 let ticking = false, visible = true;

 function layout(){
  ticking = false;
  if(!visible) return;

  const p0 = -scroller.getBoundingClientRect().top / span;   // the only per-frame read
  const p  = p0 < 0 ? 0 : (p0 > 1 ? 1 : p0);

  const activeF = p * last;
  let nearest = 0, nearestDist = Infinity;

  for(let i = 0; i < cards.length; i++){
   const card = cards[i];
   const st   = state[i];
   const d    = i - activeF;
   const ad   = d < 0 ? -d : d;

   if(ad < nearestDist){ nearestDist = ad; nearest = i; }

   const vis = ad > 2.1 ? 'hidden' : 'visible';
   if(st.v !== vis){ card.style.visibility = vis; st.v = vis; }
   if(vis === 'hidden') continue;

   // linear track: constant spacing, so cards can never drift into one another
   // Direction: +d puts an upcoming card on the RIGHT and a passed card on the LEFT,
   // so each card travels RIGHT -> CENTRE -> LEFT -> exit. (Sign inverted from before.)
   const x  = Math.round(d * step);                     // round => fewer sub-pixel repaints
   const sc = ad > 1.25 ? 0.9 : (1 - ad * 0.08);
   const o  = +(Math.max(0, 1 - ad * 0.55)).toFixed(2);
   const z  = 50 - Math.round(ad * 10);

   if(st.x !== x){
    card.style.transform = 'translate3d(calc(-50% + ' + x + 'px), -50%, 0) scale(' + sc.toFixed(3) + ')';
    st.x = x;
   }
   if(st.o !== o){ card.style.opacity = o; st.o = o; }
   if(st.z !== z){ card.style.zIndex = z; st.z = z; }
  }

  for(let i = 0; i < cards.length; i++){
   cards[i].classList.toggle('is-active', i === nearest);
  }
 }

 function onScroll(){ if(!ticking){ ticking = true; requestAnimationFrame(layout); } }

 // don't run the loop at all while the section is nowhere near the viewport
 if('IntersectionObserver' in window){
  new IntersectionObserver(function(en){
   visible = en[0].isIntersecting;
   if(visible) onScroll();
  }, { rootMargin: '200px 0px' }).observe(scroller);
 }

 measure();
 layout();
 window.addEventListener('scroll', onScroll, { passive: true });
 window.addEventListener('resize', function(){ measure(); onScroll(); });
})();
</script>
