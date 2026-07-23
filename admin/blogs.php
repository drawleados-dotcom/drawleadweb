<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_blogs_access($pdo);

$posts = $pdo->query(
    'SELECT b.*, u.name AS author_name FROM blogs b
     LEFT JOIN users u ON u.id = b.author_id
     ORDER BY b.updated_at DESC'
)->fetchAll();

$pageTitle = 'Blogs';
$pageSub = 'Write and manage blog posts — just like a WordPress blog.';
$activeNav = 'blogs';
include __DIR__ . '/includes/header.php';
?>

<div class="toolbar">
  <div class="topbar-sub"><?= count($posts) ?> post<?= count($posts) === 1 ? '' : 's' ?></div>
  <a href="blog-edit.php" class="btn btn-primary">+ New Post</a>
</div>

<div class="card">
  <?php if (empty($posts)): ?>
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
      <p>No blog posts yet.</p>
      <a class="row-link" href="blog-edit.php">Write your first post →</a>
    </div>
  <?php else: ?>
  <table>
    <thead><tr><th></th><th>Title</th><th>Status</th><th>Author</th><th>Updated</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($posts as $p): ?>
      <tr>
        <td>
          <?php if ($p['featured_image']): ?>
            <img class="thumb" src="<?= h(UPLOAD_URL . $p['featured_image']) ?>" alt="">
          <?php else: ?>
            <div class="thumb"></div>
          <?php endif; ?>
        </td>
        <td>
          <a class="row-link" href="blog-edit.php?id=<?= (int) $p['id'] ?>"><?= h($p['title']) ?></a>
          <div class="t-sub">/blog/<?= h($p['slug']) ?></div>
        </td>
        <td><span class="badge badge-<?= h($p['status']) ?>"><?= h($p['status']) ?></span></td>
        <td><?= h($p['author_name'] ?? '—') ?></td>
        <td><?= h(date('M j, Y', strtotime($p['updated_at']))) ?></td>
        <td>
          <a class="row-link" href="blog-edit.php?id=<?= (int) $p['id'] ?>">Edit</a>
          &nbsp;·&nbsp;
          <form method="post" action="blog-delete.php" style="display:inline" onsubmit="return confirm('Delete this post permanently?');">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <button type="submit" class="row-link" style="background:none;border:none;color:#e11d48;cursor:pointer;font:inherit;padding:0">Delete</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
