<?php
/** @var array $studies  set by index.php before including this template */
$activePage = 'case-studies';
include __DIR__ . '/partials/nav.php';
?>

<section id="blog-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Case Studies</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv">Real results for <span class="g">real businesses</span></h1>
 <p class="sec-sub rv" style="max-width:560px">See how Drawlead has helped clients across construction, healthcare, and marketing streamline operations and grow.</p>
</section>

<section id="blog-list">
 <div class="blog-grid">
  <?php if (empty($studies)): ?>
    <div class="empty-state" style="grid-column:1/-1">
      <p style="text-align:center;color:var(--g400);font-size:14px">No case studies published yet. Check back soon.</p>
    </div>
  <?php else: foreach ($studies as $cs): ?>
    <a class="blog-card rv" href="/case-studies/<?= h($cs['slug']) ?>">
      <?php if (!empty($cs['desktop_image'])): ?>
        <div class="blog-card-img"><img src="<?= h(UPLOAD_URL . $cs['desktop_image']) ?>" alt="<?= h($cs['title']) ?>" loading="lazy"></div>
      <?php else: ?>
        <div class="blog-card-img placeholder"></div>
      <?php endif; ?>
      <div class="blog-card-body">
        <?php if (!empty($cs['services'])): $firstService = trim(explode(',', $cs['services'])[0]); ?>
          <div class="cs-card-tag"><?= h($firstService) ?></div>
        <?php endif; ?>
        <div class="blog-card-title"><?= h($cs['title']) ?></div>
        <?php if (!empty($cs['client_name'])): ?><div class="blog-card-date"><?= h($cs['client_name']) ?></div><?php endif; ?>
        <div class="blog-card-excerpt"><?= h($cs['description']) ?></div>
        <span class="blog-card-arrow">Read Case Study →</span>
      </div>
    </a>
  <?php endforeach; endif; ?>
 </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
