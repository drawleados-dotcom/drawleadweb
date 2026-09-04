<?php
/**
 * Home 7: exact copy of the standalone redesigned homepage bundle
 * (home (1)/home/), served at /home-7 as a DB-managed page (Admin -> Pages).
 * Its stylesheet is /assets/home7.css and its animation libraries live in
 * /assets/ (matter.min.js, gsap.min.js, ScrollTrigger.min.js), all copied
 * from that bundle. The live "/" homepage is untouched.
 */
$activePage = 'home7';
include __DIR__ . '/partials/nav.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset_url('/assets/home7.css') ?>">

<section id="hero">

 <!-- Ambient background layer: technical grid, star dust and green shooting-star
      runners. Purely decorative and inert, aria-hidden, pointer-events:none, and
      pinned behind .hero-grid (which already sits at z-index:2), so no existing hero
      element is touched or obstructed. Animation is CSS-only (transform + opacity). -->
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
    <span class="hero-etxt">One OS. All Operations. Infinite Possibilities.</span>
   </div>

   <!-- MAIN HEADLINE, content fixed, browser wraps naturally -->
   <h1 class="hero-h">One <span class="grad-os">OS</span> with <span class="grad-ai">AI</span> to every function of your <span class="grad-os">business</span></h1>

   <p class="hero-p">Drawlead is the operating system for modern business. Unify ERP, AI automation, analytics, and cloud workflows into one intelligent platform built for India's growing businesses.</p>

   <div class="hero-btns">
    <button type="button" data-book class="btn btn-primary">Start Free Consultation</button>
    <a href="#functions" class="btn btn-ghost">
     <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polygon points="10,8 16,12 10,16" fill="currentColor"/></svg>
     Watch 2-Min Demo
    </a>
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
     <div class="hstat-txt"><div class="hstat-n" style="color:var(--blue)">AI</div><div class="hstat-l">Powered</div></div>
    </div>
    <div class="hstat">
     <div class="hstat-ico"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.178 8c5.096 0 5.096 8 0 8-5.095 0-7.133-8-12.739-8-4.585 0-4.585 8 0 8 5.606 0 7.644-8 12.74-8z"/></svg></div>
     <div class="hstat-txt"><div class="hstat-n">∞</div><div class="hstat-l">Scalable</div></div>
    </div>
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
    <div class="dw-topbar">
     <span class="dw-dot" style="background:#ff5f57"></span>
     <span class="dw-dot" style="background:#ffbd2e"></span>
     <span class="dw-dot" style="background:#28c840"></span>
     <span class="dw-brand">DRAWLEAD</span>
     <svg class="dw-menu" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="19" cy="12" r="1.8"/></svg>
    </div>
    <div class="dw-body" id="dwBody">
     <div class="dw-head">
      <div class="dw-title">ECOMMERCE ORDERS AND REVENUE OVERVIEW</div>
      <div class="dw-meta">
       <span class="dw-live" style="background:rgba(35,160,101,.12);color:#23a065">Live</span>
       <span class="dw-month">May 2025</span>
      </div>
     </div>
     <div class="dw-kpis">
      <div class="dw-kpi"><div class="dw-kv">842</div><div class="dw-kl">Orders Today</div></div>
      <div class="dw-kpi"><div class="dw-kv">₹18.4L</div><div class="dw-kl">GMV</div></div>
      <div class="dw-kpi"><div class="dw-kv">3.8%</div><div class="dw-kl">Conversion</div></div>
      <div class="dw-kpi"><div class="dw-kv">₹2,140</div><div class="dw-kl">Avg Order</div></div>
     </div>
     <div class="dw-chart">
      <div class="dw-chart-label">Daily Orders</div>
      <div class="dw-chart-bars">
       <div class="dw-bar" style="height:58%;background:rgba(35,160,101,.18)"></div>
       <div class="dw-bar" style="height:66%;background:rgba(35,160,101,.29)"></div>
       <div class="dw-bar" style="height:50%;background:rgba(35,160,101,.40)"></div>
       <div class="dw-bar" style="height:82%;background:rgba(35,160,101,.51)"></div>
       <div class="dw-bar" style="height:74%;background:rgba(35,160,101,.62)"></div>
       <div class="dw-bar" style="height:95%;background:#23a065"></div>
      </div>
     </div>
     <div class="dw-ai" style="border-color:#23a065">
      <div class="dw-ai-label" style="color:#23a065">AI INSIGHT</div>
      <div class="dw-ai-text">Cart abandonment at 68% on mobile checkout. Recovering 240 carts could add ₹5.1L this month.</div>
     </div>
    </div>
   </div>
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

<!-- 7 FUNCTIONS -->
<section id="functions">
 <div class="eyebrow rv"><span class="eyebrow-text">Core Platform</span></div>
 <h2 class="sec-h rv">The <span class="g">7 Functions</span> of Business <span class="fade">Unified</span></h2>
 <p class="sec-sub rv">Every core business function streamlined and intelligently connected through one operating system.</p>

 <div class="cf-scroll-outer" id="cfScrollOuter">
  <div class="cf-scroll-sticky" id="cfScrollSticky">
   <div class="cf-row" id="cfRow">

    <!-- 01 Management -->
    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg></div>
     <div class="cf-name">Management</div>
     <div class="cf-desc">Centralized dashboards and operational visibility for faster and smarter business decisions.</div>
     <div class="cf-tags"><span class="cf-tag">KPI Tracking</span><span class="cf-tag">Analytics</span><span class="cf-tag">Approvals</span></div>
     <button type="button" data-book class="cf-arrow">Explore module</button>
    </div>

    <!-- 02 Sales -->
    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polyline points="3,17 9,11 13,15 21,7"/><polyline points="14,7 21,7 21,14"/></svg></div>
     <div class="cf-name">Sales</div>
     <div class="cf-desc">Manage the whole sales cycle from first lead through to closed revenue in one unified platform.</div>
     <div class="cf-tags"><span class="cf-tag">CRM</span><span class="cf-tag">Pipeline</span><span class="cf-tag">Invoicing</span></div>
     <button type="button" data-book class="cf-arrow">Explore module</button>
    </div>

    <!-- 03 Marketing -->
    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10v4a1 1 0 001 1h2l6 4V5L6 9H4a1 1 0 00-1 1z"/><path d="M17 9a4 4 0 010 6"/><path d="M20 7a8 8 0 010 10"/></svg></div>
     <div class="cf-name">Marketing</div>
     <div class="cf-desc">Track campaigns and automate WhatsApp and email to improve customer engagement at scale.</div>
     <div class="cf-tags"><span class="cf-tag">Campaigns</span><span class="cf-tag">WhatsApp</span><span class="cf-tag">Nurturing</span></div>
     <button type="button" data-book class="cf-arrow">Explore module</button>
    </div>

    <!-- 04 Operations -->
    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/></svg></div>
     <div class="cf-name">Operations</div>
     <div class="cf-desc">Streamline everyday operations and vendor management with intelligent process automation.</div>
     <div class="cf-tags"><span class="cf-tag">Workflows</span><span class="cf-tag">Inventory</span><span class="cf-tag">Vendors</span></div>
     <button type="button" data-book class="cf-arrow">Explore module</button>
    </div>

    <!-- 05 Finance -->
    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M3 10h18"/><circle cx="16" cy="14.5" r="1.3" fill="currentColor" stroke="none"/></svg></div>
     <div class="cf-name">Finance</div>
     <div class="cf-desc">Centralize billing and expenses alongside financial reporting and accounting integrations.</div>
     <div class="cf-tags"><span class="cf-tag">Billing</span><span class="cf-tag">Expenses</span><span class="cf-tag">Reports</span></div>
     <button type="button" data-book class="cf-arrow">Explore module</button>
    </div>

    <!-- 06 HR -->
    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.5 2.7-6 6-6s6 2.5 6 6"/><circle cx="17" cy="9" r="2.6"/><path d="M15.4 20c.3-2.6 2-4.6 4.6-4.9"/></svg></div>
     <div class="cf-name">HR</div>
     <div class="cf-desc">Manage your people from attendance and payroll through to leave in one place.</div>
     <div class="cf-tags"><span class="cf-tag">Payroll</span><span class="cf-tag">Attendance</span><span class="cf-tag">Leave</span></div>
     <button type="button" data-book class="cf-arrow">Explore module</button>
    </div>

    <!-- 07 Inventory Management -->
    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/><path d="M12 11v10"/></svg></div>
     <div class="cf-name">Inventory Management</div>
     <div class="cf-desc">Track stock across every warehouse and channel with alerts before you run out.</div>
     <div class="cf-tags"><span class="cf-tag">Stock Levels</span><span class="cf-tag">Reorder Alerts</span><span class="cf-tag">Multiple warehouses</span></div>
     <button type="button" data-book class="cf-arrow">Explore module</button>
    </div>

   </div>
   <div class="sec-cta rv">
    <button type="button" data-book class="btn btn-black">Schedule a Consultation</button>
    <a href="#dashboards" class="btn btn-outline2">View Live Dashboards</a>
   </div>
  </div>
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

