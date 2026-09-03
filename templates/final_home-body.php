<?php
/**
 * Final Home — the redesigned homepage, served at /final-home as a real
 * DB-managed page (Admin -> Pages). Fully self-contained: no external
 * libraries or image files. Its stylesheet is /assets/final-home.css
 * (linked below), all animations are the vanilla scripts at the bottom of
 * this file, and every section backdrop is CSS gradients — so it renders
 * completely on its own and the live "/" homepage is untouched.
 */
$activePage = 'final_home';
include __DIR__ . '/partials/nav.php';
?>
<link rel="stylesheet" href="<?= asset_url('/assets/final-home.css') ?>">

<!-- FINAL-HOME BODY START -->

<section id="hero">

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
  <div class="hero-left">
   <div class="hero-eyebrow">
    <span class="hero-eicon"></span>
    <span class="hero-etxt">One OS. All Operations. Infinite Possibilities.</span>
   </div>

   <h1 class="hero-h">One <span class="grad-os">OS</span> with <span class="grad-ai">AI</span> to every function of your <span class="grad-os">business</span></h1>

   <p class="hero-p">Drawlead — the operating system for modern business. Unify ERP, AI automation, analytics, and cloud workflows into one intelligent platform built for India's growing businesses.</p>

   <div class="hero-btns">
    <button type="button" data-book class="btn btn-primary">Start Free Consultation →</button>
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
      <div class="dw-title">ECOMMERCE OS — ORDERS &amp; REVENUE OVERVIEW</div>
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
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Core Platform</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">The <span class="g">7 Functions</span> of Business <span class="fade">— Unified</span></h2>
 <p class="sec-sub rv">Every core business function, streamlined and intelligently connected through one operating system.</p>

 <div class="cf-scroll-outer" id="cfScrollOuter">
  <div class="cf-scroll-sticky" id="cfScrollSticky">
   <div class="cf-row" id="cfRow">

    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg></div>
     <div class="cf-num">01 — Management</div>
     <div class="cf-name">Management</div>
     <div class="cf-desc">Centralized dashboards and operational visibility for faster, smarter business decisions.</div>
     <div class="cf-tags"><span class="cf-tag">KPI Tracking</span><span class="cf-tag">Analytics</span><span class="cf-tag">Approvals</span></div>
     <button type="button" data-book class="cf-arrow">Explore module →</button>
    </div>

    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polyline points="3,17 9,11 13,15 21,7"/><polyline points="14,7 21,7 21,14"/></svg></div>
     <div class="cf-num">02 — Sales</div>
     <div class="cf-name">Sales</div>
     <div class="cf-desc">Manage leads, pipelines, customers, and revenue operations from one unified platform.</div>
     <div class="cf-tags"><span class="cf-tag">CRM</span><span class="cf-tag">Pipeline</span><span class="cf-tag">Invoicing</span></div>
     <button type="button" data-book class="cf-arrow">Explore module →</button>
    </div>

    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10v4a1 1 0 001 1h2l6 4V5L6 9H4a1 1 0 00-1 1z"/><path d="M17 9a4 4 0 010 6"/><path d="M20 7a8 8 0 010 10"/></svg></div>
     <div class="cf-num">03 — Marketing</div>
     <div class="cf-name">Marketing</div>
     <div class="cf-desc">Track campaigns, automate WhatsApp &amp; email, and improve customer engagement at scale.</div>
     <div class="cf-tags"><span class="cf-tag">Campaigns</span><span class="cf-tag">WhatsApp</span><span class="cf-tag">Nurturing</span></div>
     <button type="button" data-book class="cf-arrow">Explore module →</button>
    </div>

    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/></svg></div>
     <div class="cf-num">04 — Operations</div>
     <div class="cf-name">Operations</div>
     <div class="cf-desc">Streamline activities, inventory, and vendor management with intelligent process automation.</div>
     <div class="cf-tags"><span class="cf-tag">Workflows</span><span class="cf-tag">Inventory</span><span class="cf-tag">Vendors</span></div>
     <button type="button" data-book class="cf-arrow">Explore module →</button>
    </div>

    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M3 10h18"/><circle cx="16" cy="14.5" r="1.3" fill="currentColor" stroke="none"/></svg></div>
     <div class="cf-num">05 — Finance</div>
     <div class="cf-name">Finance</div>
     <div class="cf-desc">Centralize billing, expenses, financial reporting, and accounting integrations seamlessly.</div>
     <div class="cf-tags"><span class="cf-tag">Billing</span><span class="cf-tag">Expenses</span><span class="cf-tag">Reports</span></div>
     <button type="button" data-book class="cf-arrow">Explore module →</button>
    </div>

    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.5 2.7-6 6-6s6 2.5 6 6"/><circle cx="17" cy="9" r="2.6"/><path d="M15.4 20c.3-2.6 2-4.6 4.6-4.9"/></svg></div>
     <div class="cf-num">06 — Human Resources</div>
     <div class="cf-name">HR</div>
     <div class="cf-desc">Manage employees, attendance, payroll workflows, and leave management efficiently.</div>
     <div class="cf-tags"><span class="cf-tag">Payroll</span><span class="cf-tag">Attendance</span><span class="cf-tag">Leave</span></div>
     <button type="button" data-book class="cf-arrow">Explore module →</button>
    </div>

    <div class="cf-card">
     <div class="cf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/><path d="M12 11v10"/></svg></div>
     <div class="cf-num">07 — Inventory</div>
     <div class="cf-name">Inventory Management</div>
     <div class="cf-desc">Track stock across every warehouse and channel, with alerts before you run out.</div>
     <div class="cf-tags"><span class="cf-tag">Stock Levels</span><span class="cf-tag">Reorder Alerts</span><span class="cf-tag">Multi-warehouse</span></div>
     <button type="button" data-book class="cf-arrow">Explore module →</button>
    </div>

   </div>
  </div>
 </div>

 <div class="sec-cta rv">
 <button type="button" data-book class="btn btn-black">Schedule a Consultation →</button>
 <a href="#dashboards" class="btn btn-outline2">View Live Dashboards</a>
 </div>
</section>

