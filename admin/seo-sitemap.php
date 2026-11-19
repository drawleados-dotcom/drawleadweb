<?php
/**
 * Admin → SEO → Sitemap: what /sitemap.xml lists. Drafts and noindex pages
 * are always left out; here you choose the content types, priorities,
 * change frequencies, image entries and any URLs to exclude.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$s = seo_sitemap_settings($pdo);
$error = '';
$groups = ['home' => 'Home page', 'page' => 'Pages', 'blog' => 'Blog posts', 'case_study' => 'Case studies'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $new = seo_sitemap_defaults();
    foreach (['enabled', 'include_pages', 'include_blogs', 'include_case_studies', 'include_images'] as $k) {
        $new[$k] = isset($_POST[$k]) ? 1 : 0;
    }
    foreach (array_keys($groups) as $g) {
        $p = (string) ($_POST[$g . '_priority'] ?? '');
        $new[$g . '_priority'] = is_numeric($p) ? number_format(max(0, min(1, (float) $p)), 1) : $new[$g . '_priority'];
        $f = (string) ($_POST[$g . '_changefreq'] ?? '');
        $new[$g . '_changefreq'] = in_array($f, seo_changefreqs(), true) ? $f : $new[$g . '_changefreq'];
    }
    $exclude = [];
    foreach (preg_split('/\R/', (string) ($_POST['exclude'] ?? '')) ?: [] as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $exclude[] = str_ends_with($line, '/*') ? rtrim(seo_normalize_path(substr($line, 0, -2)), '/') . '/*' : seo_normalize_path($line);
    }
    // Ticked rows in the URL list below also become exclusions.
    foreach ((array) ($_POST['exclude_path'] ?? []) as $p) {
        if (is_string($p) && $p !== '') {
            $exclude[] = seo_normalize_path($p);
        }
    }
    $new['exclude'] = implode("\n", array_values(array_unique($exclude)));
    seo_save_json_setting($pdo, 'seo_sitemap', $new);
    seo_flash($new['enabled'] ? 'Sitemap settings saved — /sitemap.xml is updated.' : 'Sitemap switched off — /sitemap.xml now returns 404.');
    seo_back('seo-sitemap.php');
}

$entries = seo_sitemap_entries($pdo, $s);
$byType = array_count_values(array_column($entries, 'type'));
$imageCount = array_sum(array_map(fn ($e) => count($e['images']), $entries));
$excluded = seo_sitemap_exclusions($s);
$robotsMentions = stripos(seo_robots_txt($pdo), 'sitemap:') !== false;

$pageTitle = 'Sitemap';
$pageSub = 'The list of URLs search engines use to find every page.';
$activeNav = 'seo-sitemap';
include __DIR__ . '/includes/header.php';
?>

<?= seo_flash_html() ?>

<div class="stat-grid">
  <div class="stat-card"><div class="stat-v"><?= $s['enabled'] ? count($entries) : 0 ?></div><div class="stat-l">URLs listed</div></div>
  <div class="stat-card"><div class="stat-v"><?= ($byType['page'] ?? 0) + ($byType['home'] ?? 0) ?></div><div class="stat-l">Pages</div></div>
  <div class="stat-card"><div class="stat-v"><?= ($byType['blog'] ?? 0) ?> · <?= ($byType['case_study'] ?? 0) ?></div><div class="stat-l">Posts · Case studies</div></div>
  <div class="stat-card"><div class="stat-v"><?= $imageCount ?></div><div class="stat-l">Images</div></div>
</div>

<?php if (!$s['enabled']): ?>
  <div class="alert alert-error">The sitemap is switched off. Search engines can still crawl the site, but will find new pages more slowly.</div>
<?php elseif (!$robotsMentions): ?>
  <div class="alert alert-error">robots.txt doesn't mention the sitemap. <a href="seo-robots.php" style="color:inherit">Add "Sitemap: {sitemap}" in Robots</a> so crawlers find it.</div>
<?php endif; ?>

<form method="post">
  <?= csrf_field() ?>

  <div class="card">
    <div class="card-title">Your sitemap</div>
    <div class="card-desc">
      <a class="row-link" href="/sitemap.xml" target="_blank"><?= h(site_base_url()) ?>/sitemap.xml ↗</a> —
      submit this address once in <a class="row-link" href="https://search.google.com/search-console/sitemaps" target="_blank" rel="noopener">Google Search Console → Sitemaps ↗</a>
      and <a class="row-link" href="https://www.bing.com/webmasters/sitemaps" target="_blank" rel="noopener">Bing Webmaster Tools ↗</a>. It updates by itself whenever content changes.
    </div>
    <label class="seo-switch-row"><input type="checkbox" name="enabled" <?= $s['enabled'] ? 'checked' : '' ?>>
      <span><b>Sitemap is on</b><span>Turn off only if another tool generates your sitemap.</span></span></label>
    <div class="seo-two">
      <div>
        <label class="seo-switch-row"><input type="checkbox" name="include_pages" <?= $s['include_pages'] ? 'checked' : '' ?>><span><b>Pages</b><span>Every published page in Admin → Pages.</span></span></label>
        <label class="seo-switch-row"><input type="checkbox" name="include_blogs" <?= $s['include_blogs'] ? 'checked' : '' ?>><span><b>Blog</b><span>/blog and every published post.</span></span></label>
      </div>
      <div>
        <label class="seo-switch-row"><input type="checkbox" name="include_case_studies" <?= $s['include_case_studies'] ? 'checked' : '' ?>><span><b>Case studies</b><span>/case-studies and every published case study.</span></span></label>
        <label class="seo-switch-row"><input type="checkbox" name="include_images" <?= $s['include_images'] ? 'checked' : '' ?>><span><b>Image entries</b><span>Featured and case study images, so they can appear in Google Images.</span></span></label>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Priority &amp; change frequency</div>
    <div class="card-desc">Hints for crawlers. Google mostly relies on the last-modified date (filled in automatically), but Bing and others still read these.</div>
    <div class="table-wrap">
    <table>
      <thead><tr><th>Content</th><th>Priority (0.0 – 1.0)</th><th>Changes</th></tr></thead>
      <tbody>
      <?php foreach ($groups as $g => $label): ?>
        <tr>
          <td class="t-name"><?= h($label) ?></td>
          <td style="width:200px"><div class="field" style="margin:0"><input type="number" name="<?= $g ?>_priority" min="0" max="1" step="0.1" value="<?= h($s[$g . '_priority']) ?>"></div></td>
          <td style="width:220px"><div class="field" style="margin:0"><select name="<?= $g ?>_changefreq">
            <?php foreach (seo_changefreqs() as $f): ?><option value="<?= $f ?>" <?= $s[$g . '_changefreq'] === $f ? 'selected' : '' ?>><?= ucfirst($f) ?></option><?php endforeach; ?>
          </select></div></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Exclude URLs</div>
    <div class="card-desc">Leave pages out of the sitemap without hiding them from search — e.g. test pages or alternate home page versions. To hide a page from Google entirely, set it to No Index in Page SEO.</div>
    <div class="field">
      <label for="exclude">Excluded paths</label>
      <textarea id="exclude" name="exclude" rows="5" class="seo-code" placeholder="/home-2&#10;/home-5&#10;/industry-*"><?= h($s['exclude']) ?></textarea>
      <div class="field-hint">One per line. <code>/blog/*</code> leaves out a whole section, <code>/industry-*</code> every URL starting with /industry-. You can also tick rows in the list below.</div>
    </div>
  </div>

  <div class="card">
    <div class="card-title">URLs in the sitemap (<?= count($entries) ?>)</div>
    <div class="card-desc">Exactly what /sitemap.xml lists right now. Tick a row and save to exclude it.</div>
    <?php if (!$entries): ?>
      <div class="seo-empty">No URLs — every content type is switched off or excluded.</div>
    <?php else: ?>
    <div class="table-wrap">
    <table>
      <thead><tr><th style="width:36px"></th><th>URL</th><th>Type</th><th>Last modified</th><th>Priority</th><th>Images</th></tr></thead>
      <tbody>
      <?php foreach ($entries as $e): ?>
        <tr>
          <td><input type="checkbox" name="exclude_path[]" value="<?= h($e['path']) ?>" title="Exclude from sitemap" style="width:16px;height:16px;accent-color:var(--danger)"></td>
          <td><a class="row-link seo-path" href="<?= h($e['path']) ?>" target="_blank"><?= h($e['path']) ?></a></td>
          <td><?= h($groups[$e['type']] ?? $e['type']) ?></td>
          <td><?= $e['lastmod'] && strtotime($e['lastmod']) ? h(date('M j, Y', strtotime($e['lastmod']))) : '<span class="t-sub">—</span>' ?></td>
          <td><?= h($e['priority']) ?> · <?= h($e['changefreq']) ?></td>
          <td><?= count($e['images']) ?: '<span class="t-sub">—</span>' ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php endif; ?>
    <?php if ($excluded): ?>
      <p class="t-sub" style="margin-top:1rem">Currently excluded: <span class="seo-path"><?= h(implode(', ', $excluded)) ?></span></p>
    <?php endif; ?>
  </div>

  <div class="seo-savebar">
    <span class="seo-savebar-note">Drafts and No Index pages are never listed.</span>
    <button type="submit" class="btn btn-primary">Save Sitemap</button>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