<!-- METHODOLOGY -->
<section id="method">

 <div class="mth-left">
 <div class="eyebrow rv"><span class="eyebrow-text">How We Work</span></div>
 <h2 class="sec-h rv">We don't build first. <span class="g">We understand first.</span></h2>
 <p class="sec-sub rv">Before a single line of code or campaign goes live, we audit how your business actually runs, so every system we build is measurable, automated, and built to scale.</p>

 <div class="fn-grid">
 <!-- 01 Audit -->
 <div class="fn-card">
  <div class="fn-icon" style="background:#1c9558;box-shadow:0 6px 20px rgba(0,0,0,.2)">
   <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><rect x="6" y="4" width="20" height="26" rx="2" fill="rgba(255,255,255,.55)"/><path d="M11 12 L21 12 M11 17 L18 17" stroke="rgba(20,133,90,.7)" stroke-width="2" stroke-linecap="round"/><circle cx="24" cy="26" r="8" fill="none" stroke="white" stroke-width="2.5"/><path d="M30 32 L36 38" stroke="white" stroke-width="2.5" stroke-linecap="round"/></svg>
  </div>
  <div class="fn-name">Audit</div>
  <div class="fn-desc">We map your current workflows, tools, and customer journey to find exactly what's slowing growth down.</div>
  <div class="fn-tags"><span class="fn-tag">Workflows</span><span class="fn-tag">Bottlenecks</span></div>
 </div>
 <!-- 02 Measure -->
 <div class="fn-card">
  <div class="fn-icon" style="background:#1c9558;box-shadow:0 6px 20px rgba(0,0,0,.2)">
   <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><path d="M6 26 A14 14 0 0 1 34 26" fill="none" stroke="rgba(255,255,255,.4)" stroke-width="3" stroke-linecap="round"/><path d="M6 26 A14 14 0 0 1 24 13" fill="none" stroke="white" stroke-width="3" stroke-linecap="round"/><line x1="20" y1="26" x2="27" y2="17" stroke="white" stroke-width="2.5" stroke-linecap="round"/><circle cx="20" cy="26" r="2.5" fill="white"/></svg>
  </div>
  <div class="fn-name">Measure</div>
  <div class="fn-desc">We set up KPIs, dashboards, and tracking so every decision from here on is backed by real data.</div>
  <div class="fn-tags"><span class="fn-tag">KPIs</span><span class="fn-tag">Dashboards</span></div>
 </div>
 <!-- 03 Automate -->
 <div class="fn-card">
  <div class="fn-icon" style="background:#1c9558;box-shadow:0 6px 20px rgba(0,0,0,.2)">
   <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><path d="M22 3 L9 21 L18 21 L16 37 L33 16 L23 16 Z" fill="white"/><path d="M22 3 L9 21 L18 21 L17 27" fill="none" stroke="rgba(20,133,90,.5)" stroke-width="1.6" stroke-linejoin="round"/></svg>
  </div>
  <div class="fn-name">Automate</div>
  <div class="fn-desc">We remove repetitive manual work (approvals, follow-ups, notifications) before we build anything new.</div>
  <div class="fn-tags"><span class="fn-tag">Approvals</span><span class="fn-tag">Follow-ups</span></div>
 </div>
 <!-- 04 Scale -->
 <div class="fn-card">
  <div class="fn-icon" style="background:#1c9558;box-shadow:0 6px 20px rgba(0,0,0,.2)">
   <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><rect x="14" y="14" width="12" height="12" rx="2" fill="rgba(255,255,255,.5)"/><path d="M24 4 L36 4 L36 16" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M36 4 L24 16" stroke="white" stroke-width="2.5" stroke-linecap="round"/><path d="M16 36 L4 36 L4 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 36 L16 24" stroke="white" stroke-width="2.5" stroke-linecap="round"/></svg>
  </div>
  <div class="fn-name">Scale</div>
  <div class="fn-desc">Only then do we build the ERP, CRM, website, or automation platform, designed to grow with you.</div>
  <div class="fn-tags"><span class="fn-tag">Custom ERP</span><span class="fn-tag">Automation</span></div>
 </div>
 </div>

 <div class="unify-stat rv">Understand <span class="g2">→</span> Measure <span class="g2">→</span> Automate <span class="g2">→</span> Scale.</div>

 <div class="sec-cta rv">
  <button type="button" data-book class="btn btn-black">Start With an Audit</button>
 </div>
 </div><!-- /mth-left -->
</section>

<!-- SOLUTIONS -->
<section id="solutions">
 <div class="eyebrow rv"><span class="eyebrow-text">Solutions</span></div>
 <h2 class="sec-h rv">Built for <span class="g">Growth</span> <span class="fade">in Three Ways</span></h2>
 <p class="sec-sub rv">Beyond the core platform, three focused solution tracks that plug straight into your operating system.</p>

 <div class="sol-grid">

 <!-- Custom Operational Solutions (ERP) -->
 <div class="sol-card">
  <div class="sol-head">
   <div class="sol-icon">
   <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect x="5" y="5" width="13" height="13" rx="2.5" fill="white"/><rect x="22" y="5" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.6)"/><rect x="5" y="22" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.6)"/><rect x="22" y="22" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.85)"/><path d="M18 11.5 L22 11.5 M11.5 18 L11.5 22 M28.5 18 L28.5 22 M18 28.5 L22 28.5" stroke="rgba(255,255,255,.5)" stroke-width="2" stroke-linecap="round"/></svg>
  </div>
   <div class="sol-label">ERP</div>
  </div>
  <div class="sol-name">Custom Operational Solutions</div>
  <div class="sol-tag">An ERP shaped around how you actually work.</div>
  <p class="sol-desc">Off-the-shelf ERP forces your team to bend to the software. We build the opposite. Modules are mapped to your real workflows, your approval chains, your terminology, deployed as a system you own outright.</p>
  <ul class="sol-list">
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Custom modules for your exact operating process</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Role-based access, approvals and audit trails</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Migration from spreadsheets and legacy systems</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Integrations with Tally, banking and GST filing</li>
  </ul>
  <div class="sol-metrics">
   <div><div class="sol-metric-v">100%</div><div class="sol-metric-l">Ownership</div></div>
   <div><div class="sol-metric-v">1</div><div class="sol-metric-l">Source of truth</div></div>
  </div>
  <a href="/custom-erp-solution" class="sol-arrow">Explore ERP</a>
 </div>

 <!-- Ecommerce Solutions -->
 <div class="sol-card">
  <div class="sol-head">
   <div class="sol-icon">
   <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><path d="M4 10 L10 10 L14 27 L32 27" stroke="rgba(255,255,255,.55)" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M11.5 14 L35 14 L32 24 L13.8 24 Z" fill="white"/><circle cx="16" cy="33" r="3" fill="rgba(255,255,255,.85)"/><circle cx="30" cy="33" r="3" fill="rgba(255,255,255,.85)"/></svg>
  </div>
   <div class="sol-label">Ecommerce</div>
  </div>
  <div class="sol-name">Ecommerce Solutions</div>
  <div class="sol-tag">From storefront to fulfilment, one connected stack.</div>
  <p class="sol-desc">Launch and scale an online store that talks directly to your inventory, billing, and delivery operations. No spreadsheets in between, no orders lost in the gap between platforms.</p>
  <ul class="sol-list">
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Shopify, WooCommerce and custom storefront builds</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Live inventory sync across every sales channel</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Automated order, invoice and GST workflows</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Payments, logistics and returns handled end to end</li>
  </ul>
  <div class="sol-metrics">
   <div><div class="sol-metric-v">3x</div><div class="sol-metric-l">Faster launch</div></div>
   <div><div class="sol-metric-v">0</div><div class="sol-metric-l">Manual entry</div></div>
  </div>
  <a href="/ecommerce-solutions" class="sol-arrow">Explore ecommerce</a>
 </div>

 <!-- Marketing Solutions -->
 <div class="sol-card">
  <div class="sol-head">
   <div class="sol-icon">
   <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><path d="M4 6 L36 6 L24 21 L24 34 L16 30 L16 21 Z" fill="white"/><path d="M16 21 L24 21 L24 27 L16 27 Z" fill="rgba(255,255,255,.55)"/><path d="M4 6 L36 6 L31 12 L9 12 Z" fill="rgba(255,255,255,.6)"/></svg>
  </div>
   <div class="sol-label">Marketing</div>
  </div>
  <div class="sol-name">Marketing Solutions</div>
  <div class="sol-tag">Fix the leak between lead and conversion.</div>
  <p class="sol-desc">Most businesses don't have a traffic problem. They have a follow-up problem. Two engines run the funnel. Organic search that compounds over time, and paid campaigns that buy demand on demand.</p>
  <ul class="sol-list">
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Technical SEO, Core Web Vitals and site architecture</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Content engine and on-page optimisation at scale</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Google, Meta and LinkedIn performance campaigns</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Instant WhatsApp and email follow-up on every lead</li>
  </ul>
  <div class="sol-metrics">
   <div><div class="sol-metric-v">4x</div><div class="sol-metric-l">Organic traffic</div></div>
   <div><div class="sol-metric-v">&lt;5 min</div><div class="sol-metric-l">Response time</div></div>
  </div>
  <a href="/marketing-solutions" class="sol-arrow">Explore marketing</a>
 </div>

 </div>
