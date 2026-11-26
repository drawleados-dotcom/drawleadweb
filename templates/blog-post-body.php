<?php
/** @var array $post set by index.php before including this template */
/** @var array $recentPosts set by index.php before including this template */
$activePage = 'blog';
include __DIR__ . '/partials/nav.php';
?>
<link rel="stylesheet" href="<?= asset_url('/assets/home10.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/blog-modern.css') ?>">
<script>document.documentElement.classList.add('js')</script>
<div class="h10 blog-page">
<section class="journal-post-hero" id="top">
  <div class="wrap journal-post-hero__in">
    <span class="pill pill--mint"><span class="pill__dot" aria-hidden="true"></span>Drawlead Journal</span>
    <h1><?= h($post['title']) ?></h1>
    <div class="journal-post-meta"><?= h(date('F j, Y', strtotime($post['created_at']))) ?><?php if (!empty($post['author_name'])): ?> &nbsp;·&nbsp; By <?= h($post['author_name']) ?><?php else: ?> &nbsp;·&nbsp; Drawlead team<?php endif; ?></div>
    <?php if (!empty($post['featured_image'])): ?>
    <div class="journal-post-image"><img src="<?= h(UPLOAD_URL . $post['featured_image']) ?>" alt="<?= h($post['featured_image_alt'] ?: $post['title']) ?>"></div>
    <?php endif; ?>
  </div>
</section>
<section class="journal-post-section">
  <div class="wrap journal-post-layout">
    <article class="journal-post-content">
      <?= $post['content'] /* sanitized HTML from the admin editor, see sanitize_blog_html() */ ?>
      <a class="journal-back" href="/blog"><span aria-hidden="true">←</span> Back to all insights</a>
    </article>
    <div class="journal-post-aside"><?php include __DIR__ . '/partials/sidebar.php'; ?></div>
  </div>
</section>
</main>
<?php include __DIR__ . '/partials/modern-footer.php'; ?>
</div>
<script src="<?= asset_url('/assets/home10.js') ?>" defer></script>
