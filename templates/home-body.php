<?php $activePage = 'home'; include __DIR__ . '/partials/nav.php'; ?>

<section id="hero">
 <div class="grid-bg"></div>
 <div class="hero-glow-r"></div>
 <div class="hero-glow-l"></div>
 <!-- MAIN HEADLINE — Freshworks/Kissflow style: bold claim, gradient accent -->
 <h1 class="hero-h">
 One <span class="grad-os">OS</span> with <span class="grad-ai">AI</span><br>
 to every function<br>
 <span class="ghost">of your business</span>
 </h1>

 <p class="hero-p">Drawlead — the operating system for modern business. Unify ERP, AI automation, analytics, and cloud workflows into one intelligent platform built for India's growing businesses.</p>

 <!-- CTA BUTTONS — Zoho/Freshworks style: primary + ghost -->
 <div class="hero-btns">
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="btn btn-black">Start Free Consultation →</a>
 <a href="#functions" class="btn btn-ghost">
 <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polygon points="10,8 16,12 10,16" fill="currentColor"/></svg>
 Watch 2-min Demo
 </a>
 </div>



 <!-- HERO — 4 Industry Dashboards with perspective screens + tab switcher -->
 <div class="hero-product-wrap rv">

 <!-- Industry tab switcher -->
 <div class="ind-tabs" id="indTabs">
 <button class="ind-tab active" data-idx="0" onclick="switchDash(0)">
 <div class="tab-label">Construction</div>
 <div class="tab-sub">Projects · Sites · Billing</div>
 <div class="tab-bar" style="background:#14855a"></div>
 </button>
 <button class="ind-tab" data-idx="1" onclick="switchDash(1)">
 <div class="tab-label">Manufacturing</div>
 <div class="tab-sub">Production · QC · Dispatch</div>
 <div class="tab-bar"></div>
 </button>
 <button class="ind-tab" data-idx="2" onclick="switchDash(2)">
 <div class="tab-label">Jewellery</div>
 <div class="tab-sub">Stock · Orders · Sales</div>
 <div class="tab-bar"></div>
 </button>
 <button class="ind-tab" data-idx="3" onclick="switchDash(3)">
 <div class="tab-label">Hospital</div>
 <div class="tab-sub">Patients · Beds · Billing</div>
 <div class="tab-bar"></div>
 </button>
 <button class="ind-tab" data-idx="4" onclick="switchDash(4)">
 <div class="tab-label">Ecommerce</div>
 <div class="tab-sub">Orders · Inventory · Shopify</div>
 <div class="tab-bar"></div>
 </button>
 </div>

 <!-- Progress bar (flush under tabs) -->
 <div id="tabProgressWrap" style="height:2px;background:var(--border);margin-bottom:2rem;position:relative;overflow:hidden;max-width:900px;width:fit-content;min-width:660px;margin-left:auto;margin-right:auto">
 <div id="tabProgress" style="height:100%;background:var(--black);width:0%;transition:width .06s linear;position:absolute;top:0;left:0"></div>
 </div>

 <!-- Screen trio -->
 <div class="screens-row" id="screensRow">

 <!-- ═══ LEFT SCREEN (side panel) ═══ -->
 <div class="screen screen-s screen-l" id="screenLeft">
 <div class="s-bar">
 <div class="s-dot" style="background:#ff5f57"></div>
 <div class="s-dot" style="background:#ffbd2e"></div>
 <div class="s-dot" style="background:#28c840"></div>
 <div class="s-title" id="leftTitle">Site Management</div>
 </div>
 <div class="s-body" id="leftBody">
 <!-- Construction: Site Management -->
 <div class="s-mod" style="color:#14855a">Active Sites</div>
 <div class="s-krow">
 <div class="s-k"><div class="s-kv" style="color:#14855a">12</div><div class="s-kl">Sites</div></div>
 <div class="s-k"><div class="s-kv" style="color:#32b46f">8</div><div class="s-kl">On Track</div></div>
 <div class="s-k"><div class="s-kv" style="color:#14855a">4</div><div class="s-kl">Delayed</div></div>
 </div>
 <div style="font-size:8px;color:var(--g400);font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">Budget vs Actual</div>
 <div class="s-hbars">
 <div class="s-hrow"><span>Site A</span><div class="s-track"><div class="s-fill" style="width:82%;background:#14855a"></div></div><span>82%</span></div>
 <div class="s-hrow"><span>Site B</span><div class="s-track"><div class="s-fill" style="width:95%;background:#14855a"></div></div><span>95%</span></div>
 <div class="s-hrow"><span>Site C</span><div class="s-track"><div class="s-fill" style="width:61%;background:#32b46f"></div></div><span>61%</span></div>
 <div class="s-hrow"><span>Site D</span><div class="s-track"><div class="s-fill" style="width:44%;background:var(--blue)"></div></div><span>44%</span></div>
 </div>
 <div class="s-divider"></div>
 <div class="s-list">
 <div class="s-li"><div class="s-d" style="background:#14855a"></div>Site B over budget<span class="s-badge" style="background:rgba(50,180,111,.12);color:#14855a">Alert</span></div>
 <div class="s-li"><div class="s-d" style="background:#32b46f"></div>Site A milestone done<span class="s-badge" style="background:rgba(50,180,111,.12);color:#32b46f">Done</span></div>
 </div>
 </div>
 </div>

 <!-- ═══ CENTER SCREEN (main) ═══ -->
 <div class="screen screen-c" id="screenCenter">
 <div class="s-bar">
 <div class="s-dot" style="background:#ff5f57"></div>
 <div class="s-dot" style="background:#ffbd2e"></div>
 <div class="s-dot" style="background:#28c840"></div>
 <div class="s-title" id="centerTitle">Drawlead — Construction OS</div>
 </div>
 <div class="s-body" id="centerBody">

 <!-- ── CONSTRUCTION CENTER ── -->
 <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
 <div class="s-mod" style="color:var(--black);margin:0;font-size:11px">Construction OS — Project Overview</div>
 <div style="display:flex;gap:5px">
 <span style="font-size:7.5px;padding:2px 7px;background:#14855a;color:#fff;font-weight:700">Live</span>
 <div style="font-size:9px;color:var(--g400);font-weight:600">May 2025</div>
 </div>
 </div>
 <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:5px;margin-bottom:13px">
 <div class="s-k"><div class="s-kv" style="color:#14855a;font-size:13px">₹8.4Cr</div><div class="s-kl">Total Projects</div></div>
 <div class="s-k"><div class="s-kv" style="color:#32b46f;font-size:11px">↑ 18%</div><div class="s-kl">On-time Rate</div></div>
 <div class="s-k"><div class="s-kv">247</div><div class="s-kl">Workers</div></div>
 <div class="s-k"><div class="s-kv" style="color:var(--blue)">38</div><div class="s-kl">Vendors</div></div>
 </div>
 <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:11px">
 <div>
 <div style="font-size:8px;color:var(--g400);font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:7px">Project Progress</div>
 <div style="display:flex;align-items:flex-end;gap:4px;height:54px">
 <div style="flex:1;background:rgba(234,88,12,.2);height:55%"></div>
 <div style="flex:1;background:rgba(234,88,12,.35);height:70%"></div>
 <div style="flex:1;background:rgba(234,88,12,.5);height:48%"></div>
 <div style="flex:1;background:rgba(234,88,12,.65);height:80%"></div>
 <div style="flex:1;background:#14855a;height:95%"></div>
 <div style="flex:1;background:rgba(234,88,12,.3);height:40%"></div>
 </div>
 </div>
 <div>
 <div style="font-size:8px;color:var(--g400);font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:7px">Material Stock</div>
 <div style="display:flex;flex-direction:column;gap:6px">
 <div style="display:flex;align-items:center;gap:6px;font-size:9px;color:var(--g500)"><span style="width:42px;font-weight:600">Cement</span><div style="flex:1;height:4px;background:var(--border);overflow:hidden"><div style="width:72%;height:100%;background:#14855a"></div></div><span>72%</span></div>
 <div style="display:flex;align-items:center;gap:6px;font-size:9px;color:var(--g500)"><span style="width:42px;font-weight:600">Steel</span><div style="flex:1;height:4px;background:var(--border);overflow:hidden"><div style="width:45%;height:100%;background:#14855a"></div></div><span style="color:#14855a;font-weight:700">45%</span></div>
 <div style="display:flex;align-items:center;gap:6px;font-size:9px;color:var(--g500)"><span style="width:42px;font-weight:600">Bricks</span><div style="flex:1;height:4px;background:var(--border);overflow:hidden"><div style="width:88%;height:100%;background:#32b46f"></div></div><span>88%</span></div>
 </div>
 </div>
 </div>
 <div class="s-divider"></div>
 <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;padding-top:8px">
 <div style="display:flex;flex-direction:column;gap:5px">
 <div style="font-size:8px;color:var(--g400);font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px">Recent Activity</div>
 <div style="display:flex;align-items:center;gap:5px;font-size:9px;color:var(--g500)"><span style="width:5px;height:5px;background:#32b46f;display:inline-block;flex-shrink:0"></span>Contractor payment approved</div>
 <div style="display:flex;align-items:center;gap:5px;font-size:9px;color:var(--g500)"><span style="width:5px;height:5px;background:#14855a;display:inline-block;flex-shrink:0"></span>Steel reorder auto-triggered</div>
 <div style="display:flex;align-items:center;gap:5px;font-size:9px;color:var(--g500)"><span style="width:5px;height:5px;background:#14855a;display:inline-block;flex-shrink:0"></span>Site B inspection scheduled</div>
 </div>
 <div style="display:flex;justify-content:flex-end;align-items:center">
 <div style="background:rgba(50,180,111,.08);padding:8px 12px;border-left:2px solid #32b46f">
 <div style="font-size:8px;color:#14855a;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px"> AI Alert</div>
 <div style="font-size:9px;color:var(--g500);line-height:1.4">Site B at 95% budget. Predict overrun in 12 days.</div>
 </div>
 </div>
 </div>

 </div>
 </div>

 <!-- ═══ RIGHT SCREEN (side panel) ═══ -->
 <div class="screen screen-s screen-r" id="screenRight">
 <div class="s-bar">
 <div class="s-dot" style="background:#ff5f57"></div>
 <div class="s-dot" style="background:#ffbd2e"></div>
 <div class="s-dot" style="background:#28c840"></div>
 <div class="s-title" id="rightTitle">Billing & Vendors</div>
 </div>
 <div class="s-body" id="rightBody">
 <div class="s-mod" style="color:#14855a">Billing Status</div>
 <div class="s-krow">
 <div class="s-k"><div class="s-kv" style="color:#14855a">₹2.1Cr</div><div class="s-kl">Billed</div></div>
 <div class="s-k"><div class="s-kv" style="color:#32b46f">₹1.8Cr</div><div class="s-kl">Received</div></div>
 <div class="s-k"><div class="s-kv" style="color:#14855a">₹30L</div><div class="s-kl">Due</div></div>
 </div>
 <div style="font-size:8px;color:var(--g400);font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">Top Vendors</div>
 <div class="s-list">
 <div class="s-li"><div class="s-d" style="background:#14855a"></div>Rajan Steel Works<span class="s-badge" style="background:rgba(50,180,111,.12);color:#32b46f">Paid</span></div>
 <div class="s-li"><div class="s-d" style="background:#23a065"></div>Kumar Cement Co<span class="s-badge" style="background:rgba(50,180,111,.12);color:#23a065">Partial</span></div>
 <div class="s-li"><div class="s-d" style="background:#14855a"></div>ARC Electricals<span class="s-badge" style="background:rgba(50,180,111,.12);color:#14855a">Due</span></div>
 </div>
 <div class="s-divider"></div>
 <div style="font-size:8px;color:var(--g400);font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">Contractor Payroll</div>
 <div class="s-hbars">
 <div class="s-hrow"><span>Mason</span><div class="s-track"><div class="s-fill" style="width:100%;background:#32b46f"></div></div><span style="color:#32b46f"></span></div>
 <div class="s-hrow"><span>Plumber</span><div class="s-track"><div class="s-fill" style="width:100%;background:#32b46f"></div></div><span style="color:#32b46f"></span></div>
 <div class="s-hrow"><span>Electrician</span><div class="s-track"><div class="s-fill" style="width:60%;background:#23a065"></div></div><span style="color:#23a065">Pend</span></div>
 </div>
 </div>
 </div>

 </div><!-- /screens-row -->

 <!-- Floating badges -->
 <div class="hero-float hero-float-l" id="floatL">
 <div style="font-size:16px;font-weight:800;color:#14855a">12 Sites</div>
 <div style="font-size:9px;color:var(--g500);font-weight:600;text-transform:uppercase;letter-spacing:.06em">Live project tracking</div>
 </div>
 <div class="hero-float hero-float-r" id="floatR">
 <div style="font-size:16px;font-weight:800;color:#32b46f">↑ 18%</div>
 <div style="font-size:9px;color:var(--g500);font-weight:600;text-transform:uppercase;letter-spacing:.06em">On-time delivery rate</div>
 </div>

 </div><!-- /hero-product-wrap -->

 <div class="hero-stats">
 <div class="hstat"><div class="hstat-n gr">7</div><div class="hstat-l">Core functions</div></div>
 <div class="hstat"><div class="hstat-n">10+</div><div class="hstat-l">Industries</div></div>
 <div class="hstat"><div class="hstat-n" style="color:var(--blue)">AI</div><div class="hstat-l">Powered</div></div>
 <div class="hstat"><div class="hstat-n">∞</div><div class="hstat-l">Scalable</div></div>
 </div>