</section>

<!-- TECH STACK -->
<section id="tech" style="background:#0a1310;color:#fff">
 <!-- .tech-track is the tall scroll runway; .tech-pin is what actually sticks. Its
      height is set from the card count in JS so the pin releases once the last card
      has stacked. -->
 <div class="tech-track" id="techTrack">
  <div class="tech-pin" id="techPin">
  <div class="eyebrow rv"><span class="eyebrow-text" style="color:#34a87c">Technology Stack</span></div>
  <h2 class="sec-h rv" style="color:#fff"><span style="background:#34a87c;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">Built on Modern Infrastructure</span><br><span style="color:rgba(255,255,255,.22)">Unlocking Digital Potential</span></h2>
  <p class="sec-sub rv" style="color:rgba(255,255,255,.5)">Enterprise-grade technologies combining to create scalable, intelligent digital ecosystems.</p>
  <div class="tech-grid">

    <!-- ERP -->
    <div class="tech-card rv d1">
      <div class="tech-icon-new" style="background:#1c9558">
        <svg width="26" height="26" fill="none" viewBox="0 0 40 40">
          <rect x="4" y="4" width="14" height="14" rx="2" fill="rgba(255,255,255,0.9)"/>
          <rect x="22" y="4" width="14" height="14" rx="2" fill="rgba(255,255,255,0.5)"/>
          <rect x="4" y="22" width="14" height="14" rx="2" fill="rgba(255,255,255,0.5)"/>
          <rect x="22" y="22" width="14" height="14" rx="2" fill="rgba(255,255,255,0.7)"/>
          <line x1="18" y1="11" x2="22" y2="11" stroke="rgba(255,255,255,0.8)" stroke-width="2"/>
          <line x1="11" y1="18" x2="11" y2="22" stroke="rgba(255,255,255,0.8)" stroke-width="2"/>
          <line x1="29" y1="18" x2="29" y2="22" stroke="rgba(255,255,255,0.8)" stroke-width="2"/>
          <line x1="18" y1="29" x2="22" y2="29" stroke="rgba(255,255,255,0.8)" stroke-width="2"/>
        </svg>
      </div>
      <div class="tech-name">ERP Systems</div>
      <div class="tech-desc">Unified business backbone with all departments connected in a single source of truth with real-time data sync across every module.</div>
      <div class="tech-tags"><span class="t-tag">Multi-module</span><span class="t-tag">Real-time sync</span><span class="t-tag">Role-based access</span></div>
    </div>

    <!-- AI -->
    <div class="tech-card rv d2">
      <div class="tech-icon-new" style="background:#1c9558">
        <svg width="26" height="26" fill="none" viewBox="0 0 40 40">
          <circle cx="20" cy="20" r="7" fill="rgba(255,255,255,0.95)"/>
          <circle cx="20" cy="6" r="3" fill="rgba(255,255,255,0.6)"/>
          <circle cx="20" cy="34" r="3" fill="rgba(255,255,255,0.6)"/>
          <circle cx="6" cy="20" r="3" fill="rgba(255,255,255,0.6)"/>
          <circle cx="34" cy="20" r="3" fill="rgba(255,255,255,0.6)"/>
          <line x1="20" y1="9" x2="20" y2="13" stroke="rgba(255,255,255,0.7)" stroke-width="1.5"/>
          <line x1="20" y1="27" x2="20" y2="31" stroke="rgba(255,255,255,0.7)" stroke-width="1.5"/>
          <line x1="9" y1="20" x2="13" y2="20" stroke="rgba(255,255,255,0.7)" stroke-width="1.5"/>
          <line x1="27" y1="20" x2="31" y2="20" stroke="rgba(255,255,255,0.7)" stroke-width="1.5"/>
          <circle cx="11" cy="11" r="2.5" fill="rgba(255,255,255,0.4)"/>
          <circle cx="29" cy="11" r="2.5" fill="rgba(255,255,255,0.4)"/>
          <circle cx="11" cy="29" r="2.5" fill="rgba(255,255,255,0.4)"/>
          <circle cx="29" cy="29" r="2.5" fill="rgba(255,255,255,0.4)"/>
        </svg>
      </div>
      <div class="tech-name">AI Automation</div>
      <div class="tech-desc">Intelligent workflows that learn and adapt, eliminating repetitive tasks and surfacing actionable insights before you ask.</div>
      <div class="tech-tags"><span class="t-tag">Predictive AI</span><span class="t-tag">Auto-workflows</span><span class="t-tag">Smart alerts</span></div>
    </div>

    <!-- CRM -->
    <div class="tech-card rv d3">
      <div class="tech-icon-new" style="background:#1c9558">
        <svg width="26" height="26" fill="none" viewBox="0 0 40 40">
          <circle cx="14" cy="13" r="6" fill="rgba(255,255,255,0.9)"/>
          <circle cx="28" cy="10" r="4" fill="rgba(255,255,255,0.55)"/>
          <path d="M4 30c0-5.523 4.477-10 10-10s10 4.477 10 10" fill="rgba(255,255,255,0.7)"/>
          <path d="M28 24c3.314 0 6 2.686 6 6H22c0-3.314 2.686-6 6-6z" fill="rgba(255,255,255,0.4)"/>
          <rect x="30" y="20" width="8" height="2" rx="1" fill="rgba(255,255,255,0.5)"/>
          <rect x="32" y="24" width="6" height="2" rx="1" fill="rgba(255,255,255,0.5)"/>
        </svg>
      </div>
      <div class="tech-name">CRM Platform</div>
      <div class="tech-desc">360° customer management, from first touch to retention, with pipeline tracking, follow-up automation, and revenue forecasting.</div>
      <div class="tech-tags"><span class="t-tag">Lead scoring</span><span class="t-tag">Pipeline</span><span class="t-tag">Auto follow-up</span></div>
    </div>

    <!-- Analytics -->
    <div class="tech-card rv d1">
      <div class="tech-icon-new" style="background:#1c9558">
        <svg width="26" height="26" fill="none" viewBox="0 0 40 40">
          <rect x="4" y="28" width="6" height="8" rx="1" fill="rgba(255,255,255,0.5)"/>
          <rect x="13" y="20" width="6" height="16" rx="1" fill="rgba(255,255,255,0.7)"/>
          <rect x="22" y="12" width="6" height="24" rx="1" fill="rgba(255,255,255,0.9)"/>
          <rect x="31" y="16" width="6" height="20" rx="1" fill="rgba(255,255,255,0.6)"/>
          <polyline points="7,24 16,16 25,8 34,12" fill="none" stroke="rgba(255,255,255,0.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="34" cy="12" r="2.5" fill="white"/>
        </svg>
      </div>
      <div class="tech-name">Analytics Engine</div>
      <div class="tech-desc">Real-time dashboards and drill-down reporting across every function, turning raw data into strategic advantage.</div>
      <div class="tech-tags"><span class="t-tag">Live dashboards</span><span class="t-tag">Custom reports</span><span class="t-tag">KPI tracking</span></div>
    </div>

    <!-- Cloud -->
    <div class="tech-card rv d2">
      <div class="tech-icon-new" style="background:#1c9558">
        <svg width="26" height="26" fill="none" viewBox="0 0 40 40">
          <path d="M10 28a8 8 0 010-16 8.001 8.001 0 0115.32-3A7 7 0 1132 28z" fill="rgba(255,255,255,0.85)"/>
          <rect x="16" y="22" width="2" height="10" rx="1" fill="rgba(20,78,74,0.9)"/>
          <rect x="22" y="22" width="2" height="10" rx="1" fill="rgba(20,78,74,0.9)"/>
          <path d="M13 25l4-5 4 3 4-6" stroke="rgba(20,78,74,0.9)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="tech-name">Cloud Infrastructure</div>
      <div class="tech-desc">Enterprise-grade cloud with 99.9% uptime SLA, end-to-end encryption, and auto-scaling built for any load.</div>
      <div class="tech-tags"><span class="t-tag">99.9% uptime</span><span class="t-tag">Auto-scale</span><span class="t-tag">E2E encrypted</span></div>
    </div>

    <!-- Workflow -->
    <div class="tech-card rv d3">
      <div class="tech-icon-new" style="background:#1c9558">
        <svg width="26" height="26" fill="none" viewBox="0 0 40 40">
          <rect x="3" y="7" width="10" height="8" rx="2" fill="rgba(255,255,255,0.9)"/>
          <rect x="16" y="3" width="10" height="8" rx="2" fill="rgba(255,255,255,0.6)"/>
          <rect x="16" y="15" width="10" height="8" rx="2" fill="rgba(255,255,255,0.75)"/>
          <rect x="29" y="9" width="8" height="8" rx="2" fill="rgba(255,255,255,0.5)"/>
          <rect x="3" y="25" width="10" height="8" rx="2" fill="rgba(255,255,255,0.55)"/>
          <line x1="13" y1="11" x2="16" y2="7" stroke="rgba(255,255,255,0.8)" stroke-width="1.5"/>
          <line x1="13" y1="11" x2="16" y2="19" stroke="rgba(255,255,255,0.8)" stroke-width="1.5"/>
          <line x1="26" y1="7" x2="29" y2="13" stroke="rgba(255,255,255,0.7)" stroke-width="1.5"/>
          <line x1="26" y1="19" x2="29" y2="13" stroke="rgba(255,255,255,0.7)" stroke-width="1.5"/>
          <line x1="13" y1="29" x2="36" y2="29" stroke="rgba(255,255,255,0.4)" stroke-width="1.5" stroke-dasharray="2 2"/>
        </svg>
      </div>
      <div class="tech-name">Workflow Intelligence</div>
      <div class="tech-desc">Visual no-code workflow builder with triggers, conditions, and multi-step actions. Automate complex processes instantly.</div>
      <div class="tech-tags"><span class="t-tag">No-code builder</span><span class="t-tag">Triggers</span><span class="t-tag">Multi-step</span></div>
    </div>

  </div>
  <div class="sec-cta rv">
    <button type="button" data-book class="btn btn-black" style="background:#fff;color:#0a1310">Discuss Technical Requirements</button>
    <a href="#functions" class="btn btn-outline2" style="color:rgba(255,255,255,.7);border-color:rgba(255,255,255,.2)">View All Modules</a>
  </div>
  </div><!-- /tech-pin -->
 </div><!-- /tech-track -->
