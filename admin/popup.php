<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$ready = popups_ready($pdo);
$popups = $ready ? get_popups($pdo) : [];
$layouts = popup_layouts();
$targetOptions = $ready ? popup_target_options($pdo) : [];

$pageTitle = 'Popups';
$pageSub = 'Popups shown to visitors. Give each one a layout and choose the pages it appears on.';
$activeNav = 'popup';
include __DIR__ . '/includes/header.php';
?>

<?php if (!$ready): ?>
<div class="alert alert-error">
  Run <b>Migration 029</b> first, in <a href="run-migrations.php">Run Migrations</a>. It sets up multiple popups and copies your current popup in, so nothing changes on the live site.
</div>
<?php else: ?>

<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Popup deleted.</div><?php endif; ?>

<div class="toolbar">
  <div class="topbar-sub"><?= count($popups) ?> popup<?= count($popups) === 1 ? '' : 's' ?></div>
  <a href="popup-edit.php" class="btn btn-primary">+ New Popup</a>
</div>

<div class="card">
  <?php if (empty($popups)): ?>
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 9h18"/></svg>
      <p>No popups yet.</p>
      <a class="row-link" href="popup-edit.php">Create your first popup →</a>
    </div>
  <?php else: ?>
  <table>
    <thead><tr><th></th><th>Popup</th><th>Layout</th><th>Shows On</th><th>Status</th><th>Updated</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($popups as $p):
        $keys = popup_target_keys($p['targets']);
        $labels = array_map(fn ($k) => $targetOptions[$k]['label'] ?? null, $keys);
        $labels = array_values(array_filter($labels));
      ?>
      <tr>
        <td>
          <?php if ((string) $p['image'] !== ''): ?>
            <img class="thumb" src="<?= h(popup_image_src((string) $p['image'])) ?>" alt="">
          <?php else: ?>
            <div class="thumb"></div>
          <?php endif; ?>
        </td>
        <td>
          <a class="row-link" href="popup-edit.php?id=<?= (int) $p['id'] ?>"><?= h($p['name'] ?: ($p['title'] ?: 'Untitled popup')) ?></a>
          <?php if ($p['name'] !== '' && $p['title'] !== ''): ?><div class="t-sub"><?= h($p['title']) ?></div><?php endif; ?>
        </td>
        <td><?= h($layouts[$p['layout']][0] ?? 'Split') ?></td>
        <td class="t-sub">
          <?php if ($p['show_on'] === 'all'): ?>
            All pages
          <?php elseif (!$labels): ?>
            <span style="color:#a8631a">No pages picked</span>
          <?php else: ?>
            <?= h(implode(', ', array_slice($labels, 0, 3))) ?><?= count($labels) > 3 ? ' +' . (count($labels) - 3) . ' more' : '' ?>
          <?php endif; ?>
        </td>
        <td>
          <span class="badge <?= $p['enabled'] ? 'badge-published' : 'badge-draft' ?>"><?= $p['enabled'] ? 'On' : 'Off' ?></span>
        </td>
        <td><?= $p['updated_at'] ? h(date('M j, Y', strtotime($p['updated_at']))) : '' ?></td>
        <td style="white-space:nowrap">
          <a class="row-link" href="popup-edit.php?id=<?= (int) $p['id'] ?>">Edit</a>
          &nbsp;·&nbsp;
          <form method="post" action="popup-delete.php" style="display:inline" onsubmit="return confirm('Delete this popup permanently?');">
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

<div class="access-note">Each page shows at most one popup. A popup picked for a specific page beats an "All pages" popup; if two popups pick the same page, the newer one is shown.</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