</section>

<!-- ═══════════════════ MARQUEE ═══════════════════ -->
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
 <div class="fn-num" style="color:var(--blue)">01 — Management</div>
 <div class="fn-name">Management</div>
 <div class="fn-desc">Centralized dashboards and operational visibility for faster, smarter business decisions.</div>
 <div class="fn-tags"><span class="fn-tag">KPI Tracking</span><span class="fn-tag">Analytics</span><span class="fn-tag">Approvals</span></div>
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="fn-arrow" style="color:var(--blue)">Explore module →</a>
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
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="fn-arrow" style="color:#32b46f">Explore module →</a>
 </div>
 <!-- 03 Marketing -->
 <div class="fn-card rv d3">
 <div class="fn-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(0,0,0,.2)">
 <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><path d="M5 14 L5 26 L11 26 L11 14 Z" fill="rgba(255,255,255,.6)"/><path d="M11 14 L30 6 L30 34 L11 26 Z" fill="white"/><path d="M11 18 L11 22 L8 28 L5 28 L5 22" fill="rgba(255,255,255,.4)"/><path d="M32 15 Q38 20 32 25" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="2.5" stroke-linecap="round"/></svg>
 </div>
 <div class="fn-num" style="color:var(--violet)">03 — Marketing</div>
 <div class="fn-name">Marketing</div>
 <div class="fn-desc">Track campaigns, automate WhatsApp &amp; email, and improve customer engagement at scale.</div>
 <div class="fn-tags"><span class="fn-tag">Campaigns</span><span class="fn-tag">WhatsApp</span><span class="fn-tag">Nurturing</span></div>
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="fn-arrow" style="color:var(--violet)">Explore module →</a>
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
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="fn-arrow" style="color:#14855a">Explore module →</a>
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
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="fn-arrow" style="color:#32b46f">Explore module →</a>
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
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="fn-arrow" style="color:#14855a">Explore module →</a>
 </div>
 <!-- 07 R&D -->
 <div class="fn-card rv d3">
 <div class="fn-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(0,0,0,.2)">
 <svg width="26" height="26" viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="4.5" fill="white"/><ellipse cx="20" cy="20" rx="17" ry="7" fill="none" stroke="rgba(255,255,255,.85)" stroke-width="2.5"/><ellipse cx="20" cy="20" rx="17" ry="7" fill="none" stroke="rgba(255,255,255,.55)" stroke-width="2" transform="rotate(60 20 20)"/><ellipse cx="20" cy="20" rx="17" ry="7" fill="none" stroke="rgba(255,255,255,.4)" stroke-width="2" transform="rotate(120 20 20)"/></svg>
 </div>
 <div class="fn-num" style="color:#23a065">07 — R&amp;D</div>
 <div class="fn-name">R&amp;D</div>
 <div class="fn-desc">Enable innovation with AI-powered automation, predictive analytics, and custom intelligence.</div>
 <div class="fn-tags"><span class="fn-tag">AI Automation</span><span class="fn-tag">Predictive</span><span class="fn-tag">Custom</span></div>
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="fn-arrow" style="color:#23a065">Explore module →</a>
 </div>
 <!-- 08 CTA Card -->
 <div class="fn-card fn-cta-card rv d4">
 <div style="font-size:26px;font-weight:800;color:#fff;line-height:1.1;letter-spacing:-.02em">Ready to<br>unify all 7?</div>
 <div style="font-size:12px;color:rgba(255,255,255,.44);line-height:1.65;font-weight:400">Get a personalised walkthrough of all modules working in sync.</div>
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" style="background:#fff;color:var(--black);padding:11px 22px;border-radius:6px;font-size:10.5px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;text-decoration:none;display:inline-block;transition:opacity .2s" onmouseover="this.style.opacity='.84'" onmouseout="this.style.opacity='1'">Book Free Demo →</a>
 </div>
 </div>

 <div class="sec-cta rv">
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="btn btn-black">Schedule a Consultation →</a>
 <a href="#dashboards" class="btn btn-outline2">View Live Dashboards</a>
 </div>
</section>

