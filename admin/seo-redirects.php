<?php
/**
 * Admin → SEO → Redirects: send old URLs to new ones (301/302/307/308) or
 * mark them gone (410). Applied by index.php before any page loads.
 * "/old/*" matches a whole section; "/new/*" as the target keeps the rest of the path.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$ready = seo_table_ready($pdo, 'seo_redirects');
$codes = seo_redirect_codes();
$formError = '';
$form = [
    'id' => 0,
    'source' => is_string($_GET['source'] ?? null) ? $_GET['source'] : '',
    'target' => '', 'code' => 301, 'enabled' => 1, 'note' => '',
];

/** "A → B" form check. Returns [error, cleaned values]. */
function seo_redirect_validate(PDO $pdo, array $in, int $id): array
{
    [$source, $matchType] = seo_parse_redirect_source((string) $in['source']);
    $code = (int) $in['code'];
    $target = $code === 410 ? '' : seo_clean_redirect_target((string) $in['target']);
    $clean = ['source' => $source, 'match_type' => $matchType, 'target' => $target, 'code' => $code];

    if ($source === '' || $source === '/') {
        return ['Enter the old URL to redirect, e.g. /old-page. The home page itself can\'t be redirected.', $clean];
    }
    if (stripos($source, '/admin') === 0 || in_array(strtolower($source), ['/sitemap.xml', '/robots.txt'], true)) {
        return ['That URL is reserved and can\'t be redirected.', $clean];
    }
    if (!isset(seo_redirect_codes()[$code])) {
        return ['Choose a redirect type.', $clean];
    }
    if ($code !== 410 && $target === '') {
        return ['Enter where to send visitors: a path like /new-page or a full https:// address.', $clean];
    }
    if ($code !== 410 && !preg_match('#^https?://#i', $target)) {
        $tPath = seo_normalize_path((string) preg_replace('/\?.*$/', '', $target));
        if (strcasecmp($tPath, $source) === 0 || ($matchType === 'prefix' && stripos($tPath, $source . '/') === 0 && !str_ends_with($target, '/*'))) {
            return ['The new URL can\'t be the same as (or inside) the old one — that would loop forever.', $clean];
        }
        $back = $pdo->prepare("SELECT target FROM seo_redirects WHERE enabled = 1 AND match_type = 'exact' AND source = ? AND id <> ?");
        $back->execute([$tPath, $id]);
        $backTarget = $back->fetchColumn();
        if ($backTarget !== false && strcasecmp(seo_normalize_path((string) preg_replace('/\?.*$/', '', (string) $backTarget)), $source) === 0) {
            return [$tPath . ' already redirects back to ' . $source . ' — saving this would create a loop.', $clean];
        }
    }
    $dup = $pdo->prepare('SELECT id FROM seo_redirects WHERE source = ? AND match_type = ? AND id <> ?');
    $dup->execute([$source, $matchType, $id]);
    if ($dup->fetchColumn()) {
        return ['There is already a redirect for ' . $source . ($matchType === 'prefix' ? '/*' : '') . ' — edit that one instead.', $clean];
    }
    return ['', $clean];
}

