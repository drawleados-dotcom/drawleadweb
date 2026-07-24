<?php
$activePage = 'custom-erp-solution';
include __DIR__ . '/partials/nav.php';

$serviceCaseStudies = get_case_studies_by_service($pdo, 'Custom ERP Solution');
?>

<section id="svc-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Custom ERP Solution</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="max-width:820px">An ERP shaped around <span class="g">how you actually work</span></h1>
 <p class="sec-sub rv" style="max-width:620px">Off-the-shelf ERP forces your team to bend to the software. We build the opposite — modules mapped to your real workflows, your approval chains, your terminology, deployed as a system you own outright.</p>
 <div class="svc-hero-cta rv">
   <button type="button" data-book class="btn btn-black">Book a Free Consultation →</button>
   <a href="#service-cases" class="btn btn-outline2">See Case Studies</a>
 </div>
</section>

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

<?php
$serviceCtaHeading = 'Ready to build <span class="g">your ERP</span>?';
$serviceCtaSub = "Book a free consultation and we'll map out exactly what your operating system should look like.";
include __DIR__ . '/partials/service-proof.php';
?>

<?php include __DIR__ . '/partials/footer.php'; ?>
