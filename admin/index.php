<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$u = current_user();

$pageCount = (int) $pdo->query('SELECT COUNT(*) FROM pages')->fetchColumn();
$publishedCount = (int) $pdo->query("SELECT COUNT(*) FROM blogs WHERE status='published'")->fetchColumn();
$draftCount = (int) $pdo->query("SELECT COUNT(*) FROM blogs WHERE status='draft'")->fetchColumn();
$userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

$recentBlogs = $pdo->query(
    'SELECT id, title, slug, status, updated_at FROM blogs ORDER BY updated_at DESC LIMIT 5'
)->fetchAll();

$gaConnected = get_setting($pdo, 'ga_measurement_id', '') !== '';
$gscConnected = get_setting($pdo, 'gsc_verification_tag', '') !== '';

$pageTitle = 'Dashboard';
$pageSub = 'Welcome back, ' . $u['name'] . '.';
$activeNav = 'dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-v"><?= $pageCount ?></div>
    <div class="stat-l">Pages</div>
  </div>
  <div class="stat-card">
    <div class="stat-v"><?= $publishedCount ?></div>
    <div class="stat-l">Published Posts</div>
  </div>
  <div class="stat-card">
    <div class="stat-v"><?= $draftCount ?></div>
    <div class="stat-l">Draft Posts</div>
  </div>
  <div class="stat-card">
    <div class="stat-v"><?= $userCount ?></div>
    <div class="stat-l">Admin Users</div>
  </div>
</div>

<div class="card">
  <div class="card-title">Recent Blog Activity</div>
  <div class="card-desc">The last posts created or edited.</div>
  <?php if (empty($recentBlogs)): ?>
    <div class="empty-state">
      <p>No blog posts yet. <a class="row-link" href="blog-edit.php">Write your first one →</a></p>
    </div>
  <?php else: ?>
  <table>
    <thead><tr><th>Title</th><th>Status</th><th>Last Updated</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($recentBlogs as $b): ?>
      <tr>
        <td class="t-name"><?= h($b['title']) ?></td>
        <td><span class="badge badge-<?= h($b['status']) ?>"><?= h($b['status']) ?></span></td>
        <td><?= h(date('M j, Y g:i A', strtotime($b['updated_at']))) ?></td>
        <td><a class="row-link" href="blog-edit.php?id=<?= (int) $b['id'] ?>">Edit →</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">Connections</div>
  <div class="card-desc">Google Analytics &amp; Search Console status.</div>
  <table>
    <tbody>
      <tr>
        <td class="t-name">Google Analytics (GA4)</td>
        <td><span class="badge <?= $gaConnected ? 'badge-published' : 'badge-draft' ?>"><?= $gaConnected ? 'Connected' : 'Not Connected' ?></span></td>
        <td><a class="row-link" href="analytics.php">Manage →</a></td>
      </tr>
      <tr>
        <td class="t-name">Google Search Console</td>
        <td><span class="badge <?= $gscConnected ? 'badge-published' : 'badge-draft' ?>"><?= $gscConnected ? 'Connected' : 'Not Connected' ?></span></td>
        <td><a class="row-link" href="analytics.php">Manage →</a></td>
      </tr>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