if ($ready && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $rid = (int) ($_POST['id'] ?? 0);
        $form = [
            'id' => $rid,
            'source' => (string) ($_POST['source'] ?? ''),
            'target' => (string) ($_POST['target'] ?? ''),
            'code' => (int) ($_POST['code'] ?? 301),
            'enabled' => isset($_POST['enabled']) ? 1 : 0,
            'note' => mb_substr(trim((string) ($_POST['note'] ?? '')), 0, 190),
        ];
        [$formError, $c] = seo_redirect_validate($pdo, $form, $rid);
        if ($formError === '') {
            if ($rid > 0) {
                $pdo->prepare('UPDATE seo_redirects SET source = ?, match_type = ?, target = ?, code = ?, enabled = ?, note = ? WHERE id = ?')
                    ->execute([$c['source'], $c['match_type'], $c['target'], $c['code'], $form['enabled'], $form['note'], $rid]);
            } else {
                $pdo->prepare('INSERT INTO seo_redirects (source, match_type, target, code, enabled, note) VALUES (?, ?, ?, ?, ?, ?)')
                    ->execute([$c['source'], $c['match_type'], $c['target'], $c['code'], $form['enabled'], $form['note']]);
            }
            $label = $c['source'] . ($c['match_type'] === 'prefix' ? '/*' : '');
            seo_flash($c['code'] === 410 ? "$label now answers 410 Gone." : "$label now redirects to {$c['target']} ({$c['code']}).");
            if ($c['match_type'] === 'exact' && seo_path_exists($pdo, $c['source'])) {
                seo_flash($c['source'] . ' is still a live page — the redirect takes priority, so visitors no longer see it.', 'error');
            }
            if (seo_table_ready($pdo, 'seo_404_log')) {
                $pdo->prepare('DELETE FROM seo_404_log WHERE path_hash = ?')->execute([sha1(strtolower($c['source']))]);
            }
            seo_back('seo-redirects.php');
        }
    } elseif ($action === 'toggle') {
        $pdo->prepare('UPDATE seo_redirects SET enabled = 1 - enabled WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
        seo_flash('Redirect updated.');
        seo_back('seo-redirects.php?' . http_build_query(array_intersect_key($_GET, array_flip(['q', 'code', 'page']))));
    } elseif ($action === 'delete') {
        $ids = array_filter(array_map('intval', (array) ($_POST['ids'] ?? [$_POST['id'] ?? 0])));
        if ($ids) {
            $in = implode(',', array_fill(0, count($ids), '?'));
            $pdo->prepare("DELETE FROM seo_redirects WHERE id IN ($in)")->execute(array_values($ids));
            seo_flash(count($ids) === 1 ? 'Redirect deleted.' : count($ids) . ' redirects deleted.');
        }
        seo_back('seo-redirects.php');
    } elseif ($action === 'reset_hits') {
        $pdo->exec('UPDATE seo_redirects SET hits = 0, last_hit_at = NULL');
        seo_flash('Hit counters reset.');
        seo_back('seo-redirects.php');
    } elseif ($action === 'import') {
        $added = 0;
        $skipped = [];
        foreach (preg_split('/\R/', (string) ($_POST['import'] ?? '')) ?: [] as $n => $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            $parts = array_values(array_filter(array_map('trim', preg_split('/\s*(?:,|\t|->|=>|;)\s*|\s+/', $line) ?: []), 'strlen'));
            if (strtolower($parts[0] ?? '') === 'source') {
                continue; // header row
            }
            $code = 301;
            if (isset($parts[2]) && isset($codes[(int) $parts[2]])) {
                $code = (int) $parts[2];
            } elseif (isset($parts[1]) && (int) $parts[1] === 410 && !isset($parts[2])) {
                $code = 410;
            }
            $row = ['source' => $parts[0] ?? '', 'target' => $code === 410 ? '' : ($parts[1] ?? ''), 'code' => $code];
            [$err, $c] = seo_redirect_validate($pdo, $row, 0);
            if ($err !== '') {
                $skipped[] = 'Line ' . ($n + 1) . ': ' . $err;
                continue;
            }
            $pdo->prepare('INSERT INTO seo_redirects (source, match_type, target, code, enabled, note) VALUES (?, ?, ?, ?, 1, ?)')
                ->execute([$c['source'], $c['match_type'], $c['target'], $c['code'], 'Imported ' . date('M j, Y')]);
            $added++;
        }
        seo_flash("$added redirect(s) imported." . ($skipped ? ' ' . count($skipped) . ' skipped.' : ''));
        foreach (array_slice($skipped, 0, 8) as $s) {
            seo_flash($s, 'error');
        }
        seo_back('seo-redirects.php');
    }
}

