<?php
/** @var array $post  set by index.php before including this template */
$activePage = 'blog';
include __DIR__ . '/partials/nav.php';
?>

<section id="post-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Drawlead Blog</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="max-width:820px"><?= h($post['title']) ?></h1>
 <div class="post-meta rv">
   <?= h(date('F j, Y', strtotime($post['created_at']))) ?>
   <?php if (!empty($post['author_name'])): ?> · By <?= h($post['author_name']) ?><?php endif; ?>
 </div>
 <?php if (!empty($post['featured_image'])): ?>
 <div class="post-featured rv">
   <img src="<?= h(UPLOAD_URL . $post['featured_image']) ?>" alt="<?= h($post['title']) ?>">
 </div>
 <?php endif; ?>
</section>

<section id="post-body">
 <div class="post-content rv">
   <?= $post['content'] /* sanitized HTML from the admin editor, see sanitize_blog_html() */ ?>
 </div>
 <div class="post-back rv"><a href="/blog">← Back to Blog</a></div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