<!-- ═══════════════════ SOLUTIONS ═══════════════════ -->
<section id="solutions">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Solutions</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Built for <span class="g">Growth</span> <span class="fade">— Three Ways</span></h2>
 <p class="sec-sub rv">Beyond the core platform, three focused solution tracks that plug straight into your operating system.</p>

 <div class="sol-grid">

 <!-- Custom Operational Solutions (ERP) -->
 <div class="sol-card rv d1">
  <div class="sol-icon">
   <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect x="5" y="5" width="13" height="13" rx="2.5" fill="white"/><rect x="22" y="5" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.6)"/><rect x="5" y="22" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.6)"/><rect x="22" y="22" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.85)"/><path d="M18 11.5 L22 11.5 M11.5 18 L11.5 22 M28.5 18 L28.5 22 M18 28.5 L22 28.5" stroke="rgba(255,255,255,.5)" stroke-width="2" stroke-linecap="round"/></svg>
  </div>
  <div class="sol-label">Solution 01 · ERP</div>
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
  <a href="custom-operational-solutions.html" class="sol-arrow">Explore ERP →</a>
 </div>

 <!-- Ecommerce Solutions -->
 <div class="sol-card rv d2">
  <div class="sol-icon">
   <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><path d="M4 10 L10 10 L14 27 L32 27" stroke="rgba(255,255,255,.55)" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M11.5 14 L35 14 L32 24 L13.8 24 Z" fill="white"/><circle cx="16" cy="33" r="3" fill="rgba(255,255,255,.85)"/><circle cx="30" cy="33" r="3" fill="rgba(255,255,255,.85)"/></svg>
  </div>
  <div class="sol-label">Solution 02</div>
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
  <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="sol-arrow">Explore ecommerce →</a>
 </div>

 <!-- Marketing Solutions -->
 <div class="sol-card rv d3">
  <div class="sol-icon">
   <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><path d="M4 6 L36 6 L24 21 L24 34 L16 30 L16 21 Z" fill="white"/><path d="M16 21 L24 21 L24 27 L16 27 Z" fill="rgba(255,255,255,.55)"/><path d="M4 6 L36 6 L31 12 L9 12 Z" fill="rgba(255,255,255,.6)"/></svg>
  </div>
  <div class="sol-label">Solution 03</div>
  <div class="sol-name">Marketing Solutions</div>
  <div class="sol-tag">Fix the leak between lead and conversion.</div>
  <p class="sol-desc">Most businesses don't have a traffic problem — they have a follow-up problem. Two engines run the funnel: organic search that compounds over time, and paid campaigns that buy demand on demand.</p>

  <div class="sol-tabs">
   <button class="sol-tab active" data-mkt="0" onclick="switchMkt(0)">Search Engine Optimization</button>
   <button class="sol-tab" data-mkt="1" onclick="switchMkt(1)">Performance Marketing</button>
  </div>

  <!-- SEO panel -->
  <div class="sol-panel active" data-mktpanel="0">
   <ul class="sol-list">
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Technical audits, Core Web Vitals &amp; site architecture</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Keyword strategy built around buying intent</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Content engine &amp; on-page optimisation at scale</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Local SEO, Google Business Profile &amp; schema markup</li>
   </ul>
   <div class="sol-metrics">
    <div><div class="sol-metric-v">4&times;</div><div class="sol-metric-l">Organic traffic</div></div>
    <div><div class="sol-metric-v">Page 1</div><div class="sol-metric-l">Target keywords</div></div>
   </div>
  </div>

  <!-- Performance Marketing panel -->
  <div class="sol-panel" data-mktpanel="1">
   <ul class="sol-list">
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Google, Meta &amp; LinkedIn campaigns managed end to end</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Landing pages &amp; creative built for conversion testing</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Instant WhatsApp &amp; email follow-up on every lead</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Ad spend mapped to closed revenue, not just clicks</li>
   </ul>
   <div class="sol-metrics">
    <div><div class="sol-metric-v">&lt;5 min</div><div class="sol-metric-l">Response time</div></div>
    <div><div class="sol-metric-v">100%</div><div class="sol-metric-l">Spend attributed</div></div>
   </div>
  </div>

  <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="sol-arrow">Explore marketing →</a>
 </div>

 </div>
</section>

<!-- ═══════════════════ TECH STACK ═══════════════════ -->
<section id="tech" style="background:#0a1310;color:#fff">
  <div class="eyebrow rv"><div class="eyebrow-line" style="background:#34a87c"></div><span class="eyebrow-text" style="color:#34a87c">Technology Stack</span><div class="eyebrow-line" style="background:#34a87c"></div></div>
  <h2 class="sec-h rv" style="color:#fff"><span style="background:linear-gradient(115deg,#4ecb87,#34a87c);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">Built on Modern</span> <span style="color:rgba(255,255,255,.22)">Infrastructure</span></h2>
  <p class="sec-sub rv" style="color:rgba(255,255,255,.5)">Enterprise-grade technologies combining to create scalable, intelligent digital ecosystems.</p>
  <div class="tech-grid">

    <!-- ERP -->
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

    <!-- AI -->
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

    <!-- CRM -->
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

    <!-- Analytics -->
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

    <!-- Cloud -->
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

    <!-- Workflow -->
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
    <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="btn btn-black" style="background:#fff;color:#0a1310">Discuss Technical Requirements →</a>
    <a href="#functions" class="btn btn-outline2" style="color:rgba(255,255,255,.7);border-color:rgba(255,255,255,.2)">View All Modules</a>
  </div>
</section>

<!-- ═══════════════════ CASE STUDIES ═══════════════════ -->
<section id="cases">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line" style="background:#32b46f"></div><span class="eyebrow-text" style="color:#32b46f">Case Studies</span><div class="eyebrow-line" style="background:#32b46f"></div></div>
 <h2 class="sec-h rv"><span style="color:#32b46f">Real Results</span> for <span class="fade">Real Businesses</span></h2>
 <p class="sec-sub rv">How Drawlead transforms operations across industries with measurable outcomes.</p>
 <div class="cases-grid">
 <div class="case-card rv d1">
  <div class="case-screen">
    <div class="cs-bar"><div class="cs-dot" style="background:#ff5f57"></div><div class="cs-dot" style="background:#ffbd2e"></div><div class="cs-dot" style="background:#28c840"></div><div class="cs-title">Construction ERP — Drawlead</div></div>
    <div class="cs-body">
      <div class="cs-sidebar">
        <div style="font-size:7px;font-weight:800;color:#32b46f;margin-bottom:4px">Drawlead</div>
        <div class="cs-nav-item active"></div>
        <div class="cs-nav-item"></div>
        <div class="cs-nav-item"></div>
        <div class="cs-nav-item"></div>
        <div class="cs-nav-item"></div>
      </div>
      <div class="cs-main">
        <div class="cs-krow">
          <div class="cs-k"><div class="cs-kv">12</div><div class="cs-kl">Sites</div></div>
          <div class="cs-k"><div class="cs-kv" style="color:#14855a">₹8.4Cr</div><div class="cs-kl">Projects</div></div>
          <div class="cs-k"><div class="cs-kv">247</div><div class="cs-kl">Workers</div></div>
        </div>
        <div class="cs-lbl">Budget vs Actual</div>
        <div class="cs-chart">
          <div class="cs-hbar-r"><span>Site A</span><div class="cs-track2"><div class="cs-fill2" style="width:82%"></div></div><span>82%</span></div>
          <div class="cs-hbar-r"><span>Site B</span><div class="cs-track2"><div class="cs-fill2" style="width:95%;background:#14855a"></div></div><span style="color:#14855a">95%⚠</span></div>
          <div class="cs-hbar-r"><span>Site C</span><div class="cs-track2"><div class="cs-fill2" style="width:61%"></div></div><span>61%</span></div>
        </div>
        <div class="cs-list-r">
          <div class="cs-list-item"><div class="cs-dot2"></div>Contractor payment approved<span class="cs-badge-s">Done</span></div>
          <div class="cs-list-item"><div class="cs-dot2" style="background:#14855a"></div>Steel reorder triggered<span class="cs-badge-s" style="background:rgba(20,133,90,.1);color:#14855a">Auto</span></div>
        </div>
      </div>
    </div>
  </div>
  <div class="case-body">
    <span class="case-tag" style="background:rgba(50,180,111,.08);color:#32b46f;border:1px solid rgba(50,180,111,.2)">Construction &amp; Real Estate</span>
    <div class="case-title">Construction ERP Solution</div>
    <ul class="case-list">
      <li>Better operational visibility across all project sites</li>
      <li>Faster reporting workflows and billing automation</li>
      <li>Improved multi-site project management controls</li>
    </ul>
    <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="btn btn-outline2 btn-sm" style="margin-top:1.1rem;align-self:flex-start">Read Case Study →</a>
  </div>
 </div>
 <div class="case-card rv d2">
  <div class="case-screen">
    <div class="cs-bar"><div class="cs-dot" style="background:#ff5f57"></div><div class="cs-dot" style="background:#ffbd2e"></div><div class="cs-dot" style="background:#28c840"></div><div class="cs-title">Clinic OS — Drawlead</div></div>
    <div class="cs-body">
      <div class="cs-sidebar">
        <div style="font-size:7px;font-weight:800;color:#32b46f;margin-bottom:4px">Drawlead</div>
        <div class="cs-nav-item active"></div>
        <div class="cs-nav-item"></div>
        <div class="cs-nav-item"></div>
        <div class="cs-nav-item"></div>
      </div>
      <div class="cs-main">
        <div class="cs-krow">
          <div class="cs-k"><div class="cs-kv">6</div><div class="cs-kl">Branches</div></div>
          <div class="cs-k"><div class="cs-kv" style="color:#14855a">284</div><div class="cs-kl">Patients</div></div>
          <div class="cs-k"><div class="cs-kv">₹42L</div><div class="cs-kl">Revenue</div></div>
        </div>
        <div class="cs-lbl">Today's Appointments</div>
        <div class="cs-bars-row">
          <div class="cs-bar-i" style="height:55%"></div>
          <div class="cs-bar-i" style="height:72%"></div>
          <div class="cs-bar-i" style="height:60%"></div>
          <div class="cs-bar-i" style="height:88%"></div>
          <div class="cs-bar-i cs-bar-hi" style="height:95%"></div>
          <div class="cs-bar-i" style="height:70%"></div>
        </div>
        <div class="cs-list-r">
          <div class="cs-list-item"><div class="cs-dot2"></div>Chennai Branch — 48 appts<span class="cs-badge-s">Full</span></div>
          <div class="cs-list-item"><div class="cs-dot2" style="background:#14855a"></div>Billing processed — ₹12L<span class="cs-badge-s" style="background:rgba(20,133,90,.1);color:#14855a">Done</span></div>
        </div>
      </div>
    </div>
  </div>
  <div class="case-body">
    <span class="case-tag" style="background:rgba(50,180,111,.08);color:#32b46f;border:1px solid rgba(50,180,111,.2)">Healthcare &amp; Wellness</span>
    <div class="case-title">Multi-Brand Physiotherapy Management</div>
    <ul class="case-list">
      <li>Streamlined clinic workflows across branches</li>
      <li>Improved scheduling efficiency and capacity</li>
      <li>Centralized billing and cross-branch reporting</li>
    </ul>
    <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="btn btn-outline2 btn-sm" style="margin-top:1.1rem;align-self:flex-start">Read Case Study →</a>
  </div>
 </div>
 <div class="case-card rv d3">
  <div class="case-screen">
    <div class="cs-bar"><div class="cs-dot" style="background:#ff5f57"></div><div class="cs-dot" style="background:#ffbd2e"></div><div class="cs-dot" style="background:#28c840"></div><div class="cs-title">Agency OS — Drawlead</div></div>
    <div class="cs-body">
      <div class="cs-sidebar">
        <div style="font-size:7px;font-weight:800;color:#32b46f;margin-bottom:4px">Drawlead</div>
        <div class="cs-nav-item active"></div>
        <div class="cs-nav-item"></div>
        <div class="cs-nav-item"></div>
        <div class="cs-nav-item"></div>
      </div>
      <div class="cs-main">
        <div class="cs-krow">
          <div class="cs-k"><div class="cs-kv">24</div><div class="cs-kl">Clients</div></div>
          <div class="cs-k"><div class="cs-kv" style="color:#14855a">↑ 38%</div><div class="cs-kl">Output</div></div>
          <div class="cs-k"><div class="cs-kv">₹1.8Cr</div><div class="cs-kl">Billed</div></div>
        </div>
        <div class="cs-lbl">Project Delivery Rate</div>
        <div class="cs-funnel">
          <div class="cs-fbar2" style="width:100%;background:rgba(50,180,111,.1)">Active · 24 projects</div>
          <div class="cs-fbar2" style="width:75%;background:rgba(50,180,111,.18)">In Review · 18</div>
          <div class="cs-fbar2" style="width:58%;background:#32b46f;color:#fff">Delivered · 14</div>
        </div>
        <div class="cs-list-r">
          <div class="cs-list-item"><div class="cs-dot2"></div>Team productivity up 38%<span class="cs-badge-s">AI</span></div>
          <div class="cs-list-item"><div class="cs-dot2" style="background:#14855a"></div>Client NPS score 94<span class="cs-badge-s" style="background:rgba(20,133,90,.1);color:#14855a">↑</span></div>
        </div>
      </div>
    </div>
  </div>
  <div class="case-body">
    <span class="case-tag" style="background:rgba(50,180,111,.08);color:#14855a;border:1px solid rgba(20,133,90,.2)">Marketing Agencies</span>
    <div class="case-title">Agency OS</div>
    <ul class="case-list">
      <li>Improved team collaboration and project delivery</li>
      <li>Better client and pipeline management</li>
      <li>Measurable increase in team productivity</li>
    </ul>
    <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="btn btn-outline2 btn-sm" style="margin-top:1.1rem;align-self:flex-start">Read Case Study →</a>
  </div>
 </div>
 </div>
 <div class="sec-cta rv">
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="btn btn-black">Start Your Success Story →</a>
 </div>
