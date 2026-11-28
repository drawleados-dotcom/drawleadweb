<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/analytics-dash.php';
require_admin();

$error = '';
$success = '';

$gaId = get_setting($pdo, 'ga_measurement_id', '');
$gscTag = get_setting($pdo, 'gsc_verification_tag', '');
$clarityId = get_setting($pdo, 'clarity_project_id', '');
// dashboard access (the "View analytics" popups)
$gaProp = get_setting($pdo, 'ga_property_id', '');
$gscSite = get_setting($pdo, 'gsc_site_url', '');
$clarityToken = get_setting($pdo, 'clarity_api_token', '');
$sa = dash_service_account($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $newGaId = trim($_POST['ga_measurement_id'] ?? '');
    $newGscTag = trim($_POST['gsc_verification_tag'] ?? '');
    $newClarityId = trim($_POST['clarity_project_id'] ?? '');
    $newGaProp = preg_replace('#^properties/#', '', trim($_POST['ga_property_id'] ?? ''));
    $newGscSite = trim($_POST['gsc_site_url'] ?? '');
    $tokenIn = trim($_POST['clarity_api_token'] ?? '');
    $newClarityToken = !empty($_POST['remove_clarity_token']) ? '' : ($tokenIn !== '' ? $tokenIn : $clarityToken);
    $saIn = trim($_POST['google_service_account'] ?? '');
    $newSa = !empty($_POST['remove_service_account']) ? null : $sa;

    if ($newGaId !== '' && !preg_match('/^(G-[A-Za-z0-9]+|UA-\d+-\d+)$/', $newGaId)) {
        $error = 'That doesn\'t look like a valid GA4 Measurement ID (should look like G-XXXXXXXXXX).';
    }

    if (!$error && $newGaProp !== '' && !preg_match('/^\d{5,14}$/', $newGaProp)) {
        $error = 'The GA4 Property ID is a number (like 412345678). Find it in Google Analytics → Admin → Property details. It is not the G-… Measurement ID.';
    }

    if (!$error && $newClarityId !== '' && !preg_match('/^[a-z0-9]{6,20}$/i', $newClarityId)) {
        $error = 'That doesn\'t look like a valid Clarity Project ID — paste just the short code from your install URL (clarity.ms/tag/XXXXXXXXXX).';
    }

    if (!$error && $tokenIn !== '' && !preg_match('/^[A-Za-z0-9._\-]{20,}$/', $tokenIn)) {
        $error = 'That doesn\'t look like a Clarity API token. Copy the whole token from Clarity → Settings → Data Export.';
    }

    if (!$error && $newGscTag !== '') {
        // Only allow exactly one <meta name="google-site-verification" content="..."> tag through.
        if (!preg_match('/^<meta\s+[^>]*name=["\']google-site-verification["\'][^>]*>$/i', $newGscTag)) {
            $error = 'Paste the full HTML tag Google gave you — it should start with <meta name="google-site-verification" ...>';
        } else {
            $newGscTag = strip_tags($newGscTag, '<meta>');
            $newGscTag = preg_replace('/\son\w+\s*=\s*"[^"]*"/i', '', $newGscTag);
        }
    }

    if (!$error && $newGscSite !== '') {
        if (preg_match('#^https?://[^\s/]+(/\S*)?$#i', $newGscSite)) {
            $newGscSite = rtrim($newGscSite, '/') . '/';
        } elseif (!preg_match('/^sc-domain:[a-z0-9.\-]+$/i', $newGscSite)) {
            $error = 'Enter the Search Console property as it appears there: https://drawlead.com/ for a URL-prefix property, or sc-domain:drawlead.com for a domain property. Or leave it blank to find it automatically.';
        }
    }

    if (!$error && $saIn !== '') {
        [$parsed, $saError] = dash_parse_service_account($saIn);
        if ($parsed === null) {
            $error = $saError;
        } else {
            $newSa = $parsed;
        }
    }

    if (!$error) {
        $saChanged = ($newSa['client_email'] ?? '') !== ($sa['client_email'] ?? '') || ($newSa['private_key'] ?? '') !== ($sa['private_key'] ?? '');
        set_setting($pdo, 'ga_measurement_id', $newGaId);
        set_setting($pdo, 'gsc_verification_tag', $newGscTag);
        set_setting($pdo, 'clarity_project_id', $newClarityId);
        set_setting($pdo, 'ga_property_id', $newGaProp);
        set_setting($pdo, 'gsc_site_url', $newGscSite);
        set_setting($pdo, 'clarity_api_token', $newClarityToken);
        if ($saChanged) {
            set_setting($pdo, 'google_service_account', $newSa ? json_encode($newSa, JSON_UNESCAPED_SLASHES) : '');
            set_setting($pdo, 'dash_google_token', '');
        }
        // saved dashboard numbers belong to the old settings
        if ($saChanged || $newGaProp !== $gaProp) {
            dash_cache_clear($pdo, 'ga_');
        }
        if ($saChanged || $newGscSite !== $gscSite) {
            dash_cache_clear($pdo, 'gsc_');
        }
        if ($newClarityToken !== $clarityToken || $newClarityId !== $clarityId) {
            dash_cache_clear($pdo, 'clarity_');
        }
        $gaId = $newGaId;
        $gscTag = $newGscTag;
        $clarityId = $newClarityId;
        $gaProp = $newGaProp;
        $gscSite = $newGscSite;
        $clarityToken = $newClarityToken;
        $sa = $newSa;
        $success = 'Saved. These now apply to every page on the live site.';
    }
}

