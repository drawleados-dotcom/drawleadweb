<?php
/**
 * Admin → SEO → 404 Logs: every missing URL the site was asked for (recorded
 * by index.php), with hit counts, the page that linked to it, and a one-click
 * way to redirect it.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$ready = seo_table_ready($pdo, 'seo_404_log');
$cfg = seo_404_settings($pdo);

if ($ready && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';
    if ($action === 'settings') {
        seo_save_json_setting($pdo, 'seo_404', [
            'enabled' => isset($_POST['enabled']) ? 1 : 0,
            'ignore_bots' => isset($_POST['ignore_bots']) ? 1 : 0,
            'retention_days' => max(7, min(365, (int) ($_POST['retention_days'] ?? 90))),
        ]);
        seo_flash('404 logging settings saved.');
    } elseif ($action === 'delete') {
        $ids = array_filter(array_map('intval', (array) ($_POST['ids'] ?? [$_POST['id'] ?? 0])));
        if ($ids) {
            $in = implode(',', array_fill(0, count($ids), '?'));
            $pdo->prepare("DELETE FROM seo_404_log WHERE id IN ($in)")->execute(array_values($ids));
            seo_flash(count($ids) === 1 ? 'Entry deleted.' : count($ids) . ' entries deleted.');
        }
    } elseif ($action === 'clear_bots') {
        $n = $pdo->exec('DELETE FROM seo_404_log WHERE is_bot = 1');
        seo_flash("$n bot entries cleared.");
    } elseif ($action === 'clear_redirected') {
        $n = 0;
        foreach ($pdo->query('SELECT id, path FROM seo_404_log')->fetchAll() as $r) {
            if (seo_find_redirect($pdo, (string) $r['path']) || seo_path_exists($pdo, (string) $r['path'])) {
                $pdo->prepare('DELETE FROM seo_404_log WHERE id = ?')->execute([$r['id']]);
                $n++;
            }
        }
        seo_flash("$n fixed entries cleared (they now redirect or load a page).");
    } elseif ($action === 'clear_all') {
        $pdo->exec('DELETE FROM seo_404_log');
        seo_flash('404 log cleared.');
    } elseif ($action === 'bulk_redirect') {
        $target = seo_clean_redirect_target((string) ($_POST['target'] ?? ''));
        $ids = array_filter(array_map('intval', (array) ($_POST['ids'] ?? [])));
        if ($target === '' || !$ids || !seo_table_ready($pdo, 'seo_redirects')) {
            seo_flash('Tick some rows and enter where to send them.', 'error');
        } else {
            $in = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("SELECT id, path FROM seo_404_log WHERE id IN ($in)");
            $stmt->execute(array_values($ids));
            $made = 0;
            foreach ($stmt->fetchAll() as $r) {
                $src = seo_normalize_path((string) $r['path']);
                if ($src === '' || $src === '/' || strcasecmp($src, seo_normalize_path($target)) === 0) {
                    continue;
                }
                $dup = $pdo->prepare("SELECT 1 FROM seo_redirects WHERE source = ? AND match_type = 'exact'");
                $dup->execute([$src]);
                if (!$dup->fetchColumn()) {
                    $pdo->prepare("INSERT INTO seo_redirects (source, match_type, target, code, enabled, note) VALUES (?, 'exact', ?, 301, 1, 'From 404 log')")
                        ->execute([$src, $target]);
                    $made++;
                }
                $pdo->prepare('DELETE FROM seo_404_log WHERE id = ?')->execute([$r['id']]);
            }
            seo_flash("$made 301 redirect(s) to $target created.");
        }
    }
    seo_back('seo-404.php?' . http_build_query(array_intersect_key($_GET, array_flip(['q', 'who', 'sort', 'page']))));
}

$fQuery = is_string($_GET['q'] ?? null) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$fWho = in_array($_GET['who'] ?? '', ['people', 'bots'], true) ? $_GET['who'] : '';
$sort = in_array($_GET['sort'] ?? '', ['hits', 'recent', 'first'], true) ? $_GET['sort'] : 'recent';

$rows = [];
$total = 0;
$pages = 1;
$page = 1;
$stats = ['total' => 0, 'hits' => 0, 'week' => 0, 'bots' => 0];
if ($ready) {
    $stats = $pdo->query(
        'SELECT COUNT(*) AS total, COALESCE(SUM(hits), 0) AS hits,
                COALESCE(SUM(last_seen >= DATE_SUB(NOW(), INTERVAL 7 DAY)), 0) AS week,
                COALESCE(SUM(is_bot), 0) AS bots
         FROM seo_404_log'
    )->fetch();
    $where = [];
    $params = [];
    if ($fQuery !== '') {
        $like = '%' . addcslashes($fQuery, '%_\\') . '%';
        $where[] = '(path LIKE ? OR referrer LIKE ?)';
        array_push($params, $like, $like);
    }
    if ($fWho === 'people') {
        $where[] = 'is_bot = 0';
    } elseif ($fWho === 'bots') {
        $where[] = 'is_bot = 1';
    }
    $w = $where ? ' WHERE ' . implode(' AND ', $where) : '';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM seo_404_log$w");
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();
    $perPage = 50;
    $pages = max(1, (int) ceil($total / $perPage));
    $page = min($pages, max(1, (int) ($_GET['page'] ?? 1)));
    $offset = ($page - 1) * $perPage;
    $order = ['hits' => 'hits DESC, last_seen DESC', 'recent' => 'last_seen DESC', 'first' => 'first_seen DESC'][$sort];
    $stmt = $pdo->prepare("SELECT * FROM seo_404_log$w ORDER BY $order LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
}

$pageTitle = '404 Logs';
$pageSub = 'Missing URLs visitors and crawlers asked for. Redirect the ones that matter.';
$activeNav = 'seo-404';
include __DIR__ . '/includes/header.php';
?>

<?= seo_flash_html() ?>
<?php if (!$ready): ?><?= seo_migration_notice() ?><?php include __DIR__ . '/includes/footer.php'; return; endif; ?>
<?php if (!$cfg['enabled']): ?><div class="alert alert-error">404 logging is switched off — new missing URLs aren't being recorded.</div><?php endif; ?>

<div class="stat-grid">
  <div class="stat-card"><div class="stat-v"><?= number_format((int) $stats['total']) ?></div><div class="stat-l">Missing URLs</div></div>
  <div class="stat-card"><div class="stat-v"><?= number_format((int) $stats['hits']) ?></div><div class="stat-l">Total hits</div></div>
  <div class="stat-card"><div class="stat-v"><?= number_format((int) $stats['week']) ?></div><div class="stat-l">Seen in last 7 days</div></div>
  <div class="stat-card"><div class="stat-v"><?= number_format((int) $stats['bots']) ?></div><div class="stat-l">From bots</div></div>
</div>

<div class="card">
  <form class="seo-filters" method="get">
    <input type="search" name="q" value="<?= h($fQuery) ?>" placeholder="Search URL or referring page…">
    <select name="who" onchange="this.form.submit()">
      <option value="">People &amp; bots</option>
      <option value="people" <?= $fWho === 'people' ? 'selected' : '' ?>>People only</option>
      <option value="bots" <?= $fWho === 'bots' ? 'selected' : '' ?>>Bots only</option>
    </select>
    <select name="sort" onchange="this.form.submit()">
      <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Sort: last seen</option>
      <option value="hits" <?= $sort === 'hits' ? 'selected' : '' ?>>Sort: most hits</option>
      <option value="first" <?= $sort === 'first' ? 'selected' : '' ?>>Sort: first seen</option>
    </select>
    <button class="btn btn-ghost btn-sm">Filter</button>
    <?php if ($fQuery || $fWho): ?><a class="btn btn-ghost btn-sm" href="seo-404.php">Clear</a><?php endif; ?>
    <span class="seo-count"><?= number_format($total) ?> shown</span>
  </form>

  <?php if (!$rows): ?>
    <div class="seo-empty"><?= (int) $stats['total'] === 0 ? 'No 404s recorded yet. Every missing URL anyone opens will show up here.' : 'Nothing matches.' ?></div>
  <?php else: ?>
  <form method="post" id="bulk404"><?= csrf_field() ?></form>
  <div class="table-wrap">
  <table>
    <thead><tr><th style="width:30px"><input type="checkbox" data-check-all style="width:15px;height:15px"></th><th>Missing URL</th><th>Hits</th><th>Last seen</th><th>Came from</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r):
        $redir = seo_find_redirect($pdo, (string) $r['path']);
    ?>
      <tr>
        <td><input type="checkbox" name="ids[]" value="<?= (int) $r['id'] ?>" form="bulk404" data-check style="width:15px;height:15px"></td>
        <td style="max-width:320px">
          <span class="seo-path"><?= h($r['path']) ?></span>
          <div style="margin-top:3px">
            <?php if ($r['is_bot']): ?><span class="badge badge-muted" title="<?= h($r['user_agent']) ?>">Bot</span><?php endif; ?>
            <?php if ($redir): ?><span class="badge badge-published">Redirected → <?= (int) $redir['code'] === 410 ? '410' : h(mb_strimwidth($redir['location'], 0, 40, '…')) ?></span><?php endif; ?>
          </div>
        </td>
        <td class="t-name"><?= number_format((int) $r['hits']) ?></td>
        <td><?= h(date('M j, Y', strtotime($r['last_seen']))) ?><div class="t-sub"><?= h(date('g:i A', strtotime($r['last_seen']))) ?> · first <?= h(date('M j', strtotime($r['first_seen']))) ?></div></td>
        <td style="max-width:240px"><?php if ($r['referrer'] !== ''): ?><a class="row-link seo-path" style="font-weight:500" href="<?= h($r['referrer']) ?>" target="_blank" rel="noopener noreferrer"><?= h(mb_strimwidth(preg_replace('#^https?://#', '', $r['referrer']), 0, 60, '…')) ?></a><?php else: ?><span class="t-sub">Direct / unknown</span><?php endif; ?></td>
        <td>
          <div class="seo-actions">
            <?php if (!$redir): ?><a href="seo-redirects.php?source=<?= rawurlencode($r['path']) ?>#redirect-form">Redirect →</a><?php endif; ?>
            <form class="seo-inline-form" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button type="submit" class="is-danger">Delete</button></form>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <div class="seo-filters" style="margin:1rem 0 0">
    <input type="text" name="target" form="bulk404" placeholder="Redirect ticked URLs to… e.g. /blog">
    <button type="submit" form="bulk404" name="action" value="bulk_redirect" class="btn btn-primary btn-sm">Create 301 redirects</button>
    <button type="submit" form="bulk404" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete the ticked entries?')">Delete ticked</button>
  </div>
  <?= seo_pager_html($page, $pages) ?>
  <?php endif; ?>
</div>

<div class="seo-two">
  <form class="card" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="settings">
    <div class="card-title">Logging</div>
    <label class="seo-switch-row"><input type="checkbox" name="enabled" <?= $cfg['enabled'] ? 'checked' : '' ?>><span><b>Record 404s</b><span>One row per missing URL, counting hits.</span></span></label>
    <label class="seo-switch-row"><input type="checkbox" name="ignore_bots" <?= $cfg['ignore_bots'] ? 'checked' : '' ?>><span><b>Ignore bots</b><span>Skip crawlers and vulnerability scanners (e.g. /wp-login.php probes).</span></span></label>
    <div class="field" style="max-width:240px">
      <label for="retention_days">Forget URLs not seen for</label>
      <select id="retention_days" name="retention_days">
        <?php foreach ([30, 60, 90, 180, 365] as $d): ?><option value="<?= $d ?>" <?= (int) $cfg['retention_days'] === $d ? 'selected' : '' ?>><?= $d ?> days</option><?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-primary btn-sm">Save</button>
  </form>

  <div class="card">
    <div class="card-title">Clean up</div>
    <div class="card-desc">Deleting entries doesn't change the site — a URL that is still missing reappears the next time someone opens it.</div>
    <div class="seo-actions" style="flex-direction:column;align-items:flex-start;gap:.6rem">
      <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="clear_redirected"><button class="btn btn-ghost btn-sm" type="submit">Clear fixed URLs (now redirected or live)</button></form>
      <form method="post" onsubmit="return confirm('Delete every bot entry?')"><?= csrf_field() ?><input type="hidden" name="action" value="clear_bots"><button class="btn btn-ghost btn-sm" type="submit">Clear bot entries</button></form>
      <form method="post" onsubmit="return confirm('Delete the whole 404 log?')"><?= csrf_field() ?><input type="hidden" name="action" value="clear_all"><button class="btn btn-danger btn-sm" type="submit">Clear the whole log</button></form>
    </div>
  </div>
</div>

<script>
(function () {
  var all = document.querySelector('[data-check-all]');
  if (all) all.addEventListener('change', function () {
    document.querySelectorAll('[data-check]').forEach(function (c) { c.checked = all.checked; });
  });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