</section>

<!-- ═══════════════════ INDUSTRIES ═══════════════════ -->
<section id="industries" style="background:var(--white)">
 <div class="eyebrow rv"><div class="eyebrow-line" style="background:#14855a"></div><span class="eyebrow-text" style="color:#14855a">Industries</span><div class="eyebrow-line" style="background:#14855a"></div></div>
 <h2 class="sec-h rv">Built for <span style="color:#14855a">Your Industry</span></h2>
 <p class="sec-sub rv">Every industry has unique challenges. Drawlead adapts to your specific workflows, pain points, and compliance requirements — out of the box.</p>

 <div class="ind-grid">

 <!-- Construction -->
 <div class="ind-card rv d1">
 <div class="ind-card-top">
 <div class="ind-emoji" style="background:linear-gradient(135deg,#32b46f,#14855a)"><svg width="28" height="28" fill="none" viewBox="0 0 40 40">
       <rect x="4" y="28" width="32" height="8" rx="2" fill="rgba(255,255,255,.9)"/>
       <rect x="8" y="16" width="10" height="12" rx="1" fill="rgba(255,255,255,.7)"/>
       <rect x="22" y="20" width="10" height="8" rx="1" fill="rgba(255,255,255,.5)"/>
       <polygon points="2,28 20,8 38,28" fill="rgba(255,255,255,.3)"/>
       <polygon points="8,28 20,14 32,28" fill="rgba(255,255,255,.2)"/>
     </svg></div>
 <div class="ind-card-title">Construction &amp; Real Estate</div>
 <div class="ind-card-tag">Projects · Sites · Billing</div>
 </div>
 <div class="ind-card-body">
 <div class="ind-problems">
 <div class="ind-prob-label">Common Problems</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Project overruns with no visibility</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Manual contractor billing errors</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Siloed site & inventory data</div>
 </div>
 <div class="ind-solutions">
 <div class="ind-sol-label">Drawlead Solution</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Real-time multi-site project dashboard</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Automated contractor & billing workflows</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Unified inventory &amp; vendor management</div>
 </div>
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="ind-cta">Explore Construction OS →</a>
 </div>
 </div>

 <!-- Healthcare -->
 <div class="ind-card rv d2">
 <div class="ind-card-top">
 <div class="ind-emoji" style="background:linear-gradient(135deg,#32b46f,#14855a)"><svg width="28" height="28" fill="none" viewBox="0 0 40 40">
       <rect x="4" y="4" width="32" height="32" rx="6" fill="rgba(255,255,255,.15)"/>
       <rect x="16" y="8" width="8" height="24" rx="2" fill="rgba(255,255,255,.9)"/>
       <rect x="8" y="16" width="24" height="8" rx="2" fill="rgba(255,255,255,.9)"/>
     </svg></div>
 <div class="ind-card-title">Healthcare &amp; Wellness</div>
 <div class="ind-card-tag">Clinics · Appointments · Billing</div>
 </div>
 <div class="ind-card-body">
 <div class="ind-problems">
 <div class="ind-prob-label">Common Problems</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Scheduling conflicts across branches</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Manual patient billing & follow-ups</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>No centralized therapist management</div>
 </div>
 <div class="ind-solutions">
 <div class="ind-sol-label">Drawlead Solution</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Smart appointment scheduling system</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Automated billing &amp; WhatsApp reminders</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Multi-branch centralized reporting</div>
 </div>
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="ind-cta">Explore Healthcare OS →</a>
 </div>
 </div>

 <!-- Manufacturing -->
 <div class="ind-card rv d3">
 <div class="ind-card-top">
 <div class="ind-emoji" style="background:linear-gradient(135deg,#32b46f,#14855a)"><svg width="28" height="28" fill="none" viewBox="0 0 40 40">
       <rect x="3" y="28" width="34" height="8" rx="2" fill="rgba(255,255,255,.8)"/>
       <rect x="6" y="18" width="8" height="10" rx="1" fill="rgba(255,255,255,.6)"/>
       <rect x="18" y="14" width="8" height="14" rx="1" fill="rgba(255,255,255,.7)"/>
       <rect x="30" y="10" width="4" height="18" rx="1" fill="rgba(255,255,255,.5)"/>
       <circle cx="10" cy="22" r="2" fill="rgba(255,255,255,.3)"/>
       <rect x="14" y="6" width="3" height="8" rx="1.5" fill="rgba(255,255,255,.4)"/>
     </svg></div>
 <div class="ind-card-title">Manufacturing</div>
 <div class="ind-card-tag">Production · Inventory · Quality</div>
 </div>
 <div class="ind-card-body">
 <div class="ind-problems">
 <div class="ind-prob-label">Common Problems</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Production delays with no alerts</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Raw material stockout surprises</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Manual quality control records</div>
 </div>
 <div class="ind-solutions">
 <div class="ind-sol-label">Drawlead Solution</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>AI-powered production monitoring</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Predictive inventory restocking alerts</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Digital QC workflows &amp; reporting</div>
 </div>
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="ind-cta">Explore Manufacturing OS →</a>
 </div>
 </div>

 <!-- Agencies -->
 <div class="ind-card rv d1">
 <div class="ind-card-top">
 <div class="ind-emoji" style="background:linear-gradient(135deg,#32b46f,#14855a)"><svg width="28" height="28" fill="none" viewBox="0 0 40 40">
       <circle cx="20" cy="12" r="8" fill="rgba(255,255,255,.9)"/>
       <circle cx="10" cy="28" r="6" fill="rgba(255,255,255,.65)"/>
       <circle cx="30" cy="28" r="6" fill="rgba(255,255,255,.65)"/>
       <line x1="14" y1="18" x2="12" y2="23" stroke="rgba(255,255,255,.6)" stroke-width="2"/>
       <line x1="26" y1="18" x2="28" y2="23" stroke="rgba(255,255,255,.6)" stroke-width="2"/>
     </svg></div>
 <div class="ind-card-title">Marketing Agencies</div>
 <div class="ind-card-tag">Projects · Clients · Delivery</div>
 </div>
 <div class="ind-card-body">
 <div class="ind-problems">
 <div class="ind-prob-label">Common Problems</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Client projects slipping deadlines</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>No single view of leads &amp; revenue</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Team scattered across tools</div>
 </div>
 <div class="ind-solutions">
 <div class="ind-sol-label">Drawlead Solution</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Agency OS — all-in-one client hub</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Unified CRM + project + billing view</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Team tasks, timelines &amp; reports</div>
 </div>
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="ind-cta">Explore Agency OS →</a>
 </div>
 </div>

 <!-- Retail -->
 <div class="ind-card rv d2">
 <div class="ind-card-top">
 <div class="ind-emoji" style="background:linear-gradient(135deg,#32b46f,#14855a)"><svg width="28" height="28" fill="none" viewBox="0 0 40 40">
       <path d="M6 10 L10 4 L30 4 L34 10 Z" fill="rgba(255,255,255,.9)"/>
       <path d="M6 10 L6 34 Q6 36 8 36 L32 36 Q34 36 34 34 L34 10 Z" fill="rgba(255,255,255,.7)"/>
       <path d="M15 10 Q15 18 20 18 Q25 18 25 10" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="2.5" stroke-linecap="round"/>
     </svg></div>
 <div class="ind-card-title">Retail &amp; E-Commerce</div>
 <div class="ind-card-tag">Orders · Inventory · CRM</div>
 </div>
 <div class="ind-card-body">
 <div class="ind-problems">
 <div class="ind-prob-label">Common Problems</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Inventory mismatches across stores</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>No customer retention system</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Order tracking is fully manual</div>
 </div>
 <div class="ind-solutions">
 <div class="ind-sol-label">Drawlead Solution</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Live inventory sync across all channels</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Customer CRM + loyalty automation</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>End-to-end order management</div>
 </div>
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="ind-cta">Explore Retail OS →</a>
 </div>
 </div>

 <!-- Logistics -->
 <div class="ind-card rv d3">
 <div class="ind-card-top">
 <div class="ind-emoji" style="background:linear-gradient(135deg,#32b46f,#14855a)"><svg width="28" height="28" fill="none" viewBox="0 0 40 40">
       <rect x="2" y="16" width="22" height="14" rx="2" fill="rgba(255,255,255,.85)"/>
       <path d="M24 20 L34 20 L38 28 L38 30 L24 30 Z" fill="rgba(255,255,255,.65)"/>
       <circle cx="10" cy="32" r="4" fill="rgba(255,255,255,.5)" stroke="rgba(255,255,255,.9)" stroke-width="2"/>
       <circle cx="30" cy="32" r="4" fill="rgba(255,255,255,.5)" stroke="rgba(255,255,255,.9)" stroke-width="2"/>
       <rect x="6" y="10" width="10" height="6" rx="1" fill="rgba(255,255,255,.4)"/>
     </svg></div>
 <div class="ind-card-title">Logistics &amp; Transport</div>
 <div class="ind-card-tag">Fleet · Delivery · Compliance</div>
 </div>
 <div class="ind-card-body">
 <div class="ind-problems">
 <div class="ind-prob-label">Common Problems</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>No real-time fleet visibility</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Manual delivery proof &amp; billing</div>
 <div class="ind-prob"><svg width="12" height="12" fill="none" stroke="#14855a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>Compliance docs always missing</div>
 </div>
 <div class="ind-solutions">
 <div class="ind-sol-label">Drawlead Solution</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Live fleet &amp; delivery tracking dashboard</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Digital POD &amp; auto-invoicing</div>
 <div class="ind-sol"><svg width="12" height="12" fill="none" stroke="#32b46f" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Compliance document automation</div>
 </div>
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="ind-cta">Explore Logistics OS →</a>
 </div>
 </div>

 </div><!-- /ind-grid -->

 <div class="sec-cta rv" style="margin-top:3rem">
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="btn btn-black">Find Your Industry Solution →</a>
 <a href="#cases" class="btn btn-outline2">See Case Studies</a>
 </div>