$tokenHint = $clarityToken !== '' ? mb_substr($clarityToken, 0, 6) . '…' . mb_substr($clarityToken, -4) : '';
$today = site_stats_today($pdo);
$saEmail = $sa['client_email'] ?? '';

$pageTitle = 'Analytics & Search Console';
$pageSub = 'See who visits drawlead.com, and connect Google Analytics, Clarity and Search Console.';
$activeNav = 'analytics';
include __DIR__ . '/includes/header.php';
$viewBtn = fn (string $source, string $label, string $style = 'btn-ghost'): string =>
    '<button type="button" class="btn ' . $style . ' btn-sm ad-open" data-dash="' . h($source) . '" aria-haspopup="dialog" aria-label="' . h('View ' . $label . ' analytics') . '">'
    . '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>'
    . 'View analytics</button>';
?>
<link rel="stylesheet" href="<?= asset_url('/admin/assets/analytics-dash.css') ?>">

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<div class="card ad-site" id="ad-site">
  <div class="ad-card-head">
    <div>
      <div class="card-title">Website analytics</div>
      <div class="card-desc">
        drawlead.com counts its own visits: visitors, page views, top pages, traffic sources, countries and devices.
        Nothing to set up, no cookies, and no IP addresses stored. Your own visits while logged in here are not counted.
      </div>
    </div>
    <?= $viewBtn('site', 'website', 'btn-primary') ?>
  </div>
  <div class="ad-today">
    <span><b><?= number_format($today['visitors']) ?></b> visitor<?= $today['visitors'] === 1 ? '' : 's' ?> today</span>
    <span><b><?= number_format($today['views']) ?></b> page view<?= $today['views'] === 1 ? '' : 's' ?> today</span>
    <span class="t-sub"><?= $today['since'] !== '' ? 'Counting since ' . h(date('M j, Y', strtotime($today['since']))) : 'Counting starts with the next visit' ?></span>
  </div>
</div>