<!-- APP CHAOS -> SMART BOARD -->
<section id="unify">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">The Problem</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Stop running your business from <span class="g">a dozen different tabs</span></h2>
 <p class="sec-sub rv">Google Sheets, CRM, WhatsApp, Notion, billing software, phone calls — your business data is scattered everywhere. Drawlead brings it all into one Smart Board.</p>

 <div class="phys-stage rv" id="physStage">
  <div class="phys-pill" data-variant="green">Notion</div>
  <div class="phys-pill" data-variant="dark">CRM</div>
  <div class="phys-pill" data-variant="light">Calls</div>
  <div class="phys-pill" data-variant="dark">Bill Book</div>
  <div class="phys-pill" data-variant="green">Google Docs</div>
  <div class="phys-pill" data-variant="light">WhatsApp</div>
  <div class="phys-pill" data-variant="dark">Gmail</div>
  <div class="phys-pill" data-variant="green">Google Sheets</div>
 </div>

 <div class="sec-cta rv">
  <button type="button" data-book class="btn btn-black">Explore the Platform →</button>
 </div>
</section>

<!-- METHODOLOGY -->
<section id="method">

 <div class="mth-left">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">How We Work</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">We don't build first. <span class="g">We understand first.</span></h2>
 <p class="sec-sub rv">Before a single line of code or campaign goes live, we audit how your business actually runs — so every system we build is measurable, automated, and built to scale.</p>

 <div class="fn-grid">
 <div class="fn-card">
  <div class="fn-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(0,0,0,.2)">
   <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><rect x="6" y="4" width="20" height="26" rx="2" fill="rgba(255,255,255,.55)"/><path d="M11 12 L21 12 M11 17 L18 17" stroke="rgba(20,133,90,.7)" stroke-width="2" stroke-linecap="round"/><circle cx="24" cy="26" r="8" fill="none" stroke="white" stroke-width="2.5"/><path d="M30 32 L36 38" stroke="white" stroke-width="2.5" stroke-linecap="round"/></svg>
  </div>
  <div class="fn-name">Audit</div>
  <div class="fn-desc">We map your current workflows, tools, and customer journey to find exactly what's slowing growth down.</div>
  <div class="fn-tags"><span class="fn-tag">Workflows</span><span class="fn-tag">Bottlenecks</span></div>
 </div>
 <div class="fn-card">
  <div class="fn-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(0,0,0,.2)">
   <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><path d="M6 26 A14 14 0 0 1 34 26" fill="none" stroke="rgba(255,255,255,.4)" stroke-width="3" stroke-linecap="round"/><path d="M6 26 A14 14 0 0 1 24 13" fill="none" stroke="white" stroke-width="3" stroke-linecap="round"/><line x1="20" y1="26" x2="27" y2="17" stroke="white" stroke-width="2.5" stroke-linecap="round"/><circle cx="20" cy="26" r="2.5" fill="white"/></svg>
  </div>
  <div class="fn-name">Measure</div>
  <div class="fn-desc">We set up KPIs, dashboards, and tracking so every decision from here on is backed by real data.</div>
  <div class="fn-tags"><span class="fn-tag">KPIs</span><span class="fn-tag">Dashboards</span></div>
 </div>
 <div class="fn-card">
  <div class="fn-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(0,0,0,.2)">
   <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><path d="M22 3 L9 21 L18 21 L16 37 L33 16 L23 16 Z" fill="white"/><path d="M22 3 L9 21 L18 21 L17 27" fill="none" stroke="rgba(20,133,90,.5)" stroke-width="1.6" stroke-linejoin="round"/></svg>
  </div>
  <div class="fn-name">Automate</div>
  <div class="fn-desc">We remove repetitive manual work — approvals, follow-ups, notifications — before we build anything new.</div>
  <div class="fn-tags"><span class="fn-tag">Approvals</span><span class="fn-tag">Follow-ups</span></div>
 </div>
 <div class="fn-card">
  <div class="fn-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(0,0,0,.2)">
   <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><rect x="14" y="14" width="12" height="12" rx="2" fill="rgba(255,255,255,.5)"/><path d="M24 4 L36 4 L36 16" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M36 4 L24 16" stroke="white" stroke-width="2.5" stroke-linecap="round"/><path d="M16 36 L4 36 L4 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 36 L16 24" stroke="white" stroke-width="2.5" stroke-linecap="round"/></svg>
  </div>
  <div class="fn-name">Scale</div>
  <div class="fn-desc">Only then do we build the ERP, CRM, website, or automation platform — designed to grow with you.</div>
  <div class="fn-tags"><span class="fn-tag">Custom ERP</span><span class="fn-tag">Automation</span></div>
 </div>
 </div>

 <div class="unify-stat rv">Understand <span class="g2">→</span> Measure <span class="g2">→</span> Automate <span class="g2">→</span> Scale.</div>

 <div class="sec-cta rv">
  <button type="button" data-book class="btn btn-black">Start With an Audit →</button>
 </div>
 </div><!-- /mth-left -->
</section>

