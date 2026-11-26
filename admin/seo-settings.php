<?php
/**
 * Admin → SEO → Global Settings: site-wide defaults every page inherits —
 * site name and title suffix, fallback meta description and share image,
 * social handles and search engine verification codes.
 * Applied on the live site by seo_apply_globals() / seo_extra_head_tags().
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/media-lib.php';
require_admin();

$g = seo_global($pdo);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $new = seo_global_defaults();
    foreach ($new as $k => $default) {
        if (is_int($default)) {
            $new[$k] = isset($_POST[$k]) ? 1 : 0;
        } else {
            $new[$k] = is_string($_POST[$k] ?? null) ? trim($_POST[$k]) : '';
        }
    }
    $new['site_name'] = mb_substr($new['site_name'], 0, 80);
    $new['separator'] = mb_substr($new['separator'], 0, 3);
    $new['default_description'] = mb_substr($new['default_description'], 0, 320);
    $new['twitter_handle'] = ltrim($new['twitter_handle'], '@');
    // People paste the whole <meta> tag; keep only its content="…".
    foreach (['bing_verification', 'yandex_verification', 'pinterest_verification', 'baidu_verification'] as $k) {
        if (preg_match('/content=["\']([^"\']+)["\']/i', $new[$k], $m)) {
            $new[$k] = $m[1];
        }
        $new[$k] = mb_substr(strip_tags($new[$k]), 0, 190);
    }

    if ($new['site_name'] === '' && $new['append_site_name']) {
        $error = 'Enter a site name to add it to page titles.';
    } elseif ($new['twitter_handle'] !== '' && !preg_match('/^[A-Za-z0-9_]{1,15}$/', $new['twitter_handle'])) {
        $error = 'An X / Twitter handle is up to 15 letters, numbers or underscores.';
    } elseif ($new['facebook_app_id'] !== '' && !ctype_digit($new['facebook_app_id'])) {
        $error = 'A Facebook App ID is numbers only.';
    }

    // Favicon: keep the current one unless the admin removes or replaces it.
    // (The loop above blanked it since it isn't a plain text field.)
    $new['favicon'] = is_string($g['favicon'] ?? null) ? $g['favicon'] : '';
    if (!$error) {
        if (!empty($_POST['favicon_remove'])) {
            $new['favicon'] = '';
        }
        if (($_FILES['favicon_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $up = media_save_upload(
                (string) ($_FILES['favicon_file']['tmp_name'] ?? ''),
                (string) ($_FILES['favicon_file']['name'] ?? 'favicon'),
                (int) ($_FILES['favicon_file']['size'] ?? 0),
                (int) ($_FILES['favicon_file']['error'] ?? UPLOAD_ERR_NO_FILE)
            );
            if (isset($up['error'])) {
                $error = $up['error'];
            } elseif (isset($up['file'])) {
                $new['favicon'] = '/uploads/media/' . $up['file'];
            }
        }
    }

    if ($error) {
        $g = $new;
    } else {
        seo_save_json_setting($pdo, 'seo_global', $new);
        seo_flash('Global SEO settings saved — every page uses them now.');
        seo_back('seo-settings.php');
    }
}

$sampleTitle = 'Custom ERP Software for Growing Businesses';
$sep = trim($g['separator']) !== '' ? trim($g['separator']) : '|';

$pageTitle = 'Global SEO Settings';
$pageSub = 'Defaults every page inherits when it doesn\'t set its own.';
$activeNav = 'seo-settings';
include __DIR__ . '/includes/header.php';
?>

<?= seo_flash_html() ?>
<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="card">
    <div class="card-title">Site name &amp; titles</div>
    <div class="card-desc">Used for og:site_name on every page, the WebSite schema, and (optionally) added to the end of every title.</div>
    <div class="seo-two">
      <div class="field">
        <label for="site_name">Site Name</label>
        <input type="text" id="site_name" name="site_name" maxlength="80" value="<?= h($g['site_name']) ?>" data-preview-name>
      </div>
      <div class="field">
        <label for="separator">Title Separator</label>
        <select id="separator" name="separator" data-preview-sep>
          <?php foreach (['|', '-', '–', '—', '·', '•', '»', ':'] as $s): ?>
            <option value="<?= h($s) ?>" <?= $sep === $s ? 'selected' : '' ?>><?= h($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <label class="seo-switch-row">
      <input type="checkbox" name="append_site_name" <?= $g['append_site_name'] ? 'checked' : '' ?> data-preview-append>
      <span><b>Add the site name to page titles that don't already include it</b>
        <span>A page titled "<?= h($sampleTitle) ?>" shows in Google as
          "<span data-preview-out><?= h($sampleTitle . ($g['append_site_name'] ? ' ' . $sep . ' ' . $g['site_name'] : '')) ?></span>". Titles that already mention the site name are left alone.</span></span>
    </label>
  </div>

  <div class="card">
    <div class="card-title">Fallbacks</div>
    <div class="card-desc">Used only by pages that leave these fields empty.</div>
    <div class="field">
      <label for="default_description">Default Meta Description</label>
      <textarea id="default_description" name="default_description" rows="3" maxlength="320" data-count="160"><?= h($g['default_description']) ?></textarea>
      <div class="field-hint"><span data-count-out></span> · 120–160 characters works best. Without this, pages with no description let Google pick random text.</div>
    </div>
    <div class="field">
      <label for="default_og_image">Default Social Share Image</label>
      <input type="text" id="default_og_image" name="default_og_image" maxlength="255" value="<?= h($g['default_og_image']) ?>" placeholder="/uploads/media/drawlead-share.jpg or https://…">
      <div class="field-hint">Shown when a page is shared on LinkedIn, WhatsApp, Facebook or X and has no image of its own. 1200 × 630 px is ideal. Copy an image's URL from <a href="media.php" class="row-link">Media</a>.</div>
      <?php if (trim($g['default_og_image']) !== ''): ?>
        <div class="upload-preview"><img src="<?= h($g['default_og_image']) ?>" alt="Default share image" style="max-width:320px"></div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Social profiles</div>
    <div class="card-desc">Credits your accounts when pages are shared.</div>
    <div class="seo-two">
      <div class="field">
        <label for="twitter_handle">X / Twitter Handle</label>
        <input type="text" id="twitter_handle" name="twitter_handle" maxlength="16" value="<?= h($g['twitter_handle']) ?>" placeholder="drawlead">
        <div class="field-hint">Adds twitter:site to every page.</div>
      </div>
      <div class="field">
        <label for="facebook_app_id">Facebook App ID</label>
        <input type="text" id="facebook_app_id" name="facebook_app_id" maxlength="30" value="<?= h($g['facebook_app_id']) ?>" placeholder="Optional">
        <div class="field-hint">Only needed for Facebook Insights on shared links.</div>
      </div>
    </div>
    <div class="field-hint">Profile links for Google's knowledge panel go in <a href="seo-schema.php" class="row-link">Schema → Organization</a>.</div>
  </div>

  <div class="card">
    <div class="card-title">Search engine verification</div>
    <div class="card-desc">Paste the code (or the whole &lt;meta&gt; tag) each tool gives you. Google Search Console lives in <a href="analytics.php" class="row-link">Analytics &amp; Search</a>.</div>
    <div class="seo-two">
      <div class="field">
        <label for="bing_verification">Bing Webmaster Tools</label>
        <input type="text" id="bing_verification" name="bing_verification" value="<?= h($g['bing_verification']) ?>" placeholder="msvalidate.01 code">
      </div>
      <div class="field">
        <label for="yandex_verification">Yandex Webmaster</label>
        <input type="text" id="yandex_verification" name="yandex_verification" value="<?= h($g['yandex_verification']) ?>">
      </div>
      <div class="field">
        <label for="pinterest_verification">Pinterest</label>
        <input type="text" id="pinterest_verification" name="pinterest_verification" value="<?= h($g['pinterest_verification']) ?>" placeholder="p:domain_verify code">
      </div>
      <div class="field">
        <label for="baidu_verification">Baidu</label>
        <input type="text" id="baidu_verification" name="baidu_verification" value="<?= h($g['baidu_verification']) ?>">
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Favicon</div>
    <div class="card-desc">The small icon shown in the browser tab, bookmarks and history. Upload a square <b>PNG</b> — <b>512 × 512 px</b> recommended (32 × 32 px minimum). It applies to every page on the live site.</div>
    <?php if (trim($g['favicon'] ?? '') !== ''): ?>
      <div class="field">
        <label>Current favicon</label>
        <div class="upload-preview">
          <img src="<?= h($g['favicon']) ?>" alt="Current favicon" style="width:48px;height:48px;object-fit:contain;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:6px">
        </div>
      </div>
      <label class="seo-switch-row">
        <input type="checkbox" name="favicon_remove">
        <span><b>Remove the current favicon</b>
          <span>The browser falls back to its own default tab icon.</span></span>
      </label>
    <?php endif; ?>
    <div class="field">
      <label for="favicon_file"><?= trim($g['favicon'] ?? '') !== '' ? 'Replace favicon' : 'Upload favicon' ?></label>
      <input type="file" id="favicon_file" name="favicon_file" accept="image/png,image/webp">
      <div class="field-hint">PNG or WEBP, up to 10&nbsp;MB. A new upload replaces the current favicon when you save.</div>
    </div>
  </div>

  <div class="seo-savebar">
    <span class="seo-savebar-note">Changes apply to every page on the live site as soon as you save.</span>
    <button type="submit" class="btn btn-primary">Save Settings</button>
  </div>
</form>

<script>
(function () {
  var name = document.querySelector('[data-preview-name]');
  var sep = document.querySelector('[data-preview-sep]');
  var append = document.querySelector('[data-preview-append]');
  var out = document.querySelector('[data-preview-out]');
  var sample = <?= json_encode($sampleTitle) ?>;
  function preview() {
    out.textContent = sample + (append.checked && name.value.trim() ? ' ' + sep.value + ' ' + name.value.trim() : '');
  }
  [name, sep, append].forEach(function (el) { el.addEventListener('input', preview); el.addEventListener('change', preview); });

  document.querySelectorAll('[data-count]').forEach(function (el) {
    var target = el.closest('.field').querySelector('[data-count-out]');
    function count() { target.textContent = el.value.length + ' characters'; }
    el.addEventListener('input', count);
    count();
  });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
