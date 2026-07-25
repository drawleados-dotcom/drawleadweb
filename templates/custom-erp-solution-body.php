<?php
$activePage = 'custom-erp-solution';
include __DIR__ . '/partials/nav.php';

$serviceCaseStudies = get_case_studies_by_service($pdo, 'Custom ERP Solution');
?>

<!-- 1. Hero -->
<section id="svc-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Custom ERP Solution</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="max-width:820px">An ERP shaped around <span class="g">how you actually work</span></h1>
 <p class="sec-sub rv" style="max-width:620px">Off-the-shelf ERP forces your team to bend to the software. We build the opposite — modules mapped to your real workflows, your approval chains, your terminology, deployed as a system you own outright.</p>
 <div class="svc-hero-cta rv">
   <button type="button" data-book class="btn btn-black">Book a Free Consultation →</button>
   <a href="#pboard-1" class="btn btn-outline2">See It In Action</a>
 </div>
</section>

<!-- 2. Key Features -->
<section id="svc-benefits">
 <div class="svc-benefits">
  <div class="svc-benefit rv d1">
   <div class="svc-benefit-check"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text">Custom modules for your exact operating process</div>
  </div>
  <div class="svc-benefit rv d2">
   <div class="svc-benefit-check"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text">Role-based access, approvals &amp; audit trails</div>
  </div>
  <div class="svc-benefit rv d3">
   <div class="svc-benefit-check"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text">Migration from spreadsheets &amp; legacy systems</div>
  </div>
  <div class="svc-benefit rv d4">
   <div class="svc-benefit-check"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text">Integrations with Tally, banking &amp; GST filing</div>
  </div>
 </div>
</section>

<!-- 3. Board Visual #1 — live stats -->
<section id="pboard-1" class="pboard-section">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">The Board</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Your <span class="g">ERP Board</span>, at a glance</h2>
 <p class="sec-sub rv">An illustrative look at the kind of live view this solution gives you.</p>
 <div class="pboard rv">
  <div class="pboard-bar">
   <span class="pboard-dot" style="background:#e5534b"></span>
   <span class="pboard-dot" style="background:#f5a623"></span>
   <span class="pboard-dot" style="background:#32b46f"></span>
   <span class="pboard-title">ERP Overview</span>
  </div>
  <div class="pboard-body">
   <div class="d-krow">
    <div class="d-k"><div class="d-kv" style="color:#32b46f">7/7</div><div class="d-kl">Modules Live</div></div>
    <div class="d-k"><div class="d-kv" style="color:#32b46f">-60%</div><div class="d-kl">Approval Time</div></div>
    <div class="d-k"><div class="d-kv" style="color:#32b46f">12</div><div class="d-kl">Spreadsheets Replaced</div></div>
   </div>
   <div class="d-bars">
    <div class="d-bar" style="height:45%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:60%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:52%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:78%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:65%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:92%;background:#32b46f"></div>
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
   <div class="pain-text">Approvals stall in WhatsApp groups and email threads</div>
  </div>
  <div class="pain-item rv">
   <div class="pain-icon">!</div>
   <div class="pain-text">Every department keeps its own spreadsheet version of the truth</div>
  </div>
  <div class="pain-item rv">
   <div class="pain-icon">!</div>
   <div class="pain-text">New hires take weeks to learn undocumented, ad-hoc processes</div>
  </div>
 </div>
</section>

<!-- 5. Board Visual #2 — records list -->
<section class="pboard-section">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Inside the Board</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Every record, <span class="g">one list</span></h2>
 <p class="sec-sub rv">A second illustrative view — the kind of record list that lives behind the board.</p>
 <div class="pboard-list rv">
  <div class="pboard-row">
   <div class="pboard-row-label">Purchase Order #1042</div>
   <span class="pboard-status pboard-status-good">Approved</span>
  </div>
  <div class="pboard-row">
   <div class="pboard-row-label">Vendor Onboarding — Rajan Steel</div>
   <span class="pboard-status pboard-status-pending">In Review</span>
  </div>
  <div class="pboard-row">
   <div class="pboard-row-label">Monthly Close — June</div>
   <span class="pboard-status pboard-status-pending">In Progress</span>
  </div>
  <div class="pboard-row">
   <div class="pboard-row-label">Audit Trail Export</div>
   <span class="pboard-status pboard-status-good">Completed</span>
  </div>
 </div>
</section>

<!-- 6. How It Connects -->
<section id="svc-connects">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">One Operating System</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">How the <span class="g">ERP connects</span></h2>
 <p class="sec-sub rv">Nothing here lives in isolation — it's built on the same operating system as everything else.</p>
 <div class="svc-tracks svc-connect-grid">
  <div class="svc-track rv">
   <div class="svc-track-title">Ecommerce Solutions</div>
   <p class="connect-text">Orders and stock captured on your storefront flow straight into this same ERP — no manual re-entry.</p>
   <a href="/ecommerce-solutions" class="mega-know">Explore Ecommerce Solutions →</a>
  </div>
  <div class="svc-track rv">
   <div class="svc-track-title">Marketing Solutions</div>
   <p class="connect-text">Every lead your campaigns generate lands in the same system your ERP already runs on.</p>
   <a href="/marketing-solutions" class="mega-know">Explore Marketing Solutions →</a>
  </div>
  <div class="svc-track rv">
   <div class="svc-track-title">The Core Platform</div>
   <p class="connect-text">The ERP is one piece of the same operating system — Sales, Finance, HR and Operations all read from the same data.</p>
   <a href="/#functions" class="mega-know">See All 7 Functions →</a>
  </div>
 </div>
</section>

<?php
$serviceCtaHeading = 'Ready to build <span class="g">your ERP</span>?';
$serviceCtaSub = "Book a free consultation and we'll map out exactly what your operating system should look like.";
include __DIR__ . '/partials/service-proof.php';
?>

<?php include __DIR__ . '/partials/footer.php'; ?>