<!-- SOLUTIONS -->
<section id="solutions">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Solutions</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Built for <span class="g">Growth</span> <span class="fade">— Three Ways</span></h2>
 <p class="sec-sub rv">Beyond the core platform, three focused solution tracks that plug straight into your operating system.</p>

 <div class="sol-grid">

 <div class="sol-card">
  <div class="sol-head">
   <div class="sol-icon">
   <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect x="5" y="5" width="13" height="13" rx="2.5" fill="white"/><rect x="22" y="5" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.6)"/><rect x="5" y="22" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.6)"/><rect x="22" y="22" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.85)"/><path d="M18 11.5 L22 11.5 M11.5 18 L11.5 22 M28.5 18 L28.5 22 M18 28.5 L22 28.5" stroke="rgba(255,255,255,.5)" stroke-width="2" stroke-linecap="round"/></svg>
  </div>
   <div class="sol-label">Solution 01 · ERP</div>
  </div>
  <div class="sol-name">Custom Operational Solutions</div>
  <div class="sol-tag">An ERP shaped around how you actually work.</div>
  <p class="sol-desc">Off-the-shelf ERP forces your team to bend to the software. We build the opposite — modules mapped to your real workflows, your approval chains, your terminology, deployed as a system you own outright.</p>
  <ul class="sol-list">
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Custom modules for your exact operating process</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Role-based access, approvals &amp; audit trails</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Migration from spreadsheets &amp; legacy systems</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Integrations with Tally, banking &amp; GST filing</li>
  </ul>
  <div class="sol-metrics">
   <div><div class="sol-metric-v">100%</div><div class="sol-metric-l">Ownership</div></div>
   <div><div class="sol-metric-v">1</div><div class="sol-metric-l">Source of truth</div></div>
  </div>
  <a href="/custom-erp-solution" class="sol-arrow">Explore ERP →</a>
 </div>

 <div class="sol-card">
  <div class="sol-head">
   <div class="sol-icon">
   <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><path d="M4 10 L10 10 L14 27 L32 27" stroke="rgba(255,255,255,.55)" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M11.5 14 L35 14 L32 24 L13.8 24 Z" fill="white"/><circle cx="16" cy="33" r="3" fill="rgba(255,255,255,.85)"/><circle cx="30" cy="33" r="3" fill="rgba(255,255,255,.85)"/></svg>
  </div>
   <div class="sol-label">Solution 02</div>
  </div>
  <div class="sol-name">Ecommerce Solutions</div>
  <div class="sol-tag">From storefront to fulfilment — one connected stack.</div>
  <p class="sol-desc">Launch and scale an online store that talks directly to your inventory, billing, and delivery operations. No spreadsheets in between, no orders lost in the gap between platforms.</p>
  <ul class="sol-list">
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Shopify, WooCommerce &amp; custom storefront builds</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Live inventory sync across every sales channel</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Automated order, invoice &amp; GST workflows</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Payments, logistics &amp; returns handled end to end</li>
  </ul>
  <div class="sol-metrics">
   <div><div class="sol-metric-v">3&times;</div><div class="sol-metric-l">Faster launch</div></div>
   <div><div class="sol-metric-v">0</div><div class="sol-metric-l">Manual entry</div></div>
  </div>
  <a href="/ecommerce-solutions" class="sol-arrow">Explore ecommerce →</a>
 </div>

 <div class="sol-card">
  <div class="sol-head">
   <div class="sol-icon">
   <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><path d="M4 6 L36 6 L24 21 L24 34 L16 30 L16 21 Z" fill="white"/><path d="M16 21 L24 21 L24 27 L16 27 Z" fill="rgba(255,255,255,.55)"/><path d="M4 6 L36 6 L31 12 L9 12 Z" fill="rgba(255,255,255,.6)"/></svg>
  </div>
   <div class="sol-label">Solution 03</div>
  </div>
  <div class="sol-name">Marketing Solutions</div>
  <div class="sol-tag">Fix the leak between lead and conversion.</div>
  <p class="sol-desc">Most businesses don't have a traffic problem — they have a follow-up problem. Two engines run the funnel: organic search that compounds over time, and paid campaigns that buy demand on demand.</p>
  <ul class="sol-list">
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Technical SEO, Core Web Vitals &amp; site architecture</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Content engine &amp; on-page optimisation at scale</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Google, Meta &amp; LinkedIn performance campaigns</li>
   <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Instant WhatsApp &amp; email follow-up on every lead</li>
  </ul>
  <div class="sol-metrics">
   <div><div class="sol-metric-v">4&times;</div><div class="sol-metric-l">Organic traffic</div></div>
   <div><div class="sol-metric-v">&lt;5 min</div><div class="sol-metric-l">Response time</div></div>
  </div>
  <a href="/marketing-solutions" class="sol-arrow">Explore marketing →</a>
 </div>

 </div>
</section>

<!-- TECH STACK -->
<section id="tech" style="background:#0a1310;color:#fff">
 <div class="tech-track" id="techTrack">
  <div class="tech-pin" id="techPin">
  <div class="eyebrow rv"><div class="eyebrow-line" style="background:#34a87c"></div><span class="eyebrow-text" style="color:#34a87c">Technology Stack</span><div class="eyebrow-line" style="background:#34a87c"></div></div>
  <h2 class="sec-h rv" style="color:#fff"><span style="background:linear-gradient(115deg,#4ecb87,#34a87c);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">Built on Modern Infrastructure</span><br><span style="color:rgba(255,255,255,.22)">Unlocking Digital Potential</span></h2>
  <p class="sec-sub rv" style="color:rgba(255,255,255,.5)">Enterprise-grade technologies combining to create scalable, intelligent digital ecosystems.</p>
  <div class="tech-grid">

    <div class="tech-card rv d1">
      <div class="tech-icon-new" style="background:linear-gradient(135deg,#32b46f,#14855a)">
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
      <div class="tech-desc">Unified business backbone — all departments connected in a single source of truth with real-time data sync across every module.</div>
      <div class="tech-tags"><span class="t-tag">Multi-module</span><span class="t-tag">Real-time sync</span><span class="t-tag">Role-based access</span></div>
    </div>

    <div class="tech-card rv d2">
      <div class="tech-icon-new" style="background:linear-gradient(135deg,#32b46f,#14855a)">
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
      <div class="tech-desc">Intelligent workflows that learn and adapt — eliminating repetitive tasks and surfacing actionable insights before you ask.</div>
      <div class="tech-tags"><span class="t-tag">Predictive AI</span><span class="t-tag">Auto-workflows</span><span class="t-tag">Smart alerts</span></div>
    </div>

    <div class="tech-card rv d3">
      <div class="tech-icon-new" style="background:linear-gradient(135deg,#32b46f,#14855a)">
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
      <div class="tech-desc">360° customer management — first touch to retention with pipeline tracking, follow-up automation, and revenue forecasting.</div>
      <div class="tech-tags"><span class="t-tag">Lead scoring</span><span class="t-tag">Pipeline</span><span class="t-tag">Auto follow-up</span></div>
    </div>

    <div class="tech-card rv d1">
      <div class="tech-icon-new" style="background:linear-gradient(135deg,#32b46f,#14855a)">
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
      <div class="tech-desc">Real-time dashboards and drill-down reporting across every function — turning raw data into strategic advantage.</div>
      <div class="tech-tags"><span class="t-tag">Live dashboards</span><span class="t-tag">Custom reports</span><span class="t-tag">KPI tracking</span></div>
    </div>

    <div class="tech-card rv d2">
      <div class="tech-icon-new" style="background:linear-gradient(135deg,#32b46f,#14855a)">
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

    <div class="tech-card rv d3">
      <div class="tech-icon-new" style="background:linear-gradient(135deg,#32b46f,#14855a)">
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
      <div class="tech-desc">Visual no-code workflow builder with triggers, conditions, and multi-step actions — automate complex processes instantly.</div>
      <div class="tech-tags"><span class="t-tag">No-code builder</span><span class="t-tag">Triggers</span><span class="t-tag">Multi-step</span></div>
    </div>

  </div>
  <div class="sec-cta rv">
    <button type="button" data-book class="btn btn-black" style="background:#fff;color:#0a1310">Discuss Technical Requirements →</button>
    <a href="#functions" class="btn btn-outline2" style="color:rgba(255,255,255,.7);border-color:rgba(255,255,255,.2)">View All Modules</a>
  </div>
  </div><!-- /tech-pin -->
 </div><!-- /tech-track -->
