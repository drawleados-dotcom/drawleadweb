<?php
$activePage = 'marketing-solutions';
include __DIR__ . '/partials/nav.php';

$serviceCaseStudies = get_case_studies_by_service($pdo, 'Marketing Solutions');
?>

<!-- 1. Hero -->
<section id="svc-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Marketing Solutions</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="max-width:820px">Fix the leak between <span class="g">lead and conversion</span></h1>
 <p class="sec-sub rv" style="max-width:620px">Most businesses don't have a traffic problem — they have a follow-up problem. Two engines run the funnel: organic search that compounds over time, and paid campaigns that buy demand on demand.</p>
 <div class="svc-hero-cta rv">
   <button type="button" data-book class="btn btn-black">Book a Free Consultation →</button>
   <a href="#pboard-1" class="btn btn-outline2">See It In Action</a>
 </div>
</section>

<!-- 2. Key Features -->
<section id="svc-tracks-section">
 <div class="svc-tracks">
  <div class="svc-track rv d1">
   <div class="svc-track-title">
     <svg width="18" height="18" viewBox="0 0 40 40" fill="none"><rect x="5" y="5" width="13" height="13" rx="2.5" fill="#32b46f"/><rect x="22" y="5" width="13" height="13" rx="2.5" fill="#89d4ac"/><rect x="5" y="22" width="13" height="13" rx="2.5" fill="#89d4ac"/><rect x="22" y="22" width="13" height="13" rx="2.5" fill="#5fc492"/></svg>
     Search Engine Optimization
   </div>
   <ul class="sol-list">
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Technical audits, Core Web Vitals &amp; site architecture</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Keyword strategy built around buying intent</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Content engine &amp; on-page optimisation at scale</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Local SEO, Google Business Profile &amp; schema markup</li>
   </ul>
  </div>

  <div class="svc-track rv d2">
   <div class="svc-track-title">
     <svg width="18" height="18" viewBox="0 0 40 40" fill="none"><path d="M4 6 L36 6 L24 21 L24 34 L16 30 L16 21 Z" fill="#32b46f"/></svg>
     Performance Marketing
   </div>
   <ul class="sol-list">
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Google, Meta &amp; LinkedIn campaigns managed end to end</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Landing pages &amp; creative built for conversion testing</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Instant WhatsApp &amp; email follow-up on every lead</li>
    <li><span class="sol-check"><svg width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="#32b46f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Ad spend mapped to closed revenue, not just clicks</li>
   </ul>
  </div>
 </div>
</section>

<!-- 3. Board Visual #1 — live stats -->
<section id="pboard-1" class="pboard-section">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">The Board</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Your <span class="g">funnel</span>, live</h2>
 <p class="sec-sub rv">An illustrative look at the kind of live view this solution gives you.</p>
 <div class="pboard rv">
  <div class="pboard-bar">
   <span class="pboard-dot" style="background:#e5534b"></span>
   <span class="pboard-dot" style="background:#f5a623"></span>
   <span class="pboard-dot" style="background:#32b46f"></span>
   <span class="pboard-title">Funnel Overview</span>
  </div>
  <div class="pboard-body">
   <div class="d-krow">
    <div class="d-k"><div class="d-kv" style="color:#32b46f">4&times;</div><div class="d-kl">Organic Traffic</div></div>
    <div class="d-k"><div class="d-kv" style="color:#32b46f">&lt;5 min</div><div class="d-kl">Response Time</div></div>
    <div class="d-k"><div class="d-kv" style="color:#32b46f">312</div><div class="d-kl">Leads This Month</div></div>
   </div>
   <div class="d-bars">
    <div class="d-bar" style="height:40%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:58%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:48%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:72%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:64%;background:rgba(50,180,111,.4)"></div>
    <div class="d-bar" style="height:90%;background:#32b46f"></div>
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
   <div class="pain-text">Leads sit unanswered for hours because no one owns follow-up</div>
  </div>
  <div class="pain-item rv">
   <div class="pain-icon">!</div>
   <div class="pain-text">SEO and paid campaigns run by different people with no shared view</div>
  </div>
  <div class="pain-item rv">
   <div class="pain-icon">!</div>
   <div class="pain-text">No visibility into which channel actually closes deals</div>
  </div>
 </div>
</section>

<!-- 5. Board Visual #2 — records list -->
<section class="pboard-section">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Inside the Board</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Every lead, <span class="g">one list</span></h2>
 <p class="sec-sub rv">A second illustrative view — the kind of record list that lives behind the board.</p>
 <div class="pboard-list rv">
  <div class="pboard-row">
   <div class="pboard-row-label">New Lead — WhatsApp Auto-Reply</div>
   <span class="pboard-status pboard-status-good">Sent</span>
  </div>
  <div class="pboard-row">
   <div class="pboard-row-label">Google Ads Campaign</div>
   <span class="pboard-status pboard-status-good">Live</span>
  </div>
  <div class="pboard-row">
   <div class="pboard-row-label">Blog Post — "ERP for Construction"</div>
   <span class="pboard-status pboard-status-pending">Ranking #4</span>
  </div>
  <div class="pboard-row">
   <div class="pboard-row-label">Follow-up Sequence</div>
   <span class="pboard-status pboard-status-good">Completed</span>
  </div>
 </div>
</section>

<!-- 6. How It Connects -->
<section id="svc-connects">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">One Operating System</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">How the <span class="g">funnel connects</span></h2>
 <p class="sec-sub rv">Nothing here lives in isolation — it's built on the same operating system as everything else.</p>
 <div class="svc-tracks svc-connect-grid">
  <div class="svc-track rv">
   <div class="svc-track-title">Custom ERP Solution</div>
   <p class="connect-text">Every lead that converts becomes a record in the same ERP your team already works from.</p>
   <a href="/custom-erp-solution" class="mega-know">Explore Custom ERP Solution →</a>
  </div>
  <div class="svc-track rv">
   <div class="svc-track-title">Ecommerce Solutions</div>
   <p class="connect-text">Campaign traffic lands straight on a storefront wired into the same stack.</p>
   <a href="/ecommerce-solutions" class="mega-know">Explore Ecommerce Solutions →</a>
  </div>
  <div class="svc-track rv">
   <div class="svc-track-title">The Core Platform</div>
   <p class="connect-text">Marketing performance rolls up into the same operating system as Sales, Finance and Operations.</p>
   <a href="/#functions" class="mega-know">See All 7 Functions →</a>
  </div>
 </div>
</section>

<?php
$serviceCtaHeading = 'Ready to fix <span class="g">your funnel</span>?';
$serviceCtaSub = "Book a free consultation and we'll show you exactly where leads are falling through the cracks.";
include __DIR__ . '/partials/service-proof.php';
?>

<?php include __DIR__ . '/partials/footer.php'; ?>
