<?php
/**
 * Admin → SEO → Robots: edit /robots.txt (served live by index.php), switch
 * the whole site to "discourage search engines", and test whether a URL may
 * be crawled by a given bot.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$r = seo_robots_settings($pdo);
$error = '';

$bots = [
    'Googlebot' => 'Googlebot (Google Search)',
    'Googlebot-Image' => 'Googlebot-Image',
    'Bingbot' => 'Bingbot (Bing, ChatGPT search)',
    'DuckDuckBot' => 'DuckDuckBot',
    'YandexBot' => 'YandexBot',
    'GPTBot' => 'GPTBot (OpenAI training)',
    'ClaudeBot' => 'ClaudeBot (Anthropic)',
    'CCBot' => 'CCBot (Common Crawl)',
    'AhrefsBot' => 'AhrefsBot',
    'SemrushBot' => 'SemrushBot',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? 'save';
    if ($action === 'reset') {
        seo_save_json_setting($pdo, 'seo_robots', ['content' => '', 'block_all' => $r['block_all']]);
        seo_flash('robots.txt reset to the default.');
        seo_back('seo-robots.php');
    }
    $content = str_replace("\r\n", "\n", (string) ($_POST['content'] ?? ''));
    $content = mb_substr($content, 0, 20000);
    $blockAll = isset($_POST['block_all']) ? 1 : 0;

    // Flag lines robots.txt parsers won't understand.
    $known = ['user-agent', 'allow', 'disallow', 'sitemap', 'crawl-delay', 'host', 'clean-param'];
    foreach (explode("\n", $content) as $n => $line) {
        $line = trim((string) preg_replace('/#.*$/', '', $line));
        if ($line === '') {
            continue;
        }
        $field = strtolower(trim((string) strstr($line, ':', true)));
        if (!str_contains($line, ':') || !in_array($field, $known, true)) {
            $error = 'Line ' . ($n + 1) . ' ("' . mb_strimwidth($line, 0, 60, '…') . '") isn\'t a robots.txt rule. Use User-agent:, Allow:, Disallow: or Sitemap:.';
            break;
        }
        if (in_array($field, ['allow', 'disallow'], true)) {
            $val = trim(substr($line, strpos($line, ':') + 1));
            if ($val !== '' && $val[0] !== '/' && $val[0] !== '*') {
                $error = 'Line ' . ($n + 1) . ': paths in Allow/Disallow must start with "/".';
                break;
            }
        }
    }
    if ($error) {
        $r = ['content' => $content, 'block_all' => $blockAll];
    } else {
        // Saving the untouched default keeps following future default changes.
        $isDefault = trim($content) === trim(seo_robots_default_content());
        seo_save_json_setting($pdo, 'seo_robots', ['content' => $isDefault ? '' : $content, 'block_all' => $blockAll]);
        seo_flash($blockAll ? 'Saved. Search engines are now told to stay away from the whole site.' : 'robots.txt saved — it is live now.');
        seo_back('seo-robots.php');
    }
}

$editorContent = trim($r['content']) !== '' ? $r['content'] : seo_robots_default_content();
$served = seo_robots_txt($pdo);

// Tester
$testPath = is_string($_GET['test_path'] ?? null) ? trim($_GET['test_path']) : '';
$testBot = isset($bots[$_GET['test_bot'] ?? '']) ? $_GET['test_bot'] : 'Googlebot';
$testResult = null;
if ($testPath !== '') {
    if (preg_match('#^https?://#i', $testPath)) {
        $testPath = (string) (parse_url($testPath, PHP_URL_PATH) ?: '/') . (parse_url($testPath, PHP_URL_QUERY) ? '?' . parse_url($testPath, PHP_URL_QUERY) : '');
    }
    $testPath = '/' . ltrim($testPath, '/');
    $testResult = seo_robots_check($served, $testBot, $testPath);
}

$pageTitle = 'Robots';
$pageSub = 'robots.txt tells search engines which parts of the site they may crawl.';
$activeNav = 'seo-robots';
include __DIR__ . '/includes/header.php';
?>

<?= seo_flash_html() ?>
<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($r['block_all']): ?>
  <div class="alert alert-error">Search engines are blocked: robots.txt disallows everything and every page says noindex. Turn this off before the site goes live.</div>
<?php endif; ?>

<div class="seo-two">
  <form class="card" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <div class="card-title">Edit robots.txt</div>
    <div class="card-desc"><code>{sitemap}</code> becomes <?= h(site_base_url()) ?>/sitemap.xml, so the file stays right when the site moves to another domain.</div>

    <label class="seo-switch-row">
      <input type="checkbox" name="block_all" <?= $r['block_all'] ? 'checked' : '' ?>>
      <span><b>Discourage search engines from indexing this site</b><span>For staging or a site that isn't ready. Overrides the rules below with "Disallow: /" and adds noindex to every page.</span></span>
    </label>

    <div class="field">
      <label for="content">Rules</label>
      <textarea id="content" name="content" rows="14" class="seo-code" spellcheck="false"><?= h($editorContent) ?></textarea>
      <div class="field-hint">Keep <code>Disallow: /admin/</code>. Blocking a page here doesn't remove it from Google — use No Index in Page SEO for that.</div>
    </div>
    <div class="seo-tpl-btns">
      <span class="t-sub" style="align-self:center;margin-right:.3rem">Add:</span>
      <button type="button" data-add="User-agent: GPTBot&#10;Disallow: /">Block GPTBot</button>
      <button type="button" data-add="User-agent: CCBot&#10;Disallow: /">Block CCBot</button>
      <button type="button" data-add="User-agent: AhrefsBot&#10;Crawl-delay: 10">Slow AhrefsBot</button>
      <button type="button" data-add="User-agent: *&#10;Disallow: /*?*">Block URLs with ?</button>
      <button type="button" data-add="Sitemap: {sitemap}">Sitemap line</button>
    </div>
    <div class="seo-actions" style="margin-top:.6rem">
      <button type="submit" class="btn btn-primary">Save robots.txt</button>
      <button type="submit" class="btn btn-ghost" name="action" value="reset" formnovalidate onclick="return confirm('Replace the rules with the default robots.txt?')">Reset to default</button>
    </div>
  </form>

  <div>
    <div class="card">
      <div class="card-title">Live file</div>
      <div class="card-desc">What crawlers get at <a class="row-link" href="/robots.txt" target="_blank">/robots.txt ↗</a></div>
      <pre class="seo-code"><?= h($served) ?></pre>
    </div>

    <form class="card" method="get" id="tester">
      <div class="card-title">Test a URL</div>
      <div class="card-desc">Checks the live file the way Google reads it: the most specific rule wins.</div>
      <div class="field">
        <label for="test_path">URL or path</label>
        <input type="text" id="test_path" name="test_path" value="<?= h($testPath) ?>" placeholder="/blog/my-post or <?= h(site_base_url()) ?>/about-us">
      </div>
      <div class="field">
        <label for="test_bot">Crawler</label>
        <select id="test_bot" name="test_bot">
          <?php foreach ($bots as $k => $l): ?><option value="<?= h($k) ?>" <?= $testBot === $k ? 'selected' : '' ?>><?= h($l) ?></option><?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-ghost">Test</button>
      <?php if ($testResult): ?>
        <div class="seo-result <?= $testResult['allowed'] ? 'is-pass' : 'is-fail' ?>">
          <?= $testResult['allowed'] ? '✓ Allowed' : '✕ Blocked' ?> — <?= h($testBot) ?> <?= $testResult['allowed'] ? 'may' : 'may not' ?> crawl <span class="seo-path"><?= h($testPath) ?></span>.
          <div class="t-sub" style="margin-top:4px;color:inherit;opacity:.8">
            <?= $testResult['rule'] !== '' ? 'Matched rule: ' . h($testResult['rule']) : 'No rule matches, so it is allowed by default.' ?>
            <?= $testResult['group'] !== '' ? ' · Group: User-agent: ' . h($testResult['group']) : '' ?>
          </div>
        </div>
      <?php endif; ?>
    </form>
  </div>
</div>

<script>
(function () {
  var box = document.getElementById('content');
  document.querySelectorAll('[data-add]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var add = btn.getAttribute('data-add');
      if (box.value.indexOf(add) !== -1) return;
      box.value = box.value.replace(/\s*$/, '') + '\n\n' + add + '\n';
      box.focus();
    });
  });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
