<?php
$activePage = 'crm-solution';
include __DIR__ . '/partials/nav.php';

$serviceCaseStudies = get_case_studies_by_service($pdo, 'CRM');
?>

<!-- 1. Hero -->
<section id="svc-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">CRM</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="max-width:820px">A CRM built around <span class="g">how you actually sell</span></h1>
 <p class="sec-sub rv" style="max-width:620px">Capture every lead, track every deal, and never let a follow-up slip — a CRM shaped around your sales process, not a generic template, connected to the same ERP, ecommerce, and marketing systems you already run on.</p>
 <div class="svc-hero-cta rv">
   <button type="button" data-book class="btn btn-black">Book a Free Consultation →</button>
   <a href="#pboard-1" class="btn btn-outline2">See It In Action</a>
 </div>
</section>

<!-- 2. Key Features -->
<section id="svc-benefits">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">What's Included</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Everything <span class="g">your pipeline</span> needs</h2>
 <div class="svc-benefits">
  <div class="svc-benefit rv d1">
   <div class="svc-benefit-check"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text">Lead capture from every channel — website, WhatsApp, calls, referrals</div>
  </div>
  <div class="svc-benefit rv d2">
   <div class="svc-benefit-check"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text">Visual pipeline with deal stages you define</div>
  </div>
  <div class="svc-benefit rv d3">
   <div class="svc-benefit-check"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text">Automated follow-up reminders on every lead</div>
  </div>
  <div class="svc-benefit rv d4">
   <div class="svc-benefit-check"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text">Role-based access for every rep and manager</div>
  </div>
  <div class="svc-benefit rv d1">
   <div class="svc-benefit-check"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text">Real-time reports on conversion and rep performance</div>
  </div>
  <div class="svc-benefit rv d2">
   <div class="svc-benefit-check"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text">Connects directly to your ERP, ecommerce &amp; marketing systems</div>
  </div>
 </div>
</section>

<!-- 3. Board Visual #1 — live stats -->
<section id="pboard-1" class="pboard-section">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">The Board</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Your <span class="g">CRM Board</span>, at a glance</h2>
 <p class="sec-sub rv">An illustrative look at the kind of live view this solution gives you.</p>
 <div class="pboard rv">
  <div class="pboard-bar">
   <span class="pboard-dot" style="background:#e5534b"></span>
   <span class="pboard-dot" style="background:#f5a623"></span>
   <span class="pboard-dot" style="background:#32b46f"></span>
   <span class="pboard-title">CRM Overview</span>
  </div>
  <div class="pboard-body">
   <div class="d-krow">
    <div class="d-k"><div class="d-kv" style="color:#32b46f">142</div><div class="d-kl">Active Leads</div></div>
    <div class="d-k"><div class="d-kv" style="color:#32b46f">38%</div><div class="d-kl">Conversion Rate</div></div>
    <div class="d-k"><div class="d-kv" style="color:#32b46f">&lt;2 hrs</div><div class="d-kl">Avg Response Time</div></div>
   </div>
   <div class="d-bars">
    <div class="d-bar" style="height:48%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:62%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:55%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:80%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:68%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:94%;background:#32b46f"></div>
   </div>
  </div>
 </div>
</section>

<!-- 4. Why It Matters -->
<section id="svc-pain">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Why It Matters</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">What happens <span class="g">without it</span></h2>
 <div class="pain-list">
  <div class="pain-item rv">
   <div class="pain-icon">!</div>
   <div class="pain-text">Leads sit in a rep's personal WhatsApp or notebook and get forgotten</div>
  </div>
  <div class="pain-item rv">
   <div class="pain-icon">!</div>
   <div class="pain-text">No visibility into which rep followed up and which didn't</div>
  </div>
  <div class="pain-item rv">
   <div class="pain-icon">!</div>
   <div class="pain-text">Deals fall through the cracks between first contact and close</div>
  </div>
 </div>
</section>

<!-- 5. Board Visual #2 — records list -->
<section class="pboard-section">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Inside the Board</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Every deal, <span class="g">one list</span></h2>
 <p class="sec-sub rv">A second illustrative view — the kind of record list that lives behind the board.</p>
 <div class="pboard-list rv">
  <div class="pboard-row">
   <div class="pboard-row-label">Lead — Ravi Kumar</div>
   <span class="pboard-status pboard-status-pending">Follow-up Due</span>
  </div>
  <div class="pboard-row">
   <div class="pboard-row-label">Deal — ₹4.2L Retainer</div>
   <span class="pboard-status pboard-status-pending">Proposal Sent</span>
  </div>
  <div class="pboard-row">
   <div class="pboard-row-label">Lead — Priya Textiles</div>
   <span class="pboard-status pboard-status-good">Won</span>
  </div>
  <div class="pboard-row">
   <div class="pboard-row-label">Deal — Q3 Renewal</div>
   <span class="pboard-status pboard-status-pending">Negotiation</span>
  </div>
 </div>
</section>

<!-- 6. How It Connects -->
<section id="svc-connects">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">One Operating System</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">How the <span class="g">CRM connects</span></h2>
 <p class="sec-sub rv">Nothing here lives in isolation — it's built on the same operating system as everything else.</p>
 <div class="svc-tracks svc-connect-grid">
  <div class="svc-track rv">
   <div class="svc-track-title">Custom ERP Solution</div>
   <p class="connect-text">Every won deal becomes a customer record in the same ERP your team already works from.</p>
   <a href="/custom-erp-solution" class="mega-know">Explore Custom ERP Solution →</a>
  </div>
  <div class="svc-track rv">
   <div class="svc-track-title">Marketing Solutions</div>
   <p class="connect-text">Every lead your campaigns generate lands directly in this same pipeline — no manual hand-off.</p>
   <a href="/marketing-solutions" class="mega-know">Explore Marketing Solutions →</a>
  </div>
  <div class="svc-track rv">
   <div class="svc-track-title">The Core Platform</div>
   <p class="connect-text">CRM is one piece of the same operating system — Sales, Finance, HR and Operations all read from the same data.</p>
   <a href="/#functions" class="mega-know">See All 7 Functions →</a>
  </div>
 </div>
</section>

<?php
$serviceCtaHeading = 'Ready to organize <span class="g">your pipeline</span>?';
$serviceCtaSub = "Book a free consultation and we'll map your sales process onto a CRM that actually fits it.";
include __DIR__ . '/partials/service-proof.php';
?>

<?php include __DIR__ . '/partials/footer.php'; ?>
