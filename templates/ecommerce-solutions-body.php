<?php
$activePage = 'ecommerce-solutions';
include __DIR__ . '/partials/nav.php';

$serviceCaseStudies = get_case_studies_by_service($pdo, 'Ecommerce Solutions');
?>

<!-- 1. Hero -->
<section id="svc-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Ecommerce Solutions</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="max-width:820px">From storefront to fulfilment — <span class="g">one connected stack</span></h1>
 <p class="sec-sub rv" style="max-width:620px">Launch and scale an online store that talks directly to your inventory, billing, and delivery operations. No spreadsheets in between, no orders lost in the gap between platforms.</p>
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
   <div class="svc-benefit-text">Shopify, WooCommerce &amp; custom storefront builds</div>
  </div>
  <div class="svc-benefit rv d2">
   <div class="svc-benefit-check"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text">Live inventory sync across every sales channel</div>
  </div>
  <div class="svc-benefit rv d3">
   <div class="svc-benefit-check"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text">Automated order, invoice &amp; GST workflows</div>
  </div>
  <div class="svc-benefit rv d4">
   <div class="svc-benefit-check"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text">Payments, logistics &amp; returns handled end to end</div>
  </div>
 </div>
</section>

<!-- 3. Board Visual #1 — live stats -->
<section id="pboard-1" class="pboard-section">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">The Board</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Your <span class="g">storefront</span>, live</h2>
 <p class="sec-sub rv">An illustrative look at the kind of live view this solution gives you.</p>
 <div class="pboard rv">
  <div class="pboard-bar">
   <span class="pboard-dot" style="background:#e5534b"></span>
   <span class="pboard-dot" style="background:#f5a623"></span>
   <span class="pboard-dot" style="background:#32b46f"></span>
   <span class="pboard-title">Storefront Overview</span>
  </div>
  <div class="pboard-body">
   <div class="d-krow">
    <div class="d-k"><div class="d-kv" style="color:#32b46f">842</div><div class="d-kl">Orders Synced</div></div>
    <div class="d-k"><div class="d-kv" style="color:#32b46f">4</div><div class="d-kl">Channels Connected</div></div>
    <div class="d-k"><div class="d-kv" style="color:#32b46f">99.2%</div><div class="d-kl">Stock Accuracy</div></div>
   </div>
   <div class="d-bars">
    <div class="d-bar" style="height:50%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:68%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:55%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:82%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:70%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:95%;background:#32b46f"></div>
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
   <div class="pain-text">Stock oversold because two channels don't talk to each other</div>
  </div>
  <div class="pain-item rv">
   <div class="pain-icon">!</div>
   <div class="pain-text">Every order re-typed into billing and delivery software by hand</div>
  </div>
  <div class="pain-item rv">
   <div class="pain-icon">!</div>
   <div class="pain-text">Returns and refunds tracked in a separate spreadsheet</div>
  </div>
 </div>
</section>

<!-- 5. Board Visual #2 — records list -->
<section class="pboard-section">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Inside the Board</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Every order, <span class="g">one list</span></h2>
 <p class="sec-sub rv">A second illustrative view — the kind of record list that lives behind the board.</p>
 <div class="pboard-list rv">
  <div class="pboard-row">
   <div class="pboard-row-label">Order #4821</div>
   <span class="pboard-status pboard-status-good">Shipped</span>
  </div>
  <div class="pboard-row">
   <div class="pboard-row-label">Return #112</div>
   <span class="pboard-status pboard-status-pending">Refund Processing</span>
  </div>
  <div class="pboard-row">
   <div class="pboard-row-label">Shopify Sync</div>
   <span class="pboard-status pboard-status-good">Completed</span>
  </div>
  <div class="pboard-row">
   <div class="pboard-row-label">GST Invoice Batch</div>
   <span class="pboard-status pboard-status-good">Generated</span>
  </div>
 </div>
</section>

<!-- 6. How It Connects -->
<section id="svc-connects">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">One Operating System</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">How the <span class="g">storefront connects</span></h2>
 <p class="sec-sub rv">Nothing here lives in isolation — it's built on the same operating system as everything else.</p>
 <div class="svc-tracks svc-connect-grid">
  <div class="svc-track rv">
   <div class="svc-track-title">Custom ERP Solution</div>
   <p class="connect-text">Every order updates the same inventory and billing your ERP runs on — no separate system to reconcile.</p>
   <a href="/custom-erp-solution" class="mega-know">Explore Custom ERP Solution →</a>
  </div>
  <div class="svc-track rv">
   <div class="svc-track-title">Marketing Solutions</div>
   <p class="connect-text">Campaign traffic converts straight into orders you can already see on this same dashboard.</p>
   <a href="/marketing-solutions" class="mega-know">Explore Marketing Solutions →</a>
  </div>
  <div class="svc-track rv">
   <div class="svc-track-title">The Core Platform</div>
   <p class="connect-text">Your storefront plugs into the same 7-function operating system as the rest of your business.</p>
   <a href="/#functions" class="mega-know">See All 7 Functions →</a>
  </div>
 </div>
</section>

<?php
$serviceCtaHeading = 'Ready to launch <span class="g">your store</span>?';
$serviceCtaSub = "Book a free consultation and we'll map your storefront, inventory, and fulfilment onto one connected stack.";
include __DIR__ . '/partials/service-proof.php';
?>

<?php include __DIR__ . '/partials/footer.php'; ?>