</section>

<!-- ═══════════════════ WHY Drawlead ═══════════════════ -->
<section id="why" style="background:#0a1310;color:#fff">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line" style="background:#14855a"></div><span class="eyebrow-text" style="color:#14855a">Why Drawlead</span><div class="eyebrow-line" style="background:#14855a"></div></div>
 <h2 class="sec-h rv" style="color:#fff"><span style="color:#14855a">What Sets</span> <span style="color:rgba(255,255,255,.22)">Us Apart</span></h2>
 <p class="sec-sub rv" style="color:rgba(255,255,255,.5)">We're not just software — we're a long-term partner in your digital transformation and growth.</p>
 <div class="why-grid">
 <div class="why-card rv d1">
 <div class="why-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 18px rgba(0,0,0,.2)"><svg width="24" height="24" fill="none" viewBox="0 0 40 40">
       <rect x="3" y="14" width="12" height="12" rx="3" fill="rgba(255,255,255,.9)"/>
       <rect x="25" y="14" width="12" height="12" rx="3" fill="rgba(255,255,255,.9)"/>
       <rect x="14" y="18" width="12" height="4" rx="2" fill="rgba(255,255,255,.7)"/>
       <circle cx="8" cy="8" r="4" fill="rgba(255,255,255,.4)"/>
       <circle cx="32" cy="32" r="4" fill="rgba(255,255,255,.4)"/>
     </svg></div>
 <div class="why-name">Unified Ecosystem</div>
 <div class="why-desc">All 7 core functions in one platform. No more tool-switching or disconnected data silos.</div>
 </div>
 <div class="why-card rv d2">
 <div class="why-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 18px rgba(0,0,0,.2)"><svg width="24" height="24" fill="none" viewBox="0 0 40 40">
       <polygon points="22,4 10,22 20,22 18,36 30,18 20,18" fill="rgba(255,255,255,.95)"/>
       <circle cx="32" cy="8" r="3" fill="rgba(255,255,255,.4)"/>
       <circle cx="8" cy="32" r="3" fill="rgba(255,255,255,.4)"/>
     </svg></div>
 <div class="why-name">AI-Driven Efficiency</div>
 <div class="why-desc">Automate repetitive tasks and surface intelligent insights without any technical expertise.</div>
 </div>
 <div class="why-card rv d3">
 <div class="why-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 18px rgba(0,0,0,.2)"><svg width="24" height="24" fill="none" viewBox="0 0 40 40">
       <polygon points="20,4 28,12 24,12 24,18 16,18 16,12 12,12" fill="rgba(255,255,255,.9)"/>
       <polygon points="20,36 28,28 24,28 24,22 16,22 16,28 12,28" fill="rgba(255,255,255,.9)"/>
       <polygon points="4,20 12,12 12,16 18,16 18,24 12,24 12,28" fill="rgba(255,255,255,.6)"/>
       <polygon points="36,20 28,12 28,16 22,16 22,24 28,24 28,28" fill="rgba(255,255,255,.6)"/>
     </svg></div>
 <div class="why-name">Scalable Architecture</div>
 <div class="why-desc">Built for startups, SMEs, and enterprises — scales exactly as your business grows.</div>
 </div>
 <div class="why-card rv d4">
 <div class="why-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 18px rgba(0,0,0,.2)"><svg width="24" height="24" fill="none" viewBox="0 0 40 40">
       <path d="M20 4 L34 10 L34 22 Q34 32 20 37 Q6 32 6 22 L6 10 Z" fill="rgba(255,255,255,.85)"/>
       <polyline points="13,20 18,25 28,15" fill="none" stroke="#14855a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
     </svg></div>
 <div class="why-name">Secure &amp; Reliable</div>
 <div class="why-desc">Enterprise-grade security, 99.9% uptime SLA, and end-to-end encryption on all data.</div>
 </div>
 <div class="why-card rv d1">
 <div class="why-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 18px rgba(0,0,0,.2)"><svg width="24" height="24" fill="none" viewBox="0 0 40 40">
       <path d="M4 20 Q10 14 16 18 L20 22 L24 18 Q30 14 36 20" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="3" stroke-linecap="round"/>
       <path d="M4 24 Q10 18 16 22 L20 26 L24 22 Q30 18 36 24" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="3" stroke-linecap="round"/>
       <circle cx="20" cy="24" r="3" fill="white"/>
     </svg></div>
 <div class="why-name">Long-Term Partnership</div>
 <div class="why-desc">Continuous support, updates, and future-proofing — we grow and evolve with your needs.</div>
 </div>
 </div>
 <div class="sec-cta rv">
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="btn btn-black" style="background:#fff;color:#0a1310">Partner With Us →</a>
 <a href="#cases" class="btn btn-outline2" style="color:rgba(255,255,255,.7);border-color:rgba(255,255,255,.2)">See Case Studies</a>
 </div>
</section>

<!-- ═══════════════════ DASHBOARDS ═══════════════════ -->
<section id="dashboards" style="background:var(--bg2)">
 <div class="grid-bg" style="opacity:.35"></div>
 <div class="eyebrow rv"><div class="eyebrow-line" style="background:#23a065"></div><span class="eyebrow-text" style="color:#23a065">Platform Dashboards</span><div class="eyebrow-line" style="background:#23a065"></div></div>
 <h2 class="sec-h rv">Every Module. <span class="fade">One Screen.</span></h2>
 <p class="sec-sub rv">Live ERP dashboards for every function — see exactly what Drawlead looks like in action.</p>
 <div class="dash-grid">

  <!-- ── SALES ── -->
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

  <!-- ── FINANCE ── -->
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

  <!-- ── OPERATIONS ── -->
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

  <!-- ── HR ── -->
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

  <!-- ── MARKETING ── -->
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

  <!-- ── MANAGEMENT ── -->
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
        <div class="d-row"><div class="d-dot" style="background:#32b46f"></div>Sales 94%<span class="d-val" style="color:#32b46f">↑</span></div>
        <div class="d-row"><div class="d-dot" style="background:#14855a"></div>Finance 95%<span class="d-val" style="color:#14855a">↑</span></div>
      </div>
    </div>
  </div>

 </div><!-- /dash-grid -->
 <div class="sec-cta rv" style="margin-top:3rem">
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="btn btn-black">Book a Live Demo →</a>
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="btn btn-outline2">Schedule Consultation</a>
 </div>
