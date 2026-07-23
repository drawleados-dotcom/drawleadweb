<?php
/** @var array $posts  set by index.php before including this template */
$activePage = 'blog';
include __DIR__ . '/partials/nav.php';
?>

<section id="blog-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Drawlead Blog</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv">Insights on <span class="g">growth &amp; digital transformation</span></h1>
 <p class="sec-sub rv" style="max-width:560px">Practical guides on websites, SEO, performance marketing, and building systems that scale — from the Drawlead team.</p>
</section>

<section id="blog-list">
 <div class="blog-grid">
  <?php if (empty($posts)): ?>
    <div class="empty-state" style="grid-column:1/-1">
      <p style="text-align:center;color:var(--g400);font-size:14px">No posts published yet. Check back soon.</p>
    </div>
  <?php else: foreach ($posts as $post): ?>
    <a class="blog-card rv" href="/blog/<?= h($post['slug']) ?>">
      <?php if (!empty($post['featured_image'])): ?>
        <div class="blog-card-img" style="background-image:url('<?= h(UPLOAD_URL . $post['featured_image']) ?>')"></div>
      <?php else: ?>
        <div class="blog-card-img placeholder"></div>
      <?php endif; ?>
      <div class="blog-card-body">
        <div class="blog-card-date"><?= h(date('M j, Y', strtotime($post['created_at']))) ?></div>
        <div class="blog-card-title"><?= h($post['title']) ?></div>
        <div class="blog-card-excerpt"><?= h($post['excerpt']) ?></div>
        <span class="blog-card-arrow">Read More →</span>
      </div>
    </a>
  <?php endforeach; endif; ?>
 </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
