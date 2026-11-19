<?php
/**
 * Admin → SEO → Schema: structured data (JSON-LD) printed in the <head>.
 *   - Organization / Local Business, WebSite and BreadcrumbList, from settings
 *   - Article type for blog posts
 *   - Custom JSON-LD blocks (FAQ, Service, Product…) shown on chosen URLs
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$ready = seo_table_ready($pdo, 'seo_schemas');
$s = seo_schema_settings($pdo);
$error = '';
$customError = '';
$editing = null;

$action = $_POST['action'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    if ($action === 'global') {
        $new = seo_schema_defaults();
        foreach ($new as $k => $default) {
            $new[$k] = is_int($default) ? (isset($_POST[$k]) ? 1 : 0) : (is_string($_POST[$k] ?? null) ? trim($_POST[$k]) : '');
        }
        if (!array_key_exists($new['org_type'], seo_org_types())) {
            $new['org_type'] = 'Organization';
        }
        if (!in_array($new['article_type'], ['Article', 'BlogPosting', 'NewsArticle'], true)) {
            $new['article_type'] = 'Article';
        }
        if ($new['org_enabled'] && $new['org_name'] === '') {
            $error = 'Enter the organization name to turn on Organization schema.';
        } elseif ($new['org_email'] !== '' && !filter_var($new['org_email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'That email address doesn\'t look right.';
        } elseif ($new['org_url'] !== '' && !filter_var($new['org_url'], FILTER_VALIDATE_URL)) {
            $error = 'The website URL must start with https://';
        } else {
            foreach (preg_split('/\R/', $new['same_as']) ?: [] as $line) {
                if (trim($line) !== '' && !filter_var(trim($line), FILTER_VALIDATE_URL)) {
                    $error = '"' . trim($line) . '" is not a full profile URL (https://…).';
                    break;
                }
            }
        }
        if ($error) {
            $s = $new;
        } else {
            seo_save_json_setting($pdo, 'seo_schema', $new);
            seo_flash('Schema settings saved — they print on every page now.');
            seo_back('seo-schema.php');
        }
    } elseif ($ready && $action === 'custom') {
        $cid = (int) ($_POST['id'] ?? 0);
        $name = mb_substr(trim((string) ($_POST['name'] ?? '')), 0, 190);
        $targets = [];
        foreach (preg_split('/[\r\n,]+/', (string) ($_POST['target'] ?? '')) ?: [] as $t) {
            $t = trim($t);
            if ($t === '') {
                continue;
            }
            $targets[] = $t === '*' ? '*' : (str_ends_with($t, '/*') ? rtrim(seo_normalize_path(substr($t, 0, -2)), '/') . '/*' : seo_normalize_path($t));
        }
        [$json, $jsonError] = seo_validate_schema_json((string) ($_POST['schema_json'] ?? ''));
        $enabled = isset($_POST['enabled']) ? 1 : 0;
        $editing = ['id' => $cid, 'name' => $name, 'target' => implode("\n", $targets), 'schema_json' => (string) ($_POST['schema_json'] ?? ''), 'enabled' => $enabled];

        if ($name === '') {
            $customError = 'Give the schema a name, e.g. "Home page FAQ".';
        } elseif (!$targets) {
            $customError = 'Choose where it prints: * for every page, a path like /about-us, or /blog/* for all blog posts.';
        } elseif ($jsonError !== '') {
            $customError = $jsonError;
        } else {
            if ($cid > 0) {
                $pdo->prepare('UPDATE seo_schemas SET name = ?, target = ?, schema_json = ?, enabled = ? WHERE id = ?')
                    ->execute([$name, implode("\n", $targets), $json, $enabled, $cid]);
            } else {
                $pdo->prepare('INSERT INTO seo_schemas (name, target, schema_json, enabled) VALUES (?, ?, ?, ?)')
                    ->execute([$name, implode("\n", $targets), $json, $enabled]);
            }
            seo_flash('Schema "' . $name . '" saved.');
            seo_back('seo-schema.php#custom');
        }
    } elseif ($ready && $action === 'toggle') {
        $pdo->prepare('UPDATE seo_schemas SET enabled = 1 - enabled WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
        seo_flash('Schema updated.');
        seo_back('seo-schema.php#custom');
    } elseif ($ready && $action === 'delete') {
        $pdo->prepare('DELETE FROM seo_schemas WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
        seo_flash('Schema deleted.');
        seo_back('seo-schema.php#custom');
    }
}

$customs = $ready ? $pdo->query('SELECT * FROM seo_schemas ORDER BY id DESC')->fetchAll() : [];
if (!$editing && $ready && isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM seo_schemas WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
}
$editing = $editing ?? ['id' => 0, 'name' => '', 'target' => '', 'schema_json' => '', 'enabled' => 1];

// Preview: what a page gets from the global settings.
$previewPath = seo_normalize_path(is_string($_GET['preview'] ?? null) ? $_GET['preview'] : '/') ?: '/';
$previewSchemas = seo_schemas_for_uri($pdo, $previewPath, 'Example Page Title | Drawlead');

$pageTitle = 'Schema';
$pageSub = 'Structured data that helps Google show rich results — logo, breadcrumbs, FAQs and more.';
$activeNav = 'seo-schema';
include __DIR__ . '/includes/header.php';
?>

<?= seo_flash_html() ?>
<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>

<form method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="global">

  <div class="card">
    <div class="card-title">Organization</div>
    <div class="card-desc">Tells Google who runs the site — name, logo, contact details and social profiles. Printed on every page.</div>
    <label class="seo-switch-row">
      <input type="checkbox" name="org_enabled" <?= $s['org_enabled'] ? 'checked' : '' ?>>
      <span><b>Add Organization schema to every page</b><span>Recommended. Helps Google show your logo and a knowledge panel.</span></span>
    </label>
    <div class="seo-two">
      <div class="field">
        <label for="org_type">Type</label>
        <select id="org_type" name="org_type">
          <?php foreach (seo_org_types() as $k => $l): ?><option value="<?= h($k) ?>" <?= $s['org_type'] === $k ? 'selected' : '' ?>><?= h($l) ?></option><?php endforeach; ?>
        </select>
        <div class="field-hint">Pick Local Business if customers visit an office, so the address can show in Maps results.</div>
      </div>
      <div class="field">
        <label for="org_name">Name</label>
        <input type="text" id="org_name" name="org_name" maxlength="190" value="<?= h($s['org_name']) ?>">
      </div>
      <div class="field">
        <label for="org_url">Website URL</label>
        <input type="url" id="org_url" name="org_url" maxlength="255" value="<?= h($s['org_url']) ?>" placeholder="<?= h(site_base_url()) ?>/ (default)">
      </div>
      <div class="field">
        <label for="org_logo">Logo URL</label>
        <input type="text" id="org_logo" name="org_logo" maxlength="255" value="<?= h($s['org_logo']) ?>" placeholder="/uploads/media/drawlead-logo.png">
        <div class="field-hint">Square or wide PNG/JPG, at least 112 × 112 px. Copy it from <a class="row-link" href="media.php">Media</a>.</div>
      </div>
    </div>
    <div class="field">
      <label for="org_description">Short Description</label>
      <textarea id="org_description" name="org_description" rows="2" maxlength="400"><?= h($s['org_description']) ?></textarea>
    </div>
    <div class="seo-two">
      <div class="field">
        <label for="org_email">Email</label>
        <input type="email" id="org_email" name="org_email" maxlength="190" value="<?= h($s['org_email']) ?>">
      </div>
      <div class="field">
        <label for="org_phone">Phone</label>
        <input type="text" id="org_phone" name="org_phone" maxlength="40" value="<?= h($s['org_phone']) ?>" placeholder="+91 98765 43210">
      </div>
    </div>
    <div class="field"><label for="street">Street Address</label><input type="text" id="street" name="street" maxlength="190" value="<?= h($s['street']) ?>"></div>
    <div class="seo-grid-3">
      <div class="field"><label for="city">City</label><input type="text" id="city" name="city" maxlength="100" value="<?= h($s['city']) ?>"></div>
      <div class="field"><label for="region">State / Region</label><input type="text" id="region" name="region" maxlength="100" value="<?= h($s['region']) ?>"></div>
      <div class="field"><label for="postal_code">Postal Code</label><input type="text" id="postal_code" name="postal_code" maxlength="20" value="<?= h($s['postal_code']) ?>"></div>
    </div>
    <div class="field"><label for="country">Country</label><input type="text" id="country" name="country" maxlength="60" value="<?= h($s['country']) ?>" placeholder="IN"></div>
    <div class="field">
      <label for="same_as">Social Profiles</label>
      <textarea id="same_as" name="same_as" rows="4" placeholder="https://www.linkedin.com/company/drawlead&#10;https://www.instagram.com/drawlead&#10;https://www.facebook.com/drawlead"><?= h($s['same_as']) ?></textarea>
      <div class="field-hint">One full URL per line — LinkedIn, Instagram, Facebook, X, YouTube, Google Business Profile.</div>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Website, breadcrumbs &amp; articles</div>
    <label class="seo-switch-row">
      <input type="checkbox" name="website_enabled" <?= $s['website_enabled'] ? 'checked' : '' ?>>
      <span><b>WebSite schema</b><span>Gives Google the site's name (shown above results on mobile). Uses the site name from Global Settings.</span></span>
    </label>
    <label class="seo-switch-row">
      <input type="checkbox" name="breadcrumbs_enabled" <?= $s['breadcrumbs_enabled'] ? 'checked' : '' ?>>
      <span><b>Breadcrumb schema</b><span>Home › Blog › Post title, built from each page's URL. Google can show it instead of the raw URL.</span></span>
    </label>
    <div class="field" style="max-width:360px;margin-top:.6rem">
      <label for="article_type">Blog post type</label>
      <select id="article_type" name="article_type">
        <?php foreach (['Article' => 'Article', 'BlogPosting' => 'Blog Posting', 'NewsArticle' => 'News Article'] as $k => $l): ?>
          <option value="<?= h($k) ?>" <?= $s['article_type'] === $k ? 'selected' : '' ?>><?= h($l) ?></option>
        <?php endforeach; ?>
      </select>
      <div class="field-hint">Every blog post already prints Article schema with its headline, author, dates and image.</div>
    </div>
  </div>

  <div class="seo-savebar">
    <span class="seo-savebar-note">Saved settings print on the live site straight away.</span>
    <button type="submit" class="btn btn-primary">Save Schema Settings</button>
  </div>
</form>

<div class="card" style="margin-top:1.25rem">
  <div class="card-title">Preview</div>
  <div class="card-desc">The global schema blocks (plus any custom ones) a page receives. Each page also keeps its own WebPage / Article schema.</div>
  <form class="seo-filters" method="get">
    <input type="text" name="preview" value="<?= h($previewPath) ?>" placeholder="/about-us">
    <button class="btn btn-ghost btn-sm" type="submit">Preview this URL</button>
    <a class="btn btn-ghost btn-sm" href="https://search.google.com/test/rich-results?url=<?= rawurlencode(site_base_url() . $previewPath) ?>" target="_blank" rel="noopener">Test in Google ↗</a>
  </form>
  <?php if ($previewSchemas): ?>
    <pre class="seo-code"><?= h(json_encode(count($previewSchemas) === 1 ? $previewSchemas[0] : $previewSchemas, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) ?></pre>
  <?php else: ?>
    <div class="seo-empty">Nothing yet — turn on Organization, WebSite or Breadcrumb schema above, or add a custom block below.</div>
  <?php endif; ?>
</div>

<div class="card" id="custom" style="margin-top:1.25rem">
  <div class="card-title">Custom schema</div>
  <div class="card-desc">Your own JSON-LD for rich results — FAQ, Service, Product, Event, HowTo, Review, Video — on the pages you choose.</div>
  <?php if (!$ready): ?>
    <?= seo_migration_notice() ?>
  <?php elseif ($customs): ?>
  <div class="table-wrap">
  <table>
    <thead><tr><th>Name</th><th>Type</th><th>Shows on</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($customs as $c):
        $d = json_decode((string) $c['schema_json'], true);
        $ctype = is_array($d) ? ($d['@type'] ?? (isset($d['@graph']) ? '@graph' : 'List')) : 'Invalid';
    ?>
      <tr>
        <td class="t-name"><a class="row-link" href="seo-schema.php?edit=<?= (int) $c['id'] ?>#schema-form"><?= h($c['name']) ?></a></td>
        <td><?= h(is_array($ctype) ? implode(', ', $ctype) : $ctype) ?></td>
        <td><span class="seo-path"><?= nl2br(h((string) $c['target'])) ?></span></td>
        <td><span class="badge <?= $c['enabled'] ? 'badge-published' : 'badge-muted' ?>"><?= $c['enabled'] ? 'On' : 'Off' ?></span></td>
        <td>
          <div class="seo-actions">
            <a href="seo-schema.php?edit=<?= (int) $c['id'] ?>#schema-form">Edit</a>
            <form class="seo-inline-form" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int) $c['id'] ?>"><button type="submit"><?= $c['enabled'] ? 'Turn off' : 'Turn on' ?></button></form>
            <form class="seo-inline-form" method="post" onsubmit="return confirm('Delete this schema block?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $c['id'] ?>"><button type="submit" class="is-danger">Delete</button></form>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php else: ?>
    <div class="seo-empty">No custom schema yet. Add one below.</div>
  <?php endif; ?>
</div>

<?php if ($ready): ?>
<form class="card" id="schema-form" method="post" style="margin-top:1.25rem">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="custom">
  <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
  <div class="card-title"><?= $editing['id'] ? 'Edit schema' : 'Add schema' ?></div>
  <?php if ($customError): ?><div class="alert alert-error" style="margin-top:.8rem"><?= h($customError) ?></div><?php endif; ?>
  <div class="seo-two" style="margin-top:1rem">
    <div class="field">
      <label for="cs_name">Name</label>
      <input type="text" id="cs_name" name="name" maxlength="190" value="<?= h($editing['name']) ?>" placeholder="Home page FAQ">
    </div>
    <div class="field">
      <label for="cs_target">Shows on</label>
      <textarea id="cs_target" name="target" rows="2" placeholder="/ (home)&#10;/blog/*"><?= h((string) $editing['target']) ?></textarea>
      <div class="field-hint">One per line: <code>*</code> every page · <code>/about-us</code> one page · <code>/blog/*</code> the blog and every post.</div>
    </div>
  </div>
  <div class="field">
    <label for="cs_json">JSON-LD</label>
    <div class="seo-tpl-btns">
      <span class="t-sub" style="align-self:center;margin-right:.3rem">Start from:</span>
      <?php foreach (array_keys(seo_schema_templates()) as $tpl): ?><button type="button" data-tpl="<?= h($tpl) ?>"><?= h($tpl) ?></button><?php endforeach; ?>
    </div>
    <textarea id="cs_json" name="schema_json" rows="16" class="seo-code" spellcheck="false" placeholder='{"@context": "https://schema.org", "@type": "FAQPage", ...}'><?= h((string) $editing['schema_json']) ?></textarea>
    <div class="field-hint" id="cs_json_status">Paste JSON-LD, with or without the &lt;script&gt; tag. It's checked when you save.</div>
  </div>
  <label class="checkbox-row" style="max-width:220px;margin-bottom:1.1rem">
    <input type="checkbox" name="enabled" <?= $editing['enabled'] ? 'checked' : '' ?>> Turned on
  </label>
  <div class="seo-actions">
    <button type="submit" class="btn btn-primary"><?= $editing['id'] ? 'Save Changes' : 'Add Schema' ?></button>
    <?php if ($editing['id']): ?><a href="seo-schema.php#custom">Cancel</a><?php endif; ?>
  </div>
</form>

<script>
(function () {
  var templates = <?= json_encode(seo_schema_templates(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
  var box = document.getElementById('cs_json');
  var status = document.getElementById('cs_json_status');
  var name = document.getElementById('cs_name');
  document.querySelectorAll('[data-tpl]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (box.value.trim() && !confirm('Replace what is in the box with the ' + btn.dataset.tpl + ' template?')) return;
      box.value = JSON.stringify(templates[btn.dataset.tpl], null, 2);
      if (!name.value.trim()) name.value = btn.dataset.tpl;
      check();
      box.focus();
    });
  });
  function check() {
    var v = box.value.trim().replace(/^<script[^>]*>|<\/script>$/gi, '').trim();
    if (!v) { status.textContent = 'Paste JSON-LD, with or without the <script> tag. It\'s checked when you save.'; status.style.color = ''; return; }
    try { JSON.parse(v); status.textContent = '✓ Valid JSON'; status.style.color = '#0f7a4c'; }
    catch (e) { status.textContent = '✕ ' + e.message; status.style.color = '#b3123a'; }
  }
  box.addEventListener('input', check);
  check();
})();
</script>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