</section>

<!-- ═══════════════════ CTA ═══════════════════ -->
<section id="cta">
 <div class="cta-grid-bg"></div>
 <div class="cta-glow"></div>
 <h2 class="cta-h rv">Build your<br><span class="fade">business</span> <span class="gr">ERP</span><br><span class="gr2">OS</span> with <span class="gr3">AI</span></h2>
 <p class="cta-p rv">Digitize, automate, and scale with Drawlead. Start with a free consultation — no commitment needed.</p>
 <div class="cta-btns rv">
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="cta-btn-w">Schedule Free Consultation →</a>
 <a href="https://calendly.com/ulagai/ecommerce-growth-launch-strategy-call-shopify" target="_blank" class="cta-btn-g">Book a Product Demo</a>
 </div>
 <div class="cta-note rv">செயலை மாற்றும் · Intelligent Operating System · Secure · Scalable · Future-Ready</div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
// ── Marketing Solutions tab switcher (SEO / Performance Marketing) ──
function switchMkt(i){
 document.querySelectorAll('.sol-tab[data-mkt]').forEach(t=>{
  t.classList.toggle('active', t.dataset.mkt == i);
 });
 document.querySelectorAll('.sol-panel[data-mktpanel]').forEach(p=>{
  p.classList.toggle('active', p.dataset.mktpanel == i);
 });
}

