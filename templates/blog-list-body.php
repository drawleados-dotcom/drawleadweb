<?php
/** @var array $posts set by index.php before including this template */
$activePage = 'blog';
include __DIR__ . '/partials/nav.php';
$featuredPost = $posts[0] ?? null;
$morePosts = array_slice($posts ?? [], $featuredPost ? 1 : 0);
?>
<link rel="stylesheet" href="<?= asset_url('/assets/home10.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/blog-modern.css') ?>">
<script>document.documentElement.classList.add('js')</script>
<div class="h10 blog-page">
<section class="journal-hero" id="top">
  <div class="wrap journal-hero__in">
    <div class="journal-hero__copy">
      <span class="pill pill--mint"><span class="pill__dot" aria-hidden="true"></span>Drawlead Journal</span>
      <h1 class="h1x">Ideas for <span class="grn">better business</span> and smarter growth.</h1>
      <p class="lede">Practical thinking on business systems, digital commerce, automation and the technology that helps teams move forward.</p>
      <a class="h10-btn h10-btn--dark" href="#latest-insights"><span>Explore the latest insights</span><svg class="ic h10-btn__arr" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
    </div>
    <div class="journal-hero__art" aria-hidden="true"><span class="journal-orbit journal-orbit--one"></span><span class="journal-orbit journal-orbit--two"></span><span class="journal-orbit journal-orbit--three"></span><span class="journal-hero__mark">D</span><span class="journal-float journal-float--one">Systems</span><span class="journal-float journal-float--two">Ideas</span><span class="journal-float journal-float--three">Growth</span></div>
  </div>
</section>

<section class="journal-content" id="latest-insights">
  <div class="wrap">
    <div class="journal-section-head"><div><span class="journal-kicker">From our team</span><h2>Latest insights</h2></div><span class="journal-count"><?= count($posts ?? []) ?> <?= count($posts ?? []) === 1 ? 'article' : 'articles' ?></span></div>
    <?php if (!$featuredPost): ?>
      <div class="journal-empty"><span class="journal-empty__icon" aria-hidden="true">✳</span><h3>Fresh ideas are on the way.</h3><p>We’re preparing practical guides and perspectives for growing businesses. Check back soon.</p></div>
    <?php else: ?>
      <a class="journal-feature" href="/blog/<?= h($featuredPost['slug']) ?>">
        <div class="journal-feature__image">
          <?php if (!empty($featuredPost['featured_image'])): ?><img src="<?= h(UPLOAD_URL . $featuredPost['featured_image']) ?>" alt="<?= h($featuredPost['featured_image_alt'] ?: $featuredPost['title']) ?>" loading="lazy">
          <?php else: ?><div class="journal-image-placeholder"><span>Drawlead Journal</span></div><?php endif; ?>
          <span class="journal-feature__badge">Featured insight</span>
        </div>
        <div class="journal-feature__body"><div class="journal-meta"><span><?= h(date('M j, Y', strtotime($featuredPost['created_at']))) ?></span><span class="journal-meta__dot"></span><span>Drawlead team</span></div><h3><?= h($featuredPost['title']) ?></h3><p><?= h($featuredPost['excerpt']) ?></p><span class="journal-read">Read the article <span aria-hidden="true">↗</span></span></div>
      </a>
      <?php if ($morePosts): ?><div class="journal-grid">
        <?php foreach ($morePosts as $post): ?>
        <a class="journal-card" href="/blog/<?= h($post['slug']) ?>">
          <div class="journal-card__image">
            <?php if (!empty($post['featured_image'])): ?><img src="<?= h(UPLOAD_URL . $post['featured_image']) ?>" alt="<?= h($post['featured_image_alt'] ?: $post['title']) ?>" loading="lazy">
            <?php else: ?><div class="journal-image-placeholder"><span>Drawlead Journal</span></div><?php endif; ?>
          </div>
          <div class="journal-card__body"><div class="journal-meta"><span><?= h(date('M j, Y', strtotime($post['created_at']))) ?></span></div><h3><?= h($post['title']) ?></h3><p><?= h($post['excerpt']) ?></p><span class="journal-read">Read the article <span aria-hidden="true">↗</span></span></div>
        </a>
        <?php endforeach; ?>
      </div><?php endif; ?>
    <?php endif; ?>
  </div>
</section>
</main>
<?php include __DIR__ . '/partials/modern-footer.php'; ?>
</div>
<script src="<?= asset_url('/assets/home10.js') ?>" defer></script>
