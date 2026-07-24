<?php
/**
 * @var array $studies             set by index.php before including this template
 * @var array $caseStudyServices   set by index.php — services used by at least one published case study
 */
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
 <?php if (!empty($caseStudyServices)): ?>
 <div class="cs-filter-tabs rv">
  <button type="button" class="cs-filter-tab active" data-filter="all">All</button>
  <?php foreach ($caseStudyServices as $service): ?>
  <button type="button" class="cs-filter-tab" data-filter="<?= h($service['name']) ?>"><?= h($service['name']) ?></button>
  <?php endforeach; ?>
 </div>
 <?php endif; ?>
 <div class="blog-grid" id="cs-grid">
  <?php if (empty($studies)): ?>
    <div class="empty-state" style="grid-column:1/-1">
      <p style="text-align:center;color:var(--g400);font-size:14px">No case studies published yet. Check back soon.</p>
    </div>
  <?php else: foreach ($studies as $cs): ?>
    <a class="blog-card rv" href="/case-studies/<?= h($cs['slug']) ?>" data-services="<?= h($cs['services']) ?>">
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
 <div class="empty-state" id="cs-filter-empty" style="display:none">
   <p style="text-align:center;color:var(--g400);font-size:14px">No case studies for this service yet.</p>
 </div>
</section>

<?php if (!empty($caseStudyServices)): ?>
<script>
(function () {
  var tabs = document.querySelectorAll('.cs-filter-tab');
  var cards = document.querySelectorAll('#cs-grid [data-services]');
  var emptyState = document.getElementById('cs-filter-empty');
  if (!tabs.length) return;

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      tabs.forEach(function (t) { t.classList.remove('active'); });
      tab.classList.add('active');
      var filter = tab.getAttribute('data-filter');
      var visibleCount = 0;
      cards.forEach(function (card) {
        var services = (card.getAttribute('data-services') || '').split(',').map(function (s) { return s.trim(); });
        var match = filter === 'all' || services.indexOf(filter) !== -1;
        card.style.display = match ? '' : 'none';
        if (match) visibleCount++;
      });
      emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
    });
  });
})();
</script>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
