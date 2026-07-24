<?php
$activePage = 'marketing-solutions';
include __DIR__ . '/partials/nav.php';

$serviceCaseStudies = get_case_studies_by_service($pdo, 'Marketing Solutions');
?>

<section id="svc-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Marketing Solutions</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="max-width:820px">Fix the leak between <span class="g">lead and conversion</span></h1>
 <p class="sec-sub rv" style="max-width:620px">Most businesses don't have a traffic problem — they have a follow-up problem. Two engines run the funnel: organic search that compounds over time, and paid campaigns that buy demand on demand.</p>
 <div class="svc-hero-cta rv">
   <button type="button" data-book class="btn btn-black">Book a Free Consultation →</button>
   <a href="#service-cases" class="btn btn-outline2">See Case Studies</a>
 </div>
</section>

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

<?php
$serviceCtaHeading = 'Ready to fix <span class="g">your funnel</span>?';
$serviceCtaSub = "Book a free consultation and we'll show you exactly where leads are falling through the cracks.";
include __DIR__ . '/partials/service-proof.php';
?>

<?php include __DIR__ . '/partials/footer.php'; ?>