// ── Industry Dashboard Switcher ──
const industries = [
 {
 name:'Construction',
 accentColor:'#14855a',
 centerTitle:'Drawlead — Construction OS',
 leftTitle:'Site Management',
 rightTitle:'Billing & Vendors',
 leftMod:'Active Sites',
 leftMod_c:'#14855a',
 leftKpis:[{v:'12',l:'Sites',c:'#14855a'},{v:'8',l:'On Track',c:'#32b46f'},{v:'4',l:'Delayed',c:'#14855a'}],
 leftBarsLabel:'Budget vs Actual',
 leftBars:[{l:'Site A',w:82,c:'#14855a'},{l:'Site B',w:95,c:'#14855a'},{l:'Site C',w:61,c:'#32b46f'},{l:'Site D',w:44,c:'var(--blue)'}],
 leftAlerts:[{c:'#14855a',t:'Site B over budget',badge:'Alert',bc:'rgba(50,180,111,.12)',btc:'#14855a'},{c:'#32b46f',t:'Site A milestone done',badge:'Done',bc:'rgba(50,180,111,.12)',btc:'#32b46f'}],
 center_emoji:'', center_label:'Construction OS — Project Overview',
 center_tag:'Live', center_tag_c:'#14855a',
 center_kpis:[{v:'₹8.4Cr',l:'Total Projects',c:'#14855a'},{v:'↑ 18%',l:'On-time Rate',c:'#32b46f'},{v:'247',l:'Workers'},{v:'38',l:'Vendors',c:'var(--blue)'}],
 center_chart_label:'Project Progress',
 center_chart_bars:['55%','70%','48%','80%','95%','40%'],
 center_chart_c:'#14855a',
 center_right_label:'Material Stock',
 center_right_bars:[{l:'Cement',w:72,c:'#14855a'},{l:'Steel',w:45,c:'#14855a',warn:true},{l:'Bricks',w:88,c:'#32b46f'}],
 center_activity:[{c:'#32b46f',t:'Contractor payment approved'},{c:'#14855a',t:'Steel reorder auto-triggered'},{c:'#14855a',t:'Site B inspection scheduled'}],
 ai_label:' AI Alert', ai_c:'#14855a', ai_bg:'rgba(50,180,111,.08)', ai_border:'#14855a',
 ai_text:'Site B at 95% budget. Predict overrun in 12 days.',
 rightMod:'Billing Status', rightMod_c:'#14855a',
 rightKpis:[{v:'₹2.1Cr',l:'Billed',c:'#14855a'},{v:'₹1.8Cr',l:'Received',c:'#32b46f'},{v:'₹30L',l:'Due',c:'#14855a'}],
 rightListLabel:'Top Vendors',
 rightList:[{c:'#14855a',t:'Rajan Steel Works',badge:'Paid',bc:'rgba(50,180,111,.12)',btc:'#32b46f'},{c:'#23a065',t:'Kumar Cement Co',badge:'Partial',bc:'rgba(50,180,111,.12)',btc:'#23a065'},{c:'#14855a',t:'ARC Electricals',badge:'Due',bc:'rgba(50,180,111,.12)',btc:'#14855a'}],
 rightBarsLabel:'Contractor Payroll',
 rightBars:[{l:'Mason',w:100,c:'#32b46f',val:'',vc:'#32b46f'},{l:'Plumber',w:100,c:'#32b46f',val:'',vc:'#32b46f'},{l:'Electrician',w:60,c:'#23a065',val:'Pend',vc:'#23a065'}],
 floatL:{v:'12 Sites',vc:'#14855a',l:'Live project tracking'},
 floatR:{v:'↑ 18%',vc:'#32b46f',l:'On-time delivery rate'},
 },
 {
 name:'Manufacturing',
 accentColor:'#14855a',
 centerTitle:'Drawlead — Manufacturing OS',
 leftTitle:'Production Line',
 rightTitle:'Quality & Output',
 leftMod:'Production Status', leftMod_c:'#14855a',
 leftKpis:[{v:'6',l:'Lines',c:'#14855a'},{v:'94%',l:'OEE',c:'#32b46f'},{v:'18',l:'Downtime h',c:'#14855a'}],
 leftBarsLabel:'Line Efficiency',
 leftBars:[{l:'Line 1',w:94,c:'#14855a'},{l:'Line 2',w:88,c:'var(--blue)'},{l:'Line 3',w:72,c:'#23a065'},{l:'Line 4',w:56,c:'#14855a'}],
 leftAlerts:[{c:'#14855a',t:'Line 4 maintenance due',badge:'Alert',bc:'rgba(50,180,111,.12)',btc:'#14855a'},{c:'#32b46f',t:'Line 1 target achieved',badge:'Done',bc:'rgba(50,180,111,.12)',btc:'#32b46f'}],
 center_emoji:'', center_label:'Manufacturing OS — Production Overview',
 center_tag:'Live', center_tag_c:'#14855a',
 center_kpis:[{v:'4,280',l:'Units Today',c:'#14855a'},{v:'↑ 12%',l:'Output',c:'#32b46f'},{v:'99.1%',l:'Quality Rate',c:'var(--blue)'},{v:'6',l:'Active Lines'}],
 center_chart_label:'Daily Production Units',
 center_chart_bars:['62%','78%','55%','90%','85%','95%'],
 center_chart_c:'#14855a',
 center_right_label:'Raw Material Stock',
 center_right_bars:[{l:'Steel',w:80,c:'#14855a'},{l:'Plastic',w:35,c:'#14855a',warn:true},{l:'Chemicals',w:90,c:'#32b46f'}],
 center_activity:[{c:'#32b46f',t:'Line 1 hit 1000 units target'},{c:'#14855a',t:'Plastic stock below threshold'},{c:'#14855a',t:'Shift B handover completed'}],
 ai_label:' AI Predict', ai_c:'#14855a', ai_bg:'rgba(50,180,111,.08)', ai_border:'#14855a',
 ai_text:'Line 4 bearing wear detected. Schedule maintenance in 48h to avoid stoppage.',
 rightMod:'QC & Dispatch', rightMod_c:'#14855a',
 rightKpis:[{v:'4,216',l:'Passed QC',c:'#32b46f'},{v:'64',l:'Rejected',c:'#14855a'},{v:'3,980',l:'Dispatched',c:'#14855a'}],
 rightListLabel:"Today's Dispatch",
 rightList:[{c:'#14855a',t:'North Zone — 1,200 units',badge:'Sent',bc:'rgba(50,180,111,.12)',btc:'#32b46f'},{c:'#23a065',t:'South Zone — 800 units',badge:'Loading',bc:'rgba(50,180,111,.12)',btc:'#23a065'},{c:'var(--blue)',t:'Export — 600 units',badge:'QC Hold',bc:'rgba(50,180,111,.12)',btc:'var(--blue)'}],
 rightBarsLabel:'Quality Pass Rate by Line',
 rightBars:[{l:'Line 1',w:99,c:'#32b46f',val:'99%',vc:'#32b46f'},{l:'Line 2',w:97,c:'#32b46f',val:'97%',vc:'#32b46f'},{l:'Line 3',w:91,c:'#14855a',val:'91%',vc:'#14855a'}],
 floatL:{v:'4,280',vc:'#14855a',l:'Units produced today'},
 floatR:{v:'99.1%',vc:'#32b46f',l:'Quality pass rate'},
 },
 {
 name:'Jewellery',
 accentColor:'#23a065',
 centerTitle:'Drawlead — Jewellery OS',
 leftTitle:'Stock & Purity',
 rightTitle:'Orders & Billing',
 leftMod:'Metal Inventory', leftMod_c:'#23a065',
 leftKpis:[{v:'48 kg',l:'Gold Stock',c:'#23a065'},{v:'124 kg',l:'Silver',c:'var(--g400)'},{v:'99.9%',l:'Purity Avg',c:'#32b46f'}],
 leftBarsLabel:'Stock by Category',
 leftBars:[{l:'22K Gold',w:72,c:'#23a065'},{l:'18K Gold',w:55,c:'#23a065'},{l:'Silver',w:88,c:'var(--g400)'},{l:'Platinum',w:30,c:'var(--blue)'}],
 leftAlerts:[{c:'#32b46f',t:'Purity audit passed',badge:'OK',bc:'rgba(50,180,111,.12)',btc:'#32b46f'},{c:'#23a065',t:'Gold reorder triggered',badge:'Ordered',bc:'rgba(50,180,111,.12)',btc:'#23a065'}],
 center_emoji:'', center_label:'Jewellery OS — Sales & Stock Overview',
 center_tag:'Live', center_tag_c:'#23a065',
 center_kpis:[{v:'₹3.2Cr',l:'Monthly Sales',c:'#23a065'},{v:'↑ 22%',l:'YoY Growth',c:'#32b46f'},{v:'1,840',l:'SKUs Active',c:'var(--blue)'},{v:'96%',l:'Order Fulfill'}],
 center_chart_label:'Weekly Sales (₹ Lakhs)',
 center_chart_bars:['55%','62%','48%','80%','70%','95%'],
 center_chart_c:'#23a065',
 center_right_label:'Top Selling Categories',
 center_right_bars:[{l:'Necklace',w:85,c:'#23a065'},{l:'Bangles',w:70,c:'#23a065'},{l:'Rings',w:60,c:'var(--g400)'}],
 center_activity:[{c:'#32b46f',t:'Festival collection launched'},{c:'#23a065',t:'Gold rate auto-updated'},{c:'var(--blue)',t:'Online order sync active'}],
 ai_label:' AI Insight', ai_c:'#23a065', ai_bg:'rgba(50,180,111,.08)', ai_border:'#23a065',
 ai_text:'Navaratri season — predict 40% spike in necklace demand next 14 days.',
 rightMod:'Orders & Finance', rightMod_c:'#23a065',
 rightKpis:[{v:'184',l:'Orders',c:'#23a065'},{v:'₹2.9Cr',l:'Collected',c:'#32b46f'},{v:'₹28L',l:'Advance',c:'var(--blue)'}],
 rightListLabel:'Recent Orders',
 rightList:[{c:'#23a065',t:'Priya Jewels — 22K set',badge:'Ready',bc:'rgba(50,180,111,.12)',btc:'#32b46f'},{c:'#23a065',t:'Kumar Traders — Bulk',badge:'Making',bc:'rgba(50,180,111,.12)',btc:'#23a065'},{c:'var(--blue)',t:'Online — 28 orders',badge:'Packed',bc:'rgba(50,180,111,.12)',btc:'var(--blue)'}],
 rightBarsLabel:'Payment Collection',
 rightBars:[{l:'Cash',w:42,c:'#23a065',val:'42%',vc:'#23a065'},{l:'UPI',w:38,c:'#32b46f',val:'38%',vc:'#32b46f'},{l:'Credit',w:20,c:'var(--blue)',val:'20%',vc:'var(--blue)'}],
 floatL:{v:'₹3.2Cr',vc:'#23a065',l:'Monthly jewellery sales'},
 floatR:{v:'96%',vc:'#32b46f',l:'Order fulfillment rate'},
 },
 {
 name:'Hospital',
 accentColor:'#32b46f',
 centerTitle:'Drawlead — Hospital OS',
 leftTitle:'Ward & Beds',
 rightTitle:'Billing & Insurance',
 leftMod:'Bed Occupancy', leftMod_c:'#32b46f',
 leftKpis:[{v:'284',l:'Beds',c:'#32b46f'},{v:'91%',l:'Occupied',c:'#14855a'},{v:'24',l:'Available',c:'#32b46f'}],
 leftBarsLabel:'Dept. Occupancy',
 leftBars:[{l:'General',w:95,c:'#14855a'},{l:'ICU',w:88,c:'#32b46f'},{l:'Maternity',w:72,c:'#14855a'},{l:'Surgical',w:65,c:'var(--blue)'}],
 leftAlerts:[{c:'#14855a',t:'ICU near capacity',badge:'Alert',bc:'rgba(50,180,111,.12)',btc:'#14855a'},{c:'#32b46f',t:'OPD queue cleared',badge:'Clear',bc:'rgba(50,180,111,.12)',btc:'#32b46f'}],
 center_emoji:'', center_label:'Hospital OS — Patient & Operations Overview',
 center_tag:'Live', center_tag_c:'#32b46f',
 center_kpis:[{v:'1,248',l:'Patients Today',c:'#32b46f'},{v:'284',l:'Inpatients',c:'var(--blue)'},{v:'46',l:'Surgeries',c:'#14855a'},{v:'98.4%',l:'Satisfaction',c:'#32b46f'}],
 center_chart_label:'Daily OPD Footfall',
 center_chart_bars:['60%','72%','55%','88%','80%','95%'],
 center_chart_c:'#32b46f',
 center_right_label:'Dept. Patient Load',
 center_right_bars:[{l:'Cardio',w:90,c:'#14855a'},{l:'Ortho',w:75,c:'#32b46f'},{l:'Paeds',w:60,c:'#14855a'}],
 center_activity:[{c:'#32b46f',t:'46 surgeries completed today'},{c:'#14855a',t:'ICU: 2 critical admissions'},{c:'#32b46f',t:'Lab reports auto-dispatched'}],
 ai_label:' AI Monitor', ai_c:'#32b46f', ai_bg:'rgba(50,180,111,.08)', ai_border:'#32b46f',
 ai_text:'Dengue cases ↑ 18% this week. Suggest allocating 20 extra general ward beds.',
 rightMod:'Billing & Revenue', rightMod_c:'#32b46f',
 rightKpis:[{v:'₹42L',l:"Today's Revenue",c:'#32b46f'},{v:'₹28L',l:'Insurance',c:'var(--blue)'},{v:'₹14L',l:'Self-pay',c:'#32b46f'}],
 rightListLabel:'Insurance Claims',
 rightList:[{c:'#32b46f',t:'Star Health — 48 claims',badge:'Approved',bc:'rgba(50,180,111,.12)',btc:'#32b46f'},{c:'#23a065',t:'HDFC Ergo — 22 claims',badge:'Pending',bc:'rgba(50,180,111,.12)',btc:'#23a065'},{c:'#14855a',t:'National Ins. — 8 claims',badge:'Query',bc:'rgba(50,180,111,.12)',btc:'#14855a'}],
 rightBarsLabel:'Dept. Revenue Share',
 rightBars:[{l:'Surgical',w:40,c:'#32b46f',val:'40%',vc:'#32b46f'},{l:'IPD',w:35,c:'var(--blue)',val:'35%',vc:'var(--blue)'},{l:'Lab/Rad',w:25,c:'#14855a',val:'25%',vc:'#14855a'}],
 floatL:{v:'1,248',vc:'#32b46f',l:'Patients served today'},
 floatR:{v:'98.4%',vc:'#32b46f',l:'Patient satisfaction score'}
 },
 {
 name:'Ecommerce',
 accentColor:'#23a065',
 centerTitle:'Drawlead — Ecommerce OS',
 leftTitle:'Inventory Board',
 rightTitle:'Shopify Store Board',
 leftMod:'Stock Overview', leftMod_c:'#23a065',
 leftKpis:[{v:'1,240',l:'SKUs',c:'#23a065'},{v:'1,086',l:'In Stock',c:'#32b46f'},{v:'54',l:'Low Stock',c:'#14855a'}],
 leftBarsLabel:'Stock by Category',
 leftBars:[{l:'Apparel',w:78,c:'#23a065'},{l:'Footwear',w:62,c:'#32b46f'},{l:'Accessories',w:38,c:'#14855a'},{l:'Electronics',w:88,c:'var(--blue)'}],
 leftAlerts:[{c:'#14855a',t:'12 SKUs out of stock',badge:'Alert',bc:'rgba(50,180,111,.12)',btc:'#14855a'},{c:'#32b46f',t:'Restock PO auto-raised',badge:'Ordered',bc:'rgba(50,180,111,.12)',btc:'#32b46f'}],
 center_emoji:'', center_label:'Ecommerce OS — Orders & Revenue Overview',
 center_tag:'Live', center_tag_c:'#23a065',
 center_kpis:[{v:'842',l:'Orders Today',c:'#23a065'},{v:'₹18.4L',l:'GMV',c:'#32b46f'},{v:'3.8%',l:'Conversion',c:'var(--blue)'},{v:'₹2,140',l:'Avg Order'}],
 center_chart_label:'Daily Orders',
 center_chart_bars:['58%','66%','50%','82%','74%','95%'],
 center_chart_c:'#23a065',
 center_right_label:'Top Products',
 center_right_bars:[{l:'Hoodies',w:88,c:'#23a065'},{l:'Sneakers',w:71,c:'#32b46f'},{l:'Watches',w:54,c:'#14855a'}],
 center_activity:[{c:'#32b46f',t:'842 orders synced from Shopify'},{c:'#14855a',t:'18 returns auto-processed'},{c:'#23a065',t:'Courier labels bulk-generated'}],
 ai_label:' AI Insight', ai_c:'#23a065', ai_bg:'rgba(50,180,111,.08)', ai_border:'#23a065',
 ai_text:'Cart abandonment at 68% on mobile checkout. Recovering 240 carts could add ₹5.1L this month.',
 rightMod:'Shopify Store', rightMod_c:'#23a065',
 rightKpis:[{v:'₹18.4L',l:'Store Revenue',c:'#23a065'},{v:'24.8K',l:'Sessions',c:'var(--blue)'},{v:'3.8%',l:'Conv. Rate',c:'#32b46f'}],
 rightListLabel:'Live Orders',
 rightList:[{c:'#32b46f',t:'#1042 — Chennai',badge:'Shipped',bc:'rgba(50,180,111,.12)',btc:'#32b46f'},{c:'#23a065',t:'#1041 — Bengaluru',badge:'Packed',bc:'rgba(50,180,111,.12)',btc:'#23a065'},{c:'#14855a',t:'#1040 — Mumbai',badge:'Payment',bc:'rgba(50,180,111,.12)',btc:'#14855a'}],
 rightBarsLabel:'Traffic by Channel',
 rightBars:[{l:'Organic',w:42,c:'#32b46f',val:'42%',vc:'#32b46f'},{l:'Paid Ads',w:33,c:'#23a065',val:'33%',vc:'#23a065'},{l:'Direct',w:25,c:'var(--blue)',val:'25%',vc:'var(--blue)'}],
 floatL:{v:'842',vc:'#23a065',l:'Orders processed today'},
 floatR:{v:'3.8%',vc:'#32b46f',l:'Store conversion rate'},
 }
];

