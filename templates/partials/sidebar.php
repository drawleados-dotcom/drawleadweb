<?php
/**
 * Sticky right-hand sidebar shown on blog post and case study detail
 * pages: recent blog posts + a "Book a Consultation" CTA.
 * @var array $recentPosts  set by index.php before including the calling template
 */
?>
<aside class="post-sidebar">
  <div class="sidebar-card rv">
    <?php if (!empty($recentPosts)): ?>
    <div class="sidebar-card-title">Recent Posts</div>
    <?php foreach ($recentPosts as $rp): ?>
    <a class="sidebar-post" href="/blog/<?= h($rp['slug']) ?>">
      <div class="sidebar-post-title"><?= h($rp['title']) ?></div>
      <?php if (!empty($rp['excerpt'])): ?><div class="sidebar-post-excerpt"><?= h($rp['excerpt']) ?></div><?php endif; ?>
    </a>
    <?php endforeach; ?>
    <?php endif; ?>

    <div class="sidebar-cta">
      <div class="sidebar-cta-title">Book a Consultation</div>
      <p class="sidebar-cta-text">Ready to take your business to the next level?</p>
      <button type="button" data-book class="btn btn-black sidebar-cta-btn">Book a Free Consultation →</button>
    </div>
  </div>
</aside>
