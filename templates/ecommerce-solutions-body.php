<?php
$activePage = 'ecommerce-solutions';
include __DIR__ . '/partials/nav.php';

$serviceCaseStudies = get_case_studies_by_service($pdo, 'Ecommerce Solutions');
?>

<section id="svc-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Ecommerce Solutions</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="max-width:820px">From storefront to fulfilment — <span class="g">one connected stack</span></h1>
 <p class="sec-sub rv" style="max-width:620px">Launch and scale an online store that talks directly to your inventory, billing, and delivery operations. No spreadsheets in between, no orders lost in the gap between platforms.</p>
 <div class="svc-hero-cta rv">
   <button type="button" data-book class="btn btn-black">Book a Free Consultation →</button>
   <a href="#service-cases" class="btn btn-outline2">See Case Studies</a>
 </div>
</section>

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

<?php
$serviceCtaHeading = 'Ready to launch <span class="g">your store</span>?';
$serviceCtaSub = "Book a free consultation and we'll map your storefront, inventory, and fulfilment onto one connected stack.";
include __DIR__ . '/partials/service-proof.php';
?>

<?php include __DIR__ . '/partials/footer.php'; ?>