let currentIdx = 0;

function buildLeftBody(d) {
 return `
 <div class="s-mod" style="color:${d.leftMod_c}">${d.leftMod}</div>
 <div class="s-krow">
 ${d.leftKpis.map(k=>`<div class="s-k"><div class="s-kv" style="color:${k.c||'var(--black)'}">${k.v}</div><div class="s-kl">${k.l}</div></div>`).join('')}
 </div>
 <div style="font-size:8px;color:var(--g400);font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">${d.leftBarsLabel}</div>
 <div class="s-hbars">
 ${d.leftBars.map(b=>`<div class="s-hrow"><span>${b.l}</span><div class="s-track"><div class="s-fill" style="width:${b.w}%;background:${b.c}"></div></div><span>${b.w}%</span></div>`).join('')}
 </div>
 <div class="s-divider"></div>
 <div class="s-list">
 ${d.leftAlerts.map(a=>`<div class="s-li"><div class="s-d" style="background:${a.c}"></div>${a.t}<span class="s-badge" style="background:${a.bc};color:${a.btc}">${a.badge}</span></div>`).join('')}
 </div>`;
}

function buildCenterBody(d) {
 return `
 <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
 <div class="s-mod" style="color:var(--black);margin:0;font-size:11px">${d.center_emoji} ${d.center_label}</div>
 <div style="display:flex;gap:5px">
 <span style="font-size:7.5px;padding:2px 7px;background:${d.center_tag_c};color:#fff;font-weight:700">${d.center_tag}</span>
 <div style="font-size:9px;color:var(--g400);font-weight:600">May 2025</div>
 </div>
 </div>
 <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:5px;margin-bottom:13px">
 ${d.center_kpis.map(k=>`<div class="s-k"><div class="s-kv" style="color:${k.c||'var(--black)'};font-size:${k.v.length>4?'11':'13'}px">${k.v}</div><div class="s-kl">${k.l}</div></div>`).join('')}
 </div>
 <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:11px">
 <div>
 <div style="font-size:8px;color:var(--g400);font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:7px">${d.center_chart_label}</div>
 <div style="display:flex;align-items:flex-end;gap:4px;height:54px">
 ${d.center_chart_bars.map((h,i)=>`<div style="flex:1;background:${i===d.center_chart_bars.length-1?d.center_chart_c:`rgba(${hexToRgb(d.center_chart_c)},${0.15+i*0.1})`};height:${h}"></div>`).join('')}
 </div>
 </div>
 <div>
 <div style="font-size:8px;color:var(--g400);font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:7px">${d.center_right_label}</div>
 <div style="display:flex;flex-direction:column;gap:6px">
 ${d.center_right_bars.map(b=>`<div style="display:flex;align-items:center;gap:6px;font-size:9px;color:var(--g500)"><span style="width:50px;font-weight:600">${b.l}</span><div style="flex:1;height:4px;background:var(--border);overflow:hidden"><div style="width:${b.w}%;height:100%;background:${b.c}"></div></div><span style="color:${b.warn?'#14855a':'inherit'};font-weight:${b.warn?'700':'400'}">${b.w}%${b.warn?'':''}</span></div>`).join('')}
 </div>
 </div>
 </div>
 <div class="s-divider"></div>
 <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;padding-top:8px">
 <div style="display:flex;flex-direction:column;gap:5px">
 <div style="font-size:8px;color:var(--g400);font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px">Recent Activity</div>
 ${d.center_activity.map(a=>`<div style="display:flex;align-items:center;gap:5px;font-size:9px;color:var(--g500)"><span style="width:5px;height:5px;background:${a.c};display:inline-block;flex-shrink:0"></span>${a.t}</div>`).join('')}
 </div>
 <div style="display:flex;justify-content:flex-end;align-items:center">
 <div style="background:${d.ai_bg};padding:8px 12px;border-left:2px solid ${d.ai_border}">
 <div style="font-size:8px;color:${d.ai_c};font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px">${d.ai_label}</div>
 <div style="font-size:9px;color:var(--g500);line-height:1.4">${d.ai_text}</div>
 </div>
 </div>
 </div>`;
}

function buildRightBody(d) {
 return `
 <div class="s-mod" style="color:${d.rightMod_c}">${d.rightMod}</div>
 <div class="s-krow">
 ${d.rightKpis.map(k=>`<div class="s-k"><div class="s-kv" style="color:${k.c||'var(--black)'}">${k.v}</div><div class="s-kl">${k.l}</div></div>`).join('')}
 </div>
 <div style="font-size:8px;color:var(--g400);font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">${d.rightListLabel}</div>
 <div class="s-list">
 ${d.rightList.map(a=>`<div class="s-li"><div class="s-d" style="background:${a.c}"></div>${a.t}<span class="s-badge" style="background:${a.bc};color:${a.btc}">${a.badge}</span></div>`).join('')}
 </div>
 <div class="s-divider"></div>
 <div style="font-size:8px;color:var(--g400);font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">${d.rightBarsLabel}</div>
 <div class="s-hbars">
 ${d.rightBars.map(b=>`<div class="s-hrow"><span>${b.l}</span><div class="s-track"><div class="s-fill" style="width:${b.w}%;background:${b.c}"></div></div><span style="color:${b.vc};font-weight:700">${b.val}</span></div>`).join('')}
 </div>`;
}

function hexToRgb(c) {
 // For CSS var colors, return a safe fallback
 if(c.startsWith('var(')) return '100,100,100';
 const m = c.match(/^#([0-9a-f]{6})$/i);
 if(!m) return '100,100,100';
 const n = parseInt(m[1],16);
 return [(n>>16)&255,(n>>8)&255,n&255].join(',');
}

function switchDash(idx) {
 if(idx === currentIdx) return;
 progressPct = 0; // reset progress on manual click
 const row = document.getElementById('screensRow');
 row.classList.add('switching');
 setTimeout(()=>{
 currentIdx = idx;
 const d = industries[idx];

 // Update tab states
 const tabColors = ['#14855a','#14855a','#23a065','#32b46f','#23a065'];
 document.querySelectorAll('.ind-tab').forEach((t,i)=>{
 const isActive = i===idx;
 t.classList.toggle('active', isActive);
 const bar = t.querySelector('.tab-bar');
 if(bar) bar.style.background = isActive ? tabColors[i] : 'transparent';
 });

 // Update titles
 document.getElementById('leftTitle').textContent = d.leftTitle;
 document.getElementById('centerTitle').textContent = d.centerTitle;
 document.getElementById('rightTitle').textContent = d.rightTitle;

 // Update bodies
 document.getElementById('leftBody').innerHTML = buildLeftBody(d);
 document.getElementById('centerBody').innerHTML = buildCenterBody(d);
 document.getElementById('rightBody').innerHTML = buildRightBody(d);

 // Update floating badges
 const fl = document.getElementById('floatL');
 const fr = document.getElementById('floatR');
 if(fl) fl.innerHTML = `<div style="font-size:16px;font-weight:800;color:${d.floatL.vc}">${d.floatL.v}</div><div style="font-size:9px;color:var(--g500);font-weight:600;text-transform:uppercase;letter-spacing:.06em">${d.floatL.l}</div>`;
 if(fr) fr.innerHTML = `<div style="font-size:16px;font-weight:800;color:${d.floatR.vc}">${d.floatR.v}</div><div style="font-size:9px;color:var(--g500);font-weight:600;text-transform:uppercase;letter-spacing:.06em">${d.floatR.l}</div>`;

 row.classList.remove('switching');
 }, 250);
}

// Init tab bars on load
(function(){
 const tabColors = ['#14855a','#14855a','#23a065','#32b46f','#23a065'];
 document.querySelectorAll('.ind-tab').forEach((t,i)=>{
 const bar = t.querySelector('.tab-bar');
 if(bar) bar.style.background = i===0 ? tabColors[0] : 'transparent';
 });
})();

// Progress bar for auto-cycle
const progressEl = document.getElementById('tabProgress');
let progressPct = 0;
const CYCLE_MS = 5000;
const TICK = 60;

function tickProgress(){
 progressPct += (TICK/CYCLE_MS)*100;
 if(progressPct>=100){
 progressPct=0;
 switchDash((currentIdx+1) % industries.length);
 }
 if(progressEl) progressEl.style.width = progressPct + '%';
}

setInterval(tickProgress, TICK);
</script>
