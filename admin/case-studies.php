<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_case_studies_access($pdo);

$studies = $pdo->query(
    'SELECT cs.*, u.name AS author_name FROM case_studies cs
     LEFT JOIN users u ON u.id = cs.author_id
     ORDER BY cs.updated_at DESC'
)->fetchAll();

$pageTitle = 'Case Studies';
$pageSub = 'Write and manage client case studies — just like a blog post.';
$activeNav = 'case-studies';
include __DIR__ . '/includes/header.php';
?>

<div class="toolbar">
  <div class="topbar-sub"><?= count($studies) ?> case stud<?= count($studies) === 1 ? 'y' : 'ies' ?></div>
  <a href="case-study-edit.php" class="btn btn-primary">+ New Case Study</a>
</div>

<div class="card">
  <?php if (empty($studies)): ?>
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-4 4"/></svg>
      <p>No case studies yet.</p>
      <a class="row-link" href="case-study-edit.php">Add your first case study →</a>
    </div>
  <?php else: ?>
  <table>
    <thead><tr><th></th><th>Title</th><th>Client</th><th>Services</th><th>Status</th><th>Updated</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($studies as $cs): ?>
      <tr>
        <td>
          <?php if ($cs['desktop_image']): ?>
            <img class="thumb" src="<?= h(UPLOAD_URL . $cs['desktop_image']) ?>" alt="">
          <?php else: ?>
            <div class="thumb"></div>
          <?php endif; ?>
        </td>
        <td>
          <a class="row-link" href="case-study-edit.php?id=<?= (int) $cs['id'] ?>"><?= h($cs['title']) ?></a>
          <div class="t-sub">/case-studies/<?= h($cs['slug']) ?></div>
        </td>
        <td><?= h($cs['client_name'] ?: '—') ?></td>
        <td class="t-sub"><?= h($cs['services'] ?: '—') ?></td>
        <td><span class="badge badge-<?= h($cs['status']) ?>"><?= h($cs['status']) ?></span></td>
        <td><?= h(date('M j, Y', strtotime($cs['updated_at']))) ?></td>
        <td>
          <a class="row-link" href="case-study-edit.php?id=<?= (int) $cs['id'] ?>">Edit</a>
          &nbsp;·&nbsp;
          <form method="post" action="case-study-delete.php" style="display:inline" onsubmit="return confirm('Delete this case study permanently?');">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $cs['id'] ?>">
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
