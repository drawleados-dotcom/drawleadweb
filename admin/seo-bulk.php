<?php
/**
 * Admin → SEO → Bulk Editor: edit SEO titles, meta descriptions, focus
 * keywords and index settings of many pages / posts / case studies in one
 * table, plus find & replace across titles or descriptions.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$types = seo_content_types();
$fType = isset($types[$_GET['type'] ?? '']) ? $_GET['type'] : '';
$fQuery = is_string($_GET['q'] ?? null) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$fMissing = in_array($_GET['missing'] ?? '', ['meta_title', 'meta_description', 'focus_keyword'], true) ? $_GET['missing'] : '';
$backQuery = http_build_query(array_filter(['type' => $fType, 'q' => $fQuery, 'missing' => $fMissing, 'page' => $_GET['page'] ?? '']));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $rows = (array) ($_POST['rows'] ?? []);
        $orig = (array) ($_POST['orig'] ?? []);
        $saved = 0;
        $problems = [];
        foreach ($rows as $key => $fields) {
            if (!is_array($fields) || !preg_match('/^(page|blog|case_study):(\d+)$/', (string) $key, $m)) {
                continue;
            }
            $clean = [];
            foreach (['meta_title', 'meta_description', 'focus_keyword', 'robots_index'] as $f) {
                $clean[$f] = is_string($fields[$f] ?? null) ? trim($fields[$f]) : '';
            }
            $before = is_array($orig[$key] ?? null) ? $orig[$key] : [];
            $changed = false;
            foreach ($clean as $f => $v) {
                if ($v !== trim((string) ($before[$f] ?? ''))) {
                    $changed = true;
                }
            }
            if (!$changed) {
                continue;
            }
            if ($m[1] === 'page' && $clean['meta_title'] === '') {
                $problems[] = 'A page\'s SEO title can\'t be empty — that row was skipped.';
                continue;
            }
            seo_update_item($pdo, $m[1], (int) $m[2], $clean);
            $saved++;
        }
        seo_flash($saved ? "$saved row(s) saved — live now." : 'Nothing changed.');
        foreach (array_unique($problems) as $p) {
            seo_flash($p, 'error');
        }
        seo_back('seo-bulk.php?' . $backQuery);
    }

    if ($action === 'replace') {
        $field = in_array($_POST['field'] ?? '', ['meta_title', 'meta_description'], true) ? $_POST['field'] : 'meta_title';
        $find = (string) ($_POST['find'] ?? '');
        $replace = (string) ($_POST['replace'] ?? '');
        $scope = isset($types[$_POST['scope'] ?? '']) ? $_POST['scope'] : '';
        $caseSensitive = isset($_POST['case']);
        $limit = $field === 'meta_title' ? 190 : 320;
        if ($find === '') {
            seo_flash('Enter the text to find.', 'error');
            seo_back('seo-bulk.php?' . $backQuery . '#replace');
        }
        $done = 0;
        $tooLong = 0;
        foreach (seo_items($pdo, $scope) as $it) {
            $value = $it[$field];
            $new = $caseSensitive ? str_replace($find, $replace, $value) : str_ireplace($find, $replace, $value);
            if ($new === $value) {
                continue;
            }
            $new = trim((string) preg_replace('/\s{2,}/u', ' ', $new));
            if (mb_strlen($new) > $limit) {
                $tooLong++;
                continue;
            }
            if ($it['type'] === 'page' && $field === 'meta_title' && $new === '') {
                continue;
            }
            seo_update_item($pdo, $it['type'], $it['id'], [$field => $new]);
            $done++;
        }
        $label = $field === 'meta_title' ? 'SEO titles' : 'meta descriptions';
        seo_flash("Replaced in $done $label." . ($tooLong ? " $tooLong skipped because the result would be too long." : ''));
        seo_back('seo-bulk.php?' . $backQuery);
    }
}

$all = seo_items($pdo, $fType);
$items = array_values(array_filter($all, function ($it) use ($fQuery, $fMissing) {
    if ($fMissing !== '' && trim($it[$fMissing]) !== '') {
        return false;
    }
    if ($fQuery !== '' && !str_contains(mb_strtolower($it['name'] . ' ' . $it['path'] . ' ' . $it['meta_title'] . ' ' . $it['meta_description'] . ' ' . $it['focus_keyword']), mb_strtolower($fQuery))) {
        return false;
    }
    return true;
}));
$perPage = 25;
$pages = max(1, (int) ceil(count($items) / $perPage));
$page = min($pages, max(1, (int) ($_GET['page'] ?? 1)));
$shown = array_slice($items, ($page - 1) * $perPage, $perPage);

$pageTitle = 'Bulk Editor';
$pageSub = 'Edit the search snippet of many pages at once.';
$activeNav = 'seo-bulk';
include __DIR__ . '/includes/header.php';
?>

<?= seo_flash_html() ?>

<div class="card">
  <form class="seo-filters" method="get">
    <input type="search" name="q" value="<?= h($fQuery) ?>" placeholder="Search name, URL, title…">
    <select name="type" onchange="this.form.submit()">
      <option value="">All types</option>
      <?php foreach ($types as $k => $t): ?><option value="<?= h($k) ?>" <?= $fType === $k ? 'selected' : '' ?>><?= h($t[1]) ?>s</option><?php endforeach; ?>
    </select>
    <select name="missing" onchange="this.form.submit()">
      <option value="">Show everything</option>
      <option value="meta_title" <?= $fMissing === 'meta_title' ? 'selected' : '' ?>>Missing SEO title</option>
      <option value="meta_description" <?= $fMissing === 'meta_description' ? 'selected' : '' ?>>Missing meta description</option>
      <option value="focus_keyword" <?= $fMissing === 'focus_keyword' ? 'selected' : '' ?>>Missing focus keyword</option>
    </select>
    <button class="btn btn-ghost btn-sm">Filter</button>
    <?php if ($fType || $fQuery || $fMissing): ?><a class="btn btn-ghost btn-sm" href="seo-bulk.php">Clear</a><?php endif; ?>
    <span class="seo-count"><?= count($items) ?> rows · page <?= $page ?> of <?= $pages ?></span>
  </form>

  <?php if (!$shown): ?>
    <div class="seo-empty">Nothing matches.</div>
  <?php else: ?>
  <form method="post" id="bulkForm">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <div class="table-wrap">
    <table class="seo-bulk" style="min-width:980px">
      <thead><tr><th style="width:17%">Page</th><th style="width:26%">SEO title</th><th style="width:33%">Meta description</th><th style="width:14%">Focus keyword</th><th style="width:10%">Index</th></tr></thead>
      <tbody>
      <?php foreach ($shown as $it): $k = h($it['key']); ?>
        <tr data-row>
          <td>
            <a class="row-link" href="seo-page-edit.php?type=<?= h($it['type']) ?>&id=<?= (int) $it['id'] ?>"><?= h($it['name']) ?></a>
            <div class="t-sub"><?= h($types[$it['type']][1]) ?><?= $it['live'] ? '' : ' · draft' ?></div>
            <div class="t-sub seo-path"><?= h($it['path']) ?></div>
          </td>
          <td>
            <input type="text" name="rows[<?= $k ?>][meta_title]" value="<?= h($it['meta_title']) ?>" maxlength="190" data-len="30,60" placeholder="<?= h($it['name']) ?>">
            <span class="seo-len"></span>
          </td>
          <td>
            <textarea name="rows[<?= $k ?>][meta_description]" maxlength="320" rows="2" data-len="120,160" placeholder="<?= h($it['description'] !== '' ? 'Now uses: ' . mb_strimwidth($it['description'], 0, 120, '…') : 'No description') ?>"><?= h($it['meta_description']) ?></textarea>
            <span class="seo-len"></span>
          </td>
          <td><input type="text" name="rows[<?= $k ?>][focus_keyword]" value="<?= h($it['focus_keyword']) ?>" maxlength="190"></td>
          <td>
            <select name="rows[<?= $k ?>][robots_index]">
              <option value="index" <?= $it['robots_index'] !== 'noindex' ? 'selected' : '' ?>>Index</option>
              <option value="noindex" <?= $it['robots_index'] === 'noindex' ? 'selected' : '' ?>>No Index</option>
            </select>
            <input type="hidden" name="orig[<?= $k ?>][meta_title]" value="<?= h($it['meta_title']) ?>">
            <input type="hidden" name="orig[<?= $k ?>][meta_description]" value="<?= h($it['meta_description']) ?>">
            <input type="hidden" name="orig[<?= $k ?>][focus_keyword]" value="<?= h($it['focus_keyword']) ?>">
            <input type="hidden" name="orig[<?= $k ?>][robots_index]" value="<?= h($it['robots_index']) ?>">
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <div class="seo-savebar">
      <span class="seo-savebar-note" data-dirty-note>No changes yet. Only edited rows are saved.</span>
      <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
  </form>
  <?= seo_pager_html($page, $pages) ?>
  <?php endif; ?>
</div>

<form class="card" method="post" id="replace" style="margin-top:1.25rem" onsubmit="return confirm('Replace this text in every matching ' + (this.field.value === 'meta_title' ? 'SEO title' : 'meta description') + '? This can\'t be undone.')">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="replace">
  <div class="card-title">Find &amp; replace</div>
  <div class="card-desc">Change the same text everywhere at once — e.g. rename "| Drawlead" to "· Drawlead" in every title. Affects all pages of the chosen type, not just this screen.</div>
  <div class="seo-filters">
    <select name="field">
      <option value="meta_title">In SEO titles</option>
      <option value="meta_description">In meta descriptions</option>
    </select>
    <select name="scope">
      <option value="">Pages, posts &amp; case studies</option>
      <?php foreach ($types as $k => $t): ?><option value="<?= h($k) ?>"><?= h($t[1]) ?>s only</option><?php endforeach; ?>
    </select>
    <input type="text" name="find" placeholder="Find…" required>
    <input type="text" name="replace" placeholder="Replace with… (blank removes it)">
    <label class="checkbox-row" style="border:none;padding:0"><input type="checkbox" name="case"> Match case</label>
    <button class="btn btn-primary btn-sm">Replace all</button>
  </div>
</form>

<script>
(function () {
  function len(el) {
    var out = el.parentNode.querySelector('.seo-len');
    if (!out) return;
    var r = el.getAttribute('data-len').split(',').map(Number);
    var n = el.value.length;
    out.textContent = n ? n + ' chars' : '';
    out.className = 'seo-len' + (n ? (n >= r[0] && n <= r[1] ? ' is-ok' : ' is-bad') : '');
  }
  var dirty = 0;
  var note = document.querySelector('[data-dirty-note]');
  document.querySelectorAll('[data-row]').forEach(function (row) {
    var fields = row.querySelectorAll('input[type=text], textarea, select');
    fields.forEach(function (f) { f.dataset.orig = f.value; });
    function check() {
      var changed = Array.prototype.some.call(fields, function (f) { return f.value !== f.dataset.orig; });
      if (changed !== row.classList.contains('is-changed')) {
        row.classList.toggle('is-changed', changed);
        dirty += changed ? 1 : -1;
        if (note) note.textContent = dirty ? dirty + ' row(s) changed — remember to save.' : 'No changes yet. Only edited rows are saved.';
      }
    }
    fields.forEach(function (f) {
      f.addEventListener('input', function () { if (f.hasAttribute('data-len')) len(f); check(); });
      f.addEventListener('change', check);
      if (f.hasAttribute('data-len')) len(f);
    });
  });
  var form = document.getElementById('bulkForm');
  var submitting = false;
  if (form) form.addEventListener('submit', function () { submitting = true; });
  window.addEventListener('beforeunload', function (e) {
    if (dirty && !submitting) { e.preventDefault(); e.returnValue = ''; }
  });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
