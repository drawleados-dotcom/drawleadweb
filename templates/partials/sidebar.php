<?php
/**
 * Sticky right-hand sidebar shown on blog post and case study detail
 * pages: recent blog posts + an admin-managed CTA block (Admin → Sidebar).
 * @var array $recentPosts  set by index.php before including the calling template
 */
$sidebarCta = get_site_sidebar($pdo);
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

    <?php if (!empty($sidebarCta['enabled'])): ?>
    <div class="sidebar-cta">
      <?php if (!empty($sidebarCta['image'])): ?>
      <img class="sidebar-cta-img" src="<?= h(UPLOAD_URL . $sidebarCta['image']) ?>" alt="<?= h($sidebarCta['image_alt'] ?: $sidebarCta['title']) ?>">
      <?php endif; ?>
      <?php if (!empty($sidebarCta['title'])): ?><div class="sidebar-cta-title"><?= h($sidebarCta['title']) ?></div><?php endif; ?>
      <?php if (!empty($sidebarCta['text'])): ?><p class="sidebar-cta-text"><?= h($sidebarCta['text']) ?></p><?php endif; ?>
      <?php if (!empty($sidebarCta['cta_text'])): ?>
        <?php if (!empty($sidebarCta['cta_use_booking'])): ?>
        <button type="button" data-book class="btn btn-black sidebar-cta-btn"><?= h($sidebarCta['cta_text']) ?></button>
        <?php else: ?>
        <a href="<?= h($sidebarCta['cta_link'] ?: '#') ?>" class="btn btn-black sidebar-cta-btn"><?= h($sidebarCta['cta_text']) ?></a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</aside>