</section>

<!-- CASE STUDIES -->
<section id="cases">
 <div class="eyebrow rv"><span class="eyebrow-text" style="color:#32b46f">Case Studies</span></div>
 <h2 class="sec-h rv"><span style="color:#32b46f">Real Results</span> for <span class="fade">Real Businesses</span></h2>
 <p class="sec-sub rv">How Drawlead transforms operations across industries with measurable outcomes.</p>
 <div class="cases-scroll" id="casesScroll">
  <div class="cases-pin">
   <div class="cases-track" id="casesTrack">
 <div class="case-card" style="--acc:#38B976;--acc2:#9BE3C0;--atm:rgba(56,185,118,.18)">
  <div class="case-screen"><span class="case-orb" aria-hidden="true"></span></div>
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
  <div class="case-screen"><span class="case-orb" aria-hidden="true"></span></div>
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
  <div class="case-screen"><span class="case-orb" aria-hidden="true"></span></div>
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
  </div>
 </div><!-- /cases-scroll -->
 <div class="sec-cta rv">
 <button type="button" data-book class="btn btn-black">Start Your Success Story</button>
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
$indTheme = [
 'construction'  => ['#F97316','#FFB020','#1b0f04'],
 'healthcare'    => ['#2563EB','#22D3EE','#04122a'],
 'agencies'      => ['#EC4899','#F97316','#1b0a13'],
 'manufacturing' => ['#2563EB','#F59E0B','#0c1220'],
 'retail'        => ['#7C3AED','#EC4899','#150a1e'],
 'logistics'     => ['#0EA5E9','#14B8A6','#04141c'],
];
$indByKey = [];
foreach (industries_ordered() as $entry) { $indByKey[$entry['key']] = $entry['industry']; }
?>
<section id="industries">
 <div class="eyebrow rv"><span class="eyebrow-text" style="color:#14855a">Industries</span></div>
 <h2 class="sec-h rv">Built for <span style="color:#14855a">Your Industry</span></h2>
 <p class="sec-sub rv">Every industry has unique challenges. Drawlead adapts to your specific workflows, pain points, and compliance requirements, out of the box.</p>

 <div class="ind-scroll" id="indScroll">
  <div class="ind-viewport" id="indViewport">
  <?php $n = 0; foreach ($indStackOrder as $key):
   if (!isset($indByKey[$key])) { continue; }
   $ind = $indByKey[$key];
   $t   = $indTheme[$key];
   $n++;
  ?>
  <article class="ind-scard" style="--c1:<?= $t[0] ?>;--c2:<?= $t[1] ?>;--base:<?= $t[2] ?>">
   <div class="ind-scard-inner">

    <div class="ind-visual"><?= $ind['icon'] ?></div>

    <h3 class="ind-scard-title"><?= h($ind['name']) ?></h3>
    <div class="ind-scard-tag"><?= h($ind['tag']) ?></div>

    <div class="ind-block">
     <div class="ind-block-label"><span class="ind-rule ind-rule-p"></span>Common Problems</div>
     <?php foreach ($ind['problems'] as $problem): ?>
     <div class="ind-line"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg><?= h($problem) ?></div>
     <?php endforeach; ?>
    </div>

    <div class="ind-block">
     <div class="ind-block-label"><span class="ind-rule ind-rule-s"></span>Drawlead Solution</div>
     <?php foreach ($ind['solutions'] as $solution): ?>
     <div class="ind-line"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg><?= h($solution) ?></div>
     <?php endforeach; ?>
    </div>

    <a href="/industry-<?= h($key) ?>" class="ind-scard-cta">Explore <?= h($ind['name']) ?> OS</a>
   </div>
  </article>
  <?php endforeach; ?>
 </div>
 </div><!-- /ind-scroll -->

 <div class="sec-cta rv" style="margin-top:3rem">
 <button type="button" data-book class="btn btn-black">Find Your Industry Solution</button>
 <a href="#cases" class="btn btn-outline2">See Case Studies</a>
 </div>
</section>

<!-- WHY Drawlead -->
<section id="why">
 <h2 class="why-h rv">Why Drawlead</h2>

 <!-- Question mark is fixed; the magnet hangs from its lower curve on a thin string.
      The string + magnet live inside .why-pendulum, which rotates about its TOP edge,
      so the string always stays visually attached to the anchor point as it swings. -->
 <div class="why-hero rv">
  <div class="why-qmark" aria-hidden="true">?</div>
  <div class="why-pendulum" id="whyPendulum" aria-hidden="true">
   <span class="why-string"></span>
   <!-- Real logo artwork (transparent SVG). The inline SVG below is only a fallback if
        the file is ever missing, so the section can't render broken. -->
   <span class="why-magnet">
    <img class="why-magnet-img" src="/assets/img/drawlead-magnet-01.svg" alt=""
         onerror="this.remove();this.parentNode.classList.add('is-fallback');">
    <svg class="why-magnet-svg" viewBox="0 0 100 100" fill="none">
     <path d="M35 22 A28 28 0 1 1 30 76" stroke="#33B470" stroke-width="15" stroke-linecap="round"/>
     <line x1="34" y1="28" x2="19" y2="55" stroke="#33B470" stroke-width="7" stroke-linecap="round"/>
     <circle cx="16" cy="62" r="3.4" fill="#33B470"/>
     <circle cx="14" cy="70" r="2.9" fill="#33B470"/>
     <circle cx="13" cy="77" r="2.4" fill="#33B470"/>
    </svg>
   </span>
  </div>
 </div>

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
 <button type="button" data-book class="btn btn-black" style="background:#fff;color:#0a1310">Partner With Us</button>
 <a href="#cases" class="btn btn-outline2" style="color:rgba(255,255,255,.7);border-color:rgba(255,255,255,.2)">See Case Studies</a>
 </div>
