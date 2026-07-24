<?php
/**
 * Shared renderer for all 20 Industry pages. Which industry to show comes
 * from $page['slug'] (set by index.php's generic pages route, e.g.
 * /industry-jewellery → 'jewellery') — content itself lives in
 * includes/industries.php, not the database.
 * @var array $page  set by index.php before including this template
 */
$industryKey = preg_replace('#^/industry-#', '', $page['slug']);
$all = industries();
$industry = $all[$industryKey] ?? null;

if (!$industry) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    return;
}

$activePage = 'industry-' . $industryKey;
include __DIR__ . '/partials/nav.php';

$industryCaseStudies = get_case_studies_by_service($pdo, $industry['name']);
?>

<!-- Hero -->
<section id="svc-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Industry · <?= h($industry['tag']) ?></span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="max-width:820px"><?= h($industry['tagline']) ?></h1>
 <p class="sec-sub rv" style="max-width:640px"><?= h($industry['description']) ?></p>
 <div class="svc-hero-cta rv">
   <button type="button" data-book class="btn btn-black">Book a Free Consultation →</button>
   <a href="#industry-outcome" class="btn btn-outline2">See Expected Outcome</a>
 </div>
</section>

<!-- The Problem -->
<section>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">The Problem</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Where <span class="g"><?= h($industry['name']) ?></span> teams get stuck</h2>
 <div class="pain-list">
  <?php foreach ($industry['problems'] as $point): ?>
  <div class="pain-item rv">
   <div class="pain-icon">!</div>
   <div class="pain-text"><?= h($point) ?></div>
  </div>
  <?php endforeach; ?>
 </div>
</section>

<!-- Unified ERP Solution -->
<section style="background:var(--bg2)">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Unified ERP Solution</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Built for how <span class="g"><?= h($industry['name']) ?></span> actually works</h2>
 <p class="sec-sub rv">One system for the workflows that matter most in this industry.</p>
 <div class="svc-benefits">
  <?php foreach ($industry['solutions'] as $i => $solution): ?>
  <div class="svc-benefit rv d<?= ($i % 4) + 1 ?>">
   <div class="svc-benefit-check" style="background:<?= h($industry['color']) ?>26"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="<?= h($industry['color']) ?>" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text"><?= h($solution) ?></div>
  </div>
  <?php endforeach; ?>
 </div>
</section>

<!-- Expected Outcome -->
<section id="industry-outcome">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Expected Outcome</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">What <span class="g">changes</span> after go-live</h2>
 <p class="sec-sub rv">An illustrative look at the kind of results this unlocks.</p>
 <div class="d-krow" style="max-width:640px;margin:0 auto;gap:16px">
  <?php foreach ($industry['outcomes'] as $outcome): ?>
  <div class="d-k" style="padding:1.25rem 1rem;text-align:center">
   <div class="d-kv" style="color:<?= h($industry['color']) ?>;font-size:15px;margin-bottom:6px"><?= h($outcome) ?></div>
  </div>
  <?php endforeach; ?>
 </div>
</section>

<!-- Case Studies -->
<?php if (!empty($industryCaseStudies)): ?>
<section id="service-cases">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Case Studies</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Real <span class="g">results</span></h2>
 <p class="sec-sub rv">Client work delivered for <?= h($industry['name']) ?> businesses.</p>
 <div class="blog-grid">
   <?php foreach ($industryCaseStudies as $cs): ?>
   <a class="blog-card rv" href="/case-studies/<?= h($cs['slug']) ?>">
     <?php if (!empty($cs['desktop_image'])): ?>
       <div class="blog-card-img"><img src="<?= h(UPLOAD_URL . $cs['desktop_image']) ?>" alt="<?= h($cs['title']) ?>" loading="lazy"></div>
     <?php else: ?>
       <div class="blog-card-img placeholder"></div>
     <?php endif; ?>
     <div class="blog-card-body">
       <div class="blog-card-title"><?= h($cs['title']) ?></div>
       <?php if (!empty($cs['client_name'])): ?><div class="blog-card-date"><?= h($cs['client_name']) ?></div><?php endif; ?>
       <div class="blog-card-excerpt"><?= h($cs['description']) ?></div>
       <span class="blog-card-arrow">Read Case Study →</span>
     </div>
   </a>
   <?php endforeach; ?>
 </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section id="service-cta">
 <h2 class="sec-h rv" style="font-size:clamp(26px,4vw,38px)">Ready to build your <span class="g"><?= h($industry['name']) ?></span> OS?</h2>
 <p class="sec-sub rv">Book a free consultation and we'll show you exactly how this fits your operation.</p>
 <button type="button" data-book class="btn btn-black rv">Book a Free Consultation →</button>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
