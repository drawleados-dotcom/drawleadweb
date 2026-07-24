<?php
/**
 * Shared renderer for all 7 Platform module pages. Which module to show
 * comes from $page['slug'] (set by index.php's generic pages route,
 * e.g. /platform-sales → 'sales') — content itself lives in
 * includes/platform-modules.php, not the database.
 * @var array $page  set by index.php before including this template
 */
$moduleKey = preg_replace('#^/platform-#', '', $page['slug']);
$modules = platform_modules();
$module = $modules[$moduleKey] ?? null;

if (!$module) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    return;
}

$activePage = 'platform-' . $moduleKey;
include __DIR__ . '/partials/nav.php';

$moduleCaseStudies = get_case_studies_by_service($pdo, $module['name']);
$allModulesOrdered = platform_modules_ordered();
?>

<!-- 1. Hero -->
<section id="svc-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Platform · <?= h($module['number']) ?> — <?= h($module['name']) ?></span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="max-width:820px"><?= h($module['tagline']) ?></h1>
 <p class="sec-sub rv" style="max-width:640px"><?= h($module['description']) ?></p>
 <div class="svc-hero-cta rv">
   <button type="button" data-book class="btn btn-black">Book a Free Consultation →</button>
   <a href="#pboard-1" class="btn btn-outline2">See It In Action</a>
 </div>
</section>

<!-- 2. Key Features -->
<section id="svc-benefits">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">What's Included</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Everything <span class="g"><?= h($module['name']) ?></span> needs</h2>
 <div class="svc-benefits">
  <?php foreach ($module['features'] as $i => $feature): ?>
  <div class="svc-benefit rv d<?= ($i % 4) + 1 ?>">
   <div class="svc-benefit-check" style="background:<?= h($module['color']) ?>26"><svg width="14" height="14" viewBox="0 0 12 12" fill="none"><path d="M2 6.2 L4.7 9 L10 3.2" stroke="<?= h($module['color']) ?>" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
   <div class="svc-benefit-text"><?= h($feature) ?></div>
  </div>
  <?php endforeach; ?>
 </div>
</section>

<!-- 3. Board Visual #1 — live stats -->
<section id="pboard-1" class="pboard-section">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">The Board</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Your <span class="g"><?= h($module['name']) ?> Board</span>, at a glance</h2>
 <p class="sec-sub rv">An illustrative look at the kind of live view this module gives you.</p>
 <div class="pboard rv">
  <div class="pboard-bar">
   <span class="pboard-dot" style="background:#e5534b"></span>
   <span class="pboard-dot" style="background:#f5a623"></span>
   <span class="pboard-dot" style="background:<?= h($module['color']) ?>"></span>
   <span class="pboard-title"><?= h($module['name']) ?> Overview</span>
  </div>
  <div class="pboard-body">
   <div class="d-krow">
    <?php foreach ($module['board1_stats'] as $stat): ?>
    <div class="d-k"><div class="d-kv" style="color:<?= h($module['color']) ?>"><?= h($stat['v']) ?></div><div class="d-kl"><?= h($stat['l']) ?></div></div>
    <?php endforeach; ?>
   </div>
   <div class="d-bars">
    <?php foreach ($module['board1_bars'] as $i => $height): ?>
    <div class="d-bar" style="height:<?= (int) $height ?>%;background:<?= $i === count($module['board1_bars']) - 1 ? h($module['color']) : 'rgba(50,180,111,.4)' ?>"></div>
    <?php endforeach; ?>
   </div>
  </div>
 </div>
</section>

<!-- 4. Why It Matters -->
<section id="svc-pain">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Why It Matters</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">What happens <span class="g">without it</span></h2>
 <div class="pain-list">
  <?php foreach ($module['pain_points'] as $point): ?>
  <div class="pain-item rv">
   <div class="pain-icon">!</div>
   <div class="pain-text"><?= h($point) ?></div>
  </div>
  <?php endforeach; ?>
 </div>
</section>

<!-- 5. Board Visual #2 — records list -->
<section class="pboard-section">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Inside the Board</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Every record, <span class="g">one list</span></h2>
 <p class="sec-sub rv">A second illustrative view — the kind of record list that lives behind the board.</p>
 <div class="pboard-list rv">
  <?php foreach ($module['board2_rows'] as $row): ?>
  <div class="pboard-row">
   <div class="pboard-row-label"><?= h($row['label']) ?></div>
   <span class="pboard-status pboard-status-<?= h($row['tone']) ?>"><?= h($row['status']) ?></span>
  </div>
  <?php endforeach; ?>
 </div>
</section>

<!-- 6. How It Connects -->
<section id="svc-connects">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">One Operating System</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">How <span class="g"><?= h($module['name']) ?></span> connects</h2>
 <p class="sec-sub rv">Nothing here lives in isolation — it's one of 7 modules working off the same data.</p>
 <div class="svc-tracks svc-connect-grid">
  <?php foreach ($module['connects'] as $connect):
    $targetKey = null;
    foreach ($allModulesOrdered as $entry) {
        if ($entry['module']['name'] === $connect['module']) {
            $targetKey = $entry['key'];
            break;
        }
    }
  ?>
  <div class="svc-track rv">
   <div class="svc-track-title"><?= h($connect['module']) ?></div>
   <p class="connect-text"><?= h($connect['text']) ?></p>
   <?php if ($targetKey): ?><a href="/platform-<?= h($targetKey) ?>" class="mega-know">Explore <?= h($connect['module']) ?> →</a><?php endif; ?>
  </div>
  <?php endforeach; ?>
 </div>
</section>

<!-- 7. Case Studies -->
<?php if (!empty($moduleCaseStudies)): ?>
<section id="service-cases">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Case Studies</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Real <span class="g">results</span></h2>
 <p class="sec-sub rv">Client work delivered with this exact module.</p>
 <div class="blog-grid">
   <?php foreach ($moduleCaseStudies as $cs): ?>
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

<!-- 8. CTA -->
<section id="service-cta">
 <h2 class="sec-h rv" style="font-size:clamp(26px,4vw,38px)">Ready to bring <span class="g"><?= h($module['name']) ?></span> in sync?</h2>
 <p class="sec-sub rv">Book a free consultation and we'll show you exactly how this module fits your operation.</p>
 <button type="button" data-book class="btn btn-black rv">Book a Free Consultation →</button>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