</section>

<!-- DASHBOARDS -->
<section id="dashboards" style="background:var(--bg2)">
 <div class="eyebrow rv"><span class="eyebrow-text" style="color:#23a065">Platform Dashboards</span></div>
 <h2 class="sec-h rv">Every Module. <span class="fade">One Screen.</span></h2>
 <p class="sec-sub rv">Live ERP dashboards for every function. See exactly what Drawlead looks like in action.</p>
 <div class="dash-grid">

  <!-- SALES -->
  <div class="dash-card d1">
    <div class="dash-head">
      <div class="dash-ico" style="background:#1c9558;box-shadow:0 4px 12px rgba(50,180,111,.3)">
        <svg width="18" height="18" viewBox="0 0 36 36" fill="none"><rect x="3" y="22" width="6" height="10" rx="1" fill="rgba(255,255,255,.5)"/><rect x="12" y="14" width="6" height="18" rx="1" fill="rgba(255,255,255,.75)"/><rect x="21" y="6" width="6" height="26" rx="1" fill="white"/><polyline points="6,18 15,10 24,3" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="2" stroke-linecap="round"/><circle cx="24" cy="3" r="3" fill="white"/></svg>
      </div>
      <div class="dash-mod-name">Sales Pipeline</div>
    </div>
    <div class="dash-body">
      <div class="d-krow">
        <div class="d-k"><div class="d-kv" style="color:#32b46f">₹2.4Cr</div><div class="d-kl">Revenue</div></div>
        <div class="d-k"><div class="d-kv" style="color:#32b46f">↑ 28%</div><div class="d-kl">Growth</div></div>
        <div class="d-k"><div class="d-kv">247</div><div class="d-kl">Leads</div></div>
      </div>
      <div class="d-lbl">Monthly Revenue</div>
      <div class="d-bars">
        <div class="d-bar" style="height:40%"><span>J</span></div>
        <div class="d-bar" style="height:55%"><span>F</span></div>
        <div class="d-bar" style="height:48%"><span>M</span></div>
        <div class="d-bar" style="height:72%"><span>A</span></div>
        <div class="d-bar" style="height:64%"><span>M</span></div>
        <div class="d-bar" style="height:90%;background:#32b46f"><span style="color:#fff">J</span></div>
      </div>
      <div class="d-hr"></div>
      <div class="d-rows">
        <div class="d-row"><div class="d-dot" style="background:#32b46f"></div>Infra Corp Won<span class="d-val" style="color:#32b46f">₹12L</span></div>
        <div class="d-row"><div class="d-dot" style="background:#14855a"></div>MedPlus Proposal<span class="d-val" style="color:#14855a">₹8L</span></div>
        <div class="d-row"><div class="d-dot" style="background:#23a065"></div>LogiTrack Demo<span class="d-val" style="color:#23a065">₹6L</span></div>
      </div>
    </div>
  </div>

  <!-- FINANCE -->
  <div class="dash-card d2">
    <div class="dash-head">
      <div class="dash-ico" style="background:#1c9558;box-shadow:0 4px 12px rgba(50,180,111,.3)">
        <svg width="18" height="18" viewBox="0 0 36 36" fill="none"><ellipse cx="18" cy="10" rx="12" ry="4.5" fill="white"/><path d="M6 10 Q6 17 18 17 Q30 17 30 10" fill="rgba(255,255,255,.7)"/><path d="M6 17 Q6 24 18 24 Q30 24 30 17" fill="rgba(255,255,255,.45)"/><path d="M6 24 Q6 31 18 31 Q30 31 30 24" fill="rgba(255,255,255,.25)"/></svg>
      </div>
      <div class="dash-mod-name">Finance and Billing</div>
    </div>
    <div class="dash-body">
      <div class="d-krow">
        <div class="d-k"><div class="d-kv" style="color:#32b46f">₹86L</div><div class="d-kl">Invoiced</div></div>
        <div class="d-k"><div class="d-kv" style="color:#32b46f">₹72L</div><div class="d-kl">Collected</div></div>
        <div class="d-k"><div class="d-kv" style="color:#14855a">₹14L</div><div class="d-kl">Pending</div></div>
      </div>
      <div class="d-lbl">Collection Funnel</div>
      <div class="d-funnel">
        <div class="d-fbar" style="width:100%;background:rgba(50,180,111,.12);color:#32b46f">Leads · 847</div>
        <div class="d-fbar" style="width:72%;background:rgba(50,180,111,.2);color:#32b46f">Qualified · 612</div>
        <div class="d-fbar" style="width:48%;background:rgba(50,180,111,.32);color:#32b46f">Proposals · 406</div>
        <div class="d-fbar" style="width:28%;background:#32b46f;color:#fff">Closed · 237</div>
      </div>
      <div class="d-rows">
        <div class="d-row"><div class="d-dot" style="background:#32b46f"></div>GST filed on time<span class="d-val" style="color:#32b46f">✓</span></div>
        <div class="d-row"><div class="d-dot" style="background:#14855a"></div>3 invoices overdue<span class="d-val" style="color:#14855a">!</span></div>
      </div>
    </div>
  </div>

  <!-- OPERATIONS -->
  <div class="dash-card d3">
    <div class="dash-head">
      <div class="dash-ico" style="background:#1c9558;box-shadow:0 4px 12px rgba(50,180,111,.3)">
        <svg width="18" height="18" viewBox="0 0 36 36" fill="none"><circle cx="18" cy="18" r="6.5" fill="white"/><circle cx="18" cy="18" r="3" fill="rgba(50,180,111,.7)"/><rect x="16" y="2" width="4" height="6" rx="2" fill="rgba(255,255,255,.85)"/><rect x="16" y="28" width="4" height="6" rx="2" fill="rgba(255,255,255,.85)"/><rect x="2" y="16" width="6" height="4" rx="2" fill="rgba(255,255,255,.85)"/><rect x="28" y="16" width="6" height="4" rx="2" fill="rgba(255,255,255,.85)"/></svg>
      </div>
      <div class="dash-mod-name">Operations</div>
    </div>
    <div class="dash-body">
      <div class="d-krow">
        <div class="d-k"><div class="d-kv">1,248</div><div class="d-kl">Tasks</div></div>
        <div class="d-k"><div class="d-kv" style="color:#32b46f">94%</div><div class="d-kl">On Time</div></div>
        <div class="d-k"><div class="d-kv">38</div><div class="d-kl">Vendors</div></div>
      </div>
      <div class="d-lbl">Task Status</div>
      <div class="d-status">
        <div class="d-sbox" style="background:rgba(50,180,111,.08);border:1px solid rgba(50,180,111,.2)"><div class="d-sv" style="color:#32b46f">842</div><div class="d-sl">Done</div></div>
        <div class="d-sbox" style="background:rgba(35,160,101,.08);border:1px solid rgba(35,160,101,.2)"><div class="d-sv" style="color:#23a065">284</div><div class="d-sl">Active</div></div>
        <div class="d-sbox" style="background:rgba(20,133,90,.08);border:1px solid rgba(20,133,90,.2)"><div class="d-sv" style="color:#14855a">104</div><div class="d-sl">Review</div></div>
        <div class="d-sbox" style="background:rgba(50,180,111,.06);border:1px solid rgba(50,180,111,.15)"><div class="d-sv" style="color:#32b46f">18</div><div class="d-sl">Late</div></div>
      </div>
      <div class="d-rows">
        <div class="d-row"><div class="d-dot" style="background:#32b46f"></div>Warehouse restock done<span class="d-val" style="color:#32b46f">✓</span></div>
        <div class="d-row"><div class="d-dot" style="background:#14855a"></div>Vendor delay Site B<span class="d-val" style="color:#14855a">Alert</span></div>
      </div>
    </div>
  </div>

  <!-- HR -->
  <div class="dash-card d1">
    <div class="dash-head">
      <div class="dash-ico" style="background:#1c9558;box-shadow:0 4px 12px rgba(50,180,111,.3)">
        <svg width="18" height="18" viewBox="0 0 36 36" fill="none"><circle cx="13" cy="11" r="6" fill="white"/><circle cx="25" cy="13" r="4.5" fill="rgba(255,255,255,.6)"/><path d="M1 32 C1 23 7 20 13 20 C19 20 25 23 25 32 Z" fill="rgba(255,255,255,.8)"/><path d="M25 26 C25 23 28 21 31 21 C34 21 36 23 36 26 L36 32 L25 32 Z" fill="rgba(255,255,255,.4)"/></svg>
      </div>
      <div class="dash-mod-name">HR and Payroll</div>
    </div>
    <div class="dash-body">
      <div class="d-krow">
        <div class="d-k"><div class="d-kv">248</div><div class="d-kl">Staff</div></div>
        <div class="d-k"><div class="d-kv" style="color:#32b46f">97.4%</div><div class="d-kl">Present</div></div>
        <div class="d-k"><div class="d-kv" style="color:#14855a">₹34L</div><div class="d-kl">Payroll</div></div>
      </div>
      <div class="d-lbl">Dept. Headcount</div>
      <div class="d-hbars">
        <div class="d-hbar"><span>Engineering</span><div class="d-track"><div class="d-fill" style="width:80%;background:#32b46f"></div></div><span>80</span></div>
        <div class="d-hbar"><span>Sales</span><div class="d-track"><div class="d-fill" style="width:60%;background:#23a065"></div></div><span>60</span></div>
        <div class="d-hbar"><span>Operations</span><div class="d-track"><div class="d-fill" style="width:52%;background:#14855a"></div></div><span>52</span></div>
        <div class="d-hbar"><span>Finance</span><div class="d-track"><div class="d-fill" style="width:36%;background:#32b46f"></div></div><span>36</span></div>
        <div class="d-hbar"><span>HR</span><div class="d-track"><div class="d-fill" style="width:20%;background:#14855a"></div></div><span>20</span></div>
      </div>
    </div>
  </div>

  <!-- MARKETING -->
  <div class="dash-card d2">
    <div class="dash-head">
      <div class="dash-ico" style="background:#1c9558;box-shadow:0 4px 12px rgba(50,180,111,.3)">
        <svg width="18" height="18" viewBox="0 0 36 36" fill="none"><path d="M4 13 L4 23 L10 23 L10 13 Z" fill="rgba(255,255,255,.6)"/><path d="M10 13 L28 5 L28 31 L10 23 Z" fill="white"/><path d="M30 14 Q36 18 30 22" fill="none" stroke="rgba(255,255,255,.75)" stroke-width="2.5" stroke-linecap="round"/></svg>
      </div>
      <div class="dash-mod-name">Marketing</div>
    </div>
    <div class="dash-body">
      <div class="d-krow">
        <div class="d-k"><div class="d-kv">14</div><div class="d-kl">Campaigns</div></div>
        <div class="d-k"><div class="d-kv" style="color:#32b46f">↑ 44%</div><div class="d-kl">Engage.</div></div>
        <div class="d-k"><div class="d-kv">8.2K</div><div class="d-kl">Reach</div></div>
      </div>
      <div class="d-lbl">Channel Performance</div>
      <div class="d-channels">
        <div class="d-ch">
          <div class="d-ch-ico" style="background:rgba(50,180,111,.1)"><svg fill="none" stroke="#32b46f" stroke-width="2" viewBox="0 0 24 24"><path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg></div>
          <div class="d-ch-name">Email</div><div class="d-ch-pct">42%</div>
          <div class="d-ch-track"><div class="d-ch-fill" style="width:42%;background:#32b46f"></div></div>
        </div>
        <div class="d-ch">
          <div class="d-ch-ico" style="background:rgba(35,160,101,.1)"><svg fill="none" stroke="#23a065" stroke-width="2" viewBox="0 0 24 24"><path d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501"/></svg></div>
          <div class="d-ch-name">WhatsApp</div><div class="d-ch-pct">31%</div>
          <div class="d-ch-track"><div class="d-ch-fill" style="width:31%;background:#23a065"></div></div>
        </div>
        <div class="d-ch">
          <div class="d-ch-ico" style="background:rgba(20,133,90,.1)"><svg fill="none" stroke="#14855a" stroke-width="2" viewBox="0 0 24 24"><path d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3"/></svg></div>
          <div class="d-ch-name">Social</div><div class="d-ch-pct">27%</div>
          <div class="d-ch-track"><div class="d-ch-fill" style="width:27%;background:#14855a"></div></div>
        </div>
      </div>
    </div>
  </div>

  <!-- MANAGEMENT -->
  <div class="dash-card d3">
    <div class="dash-head">
      <div class="dash-ico" style="background:#1c9558;box-shadow:0 4px 12px rgba(50,180,111,.3)">
        <svg width="18" height="18" viewBox="0 0 36 36" fill="none"><rect x="3" y="3" width="13" height="13" rx="2" fill="white" opacity=".9"/><rect x="20" y="3" width="13" height="13" rx="2" fill="rgba(255,255,255,.55)"/><rect x="3" y="20" width="13" height="13" rx="2" fill="rgba(255,255,255,.55)"/><rect x="20" y="20" width="13" height="13" rx="2" fill="rgba(255,255,255,.75)"/><line x1="16" y1="9.5" x2="20" y2="9.5" stroke="rgba(255,255,255,.7)" stroke-width="1.5"/><line x1="9.5" y1="16" x2="9.5" y2="20" stroke="rgba(255,255,255,.7)" stroke-width="1.5"/><line x1="26.5" y1="16" x2="26.5" y2="20" stroke="rgba(255,255,255,.7)" stroke-width="1.5"/><line x1="16" y1="26.5" x2="20" y2="26.5" stroke="rgba(255,255,255,.7)" stroke-width="1.5"/></svg>
      </div>
      <div class="dash-mod-name">Management Overview</div>
    </div>
    <div class="dash-body">
      <div class="d-krow">
        <div class="d-k"><div class="d-kv" style="color:#32b46f">92</div><div class="d-kl">KPI Score</div></div>
        <div class="d-k"><div class="d-kv" style="color:#32b46f">↑ 18%</div><div class="d-kl">Efficiency</div></div>
        <div class="d-k"><div class="d-kv">7/7</div><div class="d-kl">Modules</div></div>
      </div>
      <div class="d-lbl">Business Health Radar</div>
      <svg viewBox="0 0 140 106" width="100%" height="96" style="margin-bottom:10px">
        <polygon points="70,10 116,36 116,76 70,102 24,76 24,36" fill="none" stroke="var(--border)" stroke-width="1.5"/>
        <polygon points="70,26 100,44 100,70 70,86 40,70 40,44" fill="none" stroke="var(--border)" stroke-width="1"/>
        <polygon points="70,42 84,52 84,64 70,72 56,64 56,52" fill="none" stroke="var(--border)" stroke-width="1"/>
        <polygon points="70,14 112,38 110,74 70,98 30,74 28,38" fill="rgba(50,180,111,.1)" stroke="#32b46f" stroke-width="1.8"/>
        <text x="70" y="60" text-anchor="middle" fill="#32b46f" font-size="12" font-family="Montserrat,sans-serif" font-weight="800">92</text>
      </svg>
      <div class="d-rows">
        <div class="d-row"><div class="d-dot" style="background:#32b46f"></div>Sales 94%<span class="d-val" style="color:#32b46f">↑</span></div>
        <div class="d-row"><div class="d-dot" style="background:#14855a"></div>Finance 95%<span class="d-val" style="color:#14855a">↑</span></div>
      </div>
    </div>
  </div>

 </div><!-- /dash-grid -->
 <div class="sec-cta rv" style="margin-top:3rem">
 <button type="button" data-book class="btn btn-black">Book a Live Demo</button>
 <button type="button" data-book class="btn btn-outline2">Schedule Consultation</button>
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
// Industry Dashboard Switcher: one window, data-driven, auto-rotates
const industries = [
 {
 name:'Ecommerce', accentColor:'#23a065',
 title:'ECOMMERCE ORDERS AND REVENUE OVERVIEW',
 kpis:[{v:'842',l:'Orders Today'},{v:'₹18.4L',l:'GMV'},{v:'3.8%',l:'Conversion'},{v:'₹2,140',l:'Avg Order'}],
 chartLabel:'Daily Orders', chartBars:[58,66,50,82,74,95],
 aiLabel:'AI INSIGHT',
 aiText:'Cart abandonment at 68% on mobile checkout. Recovering 240 carts could add ₹5.1L this month.',
 },
 {
 name:'Hospital', accentColor:'#32b46f',
 title:'HOSPITAL PATIENT AND OPERATIONS OVERVIEW',
 kpis:[{v:'1,248',l:'Patients Today'},{v:'284',l:'Inpatients'},{v:'46',l:'Surgeries'},{v:'98.4%',l:'Satisfaction'}],
 chartLabel:'Daily OPD Footfall', chartBars:[60,72,55,88,80,95],
 aiLabel:'AI MONITOR',
 aiText:'Dengue cases ↑ 18% this week. Suggest allocating 12 extra general ward beds.',
 },
 {
 name:'Jewellery', accentColor:'#23a065',
 title:'JEWELLERY SALES AND STOCK OVERVIEW',
 kpis:[{v:'₹3.2Cr',l:'Monthly Sales'},{v:'↑ 22%',l:'YoY Growth'},{v:'1,840',l:'SKUs Active'},{v:'96%',l:'Order Fulfill'}],
 chartLabel:'Weekly Sales (₹ Lakhs)', chartBars:[55,62,48,80,70,95],
 aiLabel:'AI INSIGHT',
 aiText:'Navaratri season predicts a 40% spike in necklace demand over the next 14 days.',
 },
 {
 name:'Manufacturing', accentColor:'#14855a',
 title:'MANUFACTURING PRODUCTION OVERVIEW',
 kpis:[{v:'4,280',l:'Units Today'},{v:'↑ 12%',l:'Output'},{v:'99.1%',l:'Quality Rate'},{v:'6',l:'Active Lines'}],
 chartLabel:'Daily Production Units', chartBars:[62,78,55,90,85,95],
 aiLabel:'AI PREDICT',
 aiText:'Line 4 bearing wear detected. Schedule maintenance in 48h to avoid stoppage.',
 },
 {
 name:'Construction', accentColor:'#14855a',
 title:'CONSTRUCTION PROJECT OVERVIEW',
 kpis:[{v:'₹8.4Cr',l:'Total Projects'},{v:'↑ 18%',l:'On-Time Rate'},{v:'247',l:'Workers'},{v:'38',l:'Vendors'}],
 chartLabel:'Project Progress', chartBars:[55,70,48,80,95,40],
 aiLabel:'AI ALERT',
 aiText:'Site B at 95% budget. Predict overrun in 12 days.',
 },
];

