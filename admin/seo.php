<?php
/**
 * Admin → SEO → SEO Overview: site health score, key numbers, what to fix
 * first, recent 404s, and a way into every other SEO screen.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$audit = seo_audit($pdo);
$items = $audit['items'];
$ready = seo_module_ready($pdo);

$liveItems = array_filter($items, fn ($i) => $i['live']);
$indexable = array_filter($liveItems, fn ($i) => $i['robots_index'] !== 'noindex');
$noKeyword = count(array_filter($indexable, fn ($i) => trim($i['focus_keyword']) === ''));

$recent404 = [];
$count404 = 0;
$activeRedirects = 0;
$redirectHits = 0;
if ($ready) {
    $count404 = (int) $pdo->query('SELECT COUNT(*) FROM seo_404_log WHERE last_seen >= DATE_SUB(NOW(), INTERVAL 7 DAY)')->fetchColumn();
    $recent404 = $pdo->query('SELECT * FROM seo_404_log ORDER BY last_seen DESC LIMIT 6')->fetchAll();
    $r = $pdo->query('SELECT COUNT(*) AS n, COALESCE(SUM(hits), 0) AS hits FROM seo_redirects WHERE enabled = 1')->fetch();
    $activeRedirects = (int) $r['n'];
    $redirectHits = (int) $r['hits'];
}
$inline = seo_blog_inline_images($pdo);
$missingAlt = count(array_filter($inline, fn ($i) => trim($i['alt']) === ''));

// Worst pages first: errors, then warnings, then lowest score.
$worst = array_values(array_filter($indexable, fn ($i) => $i['issues']));
usort($worst, function ($a, $b) {
    $weight = fn ($i) => array_sum(array_map(fn ($x) => ['error' => 100, 'warning' => 10, 'notice' => 1][$x['level']], $i['issues']));
    return $weight($b) <=> $weight($a);
});
$worst = array_slice($worst, 0, 6);

$failing = array_values(array_filter($audit['checks'], fn ($c) => $c['status'] !== 'pass'));
usort($failing, fn ($a, $b) => ($a['status'] === 'fail' ? 0 : 1) <=> ($b['status'] === 'fail' ? 0 : 1));

$score = $audit['score'];
$ringClass = $score >= 80 ? '' : ($score >= 50 ? 'ok' : 'bad');
$typeLabels = array_map(fn ($t) => $t[1], seo_content_types());

$pageTitle = 'SEO Overview';
$pageSub = 'How search-ready the site is right now, and what to fix first.';
$activeNav = 'seo';
include __DIR__ . '/includes/header.php';
?>

<?php if (!$ready): ?><?= seo_migration_notice() ?><?php endif; ?>

<div class="card">
  <div class="seo-hero">
    <div class="seo-ring <?= $ringClass ?>" style="--v:<?= (int) $score ?>"><span><?= (int) $score ?><small>/ 100</small></span></div>
    <div class="seo-hero-text">
      <h2>Site SEO health: <?= $score >= 80 ? 'good' : ($score >= 50 ? 'needs work' : 'poor') ?></h2>
      <p>
        <?= count(array_filter($audit['checks'], fn ($c) => $c['status'] === 'pass')) ?> of <?= count($audit['checks']) ?> site checks pass.
        <?php if ($audit['avg_score'] !== null): ?>Pages with a focus keyword average <b><?= (int) $audit['avg_score'] ?>/100</b> on-page.<?php endif; ?>
        Across all pages there are <b><?= (int) $audit['counts']['error'] ?></b> errors, <b><?= (int) $audit['counts']['warning'] ?></b> warnings and <?= (int) $audit['counts']['notice'] ?> notices.
      </p>
    </div>
    <div class="seo-actions">
      <a class="btn btn-primary btn-sm" href="seo-audit.php">Open full audit</a>
      <a class="btn btn-ghost btn-sm" href="seo-bulk.php">Bulk edit meta</a>
    </div>
  </div>
</div>

<div class="stat-grid" style="margin-top:1.25rem">
  <div class="stat-card"><div class="stat-v"><?= (int) $audit['sitemap_count'] ?></div><div class="stat-l">URLs in sitemap</div></div>
  <div class="stat-card"><div class="stat-v"><?= count($indexable) ?><span style="font-size:15px;color:var(--g400)"> / <?= count($liveItems) ?></span></div><div class="stat-l">Indexable / live pages</div></div>
  <div class="stat-card"><div class="stat-v"><?= $ready ? $count404 : '—' ?></div><div class="stat-l">404s (last 7 days)</div></div>
  <div class="stat-card"><div class="stat-v"><?= $ready ? $activeRedirects : '—' ?></div><div class="stat-l">Active redirects<?= $ready && $redirectHits ? ' · ' . number_format($redirectHits) . ' hits' : '' ?></div></div>
</div>
<div class="stat-grid">
  <div class="stat-card"><div class="stat-v"><?= $audit['avg_score'] ?? '—' ?></div><div class="stat-l">Avg on-page score</div></div>
  <div class="stat-card"><div class="stat-v"><?= $noKeyword ?></div><div class="stat-l">Pages without focus keyword</div></div>
  <div class="stat-card"><div class="stat-v"><?= (int) $audit['counts']['error'] ?></div><div class="stat-l">Page errors</div></div>
  <div class="stat-card"><div class="stat-v"><?= $missingAlt ?></div><div class="stat-l">Blog images without alt</div></div>
</div>

<div class="seo-two">
  <div class="card">
    <div class="card-title">Fix these first</div>
    <div class="card-desc">Site-wide checks that aren't passing yet.</div>
    <?php if ($failing): ?>
      <?= seo_checks_html(array_slice($failing, 0, 7)) ?>
      <?php if (count($failing) > 7): ?><p style="margin-top:.8rem"><a class="row-link" href="seo-audit.php">See all <?= count($failing) ?> →</a></p><?php endif; ?>
    <?php else: ?>
      <div class="seo-empty">Every site check passes. 🎉</div>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-title">Pages that need the most work</div>
    <div class="card-desc">Live, indexable pages ranked by how many problems they have.</div>
    <?php if ($worst): ?>
    <table>
      <tbody>
      <?php foreach ($worst as $it): ?>
        <tr>
          <td>
            <a class="row-link" href="seo-page-edit.php?type=<?= h($it['type']) ?>&id=<?= (int) $it['id'] ?>"><?= h($it['name']) ?></a>
            <div class="t-sub"><?= h($typeLabels[$it['type']]) ?> · <?= count($it['issues']) ?> issue<?= count($it['issues']) === 1 ? '' : 's' ?> — <?= h($it['issues'][0]['text']) ?></div>
          </td>
          <td style="text-align:right"><?= seo_score_html($it['score']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <p style="margin-top:.8rem"><a class="row-link" href="seo-pages.php">All pages in Page SEO →</a></p>
    <?php else: ?>
      <div class="seo-empty">No page has an open issue.</div>
    <?php endif; ?>
  </div>
</div>

<div class="seo-two">
  <div class="card">
    <div class="card-title">Latest 404s</div>
    <div class="card-desc">Missing URLs people and bots asked for. Redirect the ones that matter.</div>
    <?php if (!$ready): ?>
      <div class="seo-empty">Starts recording once migration 031 is run.</div>
    <?php elseif (!$recent404): ?>
      <div class="seo-empty">No 404s recorded yet.</div>
    <?php else: ?>
    <table>
      <tbody>
      <?php foreach ($recent404 as $r): ?>
        <tr>
          <td><span class="seo-path"><?= h($r['path']) ?></span>
            <div class="t-sub"><?= (int) $r['hits'] ?> hit<?= (int) $r['hits'] === 1 ? '' : 's' ?> · <?= h(date('M j, g:i A', strtotime($r['last_seen']))) ?><?= $r['is_bot'] ? ' · bot' : '' ?></div></td>
          <td style="text-align:right"><a class="row-link" href="seo-redirects.php?source=<?= rawurlencode($r['path']) ?>#redirect-form">Redirect →</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <p style="margin-top:.8rem"><a class="row-link" href="seo-404.php">All 404 logs →</a></p>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-title">Search engine files</div>
    <div class="card-desc">What crawlers read first. Both are generated live from your settings.</div>
    <ul class="seo-checks">
      <li class="is-<?= seo_sitemap_settings($pdo)['enabled'] ? 'pass' : 'fail' ?>"><span class="seo-check-icon"><?= seo_sitemap_settings($pdo)['enabled'] ? '✓' : '✕' ?></span>
        <div class="seo-check-body"><div class="seo-check-title">/sitemap.xml</div><div class="seo-check-detail"><?= (int) $audit['sitemap_count'] ?> URLs · <a href="/sitemap.xml" target="_blank" class="row-link">Open ↗</a></div></div>
        <a class="seo-check-link" href="seo-sitemap.php">Sitemap →</a></li>
      <li class="is-<?= seo_robots_settings($pdo)['block_all'] ? 'fail' : 'pass' ?>"><span class="seo-check-icon"><?= seo_robots_settings($pdo)['block_all'] ? '✕' : '✓' ?></span>
        <div class="seo-check-body"><div class="seo-check-title">/robots.txt</div><div class="seo-check-detail"><?= seo_robots_settings($pdo)['block_all'] ? 'Blocking all search engines' : 'Crawling allowed' ?> · <a href="/robots.txt" target="_blank" class="row-link">Open ↗</a></div></div>
        <a class="seo-check-link" href="seo-robots.php">Robots →</a></li>
    </ul>
  </div>
</div>

<div class="card" style="margin-top:1.25rem">
  <div class="card-title">SEO tools</div>
  <div class="card-desc">Everything in the SEO menu.</div>
  <div class="seo-links">
    <a class="seo-link-card" href="seo-pages.php"><b>Page SEO</b><span>Score and fix the SEO of every page, post and case study.</span></a>
    <a class="seo-link-card" href="seo-settings.php"><b>Global Settings</b><span>Site name, title suffix, default description and share image, verification codes.</span></a>
    <a class="seo-link-card" href="seo-schema.php"><b>Schema</b><span>Organization, website and breadcrumb schema, plus custom JSON-LD.</span></a>
    <a class="seo-link-card" href="seo-sitemap.php"><b>Sitemap</b><span>Choose what's listed in /sitemap.xml, priorities and exclusions.</span></a>
    <a class="seo-link-card" href="seo-robots.php"><b>Robots</b><span>Edit robots.txt and test whether a URL can be crawled.</span></a>
    <a class="seo-link-card" href="seo-redirects.php"><b>Redirects</b><span>301 / 302 / 410 rules for moved or deleted pages.</span></a>
    <a class="seo-link-card" href="seo-404.php"><b>404 Logs</b><span>Broken URLs visitors hit, ready to redirect.</span></a>
    <a class="seo-link-card" href="seo-images.php"><b>Image SEO</b><span>Alt text for every blog image, heavy files and file names.</span></a>
    <a class="seo-link-card" href="seo-audit.php"><b>SEO Audit</b><span>Full site check and a live check of any page.</span></a>
    <a class="seo-link-card" href="seo-bulk.php"><b>Bulk Editor</b><span>Edit titles, descriptions and keywords for many pages at once.</span></a>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
