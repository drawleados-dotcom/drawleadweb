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
$isAdmin = $u['role'] === 'admin';
$deletedN = max(1, (int) ($_GET['n'] ?? 1));
$pagesMsg = [
    'deleted'  => ['success', ($deletedN === 1 ? 'Page deleted.' : $deletedN . ' pages deleted.') . (isset($_GET['home']) ? ' The live homepage was skipped: it can\'t be deleted.' : '')],
    'home'     => ['error', 'The live homepage can\'t be deleted. Point another page at "/" first, then delete this one.'],
    'notfound' => ['error', 'Those pages no longer exist.'],
    'none'     => ['error', 'Select at least one page to delete.'],
    'error'    => ['error', 'The pages couldn\'t be deleted. Nothing was changed.'],
][$_GET['msg'] ?? ''] ?? null;
if ($pagesMsg): ?>
<div class="alert alert-<?= $pagesMsg[0] ?>"><?= h($pagesMsg[1]) ?></div>
<?php endif; ?>

<?php if ($isAdmin): ?>
<form id="bulk-delete" method="post" action="page-delete.php" class="bulkbar" hidden>
  <?= csrf_field() ?>
  <span class="bulkbar__n" data-bulk-count>0 selected</span>
  <button type="button" class="btn btn-sm" data-bulk-clear>Clear selection</button>
  <button type="submit" class="btn btn-danger btn-sm">Delete selected</button>
</form>
<?php endif; ?>

<div class="card">
  <table>
    <thead><tr>
      <?php if ($isAdmin): ?><th class="t-check"><input type="checkbox" data-bulk-all aria-label="Select all pages"></th><?php endif; ?>
      <th>Page Name</th><th>URL</th><th>Status</th><th>Meta Title</th><th>Last Updated</th><th></th>
    </tr></thead>
    <tbody>
      <?php foreach ($pages as $p):
        $canEdit = user_has_page_access($pdo, $u['id'], $u['role'], (int) $p['id']);
        $pStatus = $p['status'] ?? 'published';
        $isHome = $p['slug'] === '/';
      ?>
      <tr>
        <?php if ($isAdmin): ?>
        <td class="t-check">
          <?php if (!$isHome): ?><input type="checkbox" form="bulk-delete" name="ids[]" value="<?= (int) $p['id'] ?>" data-bulk-row data-name="<?= h($p['name']) ?>" aria-label="Select <?= h($p['name']) ?>"><?php else: ?><span class="t-sub" title="The live homepage can't be deleted">—</span><?php endif; ?>
        </td>
        <?php endif; ?>
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
        <td class="t-actions">
          <?php if ($canEdit): ?><a class="row-link" href="page-edit.php?id=<?= (int) $p['id'] ?>">Edit →</a><?php endif; ?>
          <?php if ($isAdmin && !$isHome): ?>
          <form method="post" action="page-delete.php" onsubmit="return confirm(<?= h(json_encode('Delete the page "' . $p['name'] . '" (' . $p['slug'] . ') permanently?' . "\n\n" . 'Its content, history and access settings are removed too. This can\'t be undone. To hide it instead, open the page and set its status to Draft.')) ?>);">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <button type="submit" class="row-del">Delete</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php if ($isAdmin): ?>
<style>
  .t-check{width:36px;text-align:center}
  .t-check input{width:16px;height:16px;cursor:pointer;accent-color:#1d7a49}
  .t-actions{white-space:nowrap}
  .t-actions form{display:inline-block;margin-left:12px}
  .row-del{background:none;border:0;padding:0;font:inherit;color:#e11d48;cursor:pointer}
  .row-del:hover{text-decoration:underline}
  .bulkbar{position:sticky;top:12px;z-index:5;display:flex;align-items:center;gap:12px;margin-bottom:14px;padding:12px 16px;border-radius:12px;background:#fff5f6;border:1px solid #f6c3cf}
  .bulkbar[hidden]{display:none}
  .bulkbar__n{margin-right:auto;font-weight:700;color:#b3123a}
  tr.is-selected td{background:#fff8f9}
</style>
<script>
(function () {
  var form = document.getElementById('bulk-delete');
  if (!form) return;
  var all = document.querySelector('[data-bulk-all]');
  var rows = Array.prototype.slice.call(document.querySelectorAll('[data-bulk-row]'));
  var count = form.querySelector('[data-bulk-count]');
  var sync = function () {
    var picked = rows.filter(function (c) { return c.checked; });
    rows.forEach(function (c) { c.closest('tr').classList.toggle('is-selected', c.checked); });
    form.hidden = picked.length === 0;
    count.textContent = picked.length + (picked.length === 1 ? ' page selected' : ' pages selected');
    if (all) { all.checked = picked.length > 0 && picked.length === rows.length; all.indeterminate = picked.length > 0 && picked.length < rows.length; }
  };
  rows.forEach(function (c) { c.addEventListener('change', sync); });
  if (all) all.addEventListener('change', function () { rows.forEach(function (c) { c.checked = all.checked; }); sync(); });
  form.querySelector('[data-bulk-clear]').addEventListener('click', function () { rows.forEach(function (c) { c.checked = false; }); sync(); });
  form.addEventListener('submit', function (e) {
    var picked = rows.filter(function (c) { return c.checked; });
    if (!picked.length) { e.preventDefault(); return; }
    var names = picked.slice(0, 8).map(function (c) { return '• ' + c.dataset.name; }).join('\n') + (picked.length > 8 ? '\n• …and ' + (picked.length - 8) + ' more' : '');
    var msg = 'Delete ' + picked.length + (picked.length === 1 ? ' page' : ' pages') + ' permanently?\n\n' + names + '\n\nTheir content, history and access settings are removed too. This can\'t be undone.';
    if (!confirm(msg)) e.preventDefault();
  });
  sync();
})();
</script>
<?php endif; ?>

<div class="access-note">Need a page you don't see here, or don't have access to one listed? Ask an admin — page access is managed from the Users page.</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