</section>

<!-- CASE STUDIES -->
<section id="cases">
 <div class="eyebrow rv"><div class="eyebrow-line" style="background:#32b46f"></div><span class="eyebrow-text" style="color:#32b46f">Case Studies</span><div class="eyebrow-line" style="background:#32b46f"></div></div>
 <h2 class="sec-h rv"><span style="color:#32b46f">Real Results</span> for <span class="fade">Real Businesses</span></h2>
 <p class="sec-sub rv">How Drawlead transforms operations across industries with measurable outcomes.</p>
 <div class="cases-scroll" id="casesScroll">
  <div class="cases-pin">
   <div class="cases-track" id="casesTrack">
 <div class="case-card" style="--acc:#38B976;--acc2:#9BE3C0;--atm:rgba(56,185,118,.18)">
  <div class="case-screen"><span class="case-orb" aria-hidden="true"></span></div>
  <div class="case-body">
    <span class="case-tag">Construction &amp; Real Estate</span>
    <div class="case-title">Construction ERP Solution</div>
    <ul class="case-list">
      <li>Better operational visibility across all project sites</li>
      <li>Faster reporting workflows and billing automation</li>
      <li>Improved multi-site project management controls</li>
    </ul>
    <button type="button" data-book class="btn btn-outline2 btn-sm" style="margin-top:1.1rem;align-self:flex-start">Read Case Study →</button>
  </div>
 </div>
 <div class="case-card" style="--acc:#2FB5AE;--acc2:#A8E6E2;--atm:rgba(47,181,174,.18)">
  <div class="case-screen"><span class="case-orb" aria-hidden="true"></span></div>
  <div class="case-body">
    <span class="case-tag">Healthcare &amp; Wellness</span>
    <div class="case-title">Multi-Brand Physiotherapy Management</div>
    <ul class="case-list">
      <li>Streamlined clinic workflows across branches</li>
      <li>Improved scheduling efficiency and capacity</li>
      <li>Centralized billing and cross-branch reporting</li>
    </ul>
    <button type="button" data-book class="btn btn-outline2 btn-sm" style="margin-top:1.1rem;align-self:flex-start">Read Case Study →</button>
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
    <button type="button" data-book class="btn btn-outline2 btn-sm" style="margin-top:1.1rem;align-self:flex-start">Read Case Study →</button>
  </div>
 </div>
   </div><!-- /cases-track -->
  </div>
 </div><!-- /cases-scroll -->
 <div class="sec-cta rv">
 <button type="button" data-book class="btn btn-black">Start Your Success Story →</button>
 </div>
</section>

<!-- INDUSTRIES -->
<?php
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
 <div class="eyebrow rv"><div class="eyebrow-line" style="background:#14855a"></div><span class="eyebrow-text" style="color:#14855a">Industries</span><div class="eyebrow-line" style="background:#14855a"></div></div>
 <h2 class="sec-h rv">Built for <span style="color:#14855a">Your Industry</span></h2>
 <p class="sec-sub rv">Every industry has unique challenges. Drawlead adapts to your specific workflows, pain points, and compliance requirements — out of the box.</p>

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

    <a href="/industry-<?= h($key) ?>" class="ind-scard-cta">Explore <?= h($ind['name']) ?> OS <span>&rsaquo;</span></a>
   </div>
  </article>
  <?php endforeach; ?>
 </div>
 </div><!-- /ind-scroll -->

 <div class="sec-cta rv" style="margin-top:3rem">
 <button type="button" data-book class="btn btn-black">Find Your Industry Solution →</button>
 <a href="#cases" class="btn btn-outline2">See Case Studies</a>
 </div>
</section>

<!-- WHY Drawlead -->
<section id="why">
 <h2 class="why-h rv">Why Drawlead</h2>

 <div class="why-hero rv">
  <div class="why-qmark" aria-hidden="true">?</div>
  <div class="why-pendulum" id="whyPendulum" aria-hidden="true">
   <span class="why-string"></span>
   <span class="why-magnet">
    <svg class="why-magnet-svg" viewBox="0 0 100 100" fill="none">
     <defs>
      <linearGradient id="fhMagGrad" x1="0" y1="0" x2="1" y2="1">
       <stop offset="0%" stop-color="#4ecb87"/>
       <stop offset="100%" stop-color="#14855a"/>
      </linearGradient>
     </defs>
     <path d="M26 18 V46 A24 24 0 0 0 74 46 V18" stroke="url(#fhMagGrad)" stroke-width="15" stroke-linecap="round" fill="none"/>
     <rect x="18.5" y="14" width="15" height="12" rx="2" fill="#e9f9f0"/>
     <rect x="66.5" y="14" width="15" height="12" rx="2" fill="#1e6b47"/>
    </svg>
   </span>
  </div>
 </div>

 <p class="why-lead rv">We're not just software — we're a long-term partner in your digital transformation and growth.</p>

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
   <div class="why-fdesc">Built for startups, SMEs, and enterprises — scales exactly as your business grows.</div>
  </div>
  <div class="why-feat rv d4">
   <div class="why-fico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 3.5v6c0 5-3.4 8.6-8 10.5-4.6-1.9-8-5.5-8-10.5v-6L12 2z"/><polyline points="8.5,12 11,14.5 15.5,9.5"/></svg></div>
   <div class="why-fname">Secure &amp; Reliable</div>
   <div class="why-fdesc">Enterprise-grade security, 99.9% uptime SLA, and end-to-end encryption on all data.</div>
  </div>
  <div class="why-feat rv d1">
   <div class="why-fico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 11q4-4 7 0l3 3 3-3q3-4 7 0"/><path d="M2 15q4-4 7 0l3 3 3-3q3-4 7 0"/></svg></div>
   <div class="why-fname">Long-Term Partnership</div>
   <div class="why-fdesc">Continuous support, updates, and future-proofing — we grow and evolve with your needs.</div>
  </div>
 </div>

 <div class="sec-cta rv">
 <button type="button" data-book class="btn btn-black" style="background:#fff;color:#0a1310">Partner With Us →</button>
 <a href="#cases" class="btn btn-outline2" style="color:rgba(255,255,255,.7);border-color:rgba(255,255,255,.2)">See Case Studies</a>
 </div>
