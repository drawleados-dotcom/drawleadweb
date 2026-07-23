<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$u = current_user();
$pages = $pdo->query('SELECT * FROM pages ORDER BY id')->fetchAll();

$pageTitle = 'Pages';
$pageSub = 'Edit the meta title, meta description, URL, and name for each page. Changes go live instantly.';
$activeNav = 'pages';
include __DIR__ . '/includes/header.php';
?>

<div class="card">
  <table>
    <thead><tr><th>Page Name</th><th>URL</th><th>Meta Title</th><th>Last Updated</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($pages as $p):
        $canEdit = user_has_page_access($pdo, $u['id'], $u['role'], (int) $p['id']);
      ?>
      <tr>
        <td class="t-name">
          <?php if ($canEdit): ?>
            <a class="row-link" href="page-edit.php?id=<?= (int) $p['id'] ?>"><?= h($p['name']) ?></a>
          <?php else: ?>
            <?= h($p['name']) ?> <span class="t-sub">(no access)</span>
          <?php endif; ?>
        </td>
        <td><code><?= h($p['slug']) ?></code></td>
        <td><?= h($p['meta_title']) ?></td>
        <td><?= h(date('M j, Y', strtotime($p['updated_at']))) ?></td>
        <td><?php if ($canEdit): ?><a class="row-link" href="page-edit.php?id=<?= (int) $p['id'] ?>">Edit →</a><?php endif; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="access-note">Need a page you don't see here, or don't have access to one listed? Ask an admin — page access is managed from the Users page.</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