let currentIdx = 0;
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function hexToRgb(c) {
 const m = c.match(/^#([0-9a-f]{6})$/i);
 if(!m) return '50,180,111';
 const n = parseInt(m[1],16);
 return [(n>>16)&255,(n>>8)&255,n&255].join(',');
}

function buildDashBody(d) {
 return `
 <div class="dw-head">
 <div class="dw-title">${d.title}</div>
 <div class="dw-meta">
 <span class="dw-live" style="background:rgba(${hexToRgb(d.accentColor)},.12);color:${d.accentColor}">Live</span>
 <span class="dw-month">May 2025</span>
 </div>
 </div>
 <div class="dw-kpis">
 ${d.kpis.map(k=>`<div class="dw-kpi"><div class="dw-kv">${k.v}</div><div class="dw-kl">${k.l}</div></div>`).join('')}
 </div>
 <div class="dw-chart">
 <div class="dw-chart-label">${d.chartLabel}</div>
 <div class="dw-chart-bars">
 ${d.chartBars.map((h,i)=>`<div class="dw-bar" style="height:${h}%;background:${i===d.chartBars.length-1?d.accentColor:`rgba(${hexToRgb(d.accentColor)},${0.18+i*0.11})`}"></div>`).join('')}
 </div>
 </div>
 <div class="dw-ai" style="border-color:${d.accentColor}">
 <div class="dw-ai-label" style="color:${d.accentColor}">${d.aiLabel}</div>
 <div class="dw-ai-text">${d.aiText}</div>
 </div>`;
}

function renderDash(idx) {
 const d = industries[idx];
 document.getElementById('dwBody').innerHTML = buildDashBody(d);
 document.querySelectorAll('.ind-tab').forEach((t,i)=>t.classList.toggle('active', i===idx));
}

function switchDash(idx) {
 if(idx === currentIdx) return;
 currentIdx = idx;
 const win = document.getElementById('dashWindow');
 if(reduceMotion){ renderDash(idx); return; }
 win.classList.add('switching');
 setTimeout(()=>{
 renderDash(idx);
 win.classList.remove('switching');
 }, 300);
}

// Auto-cycle the dashboard, Ecommerce → Hospital → Jewellery → Manufacturing → Construction → repeat.
// The tab progress indicator was removed, and with it the 60ms tick that only existed to
// paint it. Rotation is unchanged: still one industry every CYCLE_MS, driven directly.
const CYCLE_MS = 5500;

if(!reduceMotion){
 setInterval(() => switchDash((currentIdx + 1) % industries.length), CYCLE_MS);
}

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

 const STICKY_TOP = 84; // must match .cf-scroll-sticky's CSS top offset (clears the fixed nav)
 let overflow = 0;

 function measure(){
 overflow = Math.max(0, row.scrollWidth - sticky.clientWidth);
 outer.style.height = (sticky.offsetHeight + overflow) + 'px';
 }

 function onScroll(){
 if(overflow <= 0){ row.style.transform = 'translateX(0)'; return; }
 const rect = outer.getBoundingClientRect();
 const progress = Math.min(1, Math.max(0, (STICKY_TOP - rect.top) / overflow));
 row.style.transform = `translateX(${-progress * overflow}px)`;
 }

 measure();
 onScroll();
 window.addEventListener('resize', ()=>{ measure(); onScroll(); });
 window.addEventListener('scroll', onScroll, { passive: true });
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

 initStickyStack('#solutions .sol-card', { dimTo: 0.30, shrink: 0.04 });

 // Tech: pinned section, cards stack horizontally toward the right
 // Vertical scroll through the tall .tech-track drives a single progress value; each
 // card derives its own offset from it. Upcoming cards wait off to the left, the active
 // card sits flush, and superseded cards slide RIGHT and tuck behind the active one.
 (function(){
  const track = document.getElementById('techTrack');
  const pin   = document.getElementById('techPin');
  const cards = Array.from(document.querySelectorAll('#tech .tech-card'));
  if(!track || !pin || cards.length < 2) return;

  const STEP_VH   = 0.62;  // vertical scroll (in viewports) per card
  const ENTER_X   = 120;   // px an upcoming card waits to the left
  const STACK_X   = 46;    // px each superseded card shifts right
  const MAX_STACK = 3;     // cap the rightward pile so it can't leave the viewport
  let ticking = false;

  function measure(){
   // tallest card defines the shared slot height, so nothing is clipped
   let h = 0;
   cards.forEach(c => { h = Math.max(h, c.offsetHeight); });
   if(h) document.querySelector('#tech .tech-grid').style.setProperty('--tech-card-h', h + 'px');
   // runway: one viewport to pin + STEP_VH per additional card
   track.style.height = (pin.offsetHeight + (cards.length - 1) * STEP_VH * window.innerHeight) + 'px';
  }

  function update(){
   ticking = false;
   const runway = track.offsetHeight - pin.offsetHeight;
   if(runway <= 0) return;

   const scrolled = -track.getBoundingClientRect().top;
   let p = scrolled / runway;
   p = p < 0 ? 0 : (p > 1 ? 1 : p);

   const pos = p * (cards.length - 1);   // fractional "active card" index

   cards.forEach(function(card, i){
    const d = pos - i;                   // <0 upcoming, 0 active, >0 superseded
    let x, opacity, scale;

    if(d <= 0){                          // waiting to the left
     x = d * ENTER_X;
     opacity = 1 + d;                    // fades out the further left it waits
     scale = 1;
    } else {                             // superseded, slide right, tuck behind
     const dd = d > MAX_STACK ? MAX_STACK : d;
     x = dd * STACK_X;
     opacity = 1 - d * 0.42;
     scale = 1 - dd * 0.05;
    }

    opacity = opacity < 0 ? 0 : (opacity > 1 ? 1 : opacity);
    card.style.transform = 'translateX(' + x.toFixed(1) + 'px) scale(' + scale.toFixed(3) + ')';
    card.style.opacity   = opacity.toFixed(3);
    card.style.zIndex    = String(100 - Math.round(Math.abs(d) * 10));
    card.style.pointerEvents = (d > -0.5 && d < 0.5) ? 'auto' : 'none';
   });
  }

  function onScroll(){ if(!ticking){ ticking = true; requestAnimationFrame(update); } }

  measure(); update();
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', function(){ measure(); update(); });
  window.addEventListener('load', function(){ measure(); update(); });
 })();

 // How We Work: Audit → Measure → Automate → Scale, with the ambient green glow
 // strengthening as the active stage advances (CSS reads [data-stage]).
 const methodSection = document.getElementById('method');
 initStickyStack('#method .fn-card', {
  dimTo: 0.25, shrink: 0.05,
  onStage: function(i){ if(methodSection) methodSection.dataset.stage = i; }
 });

})();