</section>

<!-- DASHBOARDS -->
<section id="dashboards" style="background:var(--bg2)">
 <div class="eyebrow rv"><div class="eyebrow-line" style="background:#23a065"></div><span class="eyebrow-text" style="color:#23a065">Platform Dashboards</span><div class="eyebrow-line" style="background:#23a065"></div></div>
 <h2 class="sec-h rv">Every Module. <span class="fade">One Screen.</span></h2>
 <p class="sec-sub rv">Live ERP dashboards for every function — see exactly what Drawlead looks like in action.</p>
 <div class="dash-grid">

  <div class="dash-card d1">
    <div class="dash-head">
      <div class="dash-ico" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 4px 12px rgba(50,180,111,.3)">
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
        <div class="d-row"><div class="d-dot" style="background:#32b46f"></div>Infra Corp — Won<span class="d-val" style="color:#32b46f">₹12L</span></div>
        <div class="d-row"><div class="d-dot" style="background:#14855a"></div>MedPlus — Proposal<span class="d-val" style="color:#14855a">₹8L</span></div>
        <div class="d-row"><div class="d-dot" style="background:#23a065"></div>LogiTrack — Demo<span class="d-val" style="color:#23a065">₹6L</span></div>
      </div>
    </div>
  </div>

  <div class="dash-card d2">
    <div class="dash-head">
      <div class="dash-ico" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 4px 12px rgba(50,180,111,.3)">
        <svg width="18" height="18" viewBox="0 0 36 36" fill="none"><ellipse cx="18" cy="10" rx="12" ry="4.5" fill="white"/><path d="M6 10 Q6 17 18 17 Q30 17 30 10" fill="rgba(255,255,255,.7)"/><path d="M6 17 Q6 24 18 24 Q30 24 30 17" fill="rgba(255,255,255,.45)"/><path d="M6 24 Q6 31 18 31 Q30 31 30 24" fill="rgba(255,255,255,.25)"/></svg>
      </div>
      <div class="dash-mod-name">Finance &amp; Billing</div>
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

  <div class="dash-card d3">
    <div class="dash-head">
      <div class="dash-ico" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 4px 12px rgba(50,180,111,.3)">
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
        <div class="d-row"><div class="d-dot" style="background:#14855a"></div>Vendor delay — Site B<span class="d-val" style="color:#14855a">Alert</span></div>
      </div>
    </div>
  </div>

  <div class="dash-card d1">
    <div class="dash-head">
      <div class="dash-ico" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 4px 12px rgba(50,180,111,.3)">
        <svg width="18" height="18" viewBox="0 0 36 36" fill="none"><circle cx="13" cy="11" r="6" fill="white"/><circle cx="25" cy="13" r="4.5" fill="rgba(255,255,255,.6)"/><path d="M1 32 C1 23 7 20 13 20 C19 20 25 23 25 32 Z" fill="rgba(255,255,255,.8)"/><path d="M25 26 C25 23 28 21 31 21 C34 21 36 23 36 26 L36 32 L25 32 Z" fill="rgba(255,255,255,.4)"/></svg>
      </div>
      <div class="dash-mod-name">HR &amp; Payroll</div>
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

  <div class="dash-card d2">
    <div class="dash-head">
      <div class="dash-ico" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 4px 12px rgba(50,180,111,.3)">
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

  <div class="dash-card d3">
    <div class="dash-head">
      <div class="dash-ico" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 4px 12px rgba(50,180,111,.3)">
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
        <div class="d-row"><div class="d-dot" style="background:#32b46f"></div>Sales 94%<span class="d-val" style="color:#32b46f">✓</span></div>
        <div class="d-row"><div class="d-dot" style="background:#14855a"></div>Finance 95%<span class="d-val" style="color:#14855a">✓</span></div>
      </div>
    </div>
  </div>

 </div><!-- /dash-grid -->
 <div class="sec-cta rv" style="margin-top:3rem">
 <button type="button" data-book class="btn btn-black">Book a Live Demo →</button>
 <button type="button" data-book class="btn btn-outline2">Schedule Consultation</button>
 </div>
</section>