if ($ready && isset($_GET['edit']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $pdo->prepare('SELECT * FROM seo_redirects WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    if ($row = $stmt->fetch()) {
        $form = [
            'id' => (int) $row['id'],
            'source' => $row['source'] . ($row['match_type'] === 'prefix' ? '/*' : ''),
            'target' => (string) $row['target'], 'code' => (int) $row['code'],
            'enabled' => (int) $row['enabled'], 'note' => (string) $row['note'],
        ];
    }
}

// List
$fQuery = is_string($_GET['q'] ?? null) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$fCode = isset($codes[(int) ($_GET['code'] ?? 0)]) ? (int) $_GET['code'] : 0;
$rows = [];
$total = 0;
$pages = 1;
$page = 1;
$problems = [];
if ($ready) {
    $where = [];
    $params = [];
    if ($fQuery !== '') {
        $like = '%' . addcslashes($fQuery, '%_\\') . '%';
        $where[] = '(source LIKE ? OR target LIKE ? OR note LIKE ?)';
        array_push($params, $like, $like, $like);
    }
    if ($fCode) {
        $where[] = 'code = ?';
        $params[] = $fCode;
    }
    $w = $where ? ' WHERE ' . implode(' AND ', $where) : '';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM seo_redirects$w");
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();
    $perPage = 50;
    $pages = max(1, (int) ceil($total / $perPage));
    $page = min($pages, max(1, (int) ($_GET['page'] ?? 1)));
    $offset = ($page - 1) * $perPage;
    $stmt = $pdo->prepare("SELECT * FROM seo_redirects$w ORDER BY id DESC LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    foreach (seo_redirect_problems($pdo) as $p) {
        $problems[$p['id']] = $p;
    }
}

// Tester
$testUrl = is_string($_GET['test'] ?? null) ? trim($_GET['test']) : '';
$testMatch = null;
if ($ready && $testUrl !== '') {
    $testPath = seo_normalize_path($testUrl);
    $testMatch = $testPath !== '' ? seo_find_redirect($pdo, $testPath) : null;
}

$pageTitle = 'Redirects';
$pageSub = 'Send visitors and Google from old or deleted URLs to the right page.';
$activeNav = 'seo-redirects';
include __DIR__ . '/includes/header.php';
?>

<?= seo_flash_html() ?>
<?php if (!$ready): ?><?= seo_migration_notice() ?><?php include __DIR__ . '/includes/footer.php'; return; endif; ?>

<div class="card">
  <div class="toolbar" style="margin-bottom:1rem">
    <div>
      <div class="card-title">All redirects (<?= $total ?>)</div>
      <div class="card-desc" style="margin:0">Checked on every request before the page loads. Disabled ones are kept but ignored.</div>
    </div>
    <a class="btn btn-primary btn-sm" href="#redirect-form">+ Add redirect</a>
  </div>
  <form class="seo-filters" method="get">
    <input type="search" name="q" value="<?= h($fQuery) ?>" placeholder="Search URLs or notes…">
    <select name="code" onchange="this.form.submit()">
      <option value="">All types</option>
      <?php foreach ($codes as $k => $l): ?><option value="<?= $k ?>" <?= $fCode === $k ? 'selected' : '' ?>><?= h($l) ?></option><?php endforeach; ?>
    </select>
    <button class="btn btn-ghost btn-sm">Filter</button>
    <?php if ($fQuery || $fCode): ?><a class="btn btn-ghost btn-sm" href="seo-redirects.php">Clear</a><?php endif; ?>
  </form>

  <?php if (!$rows): ?>
    <div class="seo-empty"><?= $total === 0 && !$fQuery && !$fCode ? 'No redirects yet. Add one below, or create them straight from the 404 Logs.' : 'Nothing matches.' ?></div>
  <?php else: ?>
  <form method="post" id="bulkForm" onsubmit="return confirm('Delete the selected redirects?')">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="delete">
  </form>
  <div class="table-wrap">
  <table>
    <thead><tr><th style="width:30px"><input type="checkbox" data-check-all style="width:15px;height:15px"></th><th>From</th><th>To</th><th>Type</th><th>Hits</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): $prob = $problems[(int) $r['id']] ?? null; ?>
      <tr>
        <td><input type="checkbox" name="ids[]" value="<?= (int) $r['id'] ?>" form="bulkForm" data-check style="width:15px;height:15px"></td>
        <td style="max-width:280px">
          <a class="row-link seo-path" href="<?= h($r['source']) ?>" target="_blank"><?= h($r['source']) ?><?= $r['match_type'] === 'prefix' ? '/*' : '' ?></a>
          <?php if ($r['note'] !== ''): ?><div class="t-sub"><?= h($r['note']) ?></div><?php endif; ?>
          <?php if ($prob): ?><div class="t-sub" style="color:<?= $prob['level'] === 'error' ? 'var(--danger)' : '#a8631a' ?>">⚠ <?= h($prob['text']) ?></div><?php endif; ?>
        </td>
        <td style="max-width:280px"><?= (int) $r['code'] === 410 ? '<span class="t-sub">— (gone)</span>' : '<span class="seo-path">' . h($r['target']) . '</span>' ?></td>
        <td><span class="badge <?= (int) $r['code'] === 410 ? 'badge-error' : (in_array((int) $r['code'], [301, 308], true) ? 'badge-info' : 'badge-draft') ?>"><?= (int) $r['code'] ?></span></td>
        <td><?= number_format((int) $r['hits']) ?><?php if ($r['last_hit_at']): ?><div class="t-sub"><?= h(date('M j, g:i A', strtotime($r['last_hit_at']))) ?></div><?php endif; ?></td>
        <td><span class="badge <?= $r['enabled'] ? 'badge-published' : 'badge-muted' ?>"><?= $r['enabled'] ? 'On' : 'Off' ?></span></td>
        <td>
          <div class="seo-actions">
            <a href="seo-redirects.php?edit=<?= (int) $r['id'] ?>#redirect-form">Edit</a>
            <form class="seo-inline-form" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button type="submit"><?= $r['enabled'] ? 'Disable' : 'Enable' ?></button></form>
            <form class="seo-inline-form" method="post" onsubmit="return confirm('Delete this redirect?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button type="submit" class="is-danger">Delete</button></form>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <div class="seo-actions" style="margin-top:1rem">
    <button type="submit" form="bulkForm" class="btn btn-danger btn-sm">Delete selected</button>
    <form class="seo-inline-form" method="post" onsubmit="return confirm('Reset every hit counter to 0?')"><?= csrf_field() ?><input type="hidden" name="action" value="reset_hits"><button type="submit">Reset hit counters</button></form>
  </div>
  <?= seo_pager_html($page, $pages) ?>
  <?php endif; ?>
