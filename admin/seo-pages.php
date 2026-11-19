<?php
/**
 * Admin → SEO → Page SEO: every page, blog post and case study with its SEO
 * title, description, focus keyword, index status, score and open issues.
 * "Edit SEO" opens seo-page-edit.php for that one item.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$types = seo_content_types();
$issueLabels = seo_issue_labels();

$fType = isset($types[$_GET['type'] ?? '']) ? $_GET['type'] : '';
$fIssue = isset($issueLabels[$_GET['issue'] ?? '']) ? $_GET['issue'] : '';
$fStatus = in_array($_GET['status'] ?? '', ['live', 'draft'], true) ? $_GET['status'] : '';
$fIndex = in_array($_GET['index'] ?? '', ['index', 'noindex'], true) ? $_GET['index'] : '';
$fScore = in_array($_GET['score'] ?? '', ['good', 'ok', 'bad', 'none'], true) ? $_GET['score'] : '';
$fQuery = is_string($_GET['q'] ?? null) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$sort = in_array($_GET['sort'] ?? '', ['score', 'issues', 'name', 'updated'], true) ? $_GET['sort'] : 'issues';

$all = seo_analyzed_items($pdo);
$items = array_values(array_filter($all, function ($it) use ($fType, $fIssue, $fStatus, $fIndex, $fScore, $fQuery) {
    if ($fType !== '' && $it['type'] !== $fType) {
        return false;
    }
    if ($fStatus === 'live' && !$it['live'] || $fStatus === 'draft' && $it['live']) {
        return false;
    }
    if ($fIndex !== '' && $it['robots_index'] !== $fIndex) {
        return false;
    }
    if ($fIssue !== '' && !in_array($fIssue, array_column($it['issues'], 'code'), true)) {
        return false;
    }
    if ($fScore === 'none' && $it['score'] !== null || $fScore !== '' && $fScore !== 'none' && seo_score_class($it['score']) !== $fScore) {
        return false;
    }
    if ($fQuery !== '') {
        $hay = mb_strtolower($it['name'] . ' ' . $it['path'] . ' ' . $it['title'] . ' ' . $it['description'] . ' ' . $it['focus_keyword']);
        if (!str_contains($hay, mb_strtolower($fQuery))) {
            return false;
        }
    }
    return true;
}));

$weight = fn ($i) => array_sum(array_map(fn ($x) => ['error' => 100, 'warning' => 10, 'notice' => 1][$x['level']], $i['issues']));
usort($items, function ($a, $b) use ($sort, $weight) {
    switch ($sort) {
        case 'score':
            return ($a['score'] ?? -1) <=> ($b['score'] ?? -1);
        case 'name':
            return strcasecmp($a['name'], $b['name']);
        case 'updated':
            return strcmp($b['updated_at'], $a['updated_at']);
        default:
            return $weight($b) <=> $weight($a) ?: strcasecmp($a['name'], $b['name']);
    }
});

$perPage = 40;
$pages = max(1, (int) ceil(count($items) / $perPage));
$page = min($pages, max(1, (int) ($_GET['page'] ?? 1)));
$shown = array_slice($items, ($page - 1) * $perPage, $perPage);

$pageTitle = 'Page SEO';
$pageSub = 'The search snippet, keyword and SEO score of every page, blog post and case study.';
$activeNav = 'seo-pages';
include __DIR__ . '/includes/header.php';
?>

<?= seo_flash_html() ?>

<div class="card">
  <form class="seo-filters" method="get">
    <input type="search" name="q" value="<?= h($fQuery) ?>" placeholder="Search name, URL, title, keyword…">
    <select name="type" onchange="this.form.submit()">
      <option value="">All types</option>
      <?php foreach ($types as $k => $t): ?><option value="<?= h($k) ?>" <?= $fType === $k ? 'selected' : '' ?>><?= h($t[1]) ?>s</option><?php endforeach; ?>
    </select>
    <select name="issue" onchange="this.form.submit()">
      <option value="">Any issue</option>
      <?php foreach ($issueLabels as $k => $l): ?><option value="<?= h($k) ?>" <?= $fIssue === $k ? 'selected' : '' ?>><?= h($l) ?></option><?php endforeach; ?>
    </select>
    <select name="score" onchange="this.form.submit()">
      <option value="">Any score</option>
      <option value="good" <?= $fScore === 'good' ? 'selected' : '' ?>>Good (80+)</option>
      <option value="ok" <?= $fScore === 'ok' ? 'selected' : '' ?>>OK (50–79)</option>
      <option value="bad" <?= $fScore === 'bad' ? 'selected' : '' ?>>Poor (under 50)</option>
      <option value="none" <?= $fScore === 'none' ? 'selected' : '' ?>>No score (no keyword)</option>
    </select>
    <select name="status" onchange="this.form.submit()">
      <option value="">Live &amp; draft</option>
      <option value="live" <?= $fStatus === 'live' ? 'selected' : '' ?>>Live only</option>
      <option value="draft" <?= $fStatus === 'draft' ? 'selected' : '' ?>>Draft only</option>
    </select>
    <select name="index" onchange="this.form.submit()">
      <option value="">Index &amp; noindex</option>
      <option value="index" <?= $fIndex === 'index' ? 'selected' : '' ?>>Indexed</option>
      <option value="noindex" <?= $fIndex === 'noindex' ? 'selected' : '' ?>>No Index</option>
    </select>
    <select name="sort" onchange="this.form.submit()">
      <option value="issues" <?= $sort === 'issues' ? 'selected' : '' ?>>Sort: most issues</option>
      <option value="score" <?= $sort === 'score' ? 'selected' : '' ?>>Sort: lowest score</option>
      <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Sort: name</option>
      <option value="updated" <?= $sort === 'updated' ? 'selected' : '' ?>>Sort: recently updated</option>
    </select>
    <button class="btn btn-ghost btn-sm" type="submit">Filter</button>
    <?php if ($fType || $fIssue || $fStatus || $fIndex || $fScore || $fQuery): ?><a class="btn btn-ghost btn-sm" href="seo-pages.php">Clear</a><?php endif; ?>
    <span class="seo-count"><?= count($items) ?> of <?= count($all) ?></span>
  </form>

  <?php if (!$shown): ?>
    <div class="seo-empty">Nothing matches these filters.</div>
  <?php else: ?>
  <div class="table-wrap">
  <table>
    <thead><tr><th>Page</th><th>Search snippet</th><th>Keyword</th><th>Index</th><th>Score</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($shown as $it):
        $tLen = mb_strlen($it['title']);
        $dLen = mb_strlen($it['description']);
    ?>
      <tr>
        <td style="min-width:170px">
          <a class="row-link" href="seo-page-edit.php?type=<?= h($it['type']) ?>&id=<?= (int) $it['id'] ?>"><?= h($it['name']) ?></a>
          <div class="t-sub"><?= h($types[$it['type']][1]) ?> · <span class="seo-path"><?= h($it['path']) ?></span></div>
          <?php if (!$it['live']): ?><span class="badge badge-draft" style="margin-top:4px">Draft</span><?php endif; ?>
        </td>
        <td style="min-width:260px;max-width:420px">
          <div style="font-weight:700;color:#1a0dab;font-size:13px"><?= h(mb_strimwidth($it['title'], 0, 70, '…')) ?></div>
          <div class="t-sub" style="color:var(--g500)"><?= $it['description'] !== '' ? h(mb_strimwidth($it['description'], 0, 170, '…')) : '<i>No meta description</i>' ?></div>
          <div class="t-sub">Title <?= $tLen ?> · Description <?= $dLen ?> characters</div>
          <?php if ($it['issues']): ?>
          <ul class="seo-issues">
            <?php foreach (array_slice($it['issues'], 0, 3) as $iss): ?><li class="<?= h($iss['level']) ?>"><?= h($iss['text']) ?></li><?php endforeach; ?>
            <?php if (count($it['issues']) > 3): ?><li>+<?= count($it['issues']) - 3 ?> more</li><?php endif; ?>
          </ul>
          <?php endif; ?>
        </td>
        <td><?= $it['focus_keyword'] !== '' ? h($it['focus_keyword']) : '<span class="t-sub">—</span>' ?></td>
        <td><span class="badge <?= $it['robots_index'] === 'noindex' ? 'badge-draft' : 'badge-published' ?>"><?= h($it['robots_index']) ?></span></td>
        <td><?= seo_score_html($it['score']) ?></td>
        <td>
          <div class="seo-actions">
            <a href="seo-page-edit.php?type=<?= h($it['type']) ?>&id=<?= (int) $it['id'] ?>">Edit SEO →</a>
            <?php if ($it['live']): ?><a href="<?= h($it['path']) ?>" target="_blank">View ↗</a><?php endif; ?>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?= seo_pager_html($page, $pages) ?>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