// Section 8: industries centred 3-up card stack
// The tall .ind-scroll runway supplies scroll distance; .ind-viewport pins inside it.
// Scroll progress maps to a fractional "active index", and every card is placed by its
// signed distance d from that index:  d<0 -> left, d==0 -> centre, d>0 -> right.
// Because d is continuous, cards glide between the three slots rather than snapping.
(function(){
 const scroller = document.getElementById('indScroll');
 const viewport = document.getElementById('indViewport');
 if(!scroller || !viewport) return;
 if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

 const cards = Array.from(viewport.querySelectorAll('.ind-scard'));
 if(!cards.length) return;

 const last = cards.length - 1;
 let ticking = false;

 function layout(){
  ticking = false;

  const rect = scroller.getBoundingClientRect();
  // total distance the runway scrolls past while pinned
  const span = scroller.offsetHeight - viewport.offsetHeight;
  let p = span > 0 ? (-rect.top / span) : 0;
  p = p < 0 ? 0 : (p > 1 ? 1 : p);

  const activeF = p * last;                      // fractional active index
  const stepX = Math.max(96, cards[0].offsetWidth * 0.42);  // side-slot offset

  for(let i = 0; i < cards.length; i++){
   const card = cards[i];
   const d = i - activeF;                        // <0 left, 0 centre, >0 right
   const ad = Math.abs(d);

   if(ad > 2.2){ card.style.visibility = 'hidden'; continue; }
   card.style.visibility = 'visible';

   const clamped = ad > 1 ? 1 + (ad - 1) * 0.35 : ad;   // fold distant cards inward
   const x = Math.sign(d) * Math.min(clamped, 1.6) * stepX;
   const scale = Math.max(0.72, 1 - ad * 0.09);         // centre 1, sides ~0.91
   const op = ad <= 1 ? 1 - ad * 0.45 : Math.max(0, 0.55 - (ad - 1) * 0.55);

   card.style.transform = 'translate(calc(-50% + ' + x.toFixed(1) + 'px), -50%) scale(' + scale.toFixed(3) + ')';
   card.style.opacity = op.toFixed(3);
   card.style.zIndex = String(100 - Math.round(ad * 10));  // centre always on top
  }
 }

 function onScroll(){ if(!ticking){ ticking = true; requestAnimationFrame(layout); } }

 layout();
 window.addEventListener('scroll', onScroll, { passive: true });
 window.addEventListener('resize', onScroll);
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


// Section 9: hanging magnet: real-time cursor-driven pendulum physics
// A rigid pendulum pivoting at the string's top anchor. Every frame we integrate:
//   gravity restoring torque + cursor repulsion torque - damping
// Crucially the cursor force is evaluated in the ANIMATION LOOP from the last known
// pointer position (not inside the mousemove handler) so a stationary cursor keeps
// pushing the magnet and it settles into a deflected equilibrium, rather than the force
// vanishing the moment the mouse stops moving.
(function(){
 const pend   = document.getElementById('whyPendulum');
 const hero   = document.querySelector('#why .why-hero');
 const magnet = document.querySelector('#why .why-magnet');
 const string = document.querySelector('#why .why-string');
 if(!pend || !hero || !magnet || !string) return;
 if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

 // touch devices have no hover cursor, leave the magnet at a gentle idle sway
 const coarse = window.matchMedia('(pointer: coarse)').matches;

 // state
 let theta = 0.08;   // arm angle, radians (0 = hanging straight down)
 let omega = 0;      // angular velocity
 let tilt  = 0;      // magnet's own rotation, lags the arm for a bit of extra weight
 let stretch = 1;    // string stretch under load

 // tuning
 const G      = 30;    // gravity restoring strength
 const DAMP   = 0.991; // energy bleed per step -> gradual settle, never abrupt
 const MAXA   = 0.66;  // clamp so it stays premium, not flailing
 const RADIUS = 240;   // cursor influence radius (px)
 // Expressed in the SAME units as G (angular acceleration), so the two are directly
 // comparable. Dividing this by the arm length (as a torque would be) made it ~13x
 // weaker than gravity and the magnet barely moved.
 const PUSH   = 28;    // repulsion strength

 let mx = -9999, my = -9999, haveMouse = false;
 let mvx = 0, prevMx = -9999, prevT = 0;   // cursor velocity -> stronger swing when fast

 if(!coarse){
  window.addEventListener('mousemove', function(e){
   const now = performance.now();
   if(prevMx !== -9999 && now > prevT){
    // px/ms, smoothed; drives the "move fast = push harder" behaviour
    mvx = mvx * 0.7 + ((e.clientX - prevMx) / (now - prevT)) * 0.3;
   }
   prevMx = e.clientX; prevT = now;
   mx = e.clientX; my = e.clientY; haveMouse = true;
  }, { passive: true });
 }

 // arm length: string + half the magnet, i.e. pivot -> magnet centre
 let L = 100;
 function measure(){
  L = string.offsetHeight + magnet.offsetHeight / 2;
 }
 measure();
 window.addEventListener('resize', measure);

 let running = true;
 if('IntersectionObserver' in window){
  new IntersectionObserver(function(en){ running = en[0].isIntersecting; }, { threshold: 0 })
   .observe(hero);
 }

 let prev = performance.now();
 function frame(now){
  const dt = Math.min((now - prev) / 1000, 0.04);   // clamp: tab-switch can't explode it
  prev = now;

  if(running){
   // pivot in viewport coords. Derived from the hero box + the pendulum's CSS top, NOT
   // from pend.getBoundingClientRect(), that element rotates, so its box would shift.
   const hr = hero.getBoundingClientRect();
   const pivotX = hr.left + hr.width / 2;
   const pivotY = hr.top + pend.offsetTop;

   // where the magnet actually hangs right now
   const mgX = pivotX + Math.sin(theta) * L;
   const mgY = pivotY + Math.cos(theta) * L;

   let alpha = -G * Math.sin(theta);        // gravity pulls it back to vertical
   let load  = 0;

   if(haveMouse && !coarse){
    const dx = mgX - mx, dy = mgY - my;
    const dist = Math.hypot(dx, dy) || 0.001;

    if(dist < RADIUS){
     // quadratic falloff, gentle at the edge, firm up close
     const falloff = (1 - dist / RADIUS);
     const speedBoost = 1 + Math.min(Math.abs(mvx) * 1.6, 2.2);
     const F = PUSH * falloff * falloff * speedBoost;

     // repel along cursor -> magnet, then project onto the arm's tangent, because only
     // the tangential component can rotate a pendulum
     const ux = dx / dist, uy = dy / dist;
     const tx = Math.cos(theta), ty = -Math.sin(theta);   // unit tangent
     alpha += F * (ux * tx + uy * ty);
     load = falloff;
    }
   }

   omega += alpha * dt;
   omega *= DAMP;
   theta += omega * dt;

   if(theta >  MAXA){ theta =  MAXA; omega *= -0.35; }   // soft limits
   if(theta < -MAXA){ theta = -MAXA; omega *= -0.35; }

   // secondary motion: the magnet lags the arm slightly, and the string takes up load
   tilt    += ((omega * 2.6) - tilt) * 0.08;
   stretch += ((1 + load * 0.07 + Math.abs(omega) * 0.012) - stretch) * 0.12;

   pend.style.transform   = 'translateX(-50%) rotate(' + (theta * 57.2958).toFixed(3) + 'deg)';
   magnet.style.transform = 'rotate(' + Math.max(-14, Math.min(14, tilt * 57.2958 * 0.12)).toFixed(2) + 'deg)';
   string.style.transform = 'scaleY(' + stretch.toFixed(3) + ')';
  }
  requestAnimationFrame(frame);
 }
 requestAnimationFrame(frame);
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