<?php
// CTA INTRO — continuous letter train
$ciHeadLines = [
 ['text' => 'Ready to Transform', 'green' => []],
 ['text' => 'Your Business ERP',  'green' => ['ERP']],
 ['text' => 'with AI?',           'green' => ['AI?']],
];
$ciTrainText  = 'Ready to Transform Your Business ERP with AI?';
$ciTrainGreen = ['ERP', 'AI?'];
?>
<section id="cta-intro">
 <div class="ci-pin">

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

 <div class="ci-train" aria-hidden="true">
  <div class="ci-track">
   <?php
   $cells = [];
   foreach (explode(' ', $ciTrainText) as $wi => $word) {
       $isGreen = in_array($word, $ciTrainGreen, true);
       foreach (preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
           $cells[] = ['ch' => $ch, 'green' => $isGreen];
       }
       $cells[] = ['ch' => ' ', 'green' => false];
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

 </div><!-- /ci-pin -->
</section>
<!-- CTA -->
<section id="cta" class="cta-framed">
 <div class="cta-card">
  <div class="cta-grid-bg"></div>
  <div class="cta-glow"></div>

  <div class="cta-eyebrow rv"><span class="cta-eyebrow-dot"></span>Start Your ERP Journey</div>
  <h2 class="cta-h rv">Build your<br><span class="fade">business</span> <span class="gr">ERP</span><br><span class="gr2">OS</span> with <span class="gr3">AI</span></h2>
  <p class="cta-p rv">Digitize, automate, and scale with Drawlead. Start with a free consultation — no commitment needed.</p>
  <div class="cta-btns rv">
  <button type="button" data-book class="cta-btn-w">Schedule Free Consultation →</button>
  <button type="button" data-book class="cta-btn-g">Book a Product Demo</button>
  </div>
  <div class="cta-note rv">செயலை மாற்றும் · Intelligent Operating System · Secure · Scalable · Future-Ready</div>
 </div>
</section>

<!-- FINAL-HOME BODY END -->

<?php include __DIR__ . '/partials/footer.php'; ?>

<!-- FINAL-HOME SCRIPT START -->
<script>
// Industry Dashboard Switcher — one window, data-driven, auto-rotates
const industries = [
 {
 name:'Ecommerce', accentColor:'#23a065',
 title:'ECOMMERCE OS — ORDERS &amp; REVENUE OVERVIEW',
 kpis:[{v:'842',l:'Orders Today'},{v:'₹18.4L',l:'GMV'},{v:'3.8%',l:'Conversion'},{v:'₹2,140',l:'Avg Order'}],
 chartLabel:'Daily Orders', chartBars:[58,66,50,82,74,95],
 aiLabel:'AI INSIGHT',
 aiText:'Cart abandonment at 68% on mobile checkout. Recovering 240 carts could add ₹5.1L this month.',
 },
 {
 name:'Hospital', accentColor:'#32b46f',
 title:'HOSPITAL OS — PATIENT &amp; OPERATIONS OVERVIEW',
 kpis:[{v:'1,248',l:'Patients Today'},{v:'284',l:'Inpatients'},{v:'46',l:'Surgeries'},{v:'98.4%',l:'Satisfaction'}],
 chartLabel:'Daily OPD Footfall', chartBars:[60,72,55,88,80,95],
 aiLabel:'AI MONITOR',
 aiText:'Dengue cases ↑ 18% this week. Suggest allocating 12 extra general ward beds.',
 },
 {
 name:'Jewellery', accentColor:'#23a065',
 title:'JEWELLERY OS — SALES &amp; STOCK OVERVIEW',
 kpis:[{v:'₹3.2Cr',l:'Monthly Sales'},{v:'↑ 22%',l:'YoY Growth'},{v:'1,840',l:'SKUs Active'},{v:'96%',l:'Order Fulfill'}],
 chartLabel:'Weekly Sales (₹ Lakhs)', chartBars:[55,62,48,80,70,95],
 aiLabel:'AI INSIGHT',
 aiText:'Navaratri season — predict 40% spike in necklace demand next 14 days.',
 },
 {
 name:'Manufacturing', accentColor:'#14855a',
 title:'MANUFACTURING OS — PRODUCTION OVERVIEW',
 kpis:[{v:'4,280',l:'Units Today'},{v:'↑ 12%',l:'Output'},{v:'99.1%',l:'Quality Rate'},{v:'6',l:'Active Lines'}],
 chartLabel:'Daily Production Units', chartBars:[62,78,55,90,85,95],
 aiLabel:'AI PREDICT',
 aiText:'Line 4 bearing wear detected. Schedule maintenance in 48h to avoid stoppage.',
 },
 {
 name:'Construction', accentColor:'#14855a',
 title:'CONSTRUCTION OS — PROJECT OVERVIEW',
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

const CYCLE_MS = 5500;

if(!reduceMotion){
 setInterval(() => switchDash((currentIdx + 1) % industries.length), CYCLE_MS);
}

// Core Functions — sticky horizontal scroll
(function(){
 const outer = document.getElementById('cfScrollOuter');
 const sticky = document.getElementById('cfScrollSticky');
 const row = document.getElementById('cfRow');
 if(!outer || !sticky || !row) return;

 const skipHijack = reduceMotion || window.matchMedia('(max-width:768px)').matches;
 if(skipHijack) return;

 const STICKY_TOP = 84;
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
</script>
<script>
// Sticky card stacks — scroll-linked overlap -> fade -> next becomes active
(function(){
 const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

 function initStickyStack(selector, opts){
  const cards = Array.from(document.querySelectorAll(selector));
  if(cards.length < 2) return;

  const dimTo  = opts.dimTo  !== undefined ? opts.dimTo  : 0.30;
  const shrink = opts.shrink !== undefined ? opts.shrink : 0.04;
  let ticking = false;

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

    if(!next){ card.style.opacity = ''; card.style.transform = ''; card.style.filter = ''; continue; }

    const g        = geo[i] || { top:0, h:0 };
    const nextTop  = next.getBoundingClientRect().top;

    const fadeStart = g.top + g.h;
    const fadeEnd   = g.top + 28;

    let p = (fadeStart - nextTop) / (fadeStart - fadeEnd);
    p = p < 0 ? 0 : (p > 1 ? 1 : p);

    card.style.opacity   = String(1 - p * (1 - dimTo));
    card.style.transform = 'scale(' + (1 - p * shrink) + ')';

    if(p > 0.5) active = i + 1;
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

 // Tech — pinned section, cards stack horizontally toward the right
 (function(){
  const track = document.getElementById('techTrack');
  const pin   = document.getElementById('techPin');
  const cards = Array.from(document.querySelectorAll('#tech .tech-card'));
  if(!track || !pin || cards.length < 2) return;

  const STEP_VH   = 0.62;
  const ENTER_X   = 120;
  const STACK_X   = 46;
  const MAX_STACK = 3;
  let ticking = false;

  function measure(){
   let h = 0;
   cards.forEach(c => { h = Math.max(h, c.offsetHeight); });
   if(h) document.querySelector('#tech .tech-grid').style.setProperty('--tech-card-h', h + 'px');
   track.style.height = (pin.offsetHeight + (cards.length - 1) * STEP_VH * window.innerHeight) + 'px';
  }

  function update(){
   ticking = false;
   const runway = track.offsetHeight - pin.offsetHeight;
   if(runway <= 0) return;

   const scrolled = -track.getBoundingClientRect().top;
   let p = scrolled / runway;
   p = p < 0 ? 0 : (p > 1 ? 1 : p);

   const pos = p * (cards.length - 1);

   cards.forEach(function(card, i){
    const d = pos - i;
    let x, opacity, scale;

    if(d <= 0){
     x = d * ENTER_X;
     opacity = 1 + d;
     scale = 1;
    } else {
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

 const methodSection = document.getElementById('method');
 initStickyStack('#method .fn-card', {
  dimTo: 0.25, shrink: 0.05,
  onStage: function(i){ if(methodSection) methodSection.dataset.stage = i; }
 });

})();

// Industries — centred 3-up card stack
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
  const span = scroller.offsetHeight - viewport.offsetHeight;
  let p = span > 0 ? (-rect.top / span) : 0;
  p = p < 0 ? 0 : (p > 1 ? 1 : p);

  const activeF = p * last;
  const stepX = Math.max(96, cards[0].offsetWidth * 0.42);

  for(let i = 0; i < cards.length; i++){
   const card = cards[i];
   const d = i - activeF;
   const ad = Math.abs(d);

   if(ad > 2.2){ card.style.visibility = 'hidden'; continue; }
   card.style.visibility = 'visible';

   const clamped = ad > 1 ? 1 + (ad - 1) * 0.35 : ad;
   const x = Math.sign(d) * Math.min(clamped, 1.6) * stepX;
   const scale = Math.max(0.72, 1 - ad * 0.09);
   const op = ad <= 1 ? 1 - ad * 0.45 : Math.max(0, 0.55 - (ad - 1) * 0.55);

   card.style.transform = 'translate(calc(-50% + ' + x.toFixed(1) + 'px), -50%) scale(' + scale.toFixed(3) + ')';
   card.style.opacity = op.toFixed(3);
   card.style.zIndex = String(100 - Math.round(ad * 10));
  }
 }

 function onScroll(){ if(!ticking){ ticking = true; requestAnimationFrame(layout); } }

 layout();
 window.addEventListener('scroll', onScroll, { passive: true });
 window.addEventListener('resize', onScroll);
})();
</script>
<script>
// Physics tag stage — self-contained 2D simulation (no external library).
// Each pill is a soft capsule: gravity + floor/wall bounce + pill-to-pill
// separation + cursor repulsion. Starts once the section scrolls into view.
(function(){
 const stage = document.getElementById('physStage');
 if(!stage) return;
 if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

 const els = Array.from(stage.querySelectorAll('.phys-pill'));
 if(!els.length) return;

 let W = stage.clientWidth, H = stage.clientHeight;
 const GRAV = 0.42, REST = 0.5, FRICT = 0.86, AIR = 0.992;
 const CURSOR_R = 140;
 const mouse = { x:-9999, y:-9999, active:false };

 const P = els.map(function(el, i){
  const r = el.getBoundingClientRect();
  const w = r.width || 128, h = r.height || 44;
  return {
   el:el, w:w, h:h, rad:h/2,
   x:(W / (els.length + 1)) * (i + 1) + (Math.random()-0.5)*24,
   y:38 + Math.random()*90,
   vx:(Math.random()-0.5)*3, vy:0,
   a:(Math.random()-0.5)*0.5, va:(Math.random()-0.5)*0.06
  };
 });

 stage.addEventListener('mousemove', function(e){
  const r = stage.getBoundingClientRect();
  mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top; mouse.active = true;
 });
 stage.addEventListener('mouseleave', function(){ mouse.active = false; });

 function step(){
  for(let i = 0; i < P.length; i++){
   const p = P[i];
   p.vy += GRAV;

   if(mouse.active){
    const dx = p.x - mouse.x, dy = p.y - mouse.y;
    const d = Math.hypot(dx, dy);
    if(d < CURSOR_R && d > 0.1){
     const f = (1 - d / CURSOR_R) * 2.4;
     p.vx += (dx / d) * f; p.vy += (dy / d) * f;
    }
   }
   if(Math.random() < 0.02){ p.vx += (Math.random()-0.5)*0.6; }

   p.vx *= AIR; p.vy *= AIR;
   p.x += p.vx; p.y += p.vy;
   p.a += p.va; p.va *= 0.985;

   // walls
   const halfW = p.w/2, halfH = p.h/2;
   if(p.x < halfW){ p.x = halfW; p.vx = -p.vx * REST; p.va += p.vy * 0.002; }
   if(p.x > W - halfW){ p.x = W - halfW; p.vx = -p.vx * REST; p.va -= p.vy * 0.002; }
   if(p.y > H - halfH){ p.y = H - halfH; p.vy = -p.vy * REST; p.vx *= FRICT; p.va += p.vx * 0.004; p.va *= 0.6; }
   if(p.y < halfH){ p.y = halfH; p.vy = -p.vy * REST; }
  }

  // pill-to-pill separation (approximate capsules as circles)
  for(let i = 0; i < P.length; i++){
   for(let j = i + 1; j < P.length; j++){
    const a = P[i], b = P[j];
    const dx = b.x - a.x, dy = b.y - a.y;
    const d = Math.hypot(dx, dy) || 0.01;
    const minD = (a.w + b.w) * 0.32;
    if(d < minD){
     const push = (minD - d) / 2;
     const ux = dx / d, uy = dy / d;
     a.x -= ux * push; a.y -= uy * push;
     b.x += ux * push; b.y += uy * push;
     const rvx = b.vx - a.vx, rvy = b.vy - a.vy;
     const rel = rvx * ux + rvy * uy;
     if(rel < 0){
      const imp = -rel * (1 + REST) * 0.5;
      a.vx -= ux * imp; a.vy -= uy * imp;
      b.vx += ux * imp; b.vy += uy * imp;
     }
    }
   }
  }

  for(let i = 0; i < P.length; i++){
   const p = P[i];
   p.el.style.transform = 'translate(' + (p.x - p.w/2).toFixed(1) + 'px,' + (p.y - p.h/2).toFixed(1) + 'px) rotate(' + p.a.toFixed(3) + 'rad)';
  }
 }

 let running = false, raf = 0;
 function loop(){ step(); raf = requestAnimationFrame(loop); }
 function start(){ if(running) return; running = true; stage.classList.add('is-running'); loop(); }

 // seat the pills at spawn coords before the reveal so they don't flash top-left
 for(let i = 0; i < P.length; i++){
  const p = P[i];
  p.el.style.transform = 'translate(' + (p.x - p.w/2) + 'px,' + (p.y - p.h/2) + 'px) rotate(' + p.a + 'rad)';
 }

 if('IntersectionObserver' in window){
  const io = new IntersectionObserver(function(entries){
   entries.forEach(function(en){ if(en.isIntersecting){ start(); io.disconnect(); } });
  }, { threshold: 0.35 });
  io.observe(stage);
 } else {
  start();
 }

 let rt;
 window.addEventListener('resize', function(){
  clearTimeout(rt);
  rt = setTimeout(function(){
   W = stage.clientWidth; H = stage.clientHeight;
   for(let i = 0; i < P.length; i++){
    const p = P[i];
    if(p.x < 0 || p.x > W || p.y > H){ p.x = W/2; p.y = 60; p.vx = 0; p.vy = 0; }
   }
  }, 180);
 });
})();

// Hanging magnet: cursor-driven pendulum physics
(function(){
 const pend   = document.getElementById('whyPendulum');
 const hero   = document.querySelector('#why .why-hero');
 const magnet = document.querySelector('#why .why-magnet');
 const string = document.querySelector('#why .why-string');
 if(!pend || !hero || !magnet || !string) return;
 if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

 const coarse = window.matchMedia('(pointer: coarse)').matches;

 let theta = 0.08;
 let omega = 0;
 let tilt  = 0;
 let stretch = 1;

 const G      = 30;
 const DAMP   = 0.991;
 const MAXA   = 0.66;
 const RADIUS = 240;
 const PUSH   = 28;

 let mx = -9999, my = -9999, haveMouse = false;
 let mvx = 0, prevMx = -9999, prevT = 0;

 if(!coarse){
  window.addEventListener('mousemove', function(e){
   const now = performance.now();
   if(prevMx !== -9999 && now > prevT){
    mvx = mvx * 0.7 + ((e.clientX - prevMx) / (now - prevT)) * 0.3;
   }
   prevMx = e.clientX; prevT = now;
   mx = e.clientX; my = e.clientY; haveMouse = true;
  }, { passive: true });
 }

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
  const dt = Math.min((now - prev) / 1000, 0.04);
  prev = now;

  if(running){
   const hr = hero.getBoundingClientRect();
   const pivotX = hr.left + hr.width / 2;
   const pivotY = hr.top + pend.offsetTop;

   const mgX = pivotX + Math.sin(theta) * L;
   const mgY = pivotY + Math.cos(theta) * L;

   let alpha = -G * Math.sin(theta);
   let load  = 0;

   if(haveMouse && !coarse){
    const dx = mgX - mx, dy = mgY - my;
    const dist = Math.hypot(dx, dy) || 0.001;

    if(dist < RADIUS){
     const falloff = (1 - dist / RADIUS);
     const speedBoost = 1 + Math.min(Math.abs(mvx) * 1.6, 2.2);
     const F = PUSH * falloff * falloff * speedBoost;

     const ux = dx / dist, uy = dy / dist;
     const tx = Math.cos(theta), ty = -Math.sin(theta);
     alpha += F * (ux * tx + uy * ty);
     load = falloff;
    }
   }

   omega += alpha * dt;
   omega *= DAMP;
   theta += omega * dt;

   if(theta >  MAXA){ theta =  MAXA; omega *= -0.35; }
   if(theta < -MAXA){ theta = -MAXA; omega *= -0.35; }

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
</script>
<script>
// CTA intro — continuous letter train, driven by a sticky-pinned scroll
// runway (no external animation library). The glyphs fly in from the
// right and land on the CTA card's baseline as you scroll through the
// section; .ci-static-head is the no-JS fallback.
(function(){
 const section = document.getElementById('cta-intro');
 if(!section) return;
 const track = section.querySelector('.ci-track');
 if(!track) return;
 if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

 const ctaCard = document.querySelector('#cta .cta-card');
 if(!ctaCard) return;

 const glyphs   = track.querySelectorAll('.ci-stage .ci-ch');
 const measures = track.querySelectorAll('.ci-measure span');
 const n = glyphs.length;
 if(!n || measures.length !== n) return;

 const LAUNCH_Y = -240, LAUNCH_ROT = 32, LAUNCH_SCALE = 0.6, ARC_POW = 1.8;
 const offsets = new Float64Array(n);
 let totalWidth = 0, landX = 0, launchX = 0, jumpDistance = 0, travel = 0, runway = 1;

 function measure(){
  const vw = window.innerWidth;
  for(let i = 0; i < n; i++){
   offsets[i] = measures[i].offsetLeft;
   totalWidth = measures[i].offsetLeft + measures[i].offsetWidth;
  }
  landX   = ctaCard.getBoundingClientRect().right - 140;
  launchX = vw + 60;
  jumpDistance = Math.max(40, (totalWidth / n) * 1.30);
  travel  = totalWidth + vw * 0.8;
  runway  = Math.max(1, section.offsetHeight - window.innerHeight);
 }

 function render(p){
  const trainX = landX - p * travel;
  for(let i = 0; i < n; i++){
   const slotX = trainX + offsets[i];
   const dToLand = slotX - landX;
   let x, y, rot, sc, op;
   if(dToLand > 0){
    let jp = 1 - dToLand / jumpDistance;
    if(jp <= 0){
     x = launchX; y = LAUNCH_Y; rot = LAUNCH_ROT; sc = LAUNCH_SCALE; op = 0;
    } else {
     if(jp > 1) jp = 1;
     const e = Math.pow(jp, ARC_POW);
     x   = launchX + (landX - launchX) * e;
     y   = LAUNCH_Y + (0 - LAUNCH_Y) * e;
     rot = LAUNCH_ROT * (1 - e);
     sc  = LAUNCH_SCALE + (1 - LAUNCH_SCALE) * e;
     op  = jp * 2; if(op > 1) op = 1;
    }
   } else {
    x = slotX; y = 0; rot = 0; sc = 1; op = 1;
   }
   const el = glyphs[i];
   el.style.transform = 'translate3d(' + x.toFixed(1) + 'px,' + y.toFixed(1) + 'px,0) rotate(' + rot.toFixed(1) + 'deg) scale(' + sc.toFixed(3) + ')';
   el.style.opacity = op.toFixed(2);
  }
 }

 let ticking = false;
 function onScroll(){
  if(ticking) return;
  ticking = true;
  requestAnimationFrame(function(){
   ticking = false;
   let p = -section.getBoundingClientRect().top / runway;
   p = p < 0 ? 0 : (p > 1 ? 1 : p);
   render(p);
  });
 }

 function init(){
  section.classList.add('ci-anim');
  measure();
  render(0);
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', function(){ measure(); onScroll(); });
 }

 if(document.fonts && document.fonts.ready){
  document.fonts.ready.then(init);
 } else {
  window.addEventListener('load', init);
 }
})();

// Case studies — carousel track driven by vertical scroll
(function(){
 const scroller = document.getElementById('casesScroll');
 const track    = document.getElementById('casesTrack');
 const pin      = scroller && scroller.querySelector('.cases-pin');
 if(!scroller || !track || !pin) return;

 const cards = Array.from(track.querySelectorAll('.case-card'));
 if(!cards.length) return;

 const last = cards.length - 1;

 let span = 1, step = 1;
 function measure(){
  span = Math.max(1, scroller.offsetHeight - pin.offsetHeight);
  step = cards[0].offsetWidth + 40;
 }

 const state = cards.map(function(){ return { x:null, o:null, z:null, v:null }; });

 let ticking = false, visible = true;

 function layout(){
  ticking = false;
  if(!visible) return;

  const p0 = -scroller.getBoundingClientRect().top / span;
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

   const x  = Math.round(d * step);
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
<!-- FINAL-HOME SCRIPT END -->
