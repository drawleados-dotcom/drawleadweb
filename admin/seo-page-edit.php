<?php
/**
 * Admin → SEO → Page SEO → Edit SEO: the SEO fields of one page, blog post or
 * case study — with the same live score, Google preview and social preview
 * as its full editor (includes/seo-panel.php + assets/seo-analyzer.js).
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$types = seo_content_types();
$type = isset($types[$_GET['type'] ?? '']) ? $_GET['type'] : '';
$id = (int) ($_GET['id'] ?? 0);
$item = $type !== '' ? seo_item($pdo, $type, $id) : null;
if (!$item) {
    http_response_code(404);
    die('That page, post or case study was not found. <a href="seo-pages.php">Back to Page SEO</a>');
}

$error = '';
$fields = ['focus_keyword', 'meta_title', 'meta_description', 'canonical_url', 'robots_index', 'robots_follow', 'og_title', 'og_description', 'og_image'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $data = [];
    foreach ($fields as $f) {
        $data[$f] = is_string($_POST[$f] ?? null) ? trim($_POST[$f]) : '';
    }
    if ($type === 'page' && $data['meta_title'] === '') {
        $error = 'Pages need an SEO title (it is required on the page editor too).';
    } elseif ($data['canonical_url'] !== '' && !filter_var($data['canonical_url'], FILTER_VALIDATE_URL) && !str_starts_with($data['canonical_url'], '/')) {
        $error = 'The canonical URL must be a full address (https://…) or a path starting with "/".';
    }
    if (!$error) {
        seo_update_item($pdo, $type, $id, $data);
        seo_flash('SEO saved for "' . $item['name'] . '" — it is live now.');
        seo_back('seo-page-edit.php?type=' . $type . '&id=' . $id);
    }
    // Keep what was typed.
    $item = array_merge($item, $data);
}

// Issues for this item, measured against the whole site (for duplicates).
$all = seo_items($pdo);
$issues = seo_item_issues(
    array_merge($item, ['title' => $item['meta_title'] !== '' ? $item['meta_title'] : $item['name']]),
    seo_duplicates($all, 'title'), seo_duplicates($all, 'description'), seo_global($pdo)
);

// The analyzer reads #title/#name and #slug; [data-seo-content] feeds word count and density.
$seoRow = array_intersect_key($item, array_flip($fields));
$seoPathPrefix = $types[$type][2];
$slugValue = $type === 'page' ? $item['path'] : substr($item['path'], strlen($seoPathPrefix));

$pageTitle = 'Edit SEO';
$pageSub = $types[$type][1] . ': ' . $item['name'];
$activeNav = 'seo-pages';
include __DIR__ . '/includes/header.php';
?>

<?= seo_flash_html() ?>
<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>

<div class="toolbar">
  <a class="btn btn-ghost btn-sm" href="seo-pages.php">← All pages</a>
  <div class="seo-actions">
    <a href="<?= h($item['edit_url']) ?>">Open full editor →</a>
    <?php if ($item['live']): ?>
      <a href="seo-audit.php?check=<?= rawurlencode($item['path']) ?>#live-check">Live check →</a>
      <a href="<?= h($item['path']) ?>" target="_blank">View page ↗</a>
    <?php endif; ?>
  </div>
</div>

<?php if ($issues): ?>
<div class="card">
  <div class="card-title">Open issues (<?= count($issues) ?>)</div>
  <ul class="seo-issues" style="margin-top:.6rem">
    <?php foreach ($issues as $iss): ?><li class="<?= h($iss['level']) ?>"><b style="text-transform:capitalize"><?= h($iss['level']) ?>:</b> <?= h($iss['text']) ?></li><?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<form method="post" style="margin-top:1.25rem">
  <?= csrf_field() ?>

  <div class="card">
    <div class="card-title"><?= h($item['name']) ?></div>
    <div class="card-desc"><?= $item['live'] ? 'Live' : 'Draft — not visible to visitors' ?> · last updated <?= $item['updated_at'] ? h(date('M j, Y g:i A', strtotime($item['updated_at']))) : '—' ?>. To rename it or change its URL, use the full editor.</div>
    <div class="seo-two">
      <div class="field">
        <label for="<?= $type === 'page' ? 'name' : 'title' ?>">Name</label>
        <input type="text" id="<?= $type === 'page' ? 'name' : 'title' ?>" value="<?= h($item['name']) ?>" readonly>
      </div>
      <div class="field">
        <label for="slug">URL</label>
        <input type="text" id="slug" value="<?= h($slugValue) ?>" readonly>
        <div class="field-hint"><?= h($item['path']) ?></div>
      </div>
    </div>
    <?php if ($item['content'] !== null): ?>
      <textarea hidden data-seo-content><?= h($item['content']) ?></textarea>
    <?php endif; ?>
  </div>

  <?php include __DIR__ . '/includes/seo-panel.php'; ?>

  <div class="seo-savebar">
    <span class="seo-savebar-note">Saving updates the live page's search and social tags straight away.</span>
    <button type="submit" class="btn btn-primary">Save SEO</button>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
