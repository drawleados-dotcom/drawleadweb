<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$u = current_user();
$pages = $pdo->query('SELECT * FROM pages ORDER BY id')->fetchAll();

$pageTitle = 'Pages';
$pageSub = 'Edit each page\'s content section by section, plus its URL, name, and SEO. Changes go live instantly.';
$activeNav = 'pages';
include __DIR__ . '/includes/header.php';
?>

<?php
$pagesMsg = [
    'deleted'  => ['success', 'Page deleted.'],
    'home'     => ['error', 'The live homepage can\'t be deleted. Point another page at "/" first, then delete this one.'],
    'notfound' => ['error', 'That page no longer exists.'],
    'error'    => ['error', 'The page couldn\'t be deleted. Nothing was changed.'],
][$_GET['msg'] ?? ''] ?? null;
if ($pagesMsg): ?>
<div class="alert alert-<?= $pagesMsg[0] ?>"><?= h($pagesMsg[1]) ?></div>
<?php endif; ?>

<div class="card">
  <table>
    <thead><tr><th>Page Name</th><th>URL</th><th>Status</th><th>Meta Title</th><th>Last Updated</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($pages as $p):
        $canEdit = user_has_page_access($pdo, $u['id'], $u['role'], (int) $p['id']);
        $pStatus = $p['status'] ?? 'published';
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
        <td>
          <span class="badge badge-<?= h($pStatus) ?>"><?= h($pStatus) ?></span>
          <?php if (!empty($p['show_in_menu'])): ?><div class="t-sub">In menu</div><?php endif; ?>
        </td>
        <td><?= h($p['meta_title']) ?></td>
        <td><?= h(date('M j, Y', strtotime($p['updated_at']))) ?></td>
        <td>
          <?php if ($canEdit): ?><a class="row-link" href="page-edit.php?id=<?= (int) $p['id'] ?>">Edit →</a><?php endif; ?>
          <?php if ($u['role'] === 'admin' && $p['slug'] !== '/'): ?>
          <form method="post" action="page-delete.php" style="display:inline-block;margin-left:12px" onsubmit="return confirm(<?= h(json_encode('Delete the page "' . $p['name'] . '" (' . $p['slug'] . ') permanently?' . "\n\n" . 'Its content, history and access settings are removed too. This can\'t be undone. To hide it instead, open the page and set its status to Draft.')) ?>);">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <button type="submit" class="row-link" style="background:none;border:none;color:#e11d48;cursor:pointer;font:inherit;padding:0">Delete</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="access-note">Need a page you don't see here, or don't have access to one listed? Ask an admin — page access is managed from the Users page.</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
