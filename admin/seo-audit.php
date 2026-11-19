<?php
/**
 * Admin → SEO → SEO Audit: every site-wide check (crawling, sitemap, schema,
 * content, images, redirects, 404s), every page's open issues grouped by
 * problem, and a live check that fetches one page and inspects the HTML
 * search engines actually receive.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$audit = seo_audit($pdo);
$types = seo_content_types();
$issueLabels = seo_issue_labels();

// Group page issues by problem.
$byIssue = [];
foreach ($audit['items'] as $it) {
    if (!$it['live']) {
        continue;
    }
    foreach ($it['issues'] as $iss) {
        $byIssue[$iss['code']]['level'] = $byIssue[$iss['code']]['level'] ?? $iss['level'];
        if ($iss['level'] === 'error' || ($iss['level'] === 'warning' && $byIssue[$iss['code']]['level'] === 'notice')) {
            $byIssue[$iss['code']]['level'] = $iss['level'];
        }
        $byIssue[$iss['code']]['items'][] = ['item' => $it, 'text' => $iss['text']];
    }
}
$levelRank = ['error' => 0, 'warning' => 1, 'notice' => 2];
uksort($byIssue, fn ($a, $b) => [$levelRank[$byIssue[$a]['level']], -count($byIssue[$a]['items'])] <=> [$levelRank[$byIssue[$b]['level']], -count($byIssue[$b]['items'])]);

// Live check of one URL on this site.
$checkPath = '';
$live = null;
$checkInput = is_string($_GET['check'] ?? null) ? trim($_GET['check']) : '';
if ($checkInput !== '') {
    $host = strtolower((string) parse_url($checkInput, PHP_URL_HOST));
    if (preg_match('#^https?://#i', $checkInput) && $host !== strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''))) {
        $live = ['ok' => false, 'error' => 'Live check only works for pages on this site (' . ($_SERVER['HTTP_HOST'] ?? '') . ').'];
    } else {
        $checkPath = seo_normalize_path($checkInput);
        $live = seo_live_check($checkPath);
    }
}
$checkOptions = array_values(array_filter($audit['items'], fn ($i) => $i['live']));
usort($checkOptions, fn ($a, $b) => strcmp($a['path'], $b['path']));

$counts = ['pass' => 0, 'warn' => 0, 'fail' => 0];
foreach ($audit['checks'] as $c) {
    $counts[$c['status']]++;
}
$score = $audit['score'];

$pageTitle = 'SEO Audit';
$pageSub = 'A full check of the site, page by page, plus a live check of any URL.';
$activeNav = 'seo-audit';
include __DIR__ . '/includes/header.php';
?>

<div class="card">
  <div class="seo-hero">
    <div class="seo-ring <?= $score >= 80 ? '' : ($score >= 50 ? 'ok' : 'bad') ?>" style="--v:<?= (int) $score ?>"><span><?= (int) $score ?><small>/ 100</small></span></div>
    <div class="seo-hero-text">
      <h2><?= $counts['fail'] ?> failing · <?= $counts['warn'] ?> to improve · <?= $counts['pass'] ?> passing</h2>
      <p>Checked just now against <?= count($audit['items']) ?> pages, posts and case studies. Page-level: <?= (int) $audit['counts']['error'] ?> errors, <?= (int) $audit['counts']['warning'] ?> warnings, <?= (int) $audit['counts']['notice'] ?> notices.</p>
    </div>
    <div class="seo-actions">
      <a class="btn btn-ghost btn-sm" href="seo-audit.php">Run again</a>
      <a class="btn btn-ghost btn-sm" href="#live-check">Live check a page</a>
    </div>
  </div>
</div>

<div class="card" style="margin-top:1.25rem">
  <div class="card-title">Site checks</div>
  <div class="card-desc">Failing first.</div>
  <?php
  $sorted = $audit['checks'];
  usort($sorted, fn ($a, $b) => ['fail' => 0, 'warn' => 1, 'pass' => 2][$a['status']] <=> ['fail' => 0, 'warn' => 1, 'pass' => 2][$b['status']]);
  echo seo_checks_html($sorted);
  ?>
</div>

<div class="card" style="margin-top:1.25rem">
  <div class="card-title">Page issues</div>
  <div class="card-desc">Live pages only, grouped by problem. Click a page to fix it.</div>
  <?php if (!$byIssue): ?>
    <div class="seo-empty">No live page has an open issue. ✓</div>
  <?php else: ?>
    <?php foreach ($byIssue as $code => $group): ?>
      <details class="seo-issue-group" style="border-top:1px solid var(--border);padding:.8rem 0">
        <summary style="cursor:pointer;display:flex;align-items:center;gap:.6rem;font-size:13px;font-weight:700">
          <span class="badge <?= ['error' => 'badge-error', 'warning' => 'badge-draft', 'notice' => 'badge-muted'][$group['level']] ?>"><?= h($group['level']) ?></span>
          <?= h($issueLabels[$code] ?? $code) ?>
          <span class="t-sub" style="margin:0">— <?= count($group['items']) ?> page<?= count($group['items']) === 1 ? '' : 's' ?></span>
          <a class="seo-check-link" style="margin-left:auto" href="seo-pages.php?issue=<?= h($code) ?>">Open in Page SEO →</a>
        </summary>
        <table style="margin-top:.6rem">
          <tbody>
          <?php foreach (array_slice($group['items'], 0, 25) as $row): $it = $row['item']; ?>
            <tr>
              <td style="width:34%"><a class="row-link" href="seo-page-edit.php?type=<?= h($it['type']) ?>&id=<?= (int) $it['id'] ?>"><?= h($it['name']) ?></a><div class="t-sub seo-path"><?= h($it['path']) ?></div></td>
              <td class="t-sub" style="font-size:12px"><?= h($row['text']) ?></td>
              <td style="width:60px;text-align:right"><?= seo_score_html($it['score']) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php if (count($group['items']) > 25): ?><p class="t-sub" style="margin-top:.5rem">+<?= count($group['items']) - 25 ?> more in Page SEO.</p><?php endif; ?>
      </details>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<div class="card" id="live-check" style="margin-top:1.25rem">
  <div class="card-title">Live page check</div>
  <div class="card-desc">Loads a page from the live site the way Google does and checks the HTML it gets back — title, headings, images, schema, speed.</div>
  <form class="seo-filters" method="get" action="seo-audit.php#live-check">
    <input type="text" name="check" list="seo-check-paths" value="<?= h($checkPath !== '' ? $checkPath : $checkInput) ?>" placeholder="/ or /about-us or <?= h(site_base_url()) ?>/blog/…">
    <datalist id="seo-check-paths">
      <?php foreach ($checkOptions as $o): ?><option value="<?= h($o['path']) ?>"><?= h($o['name']) ?></option><?php endforeach; ?>
    </datalist>
    <button class="btn btn-primary btn-sm" type="submit">Check page</button>
  </form>
  <?php if ($live === null): ?>
    <div class="seo-empty">Pick a page and press Check page.</div>
  <?php elseif (!$live['ok']): ?>
    <div class="alert alert-error">Couldn't check <?= h($live['url'] ?? $checkInput) ?>: <?= h($live['error']) ?></div>
  <?php else: ?>
    <p class="t-sub" style="margin-bottom:.4rem">Checked <a class="row-link" href="<?= h($live['final_url']) ?>" target="_blank"><?= h($live['final_url']) ?> ↗</a><?= $live['final_url'] !== $live['url'] ? ' (redirected from ' . h($live['url']) . ')' : '' ?></p>
    <?php
    $lc = ['pass' => 0, 'warn' => 0, 'fail' => 0];
    foreach ($live['checks'] as $c) {
        $lc[$c['status']]++;
    }
    ?>
    <p style="font-size:13px;font-weight:700;margin-bottom:.4rem"><?= $lc['pass'] ?> passed · <?= $lc['warn'] ?> to improve · <?= $lc['fail'] ?> failed</p>
    <?= seo_checks_html($live['checks']) ?>
    <div class="seo-actions" style="margin-top:1rem">
      <a href="https://search.google.com/test/rich-results?url=<?= rawurlencode($live['final_url']) ?>" target="_blank" rel="noopener">Google Rich Results test ↗</a>
      <a href="https://pagespeed.web.dev/analysis?url=<?= rawurlencode($live['final_url']) ?>" target="_blank" rel="noopener">PageSpeed Insights ↗</a>
      <a href="https://search.google.com/test/mobile-friendly?url=<?= rawurlencode($live['final_url']) ?>" target="_blank" rel="noopener">Mobile test ↗</a>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