</div>

<div class="seo-two">
  <form class="card" method="post" id="redirect-form">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int) $form['id'] ?>">
    <div class="card-title"><?= $form['id'] ? 'Edit redirect' : 'Add a redirect' ?></div>
    <div class="card-desc">Use 301 when a page has moved for good — Google passes its ranking to the new URL.</div>
    <?php if ($formError): ?><div class="alert alert-error"><?= h($formError) ?></div><?php endif; ?>
    <div class="field">
      <label for="source">Old URL</label>
      <input type="text" id="source" name="source" maxlength="500" value="<?= h($form['source']) ?>" placeholder="/old-page  or  /old-section/*" required>
      <div class="field-hint">A path on this site. End with <code>/*</code> to redirect a whole section.</div>
    </div>
    <div class="field">
      <label for="code">Type</label>
      <select id="code" name="code" data-code>
        <?php foreach ($codes as $k => $l): ?><option value="<?= $k ?>" <?= (int) $form['code'] === $k ? 'selected' : '' ?>><?= h($l) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="field" data-target-field>
      <label for="target">New URL</label>
      <input type="text" id="target" name="target" maxlength="500" value="<?= h($form['target']) ?>" placeholder="/new-page  or  https://other-site.com/page">
      <div class="field-hint">With an old section like <code>/old/*</code>, a target of <code>/new/*</code> keeps the rest: /old/a → /new/a.</div>
    </div>
    <div class="field">
      <label for="note">Note</label>
      <input type="text" id="note" name="note" maxlength="190" value="<?= h($form['note']) ?>" placeholder="Optional — why this redirect exists">
    </div>
    <label class="checkbox-row" style="max-width:200px;margin-bottom:1.1rem"><input type="checkbox" name="enabled" <?= $form['enabled'] ? 'checked' : '' ?>> Turned on</label>
    <div class="seo-actions">
      <button type="submit" class="btn btn-primary"><?= $form['id'] ? 'Save Redirect' : 'Add Redirect' ?></button>
      <?php if ($form['id']): ?><a href="seo-redirects.php">Cancel</a><?php endif; ?>
    </div>
  </form>

  <div>
    <form class="card" method="get">
      <div class="card-title">Test a URL</div>
      <div class="card-desc">See which redirect (if any) catches a URL.</div>
      <div class="field">
        <input type="text" name="test" value="<?= h($testUrl) ?>" placeholder="/old-page">
      </div>
      <button class="btn btn-ghost btn-sm">Test</button>
      <?php if ($testUrl !== ''): ?>
        <?php if ($testMatch): ?>
          <div class="seo-result is-pass">
            <?= (int) $testMatch['code'] === 410 ? '410 Gone' : (int) $testMatch['code'] . ' → <span class="seo-path">' . h($testMatch['location']) . '</span>' ?>
            <div class="t-sub" style="color:inherit;opacity:.8;margin-top:3px">Matched <?= h($testMatch['source']) ?><?= $testMatch['match_type'] === 'prefix' ? '/*' : '' ?></div>
          </div>
        <?php else: ?>
          <div class="seo-result is-fail">No redirect matches — the URL loads normally<?= seo_path_exists($pdo, seo_normalize_path($testUrl)) ? '' : ' (and it is a 404)' ?>.</div>
        <?php endif; ?>
      <?php endif; ?>
    </form>

    <form class="card" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="import">
      <div class="card-title">Import many</div>
      <div class="card-desc">One per line: <code>old, new, code</code>. The code is optional (301 by default); use <code>/old, 410</code> for gone pages. A CSV export from another tool works too.</div>
      <div class="field">
        <textarea name="import" rows="6" class="seo-code" placeholder="/old-blog/seo-tips, /blog/seo-tips&#10;/services/erp, /custom-erp-solution, 301&#10;/old-promo, 410"></textarea>
      </div>
      <button class="btn btn-ghost btn-sm">Import</button>
    </form>
  </div>
</div>

<script>
(function () {
  var code = document.querySelector('[data-code]');
  var target = document.querySelector('[data-target-field]');
  function sync() { target.style.display = code.value === '410' ? 'none' : ''; }
  code.addEventListener('change', sync);
  sync();
  var all = document.querySelector('[data-check-all]');
  if (all) all.addEventListener('change', function () {
    document.querySelectorAll('[data-check]').forEach(function (c) { c.checked = all.checked; });
  });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