<form method="post" novalidate autocomplete="off">
  <?= csrf_field() ?>

  <div class="card" id="ad-ga">
    <div class="ad-card-head">
      <div>
        <div class="card-title">Google Analytics (GA4)</div>
        <div class="card-desc">
          In Google Analytics: Admin → Data Streams → your web stream → copy the <b>Measurement ID</b> (looks like <code>G-XXXXXXXXXX</code>).
          Paste it below and the tracking script is automatically added to every page.
        </div>
      </div>
      <?= $viewBtn('ga', 'Google Analytics') ?>
    </div>
    <div class="ad-fields">
      <div class="field">
        <label for="ga_measurement_id">Measurement ID</label>
        <input type="text" id="ga_measurement_id" name="ga_measurement_id" placeholder="G-XXXXXXXXXX" value="<?= h($gaId) ?>">
        <div class="field-hint">Turns on tracking.</div>
      </div>
      <div class="field">
        <label for="ga_property_id">Property ID</label>
        <input type="text" id="ga_property_id" name="ga_property_id" inputmode="numeric" placeholder="412345678" value="<?= h($gaProp) ?>">
        <div class="field-hint">For the dashboard. A number, from Admin → Property details. Also needs the service account below.</div>
      </div>
    </div>
  </div>

  <div class="card" id="ad-clarity">
    <div class="ad-card-head">
      <div>
        <div class="card-title">Microsoft Clarity</div>
        <div class="card-desc">
          In Clarity: Settings → Overview → copy your <b>Project ID</b> (the short code in the install URL, <code>clarity.ms/tag/<b>xxxxxxxxxx</b></code>).
          Paste it below and Clarity's heatmap &amp; session-recording script is automatically added to every page.
        </div>
      </div>
      <?= $viewBtn('clarity', 'Microsoft Clarity') ?>
    </div>
    <div class="ad-fields">
      <div class="field">
        <label for="clarity_project_id">Project ID</label>
        <input type="text" id="clarity_project_id" name="clarity_project_id" placeholder="abcd1234ef" value="<?= h($clarityId) ?>">
        <div class="field-hint">Turns on heatmaps and recordings.</div>
      </div>
      <div class="field">
        <label for="clarity_api_token">API token</label>
        <input type="password" id="clarity_api_token" name="clarity_api_token" spellcheck="false" autocomplete="new-password"
          placeholder="<?= h($clarityToken !== '' ? 'Saved (' . $tokenHint . ') — leave blank to keep it' : 'From Settings → Data Export') ?>">
        <div class="field-hint">For the dashboard. In Clarity: Settings → Data Export → Generate new API token.
          <?php if ($clarityToken !== ''): ?><label class="ad-check"><input type="checkbox" name="remove_clarity_token" value="1"> Remove the saved token</label><?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card" id="ad-gsc">
    <div class="ad-card-head">
      <div>
        <div class="card-title">Google Search Console</div>
        <div class="card-desc">
          In Search Console: Add Property → HTML tag method → copy the full <code>&lt;meta&gt;</code> tag it gives you and paste it below to verify ownership.
        </div>
      </div>
      <?= $viewBtn('gsc', 'Google Search Console') ?>
    </div>
    <div class="field">
      <label for="gsc_verification_tag">Verification Meta Tag</label>
      <textarea id="gsc_verification_tag" name="gsc_verification_tag" rows="2" placeholder='<meta name="google-site-verification" content="..." />'><?= h($gscTag) ?></textarea>
    </div>
    <div class="ad-fields">
      <div class="field">
        <label for="gsc_site_url">Property (optional)</label>
        <input type="text" id="gsc_site_url" name="gsc_site_url" placeholder="Found automatically, e.g. https://drawlead.com/" value="<?= h($gscSite) ?>">
        <div class="field-hint">For the dashboard. Leave blank and the property for this site is found for you. Also needs the service account below.</div>
      </div>
    </div>
  </div>

  <div class="card" id="ad-google-access">
    <div class="card-title">Google service account <span class="ad-optional">Optional</span></div>
    <div class="card-desc">
      Only needed to see Google's own numbers in the Google Analytics and Search Console popups. It can only read, and only the properties you add it to.
    </div>
    <?php if ($saEmail !== ''): ?>
    <div class="ad-sa">
      <span class="ad-pill"><span class="ad-dot"></span>Key saved</span>
      <code id="ad-sa-email"><?= h($saEmail) ?></code>
      <button type="button" class="btn btn-ghost btn-sm" data-copy="#ad-sa-email">Copy email</button>
    </div>
    <?php endif; ?>
    <div class="field">
      <label for="google_service_account"><?= $saEmail !== '' ? 'Replace the key (JSON)' : 'Service account key (JSON)' ?></label>
      <textarea id="google_service_account" name="google_service_account" rows="4" spellcheck="false"
        placeholder='<?= h($saEmail !== '' ? 'Saved. Leave blank to keep it, or paste a new key file to replace it.' : '{ "type": "service_account", "project_id": "…", "private_key": "…", "client_email": "…" }') ?>'></textarea>
      <?php if ($saEmail !== ''): ?><label class="ad-check"><input type="checkbox" name="remove_service_account" value="1"> Remove the saved key</label><?php endif; ?>
    </div>
    <details class="ad-steps">
      <summary>How to set this up (about 5 minutes)</summary>
      <ol>
        <li>Open <a href="https://console.cloud.google.com/" target="_blank" rel="noopener">Google Cloud Console</a> and create a project, or pick one you already have.</li>
        <li>In <b>APIs &amp; Services → Library</b>, enable <b>Google Analytics Data API</b> and <b>Google Search Console API</b>.</li>
        <li>In <b>IAM &amp; Admin → Service accounts</b>, click <b>Create service account</b>. A name is enough; skip the roles.</li>
        <li>Open the new service account → <b>Keys → Add key → Create new key → JSON</b>. A file downloads. Open it and paste all of it in the box above, then click Save Connections.</li>
        <li>In <b>Google Analytics → Admin → Property access management</b>, click <b>+ → Add users</b>, paste the service account email<?= $saEmail !== '' ? ' (' . h($saEmail) . ')' : '' ?> and give it the <b>Viewer</b> role.</li>
        <li>In <b>Search Console → Settings → Users and permissions</b>, click <b>Add user</b>, paste the same email, and choose <b>Restricted</b>.</li>
      </ol>
    </details>
  </div>

  <div class="card">
    <button type="submit" class="btn btn-primary">Save Connections</button>
  </div>
</form>

<dialog class="ad-dialog" id="adDialog" aria-labelledby="adTitle">
  <div class="ad-shell">
    <header class="ad-head">
      <div class="ad-head-main">
        <h2 id="adTitle">Analytics</h2>
        <div class="ad-sub" id="adSub"></div>
      </div>
      <button type="button" class="media-dialog-x" id="adClose" aria-label="Close">×</button>
    </header>
    <div class="ad-filters">
      <div class="ad-range" id="adRange" role="group" aria-label="Date range"></div>
      <span class="ad-updated" id="adUpdated"></span>
      <button type="button" class="btn btn-ghost btn-sm" id="adRefresh">Refresh</button>
      <span class="ad-links" id="adLinks"></span>
    </div>
    <div class="ad-body" id="adBody"></div>
  </div>
</dialog>
<script src="<?= asset_url('/admin/assets/analytics-dash.js') ?>" defer></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
